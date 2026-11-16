<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\Testimonial;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        return view('pages.home', [
            'services' => Service::where('is_published', true)->orderBy('sort_order')->orderBy('id')->get(),
            'testimonials' => Testimonial::where('featured', true)->orderBy('sort_order')->get(),
        ]);
    }
}
