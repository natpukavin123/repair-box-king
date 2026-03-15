<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Artisan, DB, Hash, Schema};
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Role;

class SetupController extends Controller
{
    // ─── Step 1: Requirements Check ───────────────────────────────────────────
    public function index()
    {
        $requirements = $this->checkRequirements();
        return view('setup.step1-requirements', compact('requirements'));
    }

    // ─── Step 2: Database Configuration (GET) ────────────────────────────────
    public function databaseForm()
    {
        // Pre-fill from existing .env
        $envData = $this->parseEnvFile();
        return view('setup.step2-database', compact('envData'));
    }

    // ─── Step 2: Database Configuration (POST) ───────────────────────────────
    public function saveDatabase(Request $request)
    {
        $request->validate([
            'db_host'     => 'required|string',
            'db_port'     => 'required|numeric',
            'db_database' => 'required|string',
            'db_username' => 'required|string',
            'db_password' => 'nullable|string',
        ]);

        // Test connection first
        $connectionTest = $this->testDbConnection(
            $request->db_host,
            $request->db_port,
            $request->db_database,
            $request->db_username,
            $request->db_password ?? ''
        );

        if (!$connectionTest['success']) {
            return back()->withErrors(['db_connection' => $connectionTest['message']])->withInput();
        }

        // Write to .env
        $this->writeEnvValues([
            'DB_HOST'     => $request->db_host,
            'DB_PORT'     => $request->db_port,
            'DB_DATABASE' => $request->db_database,
            'DB_USERNAME' => $request->db_username,
            'DB_PASSWORD' => $request->db_password ?? '',
        ]);

        return redirect('/setup/owner');
    }

    // ─── AJAX: Test DB Connection ─────────────────────────────────────────────
    public function testConnection(Request $request)
    {
        $result = $this->testDbConnection(
            $request->db_host     ?? '127.0.0.1',
            $request->db_port     ?? 3306,
            $request->db_database ?? '',
            $request->db_username ?? '',
            $request->db_password ?? ''
        );
        return response()->json($result);
    }

    // ─── Step 3: Owner / Shop Settings (GET) ─────────────────────────────────
    public function ownerForm()
    {
        return view('setup.step3-owner');
    }

    // ─── Step 3: Owner / Shop Settings (POST) ────────────────────────────────
    public function saveOwner(Request $request)
    {
        $request->validate([
            'shop_name'      => 'required|string|max:150',
            'admin_name'     => 'required|string|max:100',
            'admin_email'    => 'required|email|max:150',
            'admin_password' => 'required|string|min:6|confirmed',
        ]);

        // Store in session for later use (during migrate step)
        session([
            'setup_shop_name'      => $request->shop_name,
            'setup_admin_name'     => $request->admin_name,
            'setup_admin_email'    => $request->admin_email,
            'setup_admin_password' => $request->admin_password,
        ]);

        // Also update APP_ENV to production
        $this->writeEnvValues([
            'APP_NAME' => $request->shop_name,
            'APP_ENV'  => 'production',
            'APP_DEBUG'=> 'false',
        ]);

        return redirect('/setup/migrate');
    }

    // ─── Step 4: Migrate + Seed page (GET) ───────────────────────────────────
    public function migrateForm()
    {
        return view('setup.step4-migrate');
    }

    // ─── Step 4: Run Migrations + Seed (POST AJAX) ───────────────────────────
    public function runMigrations(Request $request)
    {
        $log = [];

        try {
            // 1. Run migrations
            $log[] = ['status' => 'info', 'msg' => 'Running database migrations...'];
            Artisan::call('migrate', ['--force' => true]);
            $log[] = ['status' => 'success', 'msg' => 'Migrations completed successfully.'];

            // 2. Run initial seeder (essential data only)
            $log[] = ['status' => 'info', 'msg' => 'Seeding initial system data...'];
            Artisan::call('db:seed', ['--class' => 'InitialDataSeeder', '--force' => true]);
            $log[] = ['status' => 'success', 'msg' => 'Initial data seeded (roles, permissions, settings).'];

            // 3. Create admin user
            $log[] = ['status' => 'info', 'msg' => 'Creating admin account...'];
            $this->createAdminUser();
            $log[] = ['status' => 'success', 'msg' => 'Admin user created successfully.'];

            // 4. Apply shop settings
            $log[] = ['status' => 'info', 'msg' => 'Applying shop settings...'];
            $this->applyShopSettings();
            $log[] = ['status' => 'success', 'msg' => 'Shop settings applied.'];

            // 5. Generate app key if not set
            if (empty(config('app.key')) || config('app.key') === 'SomeRandomString') {
                $log[] = ['status' => 'info', 'msg' => 'Generating application key...'];
                Artisan::call('key:generate', ['--force' => true]);
                $log[] = ['status' => 'success', 'msg' => 'Application key generated.'];
            }

            // 6. Optimize / cache config for prod
            Artisan::call('config:clear');
            Artisan::call('route:clear');
            Artisan::call('view:clear');

            // 7. Write install lock file
            file_put_contents(storage_path('installed'), date('Y-m-d H:i:s'));
            $log[] = ['status' => 'success', 'msg' => 'Installation lock file created.'];

            // Clear session setup data
            session()->forget(['setup_shop_name','setup_admin_name','setup_admin_email','setup_admin_password']);

            return response()->json([
                'success' => true,
                'log'     => $log,
                'redirect'=> '/setup/complete',
            ]);

        } catch (\Throwable $e) {
            $log[] = ['status' => 'error', 'msg' => 'Error: ' . $e->getMessage()];
            return response()->json(['success' => false, 'log' => $log], 500);
        }
    }

