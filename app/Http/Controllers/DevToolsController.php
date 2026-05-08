<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DevToolsController extends Controller
{
    public function index()
    {
        $tableStats = $this->getTableStats();
        return view('dev-tools.index', compact('tableStats'));
    }

    /**
     * Truncate ONLY transactional module data.
     * Preserves: master data (customers, vendors, products, parts, brands,
     *            categories, service_types, etc.), users & settings.
     */
    public function resetModules(Request $request)
    {
        $log = [];

        $modules = [
            'Sales' => [
                'ledger_transactions',
                'invoice_payments',
                'invoice_items',
                'invoices',
            ],
            'Repair' => [
                'repair_payments',
                'repair_status_histories',
                'repair_returns',
                'repairs',
            ],
            'Recharge' => [
                'recharges',
            ],
            'Expenses' => [
                'expenses',
            ],
            'PO / Purchases' => [
                'purchase_items',
                'purchases',
                'po_requests',
            ],
            'Returns & Credit Notes' => [
                'credit_note_refunds',
                'credit_note_items',
                'credit_notes',
                'customer_returns',
            ],
            'Notifications & Activity' => [
                'notifications',
                'activity_logs',
                'reminders',
            ],
        ];

        try {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');

            foreach ($modules as $moduleName => $tables) {
                $log[] = ['status' => 'info', 'msg' => "── {$moduleName} ──"];
                foreach ($tables as $table) {
                    if (Schema::hasTable($table)) {
                        DB::table($table)->truncate();
                        $log[] = ['status' => 'success', 'msg' => "Cleared: {$table}"];
                    } else {
                        $log[] = ['status' => 'warning', 'msg' => "Skipped (not found): {$table}"];
                    }
                }
            }

            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            $log[] = ['status' => 'success', 'msg' => '✅ All module transactions cleared. Master data (customers, products, brands, etc.) preserved.'];

            return response()->json(['success' => true, 'log' => $log]);

        } catch (\Throwable $e) {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            $log[] = ['status' => 'error', 'msg' => 'Error: ' . $e->getMessage()];
            return response()->json(['success' => false, 'log' => $log], 500);
        }
    }

    /**
     * Truncate ALL transactional and master data tables.
     * Preserves: users, roles, permissions, settings, email_templates.
     */
    public function resetData(Request $request)
    {
        $log = [];

        try {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');

            $allTables = [
                // ── Financial transactions ──────────────────────────────
                'ledger_transactions',
                'invoice_payments',
                'invoice_items',
                'invoices',
                // ── Repairs ─────────────────────────────────────────────
                'repair_payments',
                'repair_status_histories',
                'repair_returns',
                'repairs',
                // ── Purchases ───────────────────────────────────────────
                'purchase_items',
                'purchases',
                'po_requests',
                // ── Returns & credit notes ───────────────────────────────
                'credit_note_refunds',
                'credit_notes',
                'customer_returns',
                // ── Services & recharges ─────────────────────────────────
                'services',
                'recharges',
                // ── Expenses ─────────────────────────────────────────────
                'expenses',
                // ── Customers, vendors & stock ───────────────────────────
                'customers',
                'vendors',
                'stock_movements',
                'inventories',
                // ── Master data ──────────────────────────────────────────
                'products',
                'parts',
                'service_types',
                'subcategories',
                'categories',
                'brands',
                'recharge_providers',
                // ── Notifications & activity ─────────────────────────────
                'notifications',
                'activity_logs',
                'reminders',
                // ── Backups ──────────────────────────────────────────────
                'backup_logs',
            ];

            foreach ($allTables as $table) {
                if (Schema::hasTable($table)) {
                    DB::table($table)->truncate();
                    $log[] = ['status' => 'success', 'msg' => "Cleared: {$table}"];
                } else {
                    $log[] = ['status' => 'warning', 'msg' => "Skipped (not found): {$table}"];
                }
            }

            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            $log[] = ['status' => 'success', 'msg' => '✅ All data cleared (transactions + master data). Users & settings preserved.'];

            return response()->json(['success' => true, 'log' => $log]);

        } catch (\Throwable $e) {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            $log[] = ['status' => 'error', 'msg' => 'Error: ' . $e->getMessage()];
            return response()->json(['success' => false, 'log' => $log], 500);
        }
    }

    /**
     * Run the full DatabaseSeeder (demo data).
     */
    public function seedDemo(Request $request)
    {
        $log = [];

        try {
            $log[] = ['status' => 'info', 'msg' => 'Running DatabaseSeeder with demo data...'];
            Artisan::call('db:seed', ['--class' => 'DatabaseSeeder', '--force' => true]);
            $output = Artisan::output();
            if ($output) {
                $log[] = ['status' => 'info', 'msg' => trim($output)];
            }
            $log[] = ['status' => 'success', 'msg' => '✅ Demo data seeded successfully.'];

            return response()->json(['success' => true, 'log' => $log]);

        } catch (\Throwable $e) {
            $log[] = ['status' => 'error', 'msg' => 'Error: ' . $e->getMessage()];
            return response()->json(['success' => false, 'log' => $log], 500);
        }
    }

    /**
     * Reset transactional data then seed demo data.
     */
    public function resetAndSeed(Request $request)
    {
        $log = [];

        try {
            // Step 1: Reset
            $resetResponse = json_decode($this->resetData($request)->getContent(), true);
            $log = array_merge($log, $resetResponse['log'] ?? []);

            if (!$resetResponse['success']) {
                return response()->json(['success' => false, 'log' => $log], 500);
            }

            // Step 2: Seed
            $log[] = ['status' => 'info', 'msg' => '── Starting demo data seed ──'];
            $seedResponse = json_decode($this->seedDemo($request)->getContent(), true);
            $log = array_merge($log, $seedResponse['log'] ?? []);

            return response()->json([
                'success' => $seedResponse['success'],
                'log'     => $log,
            ]);

        } catch (\Throwable $e) {
            $log[] = ['status' => 'error', 'msg' => 'Error: ' . $e->getMessage()];
            return response()->json(['success' => false, 'log' => $log], 500);
        }
    }

    /**
     * Hard-delete a single repair ticket and ALL related data.
     */
    public function deleteRepair(Request $request)
    {
        $request->validate(['repair_id' => 'required|integer']);
        $repairId = (int) $request->input('repair_id');
        $log = [];

        $repair = DB::table('repairs')->where('id', $repairId)->first();
        if (!$repair) {
            return response()->json(['success' => false, 'log' => [
                ['status' => 'error', 'msg' => "Repair ID {$repairId} not found."]
            ]], 404);
        }

        $ticket = $repair->ticket_number ?? "#{$repairId}";
        $log[] = ['status' => 'info', 'msg' => "Deleting repair ticket {$ticket} (ID: {$repairId})…"];

        try {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');

            // Related child tables
            $childTables = [
                'repair_payments'       => 'repair_id',
                'repair_status_history' => 'repair_id',
            ];

            foreach ($childTables as $table => $fk) {
                if (!Schema::hasTable($table)) {
                    $log[] = ['status' => 'warning', 'msg' => "Table not found, skipped: {$table}"];
                    continue;
                }
                if (!Schema::hasColumn($table, $fk)) {
                    $log[] = ['status' => 'warning', 'msg' => "Column '{$fk}' missing in {$table}, skipped"];
                    continue;
                }

                $deleted = DB::table($table)->where($fk, $repairId)->delete();
                if ($deleted > 0) {
                    $log[] = ['status' => 'success', 'msg' => "Removed {$deleted} row(s) from {$table}"];
                }
            }

            // ledger_transactions — uses reference_module + reference_id
            if (Schema::hasTable('ledger_transactions')) {
                $del = DB::table('ledger_transactions')
                    ->where('reference_module', 'repair')
                    ->where('reference_id', $repairId)
                    ->delete();
                if ($del > 0) {
                    $log[] = ['status' => 'success', 'msg' => "Removed {$del} row(s) from ledger_transactions"];
                }
            }

            // activity_logs — uses module + reference_id
            if (Schema::hasTable('activity_logs')) {
                $del = DB::table('activity_logs')
                    ->where('module', 'repair')
                    ->where('reference_id', $repairId)
                    ->delete();
                if ($del > 0) {
                    $log[] = ['status' => 'success', 'msg' => "Removed {$del} row(s) from activity_logs"];
                }
            }

            // Delete the repair itself
            DB::table('repairs')->where('id', $repairId)->delete();
            $log[] = ['status' => 'success', 'msg' => "✅ Repair ticket {$ticket} permanently deleted."];

            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            return response()->json(['success' => true, 'log' => $log]);

        } catch (\Throwable $e) {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            $log[] = ['status' => 'error', 'msg' => 'Error: ' . $e->getMessage()];
            return response()->json(['success' => false, 'log' => $log], 500);
        }
    }

    private function getTableStats(): array
    {
        $tables = [
            'customers'     => 'Customers',
            'invoices'      => 'Invoices',
            'repairs'       => 'Repairs',
            'purchases'     => 'Purchases',
            'expenses'      => 'Expenses',
            'products'      => 'Products',
            'parts'         => 'Parts',
            'categories'    => 'Categories',
            'brands'        => 'Brands',
            'service_types' => 'Services',
        ];

        $stats = [];
        foreach ($tables as $table => $label) {
            try {
                $stats[$label] = Schema::hasTable($table) ? DB::table($table)->count() : 0;
            } catch (\Exception $e) {
                $stats[$label] = 0;
            }
        }
        return $stats;
    }
}
