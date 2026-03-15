<?php

namespace App\Http\Controllers;

use App\Models\{TaxRate, HsnCode, Setting};
use Illuminate\Http\Request;

class TaxController extends Controller
{
    /**
     * Tax management page (GST Rates + HSN/SAC codes).
     */
    public function index()
    {
        if (request()->ajax()) {
            return response()->json([
                'taxRates' => TaxRate::orderBy('percentage')->get(),
                'hsnCodes' => HsnCode::with('taxRate')->orderBy('code')->get(),
                'shopState' => Setting::getValue('shop_state', ''),
            ]);
        }
        return view('modules.tax.index');
    }

    // ─── Tax Rates CRUD ─────────────────────────────────────────────────

    public function storeRate(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:50',
            'percentage' => 'required|numeric|min:0|max:100',
            'is_default' => 'boolean',
        ]);

        // If this rate is set as default, unset others
        if (!empty($data['is_default'])) {
            TaxRate::where('is_default', true)->update(['is_default' => false]);
        }

        $rate = TaxRate::create($data);

        return response()->json(['success' => true, 'rate' => $rate]);
    }

    public function updateRate(Request $request, TaxRate $taxRate)
    {
        $data = $request->validate([
            'name' => 'required|string|max:50',
            'percentage' => 'required|numeric|min:0|max:100',
            'is_default' => 'boolean',
            'is_active' => 'boolean',
        ]);

        if (!empty($data['is_default'])) {
            TaxRate::where('is_default', true)->where('id', '!=', $taxRate->id)->update(['is_default' => false]);
        }

        $taxRate->update($data);

        return response()->json(['success' => true, 'rate' => $taxRate]);
    }

    public function deleteRate(TaxRate $taxRate)
    {
        $taxRate->delete();
        return response()->json(['success' => true]);
    }

    // ─── HSN/SAC Codes CRUD ─────────────────────────────────────────────

    public function storeHsn(Request $request)
    {
        $data = $request->validate([
            'code' => 'required|string|max:10',
            'type' => 'required|in:hsn,sac',
            'description' => 'required|string|max:255',
            'tax_rate_id' => 'required|exists:tax_rates,id',
        ]);

        $hsn = HsnCode::create($data);

        return response()->json(['success' => true, 'hsnCode' => $hsn->load('taxRate')]);
    }

    public function updateHsn(Request $request, HsnCode $hsnCode)
    {
        $data = $request->validate([
            'code' => 'required|string|max:10',
            'type' => 'required|in:hsn,sac',
            'description' => 'required|string|max:255',
            'tax_rate_id' => 'required|exists:tax_rates,id',
            'is_active' => 'boolean',
        ]);

        $hsnCode->update($data);

        return response()->json(['success' => true, 'hsnCode' => $hsnCode->load('taxRate')]);
    }

    public function deleteHsn(HsnCode $hsnCode)
    {
        $hsnCode->delete();
        return response()->json(['success' => true]);
    }

    // ─── API: Search HSN/SAC codes (for dropdowns) ──────────────────────

    public function searchHsn(Request $request)
    {
        $q = $request->get('q', '');
        $type = $request->get('type'); // hsn or sac

        $query = HsnCode::with('taxRate')->where('is_active', true);

        if ($type) {
            $query->where('type', $type);
        }

        if ($q) {
            $query->where(function ($qb) use ($q) {
                $qb->where('code', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%");
            });
        }

        return response()->json($query->limit(20)->get());
    }

    // ─── API: Get active tax rates (for dropdowns) ──────────────────────

    public function taxRatesList()
    {
        return response()->json(
            TaxRate::where('is_active', true)->orderBy('percentage')->get()
        );
    }

    // ─── Settings: Shop state for IGST/CGST determination ───────────────

    public function updateSettings(Request $request)
    {
        $data = $request->validate([
            'shop_gstin' => 'nullable|string|max:15',
            'shop_state' => 'nullable|string|max:100',
        ]);

        if (isset($data['shop_gstin'])) {
            Setting::setValue('shop_gstin', $data['shop_gstin']);
        }
        if (isset($data['shop_state'])) {
            Setting::setValue('shop_state', $data['shop_state']);
        }

        return response()->json(['success' => true]);
    }
}