    // ─── Step 5: Complete ─────────────────────────────────────────────────────
    public function complete()
    {
        // If not installed yet, redirect back to setup
        if (!file_exists(storage_path('installed'))) {
            return redirect('/setup');
        }
        return view('setup.complete');
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    private function checkRequirements(): array
    {
        $phpExtensions = [
            'pdo'       => extension_loaded('pdo'),
            'pdo_mysql' => extension_loaded('pdo_mysql'),
            'mbstring'  => extension_loaded('mbstring'),
            'openssl'   => extension_loaded('openssl'),
            'tokenizer' => extension_loaded('tokenizer'),
            'xml'       => extension_loaded('xml'),
            'json'      => extension_loaded('json'),
            'bcmath'    => extension_loaded('bcmath'),
            'fileinfo'  => extension_loaded('fileinfo'),
            'ctype'     => extension_loaded('ctype'),
        ];

        $phpVersion     = PHP_VERSION;
        $phpVersionOk   = version_compare($phpVersion, '8.1.0', '>=');
        $storageWritable = is_writable(storage_path());
        $envWritable     = is_writable(base_path('.env'));

        $allPassed = $phpVersionOk && $storageWritable && $envWritable && !in_array(false, $phpExtensions);

        return compact(
            'phpExtensions', 'phpVersion', 'phpVersionOk',
            'storageWritable', 'envWritable', 'allPassed'
        );
    }

    private function testDbConnection(string $host, int|string $port, string $database, string $username, string $password): array
    {
        try {
            $dsn = "mysql:host={$host};port={$port};dbname={$database}";
            new \PDO($dsn, $username, $password, [\PDO::ATTR_TIMEOUT => 5]);
            return ['success' => true, 'message' => 'Connection successful!'];
        } catch (\PDOException $e) {
            return ['success' => false, 'message' => 'Connection failed: ' . $e->getMessage()];
        }
    }

    private function writeEnvValues(array $values): void
    {
        $envPath   = base_path('.env');
        $envContent = file_exists($envPath) ? file_get_contents($envPath) : '';

        foreach ($values as $key => $value) {
            $value = str_contains($value, ' ') ? "\"{$value}\"" : $value;
            if (preg_match("/^{$key}=/m", $envContent)) {
                $envContent = preg_replace("/^{$key}=.*/m", "{$key}={$value}", $envContent);
            } else {
                $envContent .= "\n{$key}={$value}";
            }
        }

        file_put_contents($envPath, $envContent);
    }

    private function parseEnvFile(): array
    {
        $envPath = base_path('.env');
        $data = [];
        if (file_exists($envPath)) {
            foreach (file($envPath) as $line) {
                $line = trim($line);
                if (Str::startsWith($line, '#') || !Str::contains($line, '=')) continue;
                [$key, $value] = explode('=', $line, 2);
                $data[strtolower($key)] = trim($value, '"');
            }
        }
        return $data;
    }

    private function createAdminUser(): void
    {
        $adminRole = Role::where('name', 'Admin')->first();
        if (!$adminRole) return;

        $email = session('setup_admin_email', 'admin@repairbox.com');
        User::updateOrCreate(
            ['email' => $email],
            [
                'name'           => session('setup_admin_name', 'Administrator'),
                'password'       => Hash::make(session('setup_admin_password', 'password')),
                'role_id'        => $adminRole->id,
                'status'         => 'active',
                'is_super_admin' => true,   // ← Super admin — cannot be deleted or demoted
            ]
        );
    }

    private function applyShopSettings(): void
    {
        $shopName = session('setup_shop_name');
        if ($shopName) {
            \App\Models\Setting::setValue('shop_name', $shopName);
        }
    }
}
