<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(private readonly DashboardService $dashboard)
    {
    }

    public function __invoke(Request $request): View
    {
        $period = $request->string('period')->toString();

        if (! in_array($period, ['today', '7days', 'month'], true)) {
            $period = 'today';
        }

        return view('dashboard', [
            'summary' => $this->dashboard->summary($period),
        ]);
    }
}
