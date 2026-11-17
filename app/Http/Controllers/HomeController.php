<?php

namespace App\Http\Controllers;

use App\Models\ClientBrand;
use App\Models\PortfolioItem;
use App\Models\Service;
use App\Models\Testimonial;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        return view('pages.home', [
            'clientBrands' => ClientBrand::where('is_published', true)->orderBy('sort_order')->orderBy('id')->get(),
            'homePortfolioItems' => PortfolioItem::orderBy('sort_order')->orderBy('id')->limit(6)->get(),
            'services' => Service::where('is_published', true)->orderBy('sort_order')->orderBy('id')->get(),
            'testimonials' => Testimonial::where('featured', true)->orderBy('sort_order')->orderBy('id')->get(),
        ]);
    }
}
