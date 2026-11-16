<?php

namespace App\Http\Controllers;

use App\Models\PortfolioItem;
use Illuminate\View\View;

class OurWorksController extends Controller
{
    public function index(): View
    {
        return view('pages.our-works', [
            'portfolioItems' => PortfolioItem::orderBy('sort_order')->get(),
        ]);
    }

    public function show(PortfolioItem $portfolioItem): View
    {
        return view('pages.portfolio-item-show', [
            'portfolioItem' => $portfolioItem,
            'relatedItems' => PortfolioItem::where('id', '!=', $portfolioItem->id)
                ->where('project_type', $portfolioItem->project_type)
                ->orderByRaw('CASE WHEN category = ? THEN 0 ELSE 1 END', [$portfolioItem->category])
                ->orderBy('sort_order')->limit(3)->get(),
        ]);
    }
}
