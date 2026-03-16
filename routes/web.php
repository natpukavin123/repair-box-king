<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    AuthController, DashboardController, CategoryController, SubcategoryController,
    BrandController, ProductController, InventoryController, SupplierController,
    PurchaseController, InvoiceController, RepairController, CustomerController,
    RechargeController, ServiceController, ExpenseController, LedgerController,
    ReturnController, UserController, SettingController, ReportController,
    PartController, RepairReturnController, RoleController, MenuController,
    CreditNoteController, SetupController, DevToolsController, TaxController
};

// ─── Setup Wizard (public — no auth needed) ────────────────────────────────
Route::prefix('setup')->name('setup.')->group(function () {
    Route::get('/',          [SetupController::class, 'index'])->name('index');
    Route::get('/database',  [SetupController::class, 'databaseForm'])->name('database');
    Route::post('/database', [SetupController::class, 'saveDatabase'])->name('database.save');
    Route::post('/test-connection', [SetupController::class, 'testConnection'])->name('test-connection');
    Route::get('/owner',     [SetupController::class, 'ownerForm'])->name('owner');
    Route::post('/owner',    [SetupController::class, 'saveOwner'])->name('owner.save');
    Route::get('/migrate',   [SetupController::class, 'migrateForm'])->name('migrate');
    Route::post('/migrate',  [SetupController::class, 'runMigrations'])->name('migrate.run');
    Route::get('/complete',  [SetupController::class, 'complete'])->name('complete');
});

// Auth
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Repair tracking (public)
Route::get('/track/{trackingId}', [RepairController::class, 'track']);

