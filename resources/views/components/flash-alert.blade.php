@if(session('success'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" class="mb-6 transition-all duration-500">
        <x-alert type="success" :message="session('success')" />
    </div>
@endif

@if(session('error'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" class="mb-6 transition-all duration-500">
        <x-alert type="error" :message="session('error')" />
    </div>
@endif

@if(session('warning'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" class="mb-6 transition-all duration-500">
        <x-alert type="warning" :message="session('warning')" />
    </div>
@endif

@if(session('info'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" class="mb-6 transition-all duration-500">
        <x-alert type="info" :message="session('info')" />
    </div>
@endif
