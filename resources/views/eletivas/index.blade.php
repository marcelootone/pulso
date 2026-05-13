<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 leading-tight">Gestão de Eletivas / Clubes</h2></x-slot>

    <div class="py-12 max-w-7xl mx-auto sm:px-6 lg:px-8">
        
        @if(session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 shadow-sm">
                <p class="font-bold">✅ {{ session('success') }}</p>
            </div>
        @endif

        <div class="bg-white p-6 rounded-lg shadow mb-8 border-t-4 border-indigo-500">
            <h3 class="font-bold text-lg mb-4">Abrir Nova Turma Eletiva</h3>
            <form action="{{ route('eletivas.store') }}" method="POST" class="flex gap-4 items-end">
                @csrf
                <div class="flex-1">
                    <label class="block text-sm font-bold text-gray-700">Nome da Eletiva (Ex: Robótica)</label>
                    <input type="text" name="nome" class="w-full rounded-md border-gray-300" required>
                </div>
                <div class="w-32">
                    <label class="block text-sm font-bold text-gray-700">Vagas</label>
                    <input type="number" name="vagas" class="w-full rounded-md border-gray-300" min="1" required>
                </div>
                <div class="flex-1">
                    <label class="block text-sm font-bold text-gray-700">Professor Responsável</label>
                    <select name="user_id" class="w-full rounded-md border-gray-300" required>
                        @foreach($professores as $prof) <option value="{{ $prof->id }}">{{ $prof->name }}</option> @endforeach
                    </select>
                </div>
                <button type="submit" class="bg-indigo-600 text-black px-6 py-2 rounded-md font-bold hover:bg-indigo-700">CRIAR</button>
            </form>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach($eletivas as $eletiva)
            <div class="bg-white p-6 rounded-lg shadow border border-gray-200 flex flex-col justify-between">
                <div>
                    <h4 class="text-xl font-bold text-gray-800">{{ $eletiva->nome }}</h4>
                    <p class="text-sm text-gray-600 mb-4">Prof: {{ $eletiva->professor->name }}</p>
                    <div class="flex justify-between items-center mb-4">
                        <span class="bg-blue-100 text-blue-800 text-xs font-bold px-2 py-1 rounded">
                            {{ $eletiva->alunos->count() }} / {{ $eletiva->vagas }} Vagas
                        </span>
                    </div>
                </div>
                
                <div class="flex flex-col gap-2 mt-2">
                    <a href="{{ route('eletivas.show', $eletiva->id) }}" class="block w-full text-center bg-gray-800 text-black px-4 py-2 rounded hover:bg-gray-900 font-bold text-sm">
                        👥 MATRÍCULAS
                    </a>
                    
                    <div class="flex gap-2">
                        <a href="{{ route('eletivas.edit', $eletiva->id) }}" class="flex-1 text-center bg-yellow-500 text-black px-2 py-1 rounded hover:bg-yellow-600 font-bold text-xs flex items-center justify-center">
                            ✏️ EDITAR
                        </a>
                        
                        <form action="{{ route('eletivas.destroy', $eletiva->id) }}" method="POST" class="flex-1" onsubmit="return confirm('Tem certeza que deseja apagar esta Eletiva inteira?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full bg-red-600 text-black px-2 py-1 rounded hover:bg-red-700 font-bold text-xs h-full min-h-[32px]">
                                🗑️ EXCLUIR
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</x-app-layout>