<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Preview da Importação') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6 shadow-sm">
                <div class="flex">
                    <div class="ml-3">
                        <p class="text-sm text-yellow-700 font-bold">
                            Atenção: Verificação de dados.
                        </p>
                        <p class="text-sm text-yellow-600 mt-1">
                            Verifique as informações importadas pela planilha, faça as edições caso necessárias nos campos abaixo e clique no botão verde no final da página para confirmar e gravar os alunos no sistema.
                        </p>
                    </div>
                </div>
            </div>

            <div class="overflow-hidden">
                <form action="{{ route('importar.confirm') }}" method="POST">
                    @csrf
                    
                    <input type="hidden" name="turma_id" value="{{ $turma_id }}">
                    <input type="hidden" name="temp_file_path" value="{{ $temp_file_path }}">

                    <div class="overflow-x-auto pb-4">
                        <table class="w-full text-sm text-left text-gray-700 border-separate" style="border-spacing: 0 10px;">
                            <thead class="text-xs text-white uppercase bg-[#343a40]">
                                <tr>
                                    <th scope="col" class="px-4 py-4 border-r border-gray-500 w-32 font-medium">ID (RA)</th>
                                    <th scope="col" class="px-4 py-4 border-r border-gray-500 font-medium">NOME</th>
                                    <th scope="col" class="px-4 py-4 border-r border-gray-500 w-48 font-medium">NASCIMENTO</th>
                                    <th scope="col" class="px-4 py-4 border-r border-gray-500 w-24 font-medium">SEXO</th>
                                    <th scope="col" class="px-4 py-4 w-64 font-medium">TELEFONE</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($dados as $index => $row)
                                    @if(empty($row[0]) && empty($row[1]))
                                        @continue
                                    @endif
                                    <tr class="bg-gray-100 shadow-sm">
                                        <td class="px-2 py-2 align-top">
                                            <input type="text" name="alunos[{{ $index }}][0]" value="{{ $row[0] ?? '' }}" class="w-full border-gray-300 rounded shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500 bg-white h-10" required>
                                        </td>
                                        <td class="px-2 py-2 align-top">
                                            <input type="text" name="alunos[{{ $index }}][1]" value="{{ $row[1] ?? '' }}" class="w-full border-gray-300 rounded shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500 bg-white h-10" required>
                                        </td>
                                        <td class="px-2 py-2 align-top relative">
                                            <input type="text" name="alunos[{{ $index }}][2]" value="{{ $row[2] ?? '' }}" class="w-full border-gray-300 rounded shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500 bg-white h-10 pr-10" placeholder="DD/MM/AAAA">
                                            <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none mt-2">
                                                <svg class="h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                            </div>
                                        </td>
                                        <td class="px-2 py-2 align-top">
                                            <select name="alunos[{{ $index }}][3]" class="w-full border-gray-300 rounded shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500 bg-white h-10">
                                                <option value=""></option>
                                                <option value="M" {{ (isset($row[3]) && strtoupper(trim($row[3])) == 'M') ? 'selected' : '' }}>M</option>
                                                <option value="F" {{ (isset($row[3]) && strtoupper(trim($row[3])) == 'F') ? 'selected' : '' }}>F</option>
                                            </select>
                                        </td>
                                        <td class="px-2 py-2 align-top">
                                            <textarea name="alunos[{{ $index }}][4]" rows="2" class="w-full border-gray-300 rounded shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500 bg-white resize-y">{{ $row[4] ?? '' }}</textarea>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="flex items-center justify-end mt-4 mb-4">
                        <button type="submit" class="px-6 py-2 border border-transparent rounded shadow-sm text-sm font-bold text-white bg-[#28a745] hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20"><path d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5.414A1 1 0 0017.586 4L16 2.414A1 1 0 0015.293 2H4zm3 1h6v4H7V4zm8 8H5v4h10v-4z"></path></svg>
                            SALVAR
                        </button>
                    </div>

                </form>
            </div>

        </div>
    </div>
</x-app-layout>
