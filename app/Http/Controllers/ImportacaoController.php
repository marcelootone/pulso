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
        // Busca apenas as turmas ativas para mostrar no Dropdown
        $turmas = Turma::where('ativa', true)->get();

        // Busca todos os alunos para o autocomplete da aba "Vincular Aluno"
        $alunos = Aluno::orderBy('nome')->get(['id', 'nome', 'ra']);

        return view('importacao.index', compact('turmas', 'alunos'));
    }

    public function preview(Request $request)
    {
        $request->validate([
            'turma_id' => 'required|exists:turmas,id',
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
        // Salva temporariamente
        $temp_file_path = $file->store('temp');

        $planilhas = Excel::toArray(new \stdClass, Storage::path($temp_file_path));
        
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

        $turma_id = $request->turma_id;

        return view('importacao.preview', compact('dados', 'turma_id', 'temp_file_path'));
    }

    public function confirm(Request $request)
    {
        $request->validate([
            'turma_id' => 'required|exists:turmas,id',
            'alunos' => 'required|array',
            'temp_file_path' => 'nullable|string'
        ]);

        $turma_id = $request->turma_id;

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

            // Vincula o aluno à turma importada sem remover vínculos anteriores
            $anoLetivo = date('Y');
            
            $matricula = \App\Models\Matricula::firstOrCreate([
                'aluno_id' => $aluno->id,
                'ano_letivo' => $anoLetivo,
            ], [
                'status' => 'Ativa',
            ]);

            \App\Models\Enturmacao::firstOrCreate([
                'matricula_id' => $matricula->id,
                'turma_id' => $turma_id,
            ], [
                'tipo_vinculo' => 'REGULAR',
                'data_entrada' => now(),
                'status' => 'Ativo',
            ]);
        }

        // Apaga o arquivo temporário
        if ($request->temp_file_path && Storage::exists($request->temp_file_path)) {
            Storage::delete($request->temp_file_path);
        }

        return redirect()->route('importar.index')->with('success', "Estudantes importados com sucesso para a turma!");
    }
}