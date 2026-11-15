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
            'services' => Service::orderBy('sort_order')->get(),
            'testimonials' => Testimonial::where('featured', true)->orderBy('sort_order')->get(),
        ]);
    }
}
