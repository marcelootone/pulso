<?php

namespace App\Http\Controllers;

use App\Models\Turma;
use App\Models\User;
use App\Models\Aluno;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;

class ImportacaoController extends Controller
{
    public function index()
    {
        // Busca turmas ativas ordenadas
        $turmas = Turma::where('ativa', true)->orderBy('serie')->orderBy('complemento')->get();
        
        // Busca eletivas e clubes ativos ordenados alfabeticamente
        $eletivas = \App\Models\Eletiva::where('ativa', true)->where('tipo', 'eletiva')->orderBy('nome')->get();
        $clubes = \App\Models\Eletiva::where('ativa', true)->where('tipo', 'clube')->orderBy('nome')->get();

        // Busca todos os alunos para o autocomplete da aba "Vincular Aluno"
        $alunos = Aluno::orderBy('nome')->get(['id', 'nome', 'ra']);

        return view('importacao.index', compact('turmas', 'eletivas', 'clubes', 'alunos'));
    }

    public function preview(Request $request)
    {
        $request->validate([
            'destino' => 'required|string',
            'planilha' => [
                'required',
                'file',
                'extensions:csv,txt,xlsx,xls',
                'max:5120',
                function ($attribute, $value, $fail) {
                    $nameWithoutExtension = pathinfo($value->getClientOriginalName(), PATHINFO_FILENAME);
                    if ($nameWithoutExtension !== 'ImportarUsuariosSIGAE') {
                        $fail('O nome do arquivo deve ser obrigatoriamente "ImportarUsuariosSIGAE" (ex: ImportarUsuariosSIGAE.xlsx). Planilha incorreta rejeitada.');
                    }
                },
            ],
        ], [
            'planilha.extensions' => 'A planilha deve ter uma das seguintes extensões: csv, txt, xlsx, xls.',
        ]);

        $file = $request->file('planilha');
        
        // Salva temporariamente preservando a extensão original
        $extensao = strtolower($file->getClientOriginalExtension());
        $nomeArquivo = uniqid('import_') . '.' . $extensao;
        $temp_file_path = $file->storeAs('temp', $nomeArquivo);

        $mimeType = $file->getMimeType();

        // Define o tipo de leitor verificando o MimeType real para evitar que
        // arquivos XLSX renomeados manualmente para .csv sejam lidos como texto (causando caracteres estranhos).
        if (in_array($mimeType, [
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/zip',
            'application/x-zip-compressed'
        ])) {
            $readerType = \Maatwebsite\Excel\Excel::XLSX;
        } elseif ($mimeType === 'application/vnd.ms-excel') {
            $readerType = \Maatwebsite\Excel\Excel::XLS;
        } else {
            $readerType = match ($extensao) {
                'csv', 'txt' => \Maatwebsite\Excel\Excel::CSV,
                'xls' => \Maatwebsite\Excel\Excel::XLS,
                default => \Maatwebsite\Excel\Excel::XLSX,
            };
        }

        $planilhas = Excel::toArray(new \stdClass, Storage::path($temp_file_path), null, $readerType);
        
        $dados = $planilhas[0] ?? [];
        if (count($dados) > 0) {
            unset($dados[0]); // Remove a primeira linha (cabeçalho)
        }

        // Formata a data de nascimento se vier como serial do Excel
        foreach ($dados as $key => $row) {
            if (isset($row[2]) && is_numeric($row[2])) {
                try {
                    $dados[$key][2] = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[2])->format('d/m/Y');
                } catch (\Exception $e) {
                    // ignora se falhar
                }
            }
        }

        $destino = $request->destino;

        // Valida se o destino existe
        $partes = explode('_', $destino);
        if (count($partes) !== 2) {
            return back()->withErrors(['destino' => 'Destino inválido.'])->withInput();
        }
        
        [$tipoDestino, $idDestino] = $partes;
        if ($tipoDestino === 'turma') {
            if (!Turma::where('id', $idDestino)->exists()) {
                return back()->withErrors(['destino' => 'Turma não encontrada.'])->withInput();
            }
        } elseif (in_array($tipoDestino, ['eletiva', 'clube'])) {
            if (!\App\Models\Eletiva::where('id', $idDestino)->exists()) {
                return back()->withErrors(['destino' => ucfirst($tipoDestino) . ' não encontrado(a).'])->withInput();
            }
        } else {
            return back()->withErrors(['destino' => 'Tipo de destino inválido.'])->withInput();
        }

