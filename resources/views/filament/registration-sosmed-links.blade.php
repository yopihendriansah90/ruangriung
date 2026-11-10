<p class="text-sm text-gray-500 dark:text-gray-400">
    Username: <span class="font-semibold">{{ '@'.$username }}</span>
</p>

@if (empty($links))
    <p class="text-sm text-gray-500 dark:text-gray-400">Username sosmed belum diisi oleh peserta.</p>
@else
    <ul class="mt-3 flex flex-col gap-2">
        @foreach ($links as $label => $url)
            <li class="flex flex-col rounded-lg border border-gray-200 p-3 dark:border-gray-700">
                <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $label }}</span>
                <a href="{{ $url }}" target="_blank" rel="noopener" class="break-all text-sm text-primary-600 underline hover:text-primary-500 dark:text-primary-400 dark:hover:text-primary-300">
                    {{ $url }}
                </a>
            </li>
        @endforeach
    </ul>
@endif
