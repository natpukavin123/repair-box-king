<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Http\Requests\ServiceRequest;

class ServiceController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            $data = Service::with('serviceType', 'customer', 'vendor')
                ->when(request('search'), fn($q, $s) => $q->where('description', 'like', "%{$s}%"))
                ->when(request('status'), fn($q, $s) => $q->where('status', $s))
                ->latest()
                ->paginate(request('per_page', 15));
            return response()->json($data);
        }
        return view('modules.services.index');
    }

    public function create()
    {
        $serviceTypes = \App\Models\ServiceType::where('status', 'active')->orderBy('name')->get();
        return view('modules.services.create', compact('serviceTypes'));
    }

    public function store(ServiceRequest $request)
    {
        $data = $request->validated();
        $data['profit'] = ($data['customer_charge'] ?? 0) - ($data['vendor_cost'] ?? 0);
        $service = Service::create($data);
        return response()->json(['success' => true, 'data' => $service->load('serviceType', 'customer'), 'message' => 'Service created']);
    }

    public function update(ServiceRequest $request, Service $service)
    {
        $data = $request->validated();
        $data['profit'] = ($data['customer_charge'] ?? 0) - ($data['vendor_cost'] ?? 0);
        $service->update($data);
        return response()->json(['success' => true, 'data' => $service, 'message' => 'Service updated']);
    }

    public function destroy(Service $service)
    {
        $service->delete();
        return response()->json(['success' => true, 'message' => 'Service deleted']);
    }
}
