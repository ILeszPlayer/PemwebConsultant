<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-[#C084FC] border border-transparent rounded-full font-semibold text-xs text-white uppercase tracking-widest hover:bg-[#7E22CE] focus:bg-[#7E22CE] active:bg-[#7E22CE] focus:outline-none focus:ring-2 focus:ring-[#C084FC] focus:ring-offset-2 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
