<?php

namespace App\Http\Controllers;

use App\Models\CaseStudy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\View\View;

class CaseStudyController extends Controller
{
    public function index(): View
    {
        $featured = CaseStudy::where('featured', true)->orderBy('sort_order')->orderBy('id')->first();

        return view('pages.case-study', [
            'featuredCaseStudy' => $featured,
            'caseStudies' => CaseStudy::when($featured, fn (Builder $query): Builder => $query->where('id', '!=', $featured->id))->orderBy('sort_order')->orderBy('id')->get(),
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
