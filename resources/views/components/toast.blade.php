{{-- Toast Notification Component - Matches mockup design --}}
{{-- Included in layout, handles: session('success'), session('error'), session('warning'), session('info'), $errors --}}

<div id="toast-stack" class="fixed top-6 right-6 z-[9999] flex flex-col gap-3 pointer-events-none" style="max-width:400px;">

    {{-- Success Toast --}}
    @if(session('success'))
    <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show"
         x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-12"
         x-transition:enter-end="opacity-100 translate-x-0" x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-x-0" x-transition:leave-end="opacity-0 translate-x-12"
         class="pointer-events-auto flex items-center gap-3 bg-white rounded-lg shadow-lg border border-gray-100 overflow-hidden min-w-[320px]">
        <div class="w-[5px] self-stretch bg-green-500 flex-shrink-0 rounded-l-lg"></div>
        <div class="w-9 h-9 rounded-full bg-green-500 flex items-center justify-center flex-shrink-0 my-3 ml-1">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
        </div>
        <div class="flex-1 py-3 pr-2">
            <h4 class="text-sm font-bold text-gray-800">Berhasil</h4>
            <p class="text-xs text-gray-500 mt-0.5 leading-relaxed">{{ session('success') }}</p>
        </div>
        <button @click="show = false" class="text-gray-400 hover:text-gray-600 p-2 mr-2 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>
    @endif

    {{-- Error Toast --}}
    @if(session('error'))
    <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 7000)" x-show="show"
         x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-12"
         x-transition:enter-end="opacity-100 translate-x-0" x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-x-0" x-transition:leave-end="opacity-0 translate-x-12"
         class="pointer-events-auto flex items-center gap-3 bg-white rounded-lg shadow-lg border border-gray-100 overflow-hidden min-w-[320px]">
        <div class="w-[5px] self-stretch bg-red-500 flex-shrink-0 rounded-l-lg"></div>
        <div class="w-9 h-9 rounded-full bg-red-500 flex items-center justify-center flex-shrink-0 my-3 ml-1">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
        </div>
        <div class="flex-1 py-3 pr-2">
            <h4 class="text-sm font-bold text-gray-800">Gagal</h4>
            <p class="text-xs text-gray-500 mt-0.5 leading-relaxed">{{ session('error') }}</p>
        </div>
        <button @click="show = false" class="text-gray-400 hover:text-gray-600 p-2 mr-2 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>
    @endif

    {{-- Validation Errors Toast (Disabled so errors stay inline under form fields) --}}
    @if(false && $errors->any() && !$errors->has('edit'))
    <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 7000)" x-show="show"
         x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-12"
         x-transition:enter-end="opacity-100 translate-x-0" x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-x-0" x-transition:leave-end="opacity-0 translate-x-12"
         class="pointer-events-auto flex items-center gap-3 bg-white rounded-lg shadow-lg border border-gray-100 overflow-hidden min-w-[320px]">
        <div class="w-[5px] self-stretch bg-red-500 flex-shrink-0 rounded-l-lg"></div>
        <div class="w-9 h-9 rounded-full bg-red-500 flex items-center justify-center flex-shrink-0 my-3 ml-1">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
        </div>
        <div class="flex-1 py-3 pr-2">
            <h4 class="text-sm font-bold text-gray-800">Gagal</h4>
            <p class="text-xs text-gray-500 mt-0.5 leading-relaxed">{{ $errors->first() }}</p>
        </div>
        <button @click="show = false" class="text-gray-400 hover:text-gray-600 p-2 mr-2 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>
    @endif

    {{-- Edit Error Toast (jadwal) --}}
    @if($errors->has('edit'))
    <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 7000)" x-show="show"
         x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-12"
         x-transition:enter-end="opacity-100 translate-x-0" x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-x-0" x-transition:leave-end="opacity-0 translate-x-12"
         class="pointer-events-auto flex items-center gap-3 bg-white rounded-lg shadow-lg border border-gray-100 overflow-hidden min-w-[320px]">
        <div class="w-[5px] self-stretch bg-red-500 flex-shrink-0 rounded-l-lg"></div>
        <div class="w-9 h-9 rounded-full bg-red-500 flex items-center justify-center flex-shrink-0 my-3 ml-1">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
        </div>
        <div class="flex-1 py-3 pr-2">
            <h4 class="text-sm font-bold text-gray-800">Gagal</h4>
            <p class="text-xs text-gray-500 mt-0.5 leading-relaxed">{{ $errors->first('edit') }}</p>
        </div>
        <button @click="show = false" class="text-gray-400 hover:text-gray-600 p-2 mr-2 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>
    @endif

    {{-- Info Toast --}}
    @if(session('info'))
    <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show"
         x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-12"
         x-transition:enter-end="opacity-100 translate-x-0" x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-x-0" x-transition:leave-end="opacity-0 translate-x-12"
         class="pointer-events-auto flex items-center gap-3 bg-white rounded-lg shadow-lg border border-gray-100 overflow-hidden min-w-[320px]">
        <div class="w-[5px] self-stretch bg-blue-500 flex-shrink-0 rounded-l-lg"></div>
        <div class="w-9 h-9 rounded-full bg-blue-500 flex items-center justify-center flex-shrink-0 my-3 ml-1">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div class="flex-1 py-3 pr-2">
            <h4 class="text-sm font-bold text-gray-800">Info</h4>
            <p class="text-xs text-gray-500 mt-0.5 leading-relaxed">{{ session('info') }}</p>
        </div>
        <button @click="show = false" class="text-gray-400 hover:text-gray-600 p-2 mr-2 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>
    @endif

    {{-- Warning Toast --}}
    @if(session('warning'))
    <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 6000)" x-show="show"
         x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-12"
         x-transition:enter-end="opacity-100 translate-x-0" x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-x-0" x-transition:leave-end="opacity-0 translate-x-12"
         class="pointer-events-auto flex items-center gap-3 bg-white rounded-lg shadow-lg border border-gray-100 overflow-hidden min-w-[320px]">
        <div class="w-[5px] self-stretch bg-amber-400 flex-shrink-0 rounded-l-lg"></div>
        <div class="w-9 h-9 rounded-full bg-amber-400 flex items-center justify-center flex-shrink-0 my-3 ml-1">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
        </div>
        <div class="flex-1 py-3 pr-2">
            <h4 class="text-sm font-bold text-gray-800">Perhatian</h4>
            <p class="text-xs text-gray-500 mt-0.5 leading-relaxed">{{ session('warning') }}</p>
        </div>
        <button @click="show = false" class="text-gray-400 hover:text-gray-600 p-2 mr-2 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>
    @endif

</div>
