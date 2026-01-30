<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

class PageController extends Controller
{
    public function home()
    {
        return Inertia::render('Home');
    }

    public function applicationForm()
    {
        return Inertia::render('Apply');
    }

    public function privacyPolicy()
    {
        return Inertia::render('PrivacyPolicy');
    }

    public function termsOfService()
    {
        return Inertia::render('TermsOfService');
    }

    public function notFound()
    {
        return Inertia::render('NotFound', ['status' => 404]);
    }
}
