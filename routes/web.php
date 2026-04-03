<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    AuthController, DashboardController, CategoryController, SubcategoryController,
    BrandController, ProductController, InventoryController,
    InvoiceController, RepairController, CustomerController,
    RechargeController, ExpenseController, LedgerController,
    PoRequestController, UserController, SettingController, ReportController,
    PartController, RepairReturnController, RoleController, MenuController,
    CreditNoteController, SetupController, DevToolsController, HomeController,
    ReturnController, BlogController, FaqController, SeoPageController
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
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ─── Public pages ───────────────────────────────────────────────────────────
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/sitemap.xml', [HomeController::class, 'sitemap'])->name('sitemap');
Route::get('/robots.txt', [HomeController::class, 'robots'])->name('robots');

// SEO landing pages
Route::get('/screen-replacement', [HomeController::class, 'serviceLanding'])->defaults('slug', 'screen-replacement')->name('seo.screen');
Route::get('/battery-replacement', [HomeController::class, 'serviceLanding'])->defaults('slug', 'battery-replacement')->name('seo.battery');
Route::get('/iphone-repair', [HomeController::class, 'serviceLanding'])->defaults('slug', 'iphone-repair')->name('seo.iphone');

// Public Blog
Route::get('/blog', [HomeController::class, 'blogIndex'])->name('blog.public.index');
Route::get('/blog/{slug}', [HomeController::class, 'blogShow'])->name('blog.public.show');

// Public FAQ
Route::get('/faq', [HomeController::class, 'faqPage'])->name('faq.public');

// Dynamic SEO Pages (catch-all for dynamic slugs — must be LAST)
Route::get('/page/{slug}', [HomeController::class, 'dynamicPage'])->name('page.public');

// Repair tracking (public)
Route::get('/track', [RepairController::class, 'trackingLanding'])->name('track.landing');
Route::get('/track/{trackingId}', [RepairController::class, 'track'])->name('track.show');

