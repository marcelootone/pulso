<?php

namespace App\Imports;

use App\Models\Aluno;
use App\Models\User; // Importado para criar o login
use Illuminate\Support\Facades\Hash; // Importado para criptografar a senha
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class AlunosImport implements ToModel, WithStartRow, WithValidation, SkipsEmptyRows
{
    protected $turma_id;

    public function __construct($turma_id)
    {
        $this->turma_id = $turma_id;
    }

    public function startRow(): int
    {
        return 2; // Pula o cabeçalho
    }

    public function prepareForValidation($data, $index)
    {
        // Se a coluna 2 (Data) for um número serial do Excel, converte para DD/MM/AAAA
        if (isset($data[2]) && is_numeric($data[2])) {
            $data[2] = Date::excelToDateTimeObject($data[2])->format('d/m/Y');
        }

        return $data;
    }

    public function model(array $row)
    {
        if (!isset($row[0]) || !isset($row[1])) {
            return null;
        }

        // --- LÓGICA DE CRIAÇÃO DE LOGIN (OPÇÃO A) ---
        
        // 1. Gera a senha limpando a data de nascimento (ex: 15/05/2010 vira 15052010)
        $dataNascimento = $row[2] ?? '';
        $senhaPadrao = preg_replace('/[^0-9]/', '', $dataNascimento); 
        
        // Caso a planilha esteja sem data, define uma senha de segurança
        if(empty($senhaPadrao)) {
            $senhaPadrao = 'mudar123';
        }

        // Converte a data de nascimento para o banco de dados (YYYY-MM-DD)
        $nascimento_db = null;
        if ($dataNascimento) {
            try {
                if (strpos($dataNascimento, '/') !== false) {
                    $nascimento_db = \Carbon\Carbon::createFromFormat('d/m/Y', $dataNascimento)->format('Y-m-d');
                } else {
                    $nascimento_db = clone \Carbon\Carbon::parse($dataNascimento)->format('Y-m-d');
                }
            } catch (\Exception $e) {
                $nascimento_db = null;
            }
        }

        // 2. Cria ou Atualiza a conta de acesso na tabela 'users'
        User::updateOrCreate(
            ['ra' => $row[0]], // Busca pelo RA único
            [
                'name'         => trim($row[1]),
                'email'        => null,
                'password'     => Hash::make($senhaPadrao), // Senha criptografada
                'tipo_usuario' => \App\Models\User::TIPO_ESTUDANTE,
                'nascimento'   => $nascimento_db,
                'sexo'         => isset($row[3]) ? strtoupper(trim($row[3])) : null,
                'telefone'     => $row[4] ?? null,
                'cpf'          => null,
                'cidade'       => null,
                'rua'          => null,
                'numero'       => null,
                'bairro'       => null,
            ]
        );

        // 3. Cria ou Atualiza o registro do aluno na tabela 'alunos'
        return Aluno::updateOrCreate(
            ['ra' => $row[0]], 
            [
                'turma_id'   => $this->turma_id,
                'nome'       => trim($row[1]),
                'nascimento' => $nascimento_db,
                'sexo'       => isset($row[3]) ? strtoupper(trim($row[3])) : null,
                'telefone'   => $row[4] ?? null,
                'nome_mae'   => isset($row[5]) ? trim($row[5]) : null,
                'telefone_responsavel' => isset($row[6]) ? trim($row[6]) : null,
                'cep'        => isset($row[7]) ? trim($row[7]) : null,
                'logradouro' => isset($row[8]) ? trim($row[8]) : null,
                'status_matricula' => isset($row[9]) && trim($row[9]) !== '' ? trim($row[9]) : 'Ativo',
            ]
        );
    }

    public function rules(): array
    {
        return [
            '0' => 'required',
            '1' => 'required|string|regex:/^[a-zA-ZÀ-ÿ\s\'\.\-]+$/',
            '2' => 'nullable|regex:/^\d{2}[\/\-]\d{2}[\/\-]\d{4}$/',
            '3' => 'nullable|in:M,F,m,f',
            '4' => 'nullable',
            '5' => 'nullable',
            '6' => 'nullable',
            '7' => 'nullable',
            '8' => 'nullable',
            '9' => 'nullable|in:Novato,Transferido,Ativo',
        ];
    }

    public function customValidationMessages()
    {
        return [
            '0.required' => 'A coluna ID (RA) não pode estar vazia na planilha.',
            '1.required' => 'A coluna NOME é obrigatória em todas as linhas.',
            '1.regex'    => 'Formato inválido: O NOME não pode conter números ou símbolos especiais.',
            '2.regex'    => 'Formato inválido: A DATA DE NASCIMENTO deve estar no padrão DD/MM/AAAA (ex: 15/05/2010).',
            '3.in'       => 'Formato inválido: A coluna SEXO aceita apenas as letras "M" ou "F".',
        ];
    }
}