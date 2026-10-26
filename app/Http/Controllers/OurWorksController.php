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
}
