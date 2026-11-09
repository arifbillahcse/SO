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

    public function show(CaseStudy $caseStudy): View
    {
        return view('pages.case-study-show', [
            'caseStudy' => $caseStudy,
            'relatedCaseStudies' => CaseStudy::where('id', '!=', $caseStudy->id)->orderBy('sort_order')->limit(3)->get(),
        ]);
    }
}