// ─── Admin (protected) routes ────────────────────────────────────────────────
Route::prefix('admin')->middleware('auth')->group(function () {
    Route::get('/', fn() => redirect('/admin/dashboard'));
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
    Route::post('brands/{brand}/upload-image', [BrandController::class, 'uploadImage']);

    // Parts
    Route::resource('parts', PartController::class)->except(['edit', 'show']);
    Route::get('parts-search', [PartController::class, 'search']);
    Route::post('parts/{part}/upload-image', [PartController::class, 'uploadImage']);

    // Products & Inventory
    Route::resource('products', ProductController::class)->except(['edit']);
    Route::get('products-search', [ProductController::class, 'search']);
    Route::get('products-filter-data', [ProductController::class, 'filterData']);
    Route::post('products/{product}/upload-image', [ProductController::class, 'uploadImage']);
    Route::get('inventory', [InventoryController::class, 'index'])->name('inventory.index');
    Route::post('inventory/adjust', [InventoryController::class, 'adjust']);
    Route::get('inventory/adjustments', [InventoryController::class, 'adjustments']);

    // POS & Invoices
    Route::get('pos', [InvoiceController::class, 'create'])->name('pos');
    Route::resource('invoices', InvoiceController::class)->except(['create', 'edit', 'update', 'destroy']);
    Route::post('invoices/{invoice}/pay', [InvoiceController::class, 'pay'])->name('invoices.pay');
    Route::get('invoices/{invoice}/print', [InvoiceController::class, 'print'])->name('invoices.print');

    // Repairs
    Route::resource('repairs', RepairController::class)->except(['edit', 'destroy']);
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
    Route::post('repairs/{repair}/duplicate', [RepairController::class, 'duplicateRepair']);
    Route::get('repairs/{repair}/print', [RepairController::class, 'print'])->name('repairs.print');
    Route::get('repairs/{repair}/invoice', [RepairController::class, 'invoice'])->name('repairs.invoice');
    Route::get('repairs/{repair}/cost-breakdown', [RepairController::class, 'costBreakdown'])->name('repairs.cost-breakdown');

    // Customer Returns
    Route::get('returns', [ReturnController::class, 'index']);
    Route::get('returns/refunds', [ReturnController::class, 'refunds']);
    Route::post('returns/customer', [ReturnController::class, 'storeCustomerReturn']);
    Route::put('returns/{type}/{id}/status', [ReturnController::class, 'updateStatus']);

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

    // Expenses – category routes MUST come before the resource to avoid {expense} swallowing "categories"
    Route::get('expenses/categories', [ExpenseController::class, 'categories'])->name('expenses.categories');
    Route::post('expenses/categories', [ExpenseController::class, 'storeCategory'])->name('expenses.categories.store');
    Route::put('expenses/categories/{category}', [ExpenseController::class, 'updateCategory'])->name('expenses.categories.update');
    Route::delete('expenses/categories/{category}', [ExpenseController::class, 'destroyCategory'])->name('expenses.categories.destroy');
    Route::resource('expenses', ExpenseController::class)->except(['edit', 'show']);

    // Ledger
    Route::get('ledger', [LedgerController::class, 'index'])->name('ledger.index');
    Route::get('ledger/summary', [LedgerController::class, 'summary']);

    // PO Requests (Out-of-stock customer requests)
    Route::get('po', [PoRequestController::class, 'index'])->name('po.index');
    Route::post('po', [PoRequestController::class, 'store']);
    Route::put('po/{poRequest}/status', [PoRequestController::class, 'updateStatus']);

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

    // Settings
    Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
    Route::put('settings', [SettingController::class, 'update']);
    Route::get('print-settings', [SettingController::class, 'printSettingsPage'])->name('print-settings.index');
    Route::get('print-preview/{type}', [SettingController::class, 'printPreview'])->name('settings.print-preview');
    Route::get('service-types', [SettingController::class, 'serviceTypes'])->name('service-types.index');
    Route::get('service-types-search', [SettingController::class, 'searchServiceTypes']);
    Route::get('vendors-search', [SettingController::class, 'searchVendors']);
    Route::post('service-types', [SettingController::class, 'storeServiceType']);
    Route::put('service-types/{serviceType}', [SettingController::class, 'updateServiceType']);
    Route::delete('service-types/{serviceType}', [SettingController::class, 'destroyServiceType']);
    Route::post('service-types/{serviceType}/upload-image', [SettingController::class, 'uploadServiceTypeImage']);
    Route::get('recharge-providers', [SettingController::class, 'rechargeProviders']);
    Route::post('recharge-providers', [SettingController::class, 'storeRechargeProvider']);
    Route::put('recharge-providers/{rechargeProvider}', [SettingController::class, 'updateRechargeProvider']);
    Route::delete('recharge-providers/{rechargeProvider}', [SettingController::class, 'destroyRechargeProvider']);
    Route::post('recharge-providers/{rechargeProvider}/upload-image', [SettingController::class, 'uploadRechargeProviderImage']);
    Route::get('vendors', [SettingController::class, 'vendors'])->name('vendors.index');
    Route::get('vendors/create', [SettingController::class, 'createVendor'])->name('vendors.create');
    Route::post('vendors', [SettingController::class, 'storeVendor']);
    Route::put('vendors/{vendor}', [SettingController::class, 'updateVendor']);
    Route::post('vendors/{vendor}/upload-image', [SettingController::class, 'uploadVendorImage']);
    Route::get('email-templates', [SettingController::class, 'emailTemplates']);
    Route::put('email-templates/{emailTemplate}', [SettingController::class, 'updateEmailTemplate']);
    Route::post('notifications/test', [SettingController::class, 'testNotification']);
    Route::get('notifications', [SettingController::class, 'notifications'])->name('notifications.index');
    Route::get('activity-logs', [SettingController::class, 'activityLogs'])->name('activity-logs.index');
    Route::get('backups', [SettingController::class, 'backups']);
    Route::post('backups', [SettingController::class, 'createBackup']);
    Route::get('backups/{backup}/download', [SettingController::class, 'downloadBackup'])->name('backups.download');

    // Import
    Route::post('import/validate', [SettingController::class, 'validateImport']);
    Route::post('import/confirm', [SettingController::class, 'confirmImport']);

    // ─── SEO & Content Management ─────────────────────────────────────────
    // Blog Posts
    Route::resource('blog', BlogController::class)->except(['edit', 'create']);
    Route::post('blog/{blog}/upload-image', [BlogController::class, 'uploadImage']);

    // FAQs
    Route::get('faqs/categories', [FaqController::class, 'categories'])->name('faqs.categories');
    Route::post('faqs/categories', [FaqController::class, 'storeCategory'])->name('faqs.categories.store');
    Route::put('faqs/categories/{faqCategory}', [FaqController::class, 'updateCategory'])->name('faqs.categories.update');
    Route::delete('faqs/categories/{faqCategory}', [FaqController::class, 'destroyCategory'])->name('faqs.categories.destroy');
    Route::resource('faqs', FaqController::class)->except(['edit', 'create', 'show']);

    // SEO Pages (dynamic landing pages)
    Route::resource('seo-pages', SeoPageController::class)->except(['edit', 'create']);
    Route::post('seo-pages/{seoPage}/upload-image', [SeoPageController::class, 'uploadImage']);

    // SEO Settings
    Route::get('seo-settings', [SettingController::class, 'seoSettings'])->name('seo-settings.index');
    Route::put('seo-settings', [SettingController::class, 'updateSeoSettings'])->name('seo-settings.update');

    // ─── Dev Tools (admin only) ────────────────────────────────────────────
    Route::prefix('dev-tools')->name('dev-tools.')->group(function () {
        Route::get('/',           [DevToolsController::class, 'index'])->name('index');
        Route::post('/reset',     [DevToolsController::class, 'resetData'])->name('reset');
        Route::post('/seed',      [DevToolsController::class, 'seedDemo'])->name('seed');
        Route::post('/reset-seed',[DevToolsController::class, 'resetAndSeed'])->name('reset-seed');
    });
});
