@props(['size' => 'md', 'showText' => true, 'justify' => 'center'])

@php
    $iconSize = $size === 'lg' ? 'w-12 h-12 rounded-xl shadow-md' : 'w-8 h-8 rounded-lg shadow-sm';
    $svgSize = $size === 'lg' ? 'w-6 h-6' : 'w-4 h-4';
    $textSize = $size === 'lg' ? 'text-3xl' : 'text-xl';
    
    $justifyClass = 'justify-' . $justify;
@endphp

<div class="flex items-center {{ $justifyClass }} gap-3">
    <div class="{{ $iconSize }} bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center">
        <svg class="{{ $svgSize }} text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
        </svg>
    </div>
    @if($showText)
        <h1 class="{{ $textSize }} font-bold text-slate-100 tracking-tight">BITMAN <span class="text-blue-400">Vault</span></h1>
    @endif
</div>
