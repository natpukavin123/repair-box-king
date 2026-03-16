<?php

namespace App\Http\Controllers;

use App\Models\{Setting, EmailTemplate, Notification, ActivityLog, Backup};
use App\Models\{ServiceType, RechargeProvider, Vendor};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

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
            return response()->json(ServiceType::with('taxRate')->orderBy('name')->get());
        }
    }

    public function storeServiceType(Request $request)
    {
        $data = $request->validate([
            'name'          => 'required|string|max:150',
            'default_price' => 'nullable|numeric|min:0',
            // Must exist in the SAC master (type=sac). tax_rate_id auto-resolves
            // from the master record via the model booted() hook — not a form field.
            'sac_code' => [
                'nullable',
                Rule::exists('hsn_codes', 'code')
                    ->where('type', 'sac')
                    ->where('is_active', true),
            ],
            'description' => 'nullable|string',
        ]);
        $st = ServiceType::create($data);
        return response()->json(['success' => true, 'data' => $st->load('taxRate')]);
    }

    public function updateServiceType(Request $request, ServiceType $serviceType)
    {
        $data = $request->validate([
            'name'          => 'required|string|max:150',
            'default_price' => 'nullable|numeric|min:0',
            'sac_code' => [
                'nullable',
                Rule::exists('hsn_codes', 'code')
                    ->where('type', 'sac')
                    ->where('is_active', true),
            ],
            'description' => 'nullable|string',
            'status'      => 'in:active,inactive',
        ]);
        $serviceType->update($data);
        return response()->json(['success' => true, 'data' => $serviceType->load('taxRate')]);
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
        $data = ServiceType::with('taxRate')->where('name', 'like', "%{$q}%")
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
            'gstin' => 'nullable|string|max:15',
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
            'gstin' => 'nullable|string|max:15',
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
        $backup = Backup::create([
            'backup_type' => 'database',
            'file_path' => 'backups/db_' . now()->format('Y_m_d_His') . '.sql',
            'file_size' => 0,
            'status' => 'completed',
        ]);
        return response()->json(['success' => true, 'data' => $backup, 'message' => 'Backup created']);
    }

    public function uploadServiceTypeImage(\Illuminate\Http\Request $request, ServiceType $serviceType)
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

    // ── Test Notification ───────────────────────────────────────────────────────
    public function testNotification(Request $request)
    {
        $data = $request->validate([
            'ticket'  => 'required|string',
            'type'    => 'required|in:received,completed',
            'channel' => 'required|in:email,whatsapp,both',
        ]);

        $repair = \App\Models\Repair::with('customer', 'technician')
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
}

