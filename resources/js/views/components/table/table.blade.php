<table {{ $attributes->merge(['class' => 'border-t bg-slate-500 border-slate-300 dark:bg-slate-700 dark:border-slate-500 min-w-full']) }}>
    {{ $thead }}
    {{ $tbody }}
    @if (isset($tfoot))
        {{ $tfoot }}
    @endif
</table>
