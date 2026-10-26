<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\View\View;

class OurResourcesController extends Controller
{
    public function index(): View
    {
        return view('pages.our-resources', [
            'featuredArticle' => Article::where('featured', true)->first(),
            'articles' => Article::where('featured', false)->orderBy('published_at', 'desc')->get(),
        ]);
    }
}