        return view('importacao.preview', compact('dados', 'destino', 'temp_file_path'));
    }

    public function confirm(Request $request)
    {
        $request->validate([
            'destino' => 'required|string',
            'alunos' => 'required|array',
            'temp_file_path' => 'nullable|string'
        ]);

        $destino = $request->destino;
        $partes = explode('_', $destino);
        if (count($partes) !== 2) {
            return redirect()->route('importar.index')->with('error', 'Destino inválido.');
        }
        [$tipoDestino, $idDestino] = $partes;

        foreach ($request->alunos as $index => $row) {
            // $row[0] = RA, $row[1] = Nome
            if (empty($row[0]) || empty($row[1])) {
                continue; // Ignora se RA ou Nome estiverem vazios
            }

            $ra = $row[0];
            $nome = trim($row[1]);
            $nascimento = $row[2] ?? null;
            $sexo = isset($row[3]) ? strtoupper(trim($row[3])) : null;
            $telefone = $row[4] ?? null;

            // Converter data de nascimento de DD/MM/YYYY para YYYY-MM-DD
            $nascimento_db = null;
            if ($nascimento) {
                try {
                    if (strpos($nascimento, '/') !== false) {
                        $nascimento_db = \Carbon\Carbon::createFromFormat('d/m/Y', $nascimento)->format('Y-m-d');
                    } else {
                        $nascimento_db = clone \Carbon\Carbon::parse($nascimento)->format('Y-m-d');
                    }
                } catch (\Exception $e) {
                    $nascimento_db = null; // Em caso de formato inválido
                }
            }

            // Gera a senha (remove caracteres não numéricos do nascimento)
            $senhaPadrao = preg_replace('/[^0-9]/', '', $nascimento); 
            if (empty($senhaPadrao)) {
                $senhaPadrao = 'mudar123';
            }

            // Cria ou Atualiza a conta de acesso na tabela 'users'
            $user = User::updateOrCreate(
                ['ra' => $ra],
                [
                    'name'         => $nome,
                    'email'        => null,
                    'password'     => Hash::make($senhaPadrao),
                    'tipo_usuario' => \App\Models\User::TIPO_ESTUDANTE,
                    'nascimento'   => $nascimento_db,
                    'sexo'         => $sexo,
                    'telefone'     => $telefone,
                    'cpf'          => null,
                    'cidade'       => null,
                    'rua'          => null,
                    'numero'       => null,
                    'bairro'       => null,
                ]
            );

            // Cria ou Atualiza o registro do aluno
            $aluno = Aluno::updateOrCreate(
                ['ra' => $ra],
                [
                    'user_id'    => $user->id,
                    'nome'       => $nome,
                    'nascimento' => $nascimento_db,
                    'sexo'       => $sexo,
                    'telefone'   => $telefone,
                ]
            );

            // Vincula o aluno ao destino selecionado
            $anoLetivo = date('Y');
            
            if ($tipoDestino === 'turma') {
                $matricula = \App\Models\Matricula::firstOrCreate([
                    'aluno_id' => $aluno->id,
                    'ano_letivo' => $anoLetivo,
                ], [
                    'status' => 'Ativa',
                ]);

                \App\Models\Enturmacao::firstOrCreate([
                    'matricula_id' => $matricula->id,
                    'turma_id' => $idDestino,
                ], [
                    'tipo_vinculo' => 'REGULAR',
                    'data_entrada' => now(),
                    'status' => 'Ativo',
                ]);
            } else {
                // Eletiva ou Clube
                \Illuminate\Support\Facades\DB::table('aluno_eletiva')->updateOrInsert([
                    'aluno_id' => $aluno->id,
                    'eletiva_id' => $idDestino,
                ], [
                    'data_inscricao' => now(),
                    'status' => 'Ativo',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // Apaga o arquivo temporário
        if ($request->temp_file_path && Storage::exists($request->temp_file_path)) {
            Storage::delete($request->temp_file_path);
        }

        return redirect()->route('importar.index')->with('success', "Estudantes importados com sucesso para a turma!");
    }
}