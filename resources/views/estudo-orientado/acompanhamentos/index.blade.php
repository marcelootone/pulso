<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Meus Acompanhamentos - Estudo Orientado') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if($acompanhamentos->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($acompanhamentos as $acomp)
                                <div class="border rounded-lg p-6 shadow-sm hover:shadow-md transition-shadow bg-white flex flex-col justify-between">
                                    <div>
                                        <div class="flex justify-between items-start mb-4">
                                            <h3 class="text-lg font-bold text-gray-800">{{ $acomp->aluno->nome }}</h3>
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                                {{ $acomp->status === 'Aprovada' ? 'bg-blue-100 text-blue-800' : '' }}
                                                {{ $acomp->status === 'EmAtendimento' ? 'bg-indigo-100 text-indigo-800' : '' }}
                                                {{ $acomp->status === 'Concluida' ? 'bg-green-100 text-green-800' : '' }}
                                            ">
                                                {{ $acomp->status }}
                                            </span>
                                        </div>
                                        <p class="text-sm text-gray-600 mb-2"><strong>Turma:</strong> {{ $acomp->turma->serie }} {{ $acomp->turma->complemento ?? '' }}</p>
                                        <p class="text-sm text-gray-600 mb-4 line-clamp-2" title="{{ $acomp->motivo }}">
                                            <strong>Motivo:</strong> {{ $acomp->motivo }}
                                        </p>
                                    </div>
                                    <div class="mt-4 pt-4 border-t flex justify-end">
                                        <a href="{{ route('estudo-orientado.acompanhamentos.show', $acomp->id) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                            Acessar Prontuário
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-6">
                            {{ $acompanhamentos->links() }}
                        </div>
                    @else
                        <div class="text-center py-12 text-gray-500">
                            <x-heroicon-o-folder-open class="mx-auto h-12 w-12 text-gray-400 mb-4" />
                            <p class="text-lg">Nenhum aluno em acompanhamento no momento.</p>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
