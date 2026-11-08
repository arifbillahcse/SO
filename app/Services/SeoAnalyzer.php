<?php

namespace App\Services;

use App\Models\PageSeo;
use Illuminate\Support\Facades\Http;

class SeoAnalyzer
{
    /**
     * @return array<int, array{id: string, label: string, passed: bool, message: string}>
     */
    public function analyze(PageSeo $pageSeo, string $url): array
    {
        $keywords = $pageSeo->focus_keywords ?? [];
        $keyword = trim($keywords[0] ?? '');

        if ($keyword === '') {
            return [
                ['id' => 'keyword', 'label' => 'Focus keyword set', 'passed' => false, 'message' => 'Add at least one focus keyword above to run an SEO analysis.'],
            ];
        }

        try {
            $response = Http::timeout(10)->get($url);
        } catch (\Throwable) {
            return [
                ['id' => 'fetch', 'label' => 'Page reachable', 'passed' => false, 'message' => "Couldn't fetch {$url} to analyze it — the site may not be reachable from the server right now."],
            ];
        }

        if (! $response->successful()) {
            return [
                ['id' => 'fetch', 'label' => 'Page reachable', 'passed' => false, 'message' => "Page returned HTTP {$response->status()} — fix that before analyzing content."],
            ];
        }

        libxml_use_internal_errors(true);
        $dom = new \DOMDocument();
        $dom->loadHTML($response->body());
        libxml_use_internal_errors(false);

        $bodyText = trim(preg_replace('/\s+/', ' ', $dom->textContent ?? ''));
        $wordCount = $bodyText === '' ? 0 : str_word_count($bodyText);

        $headings = [];
        foreach (['h1', 'h2', 'h3'] as $tag) {
            foreach ($dom->getElementsByTagName($tag) as $node) {
                $headings[] = $node->textContent;
            }
        }
        $headingsText = implode(' ', $headings);

        $title = $pageSeo->title ?: '';
        $description = $pageSeo->meta_description ?: '';
        $slug = $pageSeo->route_name;
        $keywordLower = mb_strtolower($keyword);

        $firstWords = implode(' ', array_slice(preg_split('/\s+/', $bodyText), 0, 100));

        $keywordOccurrences = $bodyText === '' ? 0 : substr_count(mb_strtolower($bodyText), $keywordLower);
        $density = $wordCount > 0 ? round(($keywordOccurrences / $wordCount) * 100, 2) : 0;

        $checks = [
            [
                'id' => 'title',
                'label' => 'Focus keyword in SEO title',
                'passed' => str_contains(mb_strtolower($title), $keywordLower),
                'message' => 'Include "'.$keyword.'" in the SEO Title field above.',
            ],
            [
                'id' => 'description',
                'label' => 'Focus keyword in meta description',
                'passed' => str_contains(mb_strtolower($description), $keywordLower),
                'message' => 'Include "'.$keyword.'" in the Meta Description field above.',
            ],
            [
                'id' => 'slug',
                'label' => 'Focus keyword in page URL',
                'passed' => str_contains(mb_strtolower(str_replace('-', ' ', $slug)), $keywordLower),
                'message' => 'The page URL/route name doesn\'t contain "'.$keyword.'".',
            ],
            [
                'id' => 'intro',
                'label' => 'Focus keyword in the first 100 words',
                'passed' => str_contains(mb_strtolower($firstWords), $keywordLower),
                'message' => 'Mention "'.$keyword.'" early in the page content.',
            ],
            [
                'id' => 'heading',
                'label' => 'Focus keyword in a heading (H1–H3)',
                'passed' => str_contains(mb_strtolower($headingsText), $keywordLower),
                'message' => 'Use "'.$keyword.'" in at least one heading on the page.',
            ],
            [
                'id' => 'density',
                'label' => "Keyword density (0.5%–2.5%, currently {$density}%)",
                'passed' => $density >= 0.5 && $density <= 2.5,
                'message' => $density < 0.5
                    ? 'The keyword appears too rarely relative to the content length.'
                    : 'The keyword appears too often — this can look like keyword stuffing to Google.',
            ],
            [
                'id' => 'title_length',
                'label' => 'SEO title length (30–60 characters, currently '.mb_strlen($title).')',
                'passed' => mb_strlen($title) >= 30 && mb_strlen($title) <= 60,
                'message' => 'Titles outside this range often get cut off or look thin in Google search results.',
            ],
            [
                'id' => 'description_length',
                'label' => 'Meta description length (120–160 characters, currently '.mb_strlen($description).')',
                'passed' => mb_strlen($description) >= 120 && mb_strlen($description) <= 160,
                'message' => 'Descriptions outside this range often get truncated or look thin in search results.',
            ],
            [
                'id' => 'content_length',
                'label' => "Content length (300+ words, currently {$wordCount})",
                'passed' => $wordCount >= 300,
                'message' => 'Thin pages tend to rank worse — aim for at least 300 words of visible content.',
            ],
            [
                'id' => 'readability',
                'label' => 'Readability (Flesch Reading Ease)',
                'passed' => $this->fleschReadingEase($bodyText) >= 50,
                'message' => 'The text may be hard to read — try shorter sentences and simpler words.',
            ],
        ];

        return $checks;
    }

    private function fleschReadingEase(string $text): float
    {
        $sentences = max(1, preg_match_all('/[.!?]+/', $text));
        $words = preg_split('/\s+/', trim($text));
        $words = array_filter($words, fn ($w) => $w !== '');
        $wordCount = max(1, count($words));

        $syllables = 0;
        foreach ($words as $word) {
            $syllables += $this->countSyllables($word);
        }

        return round(206.835 - (1.015 * ($wordCount / $sentences)) - (84.6 * ($syllables / $wordCount)), 1);
    }

    private function countSyllables(string $word): int
    {
        $word = mb_strtolower(preg_replace('/[^a-z]/i', '', $word));

        if ($word === '') {
            return 0;
        }

        preg_match_all('/[aeiouy]+/', $word, $matches);
        $count = count($matches[0]);

        if (str_ends_with($word, 'e') && $count > 1) {
            $count--;
        }

        return max(1, $count);
    }
}
