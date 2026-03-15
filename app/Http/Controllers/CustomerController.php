<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Http\Requests\CustomerRequest;

class CustomerController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            $data = Customer::when(request('search'), fn($q, $s) => $q->where('name', 'like', "%{$s}%")->orWhere('mobile_number', 'like', "%{$s}%")->orWhere('email', 'like', "%{$s}%"))
                ->latest()
                ->paginate(request('per_page', 15));
            return response()->json($data);
        }
        return view('modules.customers.index');
    }

    public function create()
    {
        return view('modules.customers.create');
    }

    public function store(CustomerRequest $request)
    {
        $customer = Customer::create($request->validated());
        return response()->json(['success' => true, 'data' => $customer, 'message' => 'Customer created']);
    }

    public function show(Customer $customer)
    {
        $customer->load('invoices', 'repairs', 'recharges');
        return response()->json($customer);
    }

    public function update(CustomerRequest $request, Customer $customer)
    {
        $customer->update($request->validated());
        return response()->json(['success' => true, 'data' => $customer, 'message' => 'Customer updated']);
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();
        return response()->json(['success' => true, 'message' => 'Customer deleted']);
    }

    public function search()
    {
        $q       = request('q', '');
        $page    = max(1, (int) request('page', 1));
        $perPage = 15;
        $results = Customer::when($q, fn($qb) =>
                        $qb->where('name', 'like', "%{$q}%")
                           ->orWhere('mobile_number', 'like', "%{$q}%"))
                    ->orderBy('name')
                    ->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'success'  => true,
            'data'     => $results->items(),
            'has_more' => $results->hasMorePages(),
        ]);
    }
}
