<?php

namespace App\Http\Controllers;

use App\Models\OrganizationSettings;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LegalPageController extends Controller
{
    public function __invoke(Request $request): View
    {
        $page = $request->route()->getName();

        return view('pages.legal', [
            'legalPage' => OrganizationSettings::current()->legalPage($page),
            'legalRoute' => $page,
        ]);
    }
}
