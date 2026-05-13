<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Editar Eletiva: {{ $eletiva->nome }}</h2>
    </x-slot>

    <div class="py-12 max-w-3xl mx-auto sm:px-6 lg:px-8">
        
        @if ($errors->any())
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 shadow-sm">
                <ul class="list-disc ml-5 font-bold">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif

        <div class="bg-white p-6 rounded-lg shadow">
            <form action="{{ route('eletivas.update', $eletiva->id) }}" method="POST">
                @csrf
                @method('PUT') <div class="mb-4">
                    <label class="block text-sm font-bold text-gray-700">Nome da Eletiva</label>
                    <input type="text" name="nome" value="{{ $eletiva->nome }}" class="w-full rounded-md border-gray-300" required>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-bold text-gray-700">Quantidade de Vagas</label>
                    <input type="number" name="vagas" value="{{ $eletiva->vagas }}" class="w-full rounded-md border-gray-300" required>
                    <p class="text-xs text-gray-500 mt-1">Alunos atualmente matriculados: {{ $eletiva->alunos()->count() }}</p>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-bold text-gray-700">Professor Responsável</label>
                    <select name="user_id" class="w-full rounded-md border-gray-300" required>
                        @foreach($professores as $prof) 
                            <option value="{{ $prof->id }}" {{ $eletiva->user_id == $prof->id ? 'selected' : '' }}>
                                {{ $prof->name }}
                            </option> 
                        @endforeach
                    </select>
                </div>

                <div class="flex justify-between items-center">
                    <a href="{{ route('eletivas.index') }}" class="text-gray-600 hover:text-gray-900 font-bold">Voltar</a>
                    <button type="submit" class="bg-indigo-600 text-black px-6 py-2 rounded-md font-bold hover:bg-indigo-700">SALVAR ALTERAÇÕES</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>