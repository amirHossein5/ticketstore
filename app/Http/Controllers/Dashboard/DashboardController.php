<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $published = auth()->user()->tickets()
            ->published()->latest()->get();

        $drafts = auth()->user()->tickets()
            ->draft()->latest()->get();

        return view('dashboard.index', compact('published', 'drafts'));
    }
}
