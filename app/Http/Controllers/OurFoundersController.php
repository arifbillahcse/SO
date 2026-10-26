<?php

namespace App\Http\Controllers;

use App\Models\TeamMember;
use Illuminate\View\View;

class OurFoundersController extends Controller
{
    public function index(): View
    {
        return view('pages.our-founders', [
            'teamMembers' => TeamMember::orderBy('sort_order')->get(),
        ]);
    }
}
