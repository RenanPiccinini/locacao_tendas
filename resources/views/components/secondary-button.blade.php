<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center px-4 py-2 bg-white dark:bg-[#04291c] border border-gray-300 dark:border-[#083d2c] rounded-md font-semibold text-xs text-gray-700 dark:text-emerald-100 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-[#073827] focus:outline-none focus:ring-2 focus:ring-amber-400 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
