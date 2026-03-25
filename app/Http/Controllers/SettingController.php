<?php

namespace App\Http\Controllers;

use App\Models\{Setting, EmailTemplate, Notification, ActivityLog, Backup};
use App\Models\{ServiceType, RechargeProvider, Vendor};
use App\Models\{Brand, Category, Subcategory, Product, Customer, Part};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{DB, Storage, Validator};

class SettingController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            return response()->json(Setting::all()->pluck('setting_value', 'setting_key'));
        }
        return view('modules.settings.index');
    }

    public function update(Request $request)
    {
        $settings = $request->validate([
            'settings' => 'required|array',
            'settings.*' => 'nullable|string|max:500',
            'shop_icon' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        foreach ($settings['settings'] as $key => $value) {
            Setting::setValue($key, $value);
        }

        // Handle icon upload
        if ($request->hasFile('shop_icon')) {
            $file = $request->file('shop_icon');
            $disk = config('filesystems.default') === 's3' ? 's3' : 'public';

            // Delete old icon if it was stored on the same disk
            $oldIcon = Setting::getValue('shop_icon');
            if ($oldIcon && !str_starts_with($oldIcon, 'http')) {
                Storage::disk($disk)->delete($oldIcon);
            }

            $path = $file->store('shop', $disk);

            // For S3/cloud disks, store the full public URL so it works across environments.
            // For local disk, store the relative path (served via /storage symlink).
            $storedValue = $disk === 's3'
                ? Storage::disk('s3')->url($path)
                : $path;

            Setting::setValue('shop_icon', $storedValue);
        }

        return response()->json(['success' => true, 'message' => 'Settings updated']);
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
        $request->validate([
            'image'     => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $updates = [];

        if ($request->hasFile('image')) {
            if ($serviceType->image) \Storage::disk('public')->delete($serviceType->image);
            if ($serviceType->thumbnail && !$request->hasFile('thumbnail')) \Storage::disk('public')->delete($serviceType->thumbnail);

            $path = $request->file('image')->store('service-types', 'public');
            $updates['image'] = $path;

            if (!$request->hasFile('thumbnail')) {
                $thumbPath = $this->makeThumb(
                    \Storage::disk('public')->path($path),
                    'service-types/thumbs',
                    pathinfo($path, PATHINFO_FILENAME) . '_thumb.jpg'
                );
                if ($thumbPath) $updates['thumbnail'] = $thumbPath;
            }
        }

        if ($request->hasFile('thumbnail')) {
            if ($serviceType->thumbnail) \Storage::disk('public')->delete($serviceType->thumbnail);
            $path = $request->file('thumbnail')->store('service-types/thumbs', 'public');
            $updates['thumbnail'] = $path;
        }

        if ($updates) $serviceType->update($updates);

        $fresh = $serviceType->fresh();
        return response()->json([
            'success'   => true,
            'image_url' => $fresh->image ? \Storage::disk('public')->url($fresh->image) : null,
            'thumb_url' => $fresh->thumbnail ? \Storage::disk('public')->url($fresh->thumbnail) : null,
        ]);
    }

    private function makeThumb(string $src, string $destFolder, string $filename): ?string
    {
        if (!function_exists('imagecreatefromjpeg')) return null;
        $ext = strtolower(pathinfo($src, PATHINFO_EXTENSION));
        $image = match($ext) {
            'jpg', 'jpeg' => @imagecreatefromjpeg($src),
            'png'         => @imagecreatefrompng($src),
            'gif'         => @imagecreatefromgif($src),
            'webp'        => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($src) : null,
            default       => null,
        };
        if (!$image) return null;
        [$sw, $sh] = getimagesize($src);
        $ratio = min(200 / $sw, 200 / $sh);
        $nw = max(1, (int)($sw * $ratio));
        $nh = max(1, (int)($sh * $ratio));
        $thumb = imagecreatetruecolor($nw, $nh);
        imagecopyresampled($thumb, $image, 0, 0, 0, 0, $nw, $nh, $sw, $sh);
        $dir = \Storage::disk('public')->path($destFolder);
        if (!is_dir($dir)) mkdir($dir, 0755, true);
        $destPath = $dir . DIRECTORY_SEPARATOR . $filename;
        imagejpeg($thumb, $destPath, 85);
        imagedestroy($image);
        imagedestroy($thumb);
        return $destFolder . '/' . $filename;
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
        $data = $request->validate(['name' => 'required|string|max:150', 'provider_type' => 'required|string|max:50', 'commission_percentage' => 'required|numeric|min:0|max:100']);
        $rp = RechargeProvider::create($data);
        return response()->json(['success' => true, 'data' => $rp]);
    }

    public function updateRechargeProvider(Request $request, RechargeProvider $rechargeProvider)
    {
        $data = $request->validate(['name' => 'required|string|max:150', 'provider_type' => 'required|string|max:50', 'commission_percentage' => 'required|numeric|min:0|max:100']);
        $rechargeProvider->update($data);
        return response()->json(['success' => true, 'data' => $rechargeProvider]);
    }

    public function destroyRechargeProvider(RechargeProvider $rechargeProvider)
    {
        $rechargeProvider->delete();
        return response()->json(['success' => true]);
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
                'columns' => ['name'],
                'unique_key' => 'name',
                'rules' => ['name' => 'required|string|max:150'],
            ],
            'categories' => [
                'model' => Category::class,
                'label' => 'Categories',
                'columns' => ['name', 'description'],
                'unique_key' => 'name',
                'rules' => ['name' => 'required|string|max:150', 'description' => 'nullable|string'],
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
                'columns' => ['name', 'sku', 'barcode', 'category', 'brand', 'purchase_price', 'mrp', 'selling_price', 'description'],
                'unique_key' => 'sku',
                'rules' => ['name' => 'required|string|max:255', 'sku' => 'nullable|string|max:100', 'barcode' => 'nullable|string|max:100', 'category' => 'nullable|string', 'brand' => 'nullable|string', 'purchase_price' => 'nullable|numeric|min:0', 'mrp' => 'nullable|numeric|min:0', 'selling_price' => 'nullable|numeric|min:0', 'description' => 'nullable|string'],
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
                'columns' => ['name', 'provider_type', 'commission_percentage'],
                'unique_key' => 'name',
                'rules' => ['name' => 'required|string|max:150', 'provider_type' => 'required|string|max:50', 'commission_percentage' => 'required|numeric|min:0|max:100'],
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

                // Resolve foreign keys for products
                if ($type === 'products') {
                    $finalData = collect($rowData)->except(['category', 'brand'])->toArray();
                    if (!empty($rowData['category'])) {
                        $cat = Category::firstOrCreate(['name' => $rowData['category']]);
                        $finalData['category_id'] = $cat->id;
                    }
                    if (!empty($rowData['brand'])) {
                        $brand = Brand::firstOrCreate(['name' => $rowData['brand']]);
                        $finalData['brand_id'] = $brand->id;
                    }
                    // Set defaults for price fields
                    $finalData['purchase_price'] = $finalData['purchase_price'] ?: 0;
                    $finalData['mrp'] = $finalData['mrp'] ?: 0;
                    $finalData['selling_price'] = $finalData['selling_price'] ?: 0;
                    $rowData = $finalData;
                }

                $uniqueKey = $config['unique_key'];
                $uniqueValue = $rowData[$uniqueKey] ?? '';

                if (!empty($uniqueValue)) {
                    $existing = $config['model']::where($uniqueKey, $uniqueValue)->first();
                    if ($existing) {
                        $existing->update($rowData);
                        $updated++;
                    } else {
                        $config['model']::create($rowData);
                        $created++;
                    }
                } else {
                    $config['model']::create($rowData);
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
}

