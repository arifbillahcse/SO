<?php

namespace App\Http\Controllers;

use App\Models\CaseStudy;
use Illuminate\View\View;

class CaseStudyController extends Controller
{
    public function index(): View
    {
        return view('pages.case-study', [
            'featuredCaseStudy' => CaseStudy::where('featured', true)->first(),
            'caseStudies' => CaseStudy::where('featured', false)->orderBy('sort_order')->get(),
        ]);
    }
}
