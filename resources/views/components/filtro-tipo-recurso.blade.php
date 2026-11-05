@props(['action', 'tipo' => null])

<form action="{{ $action }}" method="GET" class="mb-6 flex flex-col sm:flex-row sm:items-end gap-3">
    <div class="w-full sm:w-64">
        <label for="filtro-tipo" class="block text-sm font-semibold text-gray-700 mb-2">Tipo</label>
        <select id="filtro-tipo" name="tipo" class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
            <option value="" @selected(!$tipo)>Todos</option>
            <option value="espaco" @selected($tipo === 'espaco')>Espaços</option>
            <option value="equipamento" @selected($tipo === 'equipamento')>Equipamentos</option>
        </select>
        @error('tipo')
            <p class="mt-1 text-sm text-red-600" role="alert">{{ $message }}</p>
        @enderror
    </div>
    <x-button type="submit">Filtrar</x-button>
</form>
