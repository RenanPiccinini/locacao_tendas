@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-gray-300 dark:border-emerald-700/60 dark:bg-[#021811] dark:text-white placeholder-gray-400 dark:placeholder-emerald-400/40 focus:border-amber-400 focus:ring-amber-400/20 rounded-md shadow-sm']) }}>
