<?php

namespace App\Http\Controllers;

use App\Models\{Setting, EmailTemplate, Notification, ActivityLog, Backup};
use App\Models\{ServiceType, RechargeProvider, Vendor};
use App\Models\{Brand, Category, Subcategory, Product, Customer, Part, Inventory};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{DB, Storage, Validator};
use App\Services\ImageService;

class SettingController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            return response()->json(Setting::all()->pluck('setting_value', 'setting_key'));
        }
        return view('modules.settings.index');
    }

    /**
     * Allowed keys per section — only these are accepted & saved.
     */
    private function sectionKeyMap(): array
    {
        return [
            'general' => [
                'shop_name', 'shop_address', 'shop_phone', 'shop_phone2', 'shop_email', 'shop_slogan',
                'shop_whatsapp',
                'shop_open_days', 'shop_open_time', 'shop_close_time', 'shop_holiday',
                'currency_symbol', 'invoice_prefix', 'repair_prefix', 'low_stock_threshold',
                'ui_theme', 'ui_motion',
            ],
            'landing' => [
                'hero_chip', 'hero_title', 'hero_subtitle',
                'stat1_value', 'stat1_label', 'stat2_value', 'stat2_label', 'stat3_value', 'stat3_label',
                'services_title', 'services_subtitle',
                'why_title', 'why_subtitle',
                'contact_title', 'contact_subtitle',
                'cta_title', 'cta_subtitle',
                'map_embed', 'map_zoom',
            ],
            'notifications' => [
                'notify_email_received', 'notify_email_completed',
                'notify_whatsapp_received', 'notify_whatsapp_completed',
                'whatsapp_api_url', 'whatsapp_api_token', 'whatsapp_from_number',
                'whatsapp_template_received', 'whatsapp_template_completed',
            ],
            'print' => [
                'invoice_header_title_en', 'invoice_header_title_ta',
                'invoice_footer_text', 'invoice_footer_text_ta',
                'invoice_sign_label_en', 'invoice_sign_label_ta',
                'invoice_default_language', 'invoice_paper_size',
                'invoice_shop_name_ta', 'invoice_shop_slogan_ta', 'invoice_shop_address_ta',
                'receipt_header_title_en', 'receipt_header_title_ta',
                'receipt_notes_en', 'receipt_notes_ta',
                'receipt_footer_text', 'receipt_footer_text_ta',
                'receipt_sign_label_en', 'receipt_sign_label_ta',
                'receipt_shop_name_ta', 'receipt_shop_slogan_ta', 'receipt_shop_address_ta',
                'repair_invoice_header_title_en', 'repair_invoice_header_title_ta',
                'repair_invoice_footer_text', 'repair_invoice_footer_text_ta',
            ],
        ];
    }

    private function sectionValidation(string $section): array
    {
        $base = ['settings' => 'required|array'];

        return match ($section) {
            'general' => array_merge($base, [
                'settings.shop_name'          => 'nullable|string|max:200',
                'settings.shop_address'       => 'nullable|string|max:500',
                'settings.shop_phone'         => 'nullable|string|max:20',
                'settings.shop_phone2'        => 'nullable|string|max:20',
                'settings.shop_email'         => 'nullable|email|max:150',
                'settings.shop_slogan'        => 'nullable|string|max:200',
                'settings.shop_whatsapp'      => 'nullable|string|max:20',
                'settings.shop_open_days'     => 'nullable|string|max:100',
                'settings.shop_open_time'     => 'nullable|string|max:10',
                'settings.shop_close_time'    => 'nullable|string|max:10',
                'settings.shop_holiday'       => 'nullable|string|max:200',
                'settings.currency_symbol'    => 'nullable|string|max:10',
                'settings.invoice_prefix'     => 'nullable|string|max:20',
                'settings.repair_prefix'      => 'nullable|string|max:20',
                'settings.low_stock_threshold' => 'nullable|integer|min:0|max:9999',
                'settings.ui_theme'           => 'nullable|string|in:atelier,graphite,solstice',
                'settings.ui_motion'          => 'nullable|string|in:enhanced,reduced,none',
                'shop_icon'                   => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'shop_favicon'                => 'nullable|image|mimes:png,jpg,jpeg,svg,ico|max:512',
            ]),
            'landing' => array_merge($base, [
                'settings.*' => 'nullable|string|max:1000',
            ]),
            'notifications' => array_merge($base, [
                'settings.notify_email_received'     => 'nullable|string|in:0,1',
                'settings.notify_email_completed'    => 'nullable|string|in:0,1',
                'settings.notify_whatsapp_received'  => 'nullable|string|in:0,1',
                'settings.notify_whatsapp_completed' => 'nullable|string|in:0,1',
                'settings.whatsapp_api_url'          => 'nullable|url|max:500',
                'settings.whatsapp_api_token'        => 'nullable|string|max:500',
                'settings.whatsapp_from_number'      => 'nullable|string|max:30',
                'settings.whatsapp_template_received'  => 'nullable|string|max:2000',
                'settings.whatsapp_template_completed' => 'nullable|string|max:2000',
            ]),
            'print' => array_merge($base, [
                'settings.*' => 'nullable|string|max:1000',
            ]),
            default => array_merge($base, [
                'settings.*' => 'nullable|string|max:500',
            ]),
        };
    }

    public function update(Request $request)
    {
        $section = $request->input('section', 'general');
        $allowedKeys = $this->sectionKeyMap()[$section] ?? null;
        $rules = $this->sectionValidation($section);

        $validated = $request->validate($rules);
        $incoming = $validated['settings'] ?? [];

        // Filter to only allowed keys for this section
        if ($allowedKeys) {
            $incoming = array_intersect_key($incoming, array_flip($allowedKeys));
        }

        // For landing section, save as a single JSON blob
        if ($section === 'landing') {
            Setting::setValue('landing_page', json_encode($incoming));
            return response()->json(['success' => true, 'message' => 'Landing page updated']);
        }

        foreach ($incoming as $key => $value) {
            Setting::setValue($key, $value);
        }

        // Handle icon upload (general section only)
        if ($section === 'general' && $request->hasFile('shop_icon')) {
            $img = app(ImageService::class);
            $oldIcon = Setting::getValue('shop_icon');
            $img->delete($oldIcon);

            $path = $img->store($request->file('shop_icon'), 'shop');
            Setting::setValue('shop_icon', $path);
        }

        // Handle favicon upload (general section only)
        if ($section === 'general' && $request->hasFile('shop_favicon')) {
            $img = app(ImageService::class);
            $oldFavicon = Setting::getValue('shop_favicon');
            $img->delete($oldFavicon);

            $path = $img->store($request->file('shop_favicon'), 'shop');
            Setting::setValue('shop_favicon', $path);

            // Update public/favicon.ico so Google and browsers get the new icon
            try {
                $faviconUrl = $img->url($path);
                $content = @file_get_contents($faviconUrl);
                if ($content) {
                    file_put_contents(public_path('favicon.ico'), $content);
                }
            } catch (\Throwable $e) {
                // Non-critical — favicon.ico update failed silently
            }
        }

        return response()->json(['success' => true, 'message' => 'Settings updated']);
    }

    public function printSettingsPage()
    {
        $settings = Setting::all()->pluck('setting_value', 'setting_key')->toArray();
        return view('modules.settings.print-settings', compact('settings'));
    }

    public function printPreview(string $type)
    {
        if ($type === 'sales-invoice') {
            $invoice = \App\Models\Invoice::with('items', 'payments', 'customer', 'creator')->latest()->first();
            if (!$invoice) {
                // Build a dummy invoice object with sample data
                $invoice = new \App\Models\Invoice([
                    'invoice_number' => 'INV-SAMPLE',
                    'total_amount' => 850,
                    'discount' => 0,
                    'final_amount' => 850,
                    'payment_status' => 'paid',
                ]);
                $invoice->id = 0;
                $invoice->created_at = now();
                $invoice->setRelation('customer', new \App\Models\Customer(['name' => 'John Doe', 'mobile_number' => '9876543210', 'address' => '123 Main Street']));
                $invoice->setRelation('creator', new \App\Models\User(['name' => 'Admin']));
                $item1 = new \App\Models\InvoiceItem(['item_name' => 'Phone Case (Sample)', 'quantity' => 2, 'price' => 300, 'mrp' => 350]);
                $item2 = new \App\Models\InvoiceItem(['item_name' => 'Screen Guard', 'quantity' => 1, 'price' => 150, 'mrp' => 200]);
                $item3 = new \App\Models\InvoiceItem(['item_name' => 'Charging Cable', 'quantity' => 1, 'price' => 100, 'mrp' => 120]);
                $invoice->setRelation('items', collect([$item1, $item2, $item3]));
                $pay = new \App\Models\InvoicePayment(['amount' => 850, 'payment_method' => 'cash']);
                $invoice->setRelation('payments', collect([$pay]));
            }
            return view('modules.invoices.print', compact('invoice'));
        }

        if ($type === 'repair-receipt') {
            $repair = \App\Models\Repair::with('customer', 'parts.part', 'payments', 'repairServices', 'statusHistory')->latest()->first();
            if (!$repair) {
                $repair = new \App\Models\Repair([
                    'ticket_number' => 'RPR-SAMPLE',
                    'tracking_id' => 'TRK-SAMPLE1',
                    'device_brand' => 'Samsung',
                    'device_model' => 'Galaxy S24',
                    'imei' => '123456789012345',
                    'problem_description' => 'Screen cracked, touch not working properly',
                    'estimated_cost' => 1500,
                    'service_charge' => 0,
                    'status' => 'received',
                    'expected_delivery_date' => now()->addDays(5),
                ]);
                $repair->id = 0;
                $repair->created_at = now();
                $repair->setRelation('customer', new \App\Models\Customer(['name' => 'Jane Smith', 'mobile_number' => '9876543210']));
                $pay = new \App\Models\RepairPayment(['amount' => 500, 'payment_method' => 'cash', 'direction' => 'IN', 'payment_type' => 'advance']);
                $repair->setRelation('payments', collect([$pay]));
                $repair->setRelation('parts', collect([]));
                $repair->setRelation('repairServices', collect([]));
                $repair->setRelation('statusHistory', collect([]));
            }
            return view('modules.repairs.print', compact('repair'));
        }

        if ($type === 'repair-invoice') {
            $repair = \App\Models\Repair::with('customer', 'parts.part', 'payments', 'repairServices', 'repairReturns.items')->latest()->first();
            if (!$repair) {
                $repair = new \App\Models\Repair([
                    'ticket_number' => 'RPR-SAMPLE',
                    'tracking_id' => 'TRK-SAMPLE1',
                    'device_brand' => 'Samsung',
                    'device_model' => 'Galaxy S24',
                    'imei' => '123456789012345',
                    'problem_description' => 'Screen cracked',
                    'estimated_cost' => 1300,
                    'service_charge' => 200,
                    'status' => 'completed',
                ]);
                $repair->id = 0;
                $repair->created_at = now();
                $repair->setRelation('customer', new \App\Models\Customer(['name' => 'Jane Smith', 'mobile_number' => '9876543210', 'address' => '123 Main Street']));
                $pay1 = new \App\Models\RepairPayment(['amount' => 500, 'payment_method' => 'cash', 'direction' => 'IN', 'payment_type' => 'advance']);
                $pay2 = new \App\Models\RepairPayment(['amount' => 800, 'payment_method' => 'cash', 'direction' => 'IN', 'payment_type' => 'final']);
                $repair->setRelation('payments', collect([$pay1, $pay2]));
                $repair->setRelation('parts', collect([]));
                $repair->setRelation('repairServices', collect([]));
                $repair->setRelation('repairReturns', collect([]));
            }
            return view('modules.repairs.invoice', compact('repair'));
        }

        abort(404);
    }

    // Service Types
    public function serviceTypes()
    {
        if (request()->ajax()) {
            $query = ServiceType::orderBy('name');
            if ($search = request('search')) {
                $query->where('name', 'like', "%{$search}%");
            }
            return response()->json($query->get());
        }
        return view('modules.service-types.index');
    }

    public function storeServiceType(Request $request)
    {
        $data = $request->validate([
            'name'          => 'required|string|max:150',
            'default_price' => 'nullable|numeric|min:0',
            'description'   => 'nullable|string',
            'quick_fills'   => 'nullable|array',
            'quick_fills.*' => 'string|max:100',
        ]);
        $existing = ServiceType::whereRaw('LOWER(name) = ?', [strtolower($data['name'])])->first();
        if ($existing) {
            return response()->json(['success' => true, 'data' => array_merge($existing->toArray(), ['_existing' => true])]);
        }
        $st = ServiceType::create($data);
        return response()->json(['success' => true, 'data' => $st]);
    }

    public function updateServiceType(Request $request, ServiceType $serviceType)
    {
        $data = $request->validate([
            'name'          => 'required|string|max:150',
            'default_price' => 'nullable|numeric|min:0',
            'description'   => 'nullable|string',
            'quick_fills'   => 'nullable|array',
            'quick_fills.*' => 'string|max:100',
            'status'        => 'in:active,inactive',
        ]);
        $serviceType->update($data);
        return response()->json(['success' => true, 'data' => $serviceType]);
    }

    public function destroyServiceType(ServiceType $serviceType)
    {
        $serviceType->delete();
        return response()->json(['success' => true]);
    }

    public function uploadServiceTypeImage(Request $request, ServiceType $serviceType)
    {
        return response()->json(app(ImageService::class)->handleUpload($request, $serviceType, 'service-types'));
    }

    // Recharge Providers
    public function rechargeProviders()
    {
        return response()->json(RechargeProvider::orderBy('name')->get());
    }

    // Search Service Types (for auto-suggest)
    public function searchServiceTypes(Request $request)
    {
        $q = $request->input('q', '');
        $data = ServiceType::where('name', 'like', "%{$q}%")
            ->where('status', 'active')
            ->orderBy('name')
            ->paginate(15);
        return response()->json([
            'success' => true,
            'data' => $data->items(),
            'has_more' => $data->hasMorePages(),
            'page' => $data->currentPage(),
        ]);
    }

    // Search Vendors (for auto-suggest)
    public function searchVendors(Request $request)
    {
        $q = $request->input('q', '');
        $data = Vendor::where('name', 'like', "%{$q}%")
            ->where('status', 'active')
            ->orderBy('name')
            ->paginate(15);
        return response()->json([
            'success' => true,
            'data' => $data->items(),
            'has_more' => $data->hasMorePages(),
            'page' => $data->currentPage(),
        ]);
    }

    public function storeRechargeProvider(Request $request)
    {
        $data = $request->validate(['name' => 'required|string|max:150']);
        $rp = RechargeProvider::create($data);
        return response()->json(['success' => true, 'data' => $rp]);
    }

    public function updateRechargeProvider(Request $request, RechargeProvider $rechargeProvider)
    {
        $data = $request->validate(['name' => 'required|string|max:150']);
        $rechargeProvider->update($data);
        return response()->json(['success' => true, 'data' => $rechargeProvider]);
    }

    public function destroyRechargeProvider(RechargeProvider $rechargeProvider)
    {
        $rechargeProvider->delete();
        return response()->json(['success' => true]);
    }

    public function uploadRechargeProviderImage(Request $request, RechargeProvider $rechargeProvider)
    {
        return response()->json(app(ImageService::class)->handleUpload($request, $rechargeProvider, 'recharge-providers'));
    }

    // Vendors
    public function vendors()
    {
        if (request()->ajax()) {
            $data = Vendor::when(request('search'), fn($q, $s) => $q->where('name', 'like', "%{$s}%"))
                ->orderBy('name')->paginate(15);
            return response()->json($data);
        }
        return view('modules.vendors.index');
    }

    public function createVendor()
    {
        return view('modules.vendors.create');
    }

    public function storeVendor(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:150',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'specialization' => 'nullable|string|max:255',
        ]);
        $vendor = Vendor::create($data);
        return response()->json(['success' => true, 'data' => $vendor]);
    }

    public function updateVendor(Request $request, Vendor $vendor)
    {
        $data = $request->validate([
            'name' => 'required|string|max:150',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'specialization' => 'nullable|string|max:255',
            'status' => 'in:active,inactive',
        ]);
        $vendor->update($data);
        return response()->json(['success' => true, 'data' => $vendor]);
    }

    public function uploadVendorImage(Request $request, Vendor $vendor)
    {
        return response()->json(app(ImageService::class)->handleUpload($request, $vendor, 'vendors'));
    }

    // Email Templates
    public function emailTemplates()
    {
        return response()->json(EmailTemplate::all());
    }

    public function updateEmailTemplate(Request $request, EmailTemplate $emailTemplate)
    {
        $data = $request->validate(['subject' => 'nullable|string|max:255', 'body' => 'nullable|string', 'status' => 'in:active,inactive']);
        $emailTemplate->update($data);
        return response()->json(['success' => true, 'data' => $emailTemplate]);
    }

    // Notifications
    public function notifications()
    {
        $data = Notification::latest()->paginate(20);
        return response()->json($data);
    }

    // Activity Logs
    public function activityLogs()
    {
        if (request()->ajax()) {
            $data = ActivityLog::with('user')
                ->when(request('module'), fn($q, $m) => $q->where('module', $m))
                ->when(request('user_id'), fn($q, $id) => $q->where('user_id', $id))
                ->latest()
                ->paginate(20);
            return response()->json($data);
        }
        return view('modules.settings.activity-logs');
    }

    // Backups
    public function backups()
    {
        return response()->json(Backup::latest()->get());
    }

    public function createBackup()
    {
        $filename = 'db_' . now()->format('Y_m_d_His') . '.sql';
        $relativePath = 'backups/' . $filename;
        $fullPath = storage_path('app/' . $relativePath);

        // Ensure directory exists
        $dir = dirname($fullPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        // Build mysqldump command
        $host = config('database.connections.mysql.host', '127.0.0.1');
        $port = config('database.connections.mysql.port', '3306');
        $database = config('database.connections.mysql.database');
        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password', '');

        $cmd = sprintf(
            'mysqldump --host=%s --port=%s --user=%s %s %s > %s 2>&1',
            escapeshellarg($host),
            escapeshellarg($port),
            escapeshellarg($username),
            $password ? '--password=' . escapeshellarg($password) : '',
            escapeshellarg($database),
            escapeshellarg($fullPath)
        );

        exec($cmd, $output, $exitCode);

        if ($exitCode !== 0 || !file_exists($fullPath) || filesize($fullPath) === 0) {
            // Cleanup empty/failed file
            if (file_exists($fullPath)) unlink($fullPath);
            return response()->json([
                'success' => false,
                'message' => 'Backup failed: ' . implode("\n", $output),
            ], 500);
        }

        $backup = Backup::create([
            'backup_type' => 'database',
            'file_path' => $relativePath,
            'file_size' => filesize($fullPath),
            'status' => 'completed',
        ]);

        return response()->json(['success' => true, 'data' => $backup, 'message' => 'Backup created successfully']);
    }

    public function downloadBackup(Backup $backup)
    {
        $fullPath = storage_path('app/' . $backup->file_path);

        if (!file_exists($fullPath)) {
            if (request()->ajax()) {
                return response()->json(['success' => false, 'message' => 'Backup file not found on disk.'], 404);
            }
            abort(404, 'Backup file not found.');
        }

        return response()->download($fullPath, basename($backup->file_path));
    }

    // ── Test Notification ───────────────────────────────────────────────────────
    public function testNotification(Request $request)
    {
        $data = $request->validate([
            'ticket'  => 'required|string',
            'type'    => 'required|in:received,completed',
            'channel' => 'required|in:email,whatsapp,both',
        ]);

        $repair = \App\Models\Repair::with('customer')
            ->where('ticket_number', $data['ticket'])
            ->first();

        if (! $repair) {
            return response()->json(['success' => false, 'message' => "Repair ticket '{$data['ticket']}' not found."], 404);
        }

        $svc  = new \App\Services\NotificationService();
        $sent = [];

        // Temporarily override toggles using a mini closure
        $sendEmail = fn() => $svc->{'sendRepair' . ucfirst($data['type'])}($repair);

        try {
            if ($data['channel'] === 'email' || $data['channel'] === 'both') {
                // Force email on for the test by temporarily patching settings in memory
                \App\Models\Setting::setValue('notify_email_'.$data['type'], '1');
                \App\Models\Setting::setValue('notify_whatsapp_'.$data['type'], '0');
                $svc->{'sendRepair' . ucfirst($data['type'])}($repair);
                $sent[] = 'email';
            }

            if ($data['channel'] === 'whatsapp' || $data['channel'] === 'both') {
                \App\Models\Setting::setValue('notify_email_'.$data['type'], '0');
                \App\Models\Setting::setValue('notify_whatsapp_'.$data['type'], '1');
                $svc->{'sendRepair' . ucfirst($data['type'])}($repair);
                $sent[] = 'WhatsApp';
            }
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 422);
        }

        return response()->json(['success' => true, 'message' => 'Test notification sent via: ' . implode(' & ', $sent) . '. Check logs if recipients didn\'t receive it.']);
    }

    // ── Import ─────────────────────────────────────────────────────────────────

    private function getImportConfig(): array
    {
        return [
            'brands' => [
                'model' => Brand::class,
                'label' => 'Brands',
                'columns' => ['name', 'models'],
                'unique_key' => 'name',
                'rules' => ['name' => 'required|string|max:150', 'models' => 'nullable|string'],
            ],
            'categories' => [
                'model' => Category::class,
                'label' => 'Categories',
                'columns' => ['name', 'description'],
                'unique_key' => 'name',
                'rules' => ['name' => 'required|string|max:150', 'description' => 'nullable|string'],
            ],
            'subcategories' => [
                'model' => Subcategory::class,
                'label' => 'Subcategories',
                'columns' => ['category', 'name'],
                'unique_key' => 'name',
                'rules' => ['category' => 'required|string', 'name' => 'required|string|max:150'],
            ],
            'customers' => [
                'model' => Customer::class,
                'label' => 'Customers',
                'columns' => ['name', 'mobile_number', 'email', 'address', 'notes'],
                'unique_key' => 'mobile_number',
                'rules' => ['name' => 'required|string|max:150', 'mobile_number' => 'required|string|max:20', 'email' => 'nullable|string|max:150', 'address' => 'nullable|string', 'notes' => 'nullable|string'],
            ],
            'products' => [
                'model' => Product::class,
                'label' => 'Products',
                'columns' => ['name', 'sku', 'barcode', 'category', 'subcategory', 'brand', 'purchase_price', 'mrp', 'selling_price', 'description', 'opening_stock', 'image_url'],
                'unique_key' => 'sku',
                'rules' => ['name' => 'required|string|max:255', 'sku' => 'nullable|string|max:100', 'barcode' => 'nullable|string|max:100', 'category' => 'nullable|string', 'subcategory' => 'nullable|string', 'brand' => 'nullable|string', 'purchase_price' => 'nullable|numeric|min:0', 'mrp' => 'nullable|numeric|min:0', 'selling_price' => 'nullable|numeric|min:0', 'description' => 'nullable|string', 'opening_stock' => 'nullable|integer|min:0', 'image_url' => 'nullable|string|max:500'],
            ],
            'parts' => [
                'model' => Part::class,
                'label' => 'Parts',
                'columns' => ['name', 'sku', 'cost_price', 'selling_price'],
                'unique_key' => 'sku',
                'rules' => ['name' => 'required|string|max:150', 'sku' => 'nullable|string|max:50', 'cost_price' => 'nullable|numeric|min:0', 'selling_price' => 'nullable|numeric|min:0'],
            ],
            'vendors' => [
                'model' => Vendor::class,
                'label' => 'Vendors',
                'columns' => ['name', 'phone', 'address', 'specialization'],
                'unique_key' => 'name',
                'rules' => ['name' => 'required|string|max:150', 'phone' => 'nullable|string|max:20', 'address' => 'nullable|string', 'specialization' => 'nullable|string|max:255'],
            ],
            'recharge_providers' => [
                'model' => RechargeProvider::class,
                'label' => 'Recharge Providers',
                'columns' => ['name'],
                'unique_key' => 'name',
                'rules' => ['name' => 'required|string|max:150'],
            ],
            'service_types' => [
                'model' => ServiceType::class,
                'label' => 'Service Types',
                'columns' => ['name', 'default_price', 'description'],
                'unique_key' => 'name',
                'rules' => ['name' => 'required|string|max:150', 'default_price' => 'nullable|numeric|min:0', 'description' => 'nullable|string'],
            ],
        ];
    }

    private function parseCsv(string $content): array
    {
        $lines = preg_split('/\r\n|\r|\n/', trim($content));
        if (count($lines) < 2) return [];

        $headers = str_getcsv(array_shift($lines));
        $headers = array_map(fn($h) => strtolower(trim($h)), $headers);

        $rows = [];
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '') continue;
            $values = str_getcsv($line);
            $row = [];
            foreach ($headers as $i => $header) {
                $row[$header] = isset($values[$i]) ? trim($values[$i]) : '';
            }
            $rows[] = $row;
        }
        return $rows;
    }

    public function validateImport(Request $request)
    {
        $request->validate([
            'type' => 'required|string',
            'file' => 'required|file|mimes:csv,txt|max:5120',
        ]);

        $configs = $this->getImportConfig();
        $type = $request->input('type');

        if (!isset($configs[$type])) {
            return response()->json(['success' => false, 'message' => 'Invalid import type.'], 422);
        }

        $config = $configs[$type];
        $content = file_get_contents($request->file('file')->getRealPath());
        $rows = $this->parseCsv($content);

        if (empty($rows)) {
            return response()->json(['success' => false, 'message' => 'CSV file is empty or has no data rows.'], 422);
        }

        // Validate headers
        $csvHeaders = array_keys($rows[0]);
        $missingHeaders = array_diff($config['columns'], $csvHeaders);
        $extraHeaders = array_diff($csvHeaders, $config['columns']);

        // Only require that mandatory columns from rules are present
        $requiredColumns = [];
        foreach ($config['rules'] as $col => $rule) {
            if (str_contains($rule, 'required') && in_array($col, $config['columns'])) {
                $requiredColumns[] = $col;
            }
        }
        $missingRequired = array_diff($requiredColumns, $csvHeaders);
        if (!empty($missingRequired)) {
            return response()->json([
                'success' => false,
                'message' => 'Missing required columns: ' . implode(', ', $missingRequired),
            ], 422);
        }

        // Validate each row
        $results = [];
        $errorCount = 0;
        $createCount = 0;
        $updateCount = 0;

        foreach ($rows as $i => $row) {
            $rowNum = $i + 2; // +2 for header row + 0-index
            $rowData = array_intersect_key($row, array_flip($config['columns']));

            $validator = Validator::make($rowData, $config['rules']);
            $errors = [];

            if ($validator->fails()) {
                $errors = $validator->errors()->all();
            }

            // Check if record exists (for create vs update)
            $uniqueKey = $config['unique_key'];
            $uniqueValue = $rowData[$uniqueKey] ?? '';
            $action = 'create';

            if (!empty($uniqueValue)) {
                if ($type === 'products' && $uniqueKey === 'sku') {
                    $existing = Product::where('sku', $uniqueValue)->first();
                } elseif ($type === 'customers' && $uniqueKey === 'mobile_number') {
                    $existing = Customer::where('mobile_number', $uniqueValue)->first();
                } else {
                    $existing = $config['model']::where($uniqueKey, $uniqueValue)->first();
                }

                if ($existing) {
                    $action = 'update';
                    $updateCount++;
                } else {
                    $createCount++;
                }
            } else {
                $createCount++;
            }

            if (!empty($errors)) {
                $errorCount++;
            }

            $results[] = [
                'row' => $rowNum,
                'data' => $rowData,
                'action' => $action,
                'errors' => $errors,
            ];
        }

        // Store validated data in session for confirm step
        $request->session()->put('import_data', [
            'type' => $type,
            'rows' => $rows,
            'results' => $results,
        ]);

        return response()->json([
            'success' => true,
            'type' => $type,
            'label' => $config['label'],
            'total' => count($rows),
            'creates' => $createCount,
            'updates' => $updateCount,
            'errors' => $errorCount,
            'results' => $results,
            'columns' => $config['columns'],
        ]);
    }

    public function confirmImport(Request $request)
    {
        $importData = $request->session()->get('import_data');
        if (!$importData) {
            return response()->json(['success' => false, 'message' => 'No import data found. Please validate again.'], 422);
        }

        $configs = $this->getImportConfig();
        $type = $importData['type'];
        $config = $configs[$type];
        $rows = $importData['rows'];

        $created = 0;
        $updated = 0;
        $skipped = 0;

        DB::beginTransaction();
        try {
            foreach ($rows as $row) {
                $rowData = array_intersect_key($row, array_flip($config['columns']));

                // Validate again
                $validator = Validator::make($rowData, $config['rules']);
                if ($validator->fails()) {
                    $skipped++;
                    continue;
                }

                // Resolve foreign keys for subcategories
                if ($type === 'subcategories') {
                    $finalData = collect($rowData)->except(['category'])->toArray();
                    if (!empty($rowData['category'])) {
                        $cat = Category::firstOrCreate(['name' => $rowData['category']]);
                        $finalData['category_id'] = $cat->id;
                    }
                    $rowData = $finalData;
                }

                // Resolve foreign keys for products
                $openingStock = null;
                $imageUrl = null;
                if ($type === 'products') {
                    $openingStock = isset($rowData['opening_stock']) && $rowData['opening_stock'] !== '' ? (int) $rowData['opening_stock'] : null;
                    $imageUrl = !empty($rowData['image_url']) ? $rowData['image_url'] : null;
                    $finalData = collect($rowData)->except(['category', 'subcategory', 'brand', 'opening_stock', 'image_url'])->toArray();
                    if (!empty($rowData['category'])) {
                        $cat = Category::firstOrCreate(['name' => $rowData['category']]);
                        $finalData['category_id'] = $cat->id;
                    }
                    if (!empty($rowData['subcategory'])) {
                        $catId = $finalData['category_id'] ?? null;
                        $subcat = Subcategory::firstOrCreate(['name' => $rowData['subcategory'], 'category_id' => $catId]);
                        $finalData['subcategory_id'] = $subcat->id;
                    }
                    if (!empty($rowData['brand'])) {
                        $brand = Brand::firstOrCreate(['name' => $rowData['brand']]);
                        $finalData['brand_id'] = $brand->id;
                    }
                    if ($imageUrl) {
                        $finalData['image'] = $imageUrl;
                    }
                    // Set defaults for price fields
                    $finalData['purchase_price'] = $finalData['purchase_price'] ?: 0;
                    $finalData['mrp'] = $finalData['mrp'] ?: 0;
                    $finalData['selling_price'] = $finalData['selling_price'] ?: 0;
                    $rowData = $finalData;
                }

                // Set defaults for parts price fields (empty strings break decimal columns)
                if ($type === 'parts') {
                    $rowData['cost_price']    = isset($rowData['cost_price'])    && $rowData['cost_price']    !== '' ? $rowData['cost_price']    : 0;
                    $rowData['selling_price'] = isset($rowData['selling_price']) && $rowData['selling_price'] !== '' ? $rowData['selling_price'] : 0;
                }

                // Convert semicolon-separated models string to array for brands
                if ($type === 'brands' && isset($rowData['models'])) {
                    $rawModels = trim($rowData['models']);
                    $rowData['models'] = $rawModels !== ''
                        ? array_values(array_filter(array_map('trim', explode(';', $rawModels))))
                        : null;
                }

                $uniqueKey = $config['unique_key'];
                $uniqueValue = $rowData[$uniqueKey] ?? '';

                if (!empty($uniqueValue)) {
                    $existing = $config['model']::where($uniqueKey, $uniqueValue)->first();
                    if ($existing) {
                        $existing->update($rowData);
                        // Update inventory opening stock if provided
                        if ($type === 'products' && $openingStock !== null) {
                            $inv = Inventory::firstOrCreate(['product_id' => $existing->id], ['current_stock' => 0, 'reserved_stock' => 0]);
                            $inv->update(['current_stock' => $openingStock]);
                        }
                        $updated++;
                    } else {
                        $record = $config['model']::create($rowData);
                        if ($type === 'products') {
                            Inventory::create(['product_id' => $record->id, 'current_stock' => $openingStock ?? 0, 'reserved_stock' => 0]);
                        }
                        $created++;
                    }
                } else {
                    $record = $config['model']::create($rowData);
                    if ($type === 'products') {
                        Inventory::create(['product_id' => $record->id, 'current_stock' => $openingStock ?? 0, 'reserved_stock' => 0]);
                    }
                    $created++;
                }
            }

            DB::commit();
            $request->session()->forget('import_data');

            return response()->json([
                'success' => true,
                'message' => "Import completed: {$created} created, {$updated} updated, {$skipped} skipped.",
                'created' => $created,
                'updated' => $updated,
                'skipped' => $skipped,
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Import failed: ' . $e->getMessage()], 500);
        }
    }

    // ─── SEO Settings ──────────────────────────────────────────────────────
    public function seoSettings()
    {
        if (request()->ajax()) {
            $keys = [
                'seo_global_title_suffix', 'seo_global_description', 'seo_global_keywords',
                'seo_google_analytics', 'seo_google_tag_manager', 'seo_google_verification',
                'seo_bing_verification', 'seo_schema_business_type', 'seo_schema_price_range',
                'seo_schema_opening_hours', 'seo_schema_geo_lat', 'seo_schema_geo_lng',
                'seo_og_default_image', 'seo_twitter_handle',
                'seo_robots_custom', 'seo_head_scripts', 'seo_body_scripts',
            ];
            $settings = [];
            foreach ($keys as $key) {
                $settings[$key] = Setting::getValue($key, '');
            }
            return response()->json($settings);
        }
        return view('modules.seo-settings.index');
    }

    public function updateSeoSettings(Request $request)
    {
        $keys = [
            'seo_global_title_suffix', 'seo_global_description', 'seo_global_keywords',
            'seo_google_analytics', 'seo_google_tag_manager', 'seo_google_verification',
            'seo_bing_verification', 'seo_schema_business_type', 'seo_schema_price_range',
            'seo_schema_opening_hours', 'seo_schema_geo_lat', 'seo_schema_geo_lng',
            'seo_og_default_image', 'seo_twitter_handle',
            'seo_robots_custom', 'seo_head_scripts', 'seo_body_scripts',
        ];

        foreach ($keys as $key) {
            if ($request->has($key)) {
                Setting::setValue($key, $request->input($key, ''));
            }
        }

        return response()->json(['success' => true, 'message' => 'SEO settings updated']);
    }
}

