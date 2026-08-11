@props([
    'texto' => '© BITMAN Vault — Plataforma de Criptografía y Gestión Segura de Secretos',
])

<footer {{ $attributes->merge(['class' => 'relative z-10 w-full py-4 text-center border-t border-slate-800/80 bg-slate-900/40 text-xs text-slate-500']) }}>
    <p>{{ $texto }}</p>
</footer>