// Protected routes
Route::middleware('auth')->group(function () {
    Route::get('/', fn() => redirect('/dashboard'));
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/dashboard/reminders', [DashboardController::class, 'storeReminder']);
    Route::put('/dashboard/reminders/{reminder}/toggle', [DashboardController::class, 'toggleReminder']);
    Route::delete('/dashboard/reminders/{reminder}', [DashboardController::class, 'deleteReminder']);

    // Master Data
    Route::resource('categories', CategoryController::class)->except(['edit']);
    Route::get('categories/{category}/subcategories', [CategoryController::class, 'subcategories'])->name('categories.subcategories');
    Route::resource('subcategories', SubcategoryController::class)->except(['edit', 'show']);
    Route::get('subcategories/by-category/{category}', [SubcategoryController::class, 'byCategory']);
    Route::resource('brands', BrandController::class)->except(['edit', 'show']);

    // Parts
    Route::resource('parts', PartController::class)->except(['edit', 'show']);
    Route::get('parts-search', [PartController::class, 'search']);

    // Products & Inventory
    Route::resource('products', ProductController::class)->except(['edit']);
    Route::get('products-search', [ProductController::class, 'search']);
    Route::post('products/{product}/upload-image', [ProductController::class, 'uploadImage']);
    Route::get('inventory', [InventoryController::class, 'index'])->name('inventory.index');
    Route::post('inventory/adjust', [InventoryController::class, 'adjust']);
    Route::get('inventory/adjustments', [InventoryController::class, 'adjustments']);

    // Suppliers & Purchases
    Route::resource('suppliers', SupplierController::class)->except(['edit']);
    Route::resource('purchases', PurchaseController::class)->except(['edit', 'update']);

    // POS & Invoices
    Route::get('pos', [InvoiceController::class, 'create'])->name('pos');
    Route::resource('invoices', InvoiceController::class)->except(['create', 'edit', 'update', 'destroy']);
    Route::get('invoices/{invoice}/print', [InvoiceController::class, 'print'])->name('invoices.print');

    // Repairs
    Route::resource('repairs', RepairController::class)->except(['edit', 'update', 'destroy']);
    Route::put('repairs/{repair}/status', [RepairController::class, 'updateStatus']);
    Route::put('repairs/{repair}/service-charge', [RepairController::class, 'updateServiceCharge']);
    Route::post('repairs/{repair}/payment', [RepairController::class, 'addPayment']);
    Route::post('repairs/{repair}/cancel', [RepairController::class, 'cancel']);
    Route::post('repairs/{repair}/parts', [RepairController::class, 'addPart']);
    Route::delete('repairs/{repair}/parts/{partId}', [RepairController::class, 'removePart']);
    Route::post('repairs/{repair}/services', [RepairController::class, 'addService']);
    Route::put('repairs/{repair}/services/{serviceId}', [RepairController::class, 'updateService']);
    Route::delete('repairs/{repair}/services/{serviceId}', [RepairController::class, 'removeService']);
    Route::post('repairs/{repair}/cancel-refund', [RepairController::class, 'cancelWithRefund']);
    Route::post('repairs/{repair}/void', [RepairController::class, 'voidRepair']);
    Route::post('repairs/{repair}/duplicate', [RepairController::class, 'duplicateRepair']);
    Route::get('repairs/{repair}/print', [RepairController::class, 'print'])->name('repairs.print');
    Route::get('repairs/{repair}/invoice', [RepairController::class, 'invoice'])->name('repairs.invoice');
    Route::get('repairs/{repair}/cost-breakdown', [RepairController::class, 'costBreakdown'])->name('repairs.cost-breakdown');

    // Repair Returns
    Route::get('repairs/{repair}/returns/create', [RepairReturnController::class, 'create'])->name('repair-returns.create');
    Route::post('repairs/{repair}/returns', [RepairReturnController::class, 'store'])->name('repair-returns.store');
    Route::get('repairs/{repair}/returns/{return}', [RepairReturnController::class, 'show'])->name('repair-returns.show');
    Route::get('repairs/{repair}/returns/{return}/invoice', [RepairReturnController::class, 'invoice'])->name('repair-returns.invoice');

    // Customers
    Route::resource('customers', CustomerController::class)->except(['edit']);
    Route::get('customers-search', [CustomerController::class, 'search']);

    // Recharges
    Route::resource('recharges', RechargeController::class)->except(['create', 'edit', 'update', 'destroy']);

    // Services
    Route::resource('services', ServiceController::class)->except(['edit', 'show']);

    // Expenses – category routes MUST come before the resource to avoid {expense} swallowing "categories"
    Route::get('expenses/categories', [ExpenseController::class, 'categories'])->name('expenses.categories');
    Route::post('expenses/categories', [ExpenseController::class, 'storeCategory'])->name('expenses.categories.store');
    Route::put('expenses/categories/{category}', [ExpenseController::class, 'updateCategory'])->name('expenses.categories.update');
    Route::delete('expenses/categories/{category}', [ExpenseController::class, 'destroyCategory'])->name('expenses.categories.destroy');
    Route::resource('expenses', ExpenseController::class)->except(['edit', 'show']);

    // Ledger
    Route::get('ledger', [LedgerController::class, 'index'])->name('ledger.index');
    Route::get('ledger/summary', [LedgerController::class, 'summary']);

    // Returns & Refunds
    Route::get('returns', [ReturnController::class, 'index'])->name('returns.index');
    Route::post('returns/customer', [ReturnController::class, 'storeCustomerReturn']);
    Route::post('returns/supplier', [ReturnController::class, 'storeSupplierReturn']);
    Route::put('returns/{type}/{id}/status', [ReturnController::class, 'updateStatus']);
    Route::get('refunds', [ReturnController::class, 'refunds']);
    Route::post('refunds', [ReturnController::class, 'storeRefund']);

    // Users
    Route::resource('users', UserController::class)->except(['edit']);

    // Roles & Permissions
    Route::resource('roles', RoleController::class)->except(['edit', 'create']);
    Route::get('permissions/grouped', [RoleController::class, 'allPermissionsGrouped']);

    // Menu Management
    Route::resource('menus', MenuController::class)->except(['edit', 'show', 'create']);
    Route::post('menus/reorder', [MenuController::class, 'reorder']);

    // Credit Notes
    Route::get('credit-notes', [CreditNoteController::class, 'index'])->name('credit-notes.index');
    Route::get('credit-notes/from-invoice/{invoice}', [CreditNoteController::class, 'createFromInvoice']);
    Route::get('credit-notes/from-repair/{repair}', [CreditNoteController::class, 'createFromRepair']);
    Route::post('credit-notes', [CreditNoteController::class, 'store']);
    Route::get('credit-notes/{creditNote}', [CreditNoteController::class, 'show']);
    Route::post('credit-notes/{creditNote}/approve', [CreditNoteController::class, 'approve']);
    Route::post('credit-notes/{creditNote}/refund', [CreditNoteController::class, 'processRefund']);
    Route::post('credit-notes/{creditNote}/apply-repair', [CreditNoteController::class, 'applyToRepair']);
    Route::post('credit-notes/{creditNote}/apply-invoice', [CreditNoteController::class, 'applyToInvoice']);
    Route::post('credit-notes/{creditNote}/cancel', [CreditNoteController::class, 'cancel']);

    // Reports
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/sales', [ReportController::class, 'sales']);
    Route::get('reports/profit', [ReportController::class, 'profit']);

    // GST & Tax Management
    Route::get('tax', [TaxController::class, 'index'])->name('tax.index');
    Route::post('tax/rates', [TaxController::class, 'storeRate']);
    Route::put('tax/rates/{taxRate}', [TaxController::class, 'updateRate']);
    Route::delete('tax/rates/{taxRate}', [TaxController::class, 'deleteRate']);
    Route::post('tax/hsn', [TaxController::class, 'storeHsn']);
    Route::put('tax/hsn/{hsnCode}', [TaxController::class, 'updateHsn']);
    Route::delete('tax/hsn/{hsnCode}', [TaxController::class, 'deleteHsn']);
    Route::get('tax/hsn-search', [TaxController::class, 'searchHsn']);
    Route::get('tax/rates-list', [TaxController::class, 'taxRatesList']);
    Route::put('tax/settings', [TaxController::class, 'updateSettings']);

    // Settings
    Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
    Route::put('settings', [SettingController::class, 'update']);
    Route::get('service-types', [SettingController::class, 'serviceTypes']);
    Route::get('service-types-search', [SettingController::class, 'searchServiceTypes']);
    Route::get('vendors-search', [SettingController::class, 'searchVendors']);
    Route::post('service-types', [SettingController::class, 'storeServiceType']);
    Route::put('service-types/{serviceType}', [SettingController::class, 'updateServiceType']);
    Route::post('service-types/{serviceType}/upload-image', [SettingController::class, 'uploadServiceTypeImage']);
    Route::get('recharge-providers', [SettingController::class, 'rechargeProviders']);
    Route::post('recharge-providers', [SettingController::class, 'storeRechargeProvider']);
    Route::get('vendors', [SettingController::class, 'vendors'])->name('vendors.index');
    Route::get('vendors/create', [SettingController::class, 'createVendor'])->name('vendors.create');
    Route::post('vendors', [SettingController::class, 'storeVendor']);
    Route::put('vendors/{vendor}', [SettingController::class, 'updateVendor']);
    Route::get('email-templates', [SettingController::class, 'emailTemplates']);
    Route::put('email-templates/{emailTemplate}', [SettingController::class, 'updateEmailTemplate']);
    Route::post('notifications/test', [SettingController::class, 'testNotification']);
    Route::get('notifications', [SettingController::class, 'notifications'])->name('notifications.index');
    Route::get('activity-logs', [SettingController::class, 'activityLogs'])->name('activity-logs.index');
    Route::get('backups', [SettingController::class, 'backups']);
    Route::post('backups', [SettingController::class, 'createBackup']);

    // ─── Dev Tools (admin only) ────────────────────────────────────────────
    Route::prefix('dev-tools')->name('dev-tools.')->group(function () {
        Route::get('/',           [DevToolsController::class, 'index'])->name('index');
        Route::post('/reset',     [DevToolsController::class, 'resetData'])->name('reset');
        Route::post('/seed',      [DevToolsController::class, 'seedDemo'])->name('seed');
        Route::post('/reset-seed',[DevToolsController::class, 'resetAndSeed'])->name('reset-seed');
    });
});

