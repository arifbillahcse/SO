<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\ArticleCategory;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OurResourcesController extends Controller
{
    public function index(Request $request): View
    {
        $request->validate(['category' => ['nullable', 'string', 'max:255']]);
        $activeCategory = $request->query('category');
        $categories = ArticleCategory::whereHas('articles', fn ($query) => $query->where('published_at', '<=', now()))
            ->orderBy('name')->get();
        $query = Article::where('published_at', '<=', now())->orderByDesc('published_at')->orderByDesc('id');
        if (filled($activeCategory)) {
            $query->whereHas('articleCategory', fn ($category) => $category->where('slug', $activeCategory));
        }
        $collection = $query->get();
        $featured = $collection->firstWhere('featured', true);

        return view('pages.our-resources', [
            'featuredArticle' => $featured,
            'articles' => $collection->reject(fn (Article $article): bool => $article->id === $featured?->id),
            'categories' => $categories,
            'activeCategory' => $activeCategory,
            'articleCount' => $collection->count(),
        ]);
    }

    public function show(Article $article): View
    {
        return view('pages.article-show', [
            'article' => $article,
            'relatedArticles' => Article::where('id', '!=', $article->id)->where('published_at', '<=', now())
                ->orderBy('published_at', 'desc')->limit(3)->get(),
        ]);
    }
}
