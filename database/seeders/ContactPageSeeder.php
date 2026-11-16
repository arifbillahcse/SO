<?php

namespace Database\Seeders;

use App\Models\ContactPageSettings;
use App\Models\PageSeo;
use Illuminate\Database\Seeder;

class ContactPageSeeder extends Seeder
{
    public function run(): void
    {
        ContactPageSettings::current();
        $data = json_decode(file_get_contents(database_path('seeders/data/main-pages-seo.json')), true, flags: JSON_THROW_ON_ERROR);
        $entry = collect($data['pages'])->firstWhere('route_name', 'contact');
        $seo = PageSeo::forRoute('contact');
        $fields = array_intersect_key($entry, array_flip(['title', 'meta_description', 'focus_keyword', 'secondary_keywords', 'content_notes']));
        $fields += [
            'og_title' => $seo->title ?: $entry['title'],
            'og_description' => $seo->meta_description ?: $entry['meta_description'],
            'og_image' => config('seo.default_og_image'),
            'twitter_title' => $seo->title ?: $entry['title'],
            'twitter_description' => $seo->meta_description ?: $entry['meta_description'],
            'twitter_image' => config('seo.default_og_image'),
        ];
        foreach ($fields as $field => $value) {
            if (blank($seo->getAttribute($field))) {
                $seo->setAttribute($field, $value);
            }
        }
        if ($seo->isDirty()) {
            $seo->save();
        }
    }
}
