<x-filament-panels::page>
    @php($totals = $this->getTotals())

    <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
        <div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-white/10 dark:bg-gray-900">
            <p class="text-sm text-gray-500">Total Pages</p>
            <p class="text-2xl font-bold">{{ $totals['total'] }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-white/10 dark:bg-gray-900">
            <p class="text-sm text-gray-500">Indexable</p>
            <p class="text-2xl font-bold text-success-600">{{ $totals['indexable'] }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-white/10 dark:bg-gray-900">
            <p class="text-sm text-gray-500">Noindex</p>
            <p class="text-2xl font-bold text-gray-500">{{ $totals['noindex'] }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-white/10 dark:bg-gray-900">
            <p class="text-sm text-gray-500">Fully Optimized</p>
            <p class="text-2xl font-bold text-primary-600">{{ $totals['complete'] }}</p>
        </div>
    </div>

    <div class="mt-6 overflow-x-auto rounded-xl border border-gray-200 dark:border-white/10">
        <table class="w-full text-left text-sm">
            <thead class="bg-gray-50 dark:bg-white/5">
                <tr>
                    <th class="px-4 py-3 font-medium">Content Type</th>
                    <th class="px-4 py-3 font-medium">Total</th>
                    <th class="px-4 py-3 font-medium">Indexable</th>
                    <th class="px-4 py-3 font-medium">Noindex</th>
                    <th class="px-4 py-3 font-medium">Has Title</th>
                    <th class="px-4 py-3 font-medium">Has Description</th>
                    <th class="px-4 py-3 font-medium">Has Keywords</th>
                    <th class="px-4 py-3 font-medium">Fully Optimized</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sections as $section)
                <tr class="border-t border-gray-100 dark:border-white/5">
                    <td class="px-4 py-3 font-medium">{{ $section['label'] }}</td>
                    <td class="px-4 py-3">{{ $section['total'] }}</td>
                    <td class="px-4 py-3">{{ $section['indexable'] }}</td>
                    <td class="px-4 py-3">{{ $section['noindex'] }}</td>
                    <td class="px-4 py-3">{{ $section['with_title'] }} / {{ $section['total'] }}</td>
                    <td class="px-4 py-3">{{ $section['with_description'] }} / {{ $section['total'] }}</td>
                    <td class="px-4 py-3">{{ $section['with_keywords'] }} / {{ $section['total'] }}</td>
                    <td class="px-4 py-3">{{ $section['complete'] }} / {{ $section['total'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <p class="mt-4 text-sm text-gray-500">
        "Has Title/Description" below 100% doesn't mean the live page is blank — Static Pages fall back to the
        title/description written directly in their Blade template, and Articles/Case Studies/Portfolio Items fall
        back to their own title/excerpt/description field. These counts show where a custom SEO override hasn't
        been set yet, which is an optimization opportunity, not a broken page. "Has Keywords" has no fallback —
        a blank count there means no focus keyword has been recorded for that page at all.
    </p>

    <p class="mt-2 text-sm text-gray-500">
        Sitemap: <a href="{{ route('sitemap') }}" target="_blank" class="text-primary-600 underline">{{ route('sitemap') }}</a>
        currently lists every indexable page shown above (static + dynamic), generated live from the database.
    </p>
</x-filament-panels::page>
