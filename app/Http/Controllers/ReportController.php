<?php

namespace App\Http\Controllers;

use App\Services\ReportService;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        return view('modules.reports.index');
    }

    public function sales(Request $request, ReportService $service)
    {
        $data = $request->validate(['from' => 'required|date', 'to' => 'required|date']);
        return response()->json($service->getSalesReport($data['from'], $data['to']));
    }

    public function profit(Request $request, ReportService $service)
    {
        $data = $request->validate(['from' => 'required|date', 'to' => 'required|date']);
        return response()->json($service->getProfitReport($data['from'], $data['to']));
    }
}
