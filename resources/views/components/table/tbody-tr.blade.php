<tr
    {{ $attributes->merge(['class' => 'border-t last:border-b border-slate-500 bg-slate-200 dark:bg-slate-300 hover:bg-slate-300 dark:hover:bg-slate-400 hover:font-bold cursor-pointer']) }}>
    {{ $slot }}
</tr>
