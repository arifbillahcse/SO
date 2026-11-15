<?php

namespace App\Services\Seo;

/**
 * Used while Google Search Console is not connected: reports no data rather
 * than estimates, so the dashboard shows "Not connected" / "Unknown".
 */
class NullSearchPerformance implements SearchPerformance
{
    public function isConnected(): bool
    {
        return false;
    }

    public function forUrl(string $url, string $range = '28d'): ?array
    {
        return null;
    }

    public function siteTotals(string $range = '28d'): ?array
    {
        return null;
    }

    public function topQueries(string $range = '28d', int $limit = 10): array
    {
        return [];
    }

    public function topPages(string $range = '28d', int $limit = 10): array
    {
        return [];
    }

    public function isIndexed(string $url): ?bool
    {
        return null;
    }
}
