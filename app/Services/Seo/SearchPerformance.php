<?php

namespace App\Services\Seo;

/**
 * Real Google Search performance for the site. Bound to NullSearchPerformance
 * until Search Console API access is configured; a GSC-backed implementation
 * can be swapped in via AppServiceProvider without touching the dashboard.
 */
interface SearchPerformance
{
    public function isConnected(): bool;

    /**
     * @param  '7d'|'28d'|'3m'  $range
     * @return array{clicks: int, impressions: int, ctr: float, position: float}|null null when no real data exists
     */
    public function forUrl(string $url, string $range = '28d'): ?array;

    /**
     * @return array{clicks: int, impressions: int, ctr: float, position: float}|null
     */
    public function siteTotals(string $range = '28d'): ?array;

    /**
     * @return list<array{query: string, clicks: int, impressions: int, ctr: float, position: float}>
     */
    public function topQueries(string $range = '28d', int $limit = 10): array;

    /**
     * @return list<array{url: string, clicks: int, impressions: int, ctr: float, position: float}>
     */
    public function topPages(string $range = '28d', int $limit = 10): array;

    /**
     * Whether Google has indexed a URL: true/false when verified, null when unknown.
     */
    public function isIndexed(string $url): ?bool;
}
