<?php

namespace Database\Seeders;

use App\Models\{
    Role, Permission, User, Category, Subcategory, Brand, ServiceType,
    RechargeProvider, Vendor, Customer, Product, Inventory, Supplier,
    Purchase, PurchaseItem, Invoice, InvoiceItem, InvoicePayment,
    Repair, RepairStatusHistory, RepairPart, RepairPayment,
    Recharge, Service, ExpenseCategory, Expense, Setting,
    EmailTemplate, LedgerTransaction, ActivityLog, Part
};
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // === ROLES ===
        $admin = Role::create(['name' => 'Admin', 'description' => 'Full system access']);
        $stockMgr = Role::create(['name' => 'Stock Manager', 'description' => 'Manage inventory and purchases']);
        $tech = Role::create(['name' => 'Technician', 'description' => 'Handle repairs']);
        $billing = Role::create(['name' => 'Billing Staff', 'description' => 'POS and billing']);

        // === PERMISSIONS ===
        $modules = ['dashboard', 'categories', 'products', 'inventory', 'purchases', 'suppliers',
            'pos', 'invoices', 'repairs', 'recharges', 'services', 'customers', 'returns',
            'ledger', 'expenses', 'reports', 'users', 'settings', 'backups', 'notifications'];
        $actions = ['view', 'create', 'edit', 'delete'];
        $permIds = [];
        foreach ($modules as $mod) {
            foreach ($actions as $act) {
                $p = Permission::create(['name' => "{$mod}.{$act}", 'module' => $mod]);
                $permIds[$mod][] = $p->id;
            }
        }
        // Admin gets all
        $admin->permissions()->attach(Permission::pluck('id'));
        // Stock Manager gets inventory-related
        foreach (['dashboard', 'categories', 'products', 'inventory', 'purchases', 'suppliers'] as $m) {
            $stockMgr->permissions()->attach($permIds[$m]);
        }
        // Technician gets repairs
        foreach (['dashboard', 'repairs', 'products', 'inventory'] as $m) {
            $tech->permissions()->attach($permIds[$m]);
        }
        // Billing gets POS
        foreach (['dashboard', 'pos', 'invoices', 'customers', 'products', 'recharges', 'services'] as $m) {
            $billing->permissions()->attach($permIds[$m]);
        }

        // === USERS ===
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@repairbox.com',
            'password' => Hash::make('password'),
            'role_id' => $admin->id,
            'status' => 'active',
        ]);
        User::create([
            'name' => 'Stock Manager',
            'email' => 'stock@repairbox.com',
            'password' => Hash::make('password'),
            'role_id' => $stockMgr->id,
            'status' => 'active',
        ]);
        $techUser = User::create([
            'name' => 'Rajesh Kumar',
            'email' => 'tech@repairbox.com',
            'password' => Hash::make('password'),
            'role_id' => $tech->id,
            'status' => 'active',
        ]);
        User::create([
            'name' => 'Billing Staff',
            'email' => 'billing@repairbox.com',
            'password' => Hash::make('password'),
            'role_id' => $billing->id,
            'status' => 'active',
        ]);

        // === CATEGORIES ===
        $catMobile = Category::create(['name' => 'Mobile Phones', 'description' => 'Smartphones and feature phones']);
        $catAccessory = Category::create(['name' => 'Accessories', 'description' => 'Phone accessories']);
        $catParts = Category::create(['name' => 'Spare Parts', 'description' => 'Repair spare parts']);
        $catElec = Category::create(['name' => 'Electronics', 'description' => 'Electronic gadgets']);

        // === SUBCATEGORIES ===
        $subSmartphone = Subcategory::create(['category_id' => $catMobile->id, 'name' => 'Smartphones']);
        $subFeature = Subcategory::create(['category_id' => $catMobile->id, 'name' => 'Feature Phones']);
        $subCases = Subcategory::create(['category_id' => $catAccessory->id, 'name' => 'Cases & Covers']);
        $subChargers = Subcategory::create(['category_id' => $catAccessory->id, 'name' => 'Chargers']);
        $subScreens = Subcategory::create(['category_id' => $catParts->id, 'name' => 'Screens']);
        $subBatteries = Subcategory::create(['category_id' => $catParts->id, 'name' => 'Batteries']);
        $subEarphones = Subcategory::create(['category_id' => $catAccessory->id, 'name' => 'Earphones']);
        $subTablets = Subcategory::create(['category_id' => $catElec->id, 'name' => 'Tablets']);

        // === BRANDS ===
        $brandSamsung = Brand::create(['name' => 'Samsung']);
        $brandApple = Brand::create(['name' => 'Apple']);
        $brandXiaomi = Brand::create(['name' => 'Xiaomi']);
        $brandOnePlus = Brand::create(['name' => 'OnePlus']);
        $brandVivo = Brand::create(['name' => 'Vivo']);
        $brandOppo = Brand::create(['name' => 'Oppo']);
        $brandRealme = Brand::create(['name' => 'Realme']);
        $brandNokia = Brand::create(['name' => 'Nokia']);

        // === SERVICE TYPES ===
        ServiceType::create(['name' => 'Screen Replacement', 'default_price' => 1500, 'description' => 'LCD/AMOLED screen replacement']);
        ServiceType::create(['name' => 'Battery Replacement', 'default_price' => 800, 'description' => 'Battery swap']);
        ServiceType::create(['name' => 'Software Update', 'default_price' => 300, 'description' => 'OS update/flash']);
        ServiceType::create(['name' => 'Data Recovery', 'default_price' => 1000, 'description' => 'Recover data from device']);
        ServiceType::create(['name' => 'Water Damage Repair', 'default_price' => 2000, 'description' => 'Water damage diagnosis and repair']);
        ServiceType::create(['name' => 'Charging Port Repair', 'default_price' => 600, 'description' => 'Fix charging port issues']);

        // === RECHARGE PROVIDERS ===
        RechargeProvider::create(['name' => 'Jio', 'provider_type' => 'mobile']);
        RechargeProvider::create(['name' => 'Airtel', 'provider_type' => 'mobile']);
        RechargeProvider::create(['name' => 'Vi (Vodafone Idea)', 'provider_type' => 'mobile']);
        RechargeProvider::create(['name' => 'BSNL', 'provider_type' => 'mobile']);
        RechargeProvider::create(['name' => 'Tata Play', 'provider_type' => 'dth']);
        RechargeProvider::create(['name' => 'Airtel DTH', 'provider_type' => 'dth']);

        // === VENDORS ===
        $vendor1 = Vendor::create(['name' => 'QuickFix Repairs', 'phone' => '9876543210', 'address' => 'Shop 5, Tech Market, Delhi', 'specialization' => 'Motherboard repair']);
        $vendor2 = Vendor::create(['name' => 'Screen Pro', 'phone' => '9876543211', 'address' => 'Shop 12, Mobile Lane, Mumbai', 'specialization' => 'Screen replacement']);

        // === CUSTOMERS ===
        $cust1 = Customer::create(['name' => 'Amit Sharma', 'mobile_number' => '9876500001', 'email' => 'amit@email.com', 'address' => '123, MG Road, Delhi', 'loyalty_points' => 150, 'total_spent' => 12500]);
        $cust2 = Customer::create(['name' => 'Priya Patel', 'mobile_number' => '9876500002', 'email' => 'priya@email.com', 'address' => '456, Park Street, Mumbai', 'loyalty_points' => 80, 'total_spent' => 8500]);
        $cust3 = Customer::create(['name' => 'Rahul Singh', 'mobile_number' => '9876500003', 'address' => '789, Gandhi Nagar, Bangalore', 'loyalty_points' => 200, 'total_spent' => 25000]);
        $cust4 = Customer::create(['name' => 'Sneha Gupta', 'mobile_number' => '9876500004', 'email' => 'sneha@email.com', 'address' => '321, Lake Road, Kolkata']);
        $cust5 = Customer::create(['name' => 'Vikram Joshi', 'mobile_number' => '9876500005', 'address' => '654, Civil Lines, Jaipur']);

        // === PRODUCTS ===
        $products = [
            ['category_id' => $catMobile->id, 'subcategory_id' => $subSmartphone->id, 'brand_id' => $brandSamsung->id, 'name' => 'Samsung Galaxy A14', 'sku' => 'SAM-A14-001', 'barcode' => '8901234560001', 'purchase_price' => 8500, 'mrp' => 12999, 'selling_price' => 11999],
            ['category_id' => $catMobile->id, 'subcategory_id' => $subSmartphone->id, 'brand_id' => $brandApple->id, 'name' => 'iPhone 13', 'sku' => 'APL-I13-001', 'barcode' => '8901234560002', 'purchase_price' => 45000, 'mrp' => 59999, 'selling_price' => 56999],
            ['category_id' => $catMobile->id, 'subcategory_id' => $subSmartphone->id, 'brand_id' => $brandXiaomi->id, 'name' => 'Redmi Note 12', 'sku' => 'XIA-RN12-001', 'barcode' => '8901234560003', 'purchase_price' => 10000, 'mrp' => 15999, 'selling_price' => 14499],
            ['category_id' => $catAccessory->id, 'subcategory_id' => $subCases->id, 'brand_id' => $brandSamsung->id, 'name' => 'Samsung Clear Case A14', 'sku' => 'ACC-SC-001', 'barcode' => '8901234560004', 'purchase_price' => 100, 'mrp' => 499, 'selling_price' => 399],
            ['category_id' => $catAccessory->id, 'subcategory_id' => $subChargers->id, 'brand_id' => $brandApple->id, 'name' => 'Apple 20W Charger', 'sku' => 'ACC-AC-001', 'barcode' => '8901234560005', 'purchase_price' => 800, 'mrp' => 1999, 'selling_price' => 1799],
            ['category_id' => $catParts->id, 'subcategory_id' => $subScreens->id, 'brand_id' => $brandSamsung->id, 'name' => 'Samsung A14 Screen', 'sku' => 'PRT-SS-001', 'barcode' => '8901234560006', 'purchase_price' => 1200, 'mrp' => 2500, 'selling_price' => 2200],
            ['category_id' => $catParts->id, 'subcategory_id' => $subBatteries->id, 'brand_id' => $brandApple->id, 'name' => 'iPhone 13 Battery', 'sku' => 'PRT-IB-001', 'barcode' => '8901234560007', 'purchase_price' => 500, 'mrp' => 1200, 'selling_price' => 999],
            ['category_id' => $catAccessory->id, 'subcategory_id' => $subEarphones->id, 'brand_id' => $brandOnePlus->id, 'name' => 'OnePlus Buds Z2', 'sku' => 'ACC-OB-001', 'barcode' => '8901234560008', 'purchase_price' => 1200, 'mrp' => 2999, 'selling_price' => 2499],
            ['category_id' => $catMobile->id, 'subcategory_id' => $subFeature->id, 'brand_id' => $brandNokia->id, 'name' => 'Nokia 105', 'sku' => 'NOK-105-001', 'barcode' => '8901234560009', 'purchase_price' => 900, 'mrp' => 1499, 'selling_price' => 1299],
            ['category_id' => $catMobile->id, 'subcategory_id' => $subSmartphone->id, 'brand_id' => $brandVivo->id, 'name' => 'Vivo Y16', 'sku' => 'VIV-Y16-001', 'barcode' => '8901234560010', 'purchase_price' => 7000, 'mrp' => 10999, 'selling_price' => 9999],
        ];

        $createdProducts = [];
        foreach ($products as $p) {
            $prod = Product::create($p);
            $createdProducts[] = $prod;
            Inventory::create(['product_id' => $prod->id, 'current_stock' => rand(5, 50), 'reserved_stock' => 0]);
        }

        // === SUPPLIERS ===
        $sup1 = Supplier::create(['name' => 'MobileTech Distributors', 'contact_person' => 'Suresh Mehta', 'phone' => '9888000001', 'email' => 'suresh@mobiletech.com', 'address' => 'Nehru Place, Delhi', 'gst_number' => '07AAACM1234R1Z5']);
        $sup2 = Supplier::create(['name' => 'SpareHub India', 'contact_person' => 'Ravi Kapoor', 'phone' => '9888000002', 'email' => 'ravi@sparehub.com', 'address' => 'Lamington Road, Mumbai', 'gst_number' => '27AADCS5678P1Z8']);

        // === PURCHASES ===
        $pur1 = Purchase::create(['supplier_id' => $sup1->id, 'purchase_date' => now()->subDays(30), 'invoice_number' => 'SUP-INV-001', 'total_amount' => 85000]);
        $pi1 = PurchaseItem::create(['purchase_id' => $pur1->id, 'product_id' => $createdProducts[0]->id, 'quantity' => 10, 'purchase_price' => 8500, 'remaining_quantity' => 8]);
        $pur2 = Purchase::create(['supplier_id' => $sup2->id, 'purchase_date' => now()->subDays(15), 'invoice_number' => 'SUP-INV-002', 'total_amount' => 24000]);
        PurchaseItem::create(['purchase_id' => $pur2->id, 'product_id' => $createdProducts[5]->id, 'quantity' => 20, 'purchase_price' => 1200, 'remaining_quantity' => 15]);

        // === INVOICES ===
        $inv1 = Invoice::create([
            'invoice_number' => 'INV-000001',
            'customer_id' => $cust1->id,
            'total_amount' => 14198,
            'discount' => 200,
            'final_amount' => 13998,
            'payment_status' => 'paid',
            'created_by' => 1,
        ]);
        InvoiceItem::create(['invoice_id' => $inv1->id, 'item_type' => 'product', 'product_id' => $createdProducts[2]->id, 'item_name' => 'Redmi Note 12', 'quantity' => 1, 'price' => 14499, 'total' => 14499]);
        InvoiceItem::create(['invoice_id' => $inv1->id, 'item_type' => 'product', 'product_id' => $createdProducts[3]->id, 'item_name' => 'Samsung Clear Case A14', 'quantity' => 1, 'price' => 399, 'total' => 399]);
        InvoicePayment::create(['invoice_id' => $inv1->id, 'payment_method' => 'cash', 'amount' => 10000]);
        InvoicePayment::create(['invoice_id' => $inv1->id, 'payment_method' => 'upi', 'amount' => 3998, 'transaction_reference' => 'UPI123456']);

        $inv2 = Invoice::create([
            'invoice_number' => 'INV-000002',
            'customer_id' => $cust2->id,
            'total_amount' => 1799,
            'discount' => 0,
            'final_amount' => 1799,
            'payment_status' => 'paid',
            'created_by' => 1,
        ]);
        InvoiceItem::create(['invoice_id' => $inv2->id, 'item_type' => 'product', 'product_id' => $createdProducts[4]->id, 'item_name' => 'Apple 20W Charger', 'quantity' => 1, 'price' => 1799, 'total' => 1799]);
        InvoicePayment::create(['invoice_id' => $inv2->id, 'payment_method' => 'card', 'amount' => 1799]);

        // === PARTS (Spare parts for repairs) ===
        $partScreen = Part::create(['name' => 'Samsung Galaxy S21 Screen', 'sku' => 'PRT-SS21-SCR', 'cost_price' => 1200, 'selling_price' => 1800]);
        $partBattery = Part::create(['name' => 'Samsung Galaxy S21 Battery', 'sku' => 'PRT-SS21-BAT', 'cost_price' => 450, 'selling_price' => 700]);
        Part::create(['name' => 'iPhone 12 Screen', 'sku' => 'PRT-IP12-SCR', 'cost_price' => 2500, 'selling_price' => 3500]);
        $partIpBat = Part::create(['name' => 'iPhone 12 Battery', 'sku' => 'PRT-IP12-BAT', 'cost_price' => 800, 'selling_price' => 1200]);
        Part::create(['name' => 'Samsung A54 Screen', 'sku' => 'PRT-SA54-SCR', 'cost_price' => 900, 'selling_price' => 1400]);
        Part::create(['name' => 'OnePlus Nord Charging Port', 'sku' => 'PRT-OPN-CHP', 'cost_price' => 200, 'selling_price' => 400]);
        Part::create(['name' => 'Universal SIM Tray', 'sku' => 'PRT-UNI-SIM', 'cost_price' => 30, 'selling_price' => 80]);
        Part::create(['name' => 'iPhone 13 Back Glass', 'sku' => 'PRT-IP13-BGL', 'cost_price' => 600, 'selling_price' => 1000]);
        Part::create(['name' => 'Samsung S21 Charging Flex', 'sku' => 'PRT-SS21-CFX', 'cost_price' => 350, 'selling_price' => 600]);
        Part::create(['name' => 'Xiaomi Redmi Note 11 Screen', 'sku' => 'PRT-XRN11-SCR', 'cost_price' => 700, 'selling_price' => 1100]);
        Part::create(['name' => 'Universal Earpiece Speaker', 'sku' => 'PRT-UNI-EAR', 'cost_price' => 80, 'selling_price' => 200]);
        Part::create(['name' => 'iPhone 12 Charging Port', 'sku' => 'PRT-IP12-CHP', 'cost_price' => 500, 'selling_price' => 900]);

        // === REPAIRS ===
        // Repair 1: In Progress (with advance payment)
        $rep1 = Repair::create([
            'ticket_number' => 'RPR-000001',
            'tracking_id' => 'TRK-A1B2C3D4',
            'customer_id' => $cust3->id,
            'device_brand' => 'Samsung',
            'device_model' => 'Galaxy S21',
            'imei' => '352345678901234',
            'problem_description' => 'Cracked screen, touch not working',
            'estimated_cost' => 3500,
            'expected_delivery_date' => now()->addDays(3),
            'technician_id' => $techUser->id,
            'status' => 'in_progress',
            'record_type' => 'original',
        ]);
        RepairStatusHistory::create(['repair_id' => $rep1->id, 'status' => 'received', 'notes' => 'Device received', 'updated_by' => 1]);
        RepairStatusHistory::create(['repair_id' => $rep1->id, 'status' => 'in_progress', 'notes' => 'Screen replacement started', 'updated_by' => $techUser->id]);
        RepairPart::create(['repair_id' => $rep1->id, 'part_id' => $partScreen->id, 'quantity' => 1, 'cost_price' => 1200]);
        RepairPayment::create(['repair_id' => $rep1->id, 'payment_type' => 'advance', 'amount' => 1500, 'payment_method' => 'cash', 'direction' => 'IN']);

        // Repair 2: Completed (ready for service charge & payment)
        $rep2 = Repair::create([
            'ticket_number' => 'RPR-000002',
            'tracking_id' => 'TRK-E5F6G7H8',
            'customer_id' => $cust4->id,
            'device_brand' => 'Apple',
            'device_model' => 'iPhone 12',
            'imei' => '352345678905678',
            'problem_description' => 'Battery draining fast, device overheating',
            'estimated_cost' => 2500,
            'service_charge' => 500,
            'expected_delivery_date' => now()->addDays(2),
            'technician_id' => $techUser->id,
            'status' => 'completed',
            'completed_at' => now()->subDay(),
            'record_type' => 'original',
        ]);
        RepairStatusHistory::create(['repair_id' => $rep2->id, 'status' => 'received', 'notes' => 'Device received', 'updated_by' => 1]);
        RepairStatusHistory::create(['repair_id' => $rep2->id, 'status' => 'in_progress', 'notes' => 'Battery diagnosis started', 'updated_by' => $techUser->id]);
        RepairStatusHistory::create(['repair_id' => $rep2->id, 'status' => 'completed', 'notes' => 'Battery replaced successfully', 'updated_by' => $techUser->id]);
        RepairPart::create(['repair_id' => $rep2->id, 'part_id' => $partIpBat->id, 'quantity' => 1, 'cost_price' => 800]);
        RepairPayment::create(['repair_id' => $rep2->id, 'payment_type' => 'advance', 'amount' => 500, 'payment_method' => 'upi', 'direction' => 'IN']);

        // Repair 3: Received (just created, with advance)
        $rep3 = Repair::create([
            'ticket_number' => 'RPR-000003',
            'tracking_id' => 'TRK-J9K0L1M2',
            'customer_id' => $cust1->id,
            'device_brand' => 'OnePlus',
            'device_model' => 'Nord CE 3',
            'imei' => '352345678909012',
            'problem_description' => 'Charging port not working, loose connection',
            'estimated_cost' => 1200,
            'expected_delivery_date' => now()->addDays(2),
            'status' => 'received',
            'record_type' => 'original',
        ]);
        RepairStatusHistory::create(['repair_id' => $rep3->id, 'status' => 'received', 'notes' => 'Device received with charger', 'updated_by' => 1]);
        RepairPayment::create(['repair_id' => $rep3->id, 'payment_type' => 'advance', 'amount' => 300, 'payment_method' => 'cash', 'direction' => 'IN']);

        // Repair 4: Payment stage (fully repaired, collecting payment)
        $rep4 = Repair::create([
            'ticket_number' => 'RPR-000004',
            'tracking_id' => 'TRK-N3O4P5Q6',
            'customer_id' => $cust2->id,
            'device_brand' => 'Xiaomi',
            'device_model' => 'Redmi Note 11',
            'problem_description' => 'Screen flickering and ghost touch',
            'estimated_cost' => 2000,
            'service_charge' => 300,
            'expected_delivery_date' => now()->subDay(),
            'technician_id' => $techUser->id,
            'status' => 'payment',
            'completed_at' => now()->subDays(2),
            'record_type' => 'original',
        ]);
        RepairStatusHistory::create(['repair_id' => $rep4->id, 'status' => 'received', 'notes' => 'Device received', 'updated_by' => 1]);
        RepairStatusHistory::create(['repair_id' => $rep4->id, 'status' => 'in_progress', 'notes' => 'Screen replacement', 'updated_by' => $techUser->id]);
        RepairStatusHistory::create(['repair_id' => $rep4->id, 'status' => 'completed', 'notes' => 'Screen replaced', 'updated_by' => $techUser->id]);
        RepairStatusHistory::create(['repair_id' => $rep4->id, 'status' => 'payment', 'notes' => 'Ready for payment collection', 'updated_by' => 1]);
        RepairPart::create(['repair_id' => $rep4->id, 'part_id' => Part::where('sku', 'PRT-XRN11-SCR')->first()->id, 'quantity' => 1, 'cost_price' => 700]);
        RepairPayment::create(['repair_id' => $rep4->id, 'payment_type' => 'advance', 'amount' => 500, 'payment_method' => 'cash', 'direction' => 'IN']);

        // Repair 5: Closed (fully paid, invoice ready)
        $rep5 = Repair::create([
            'ticket_number' => 'RPR-000005',
            'tracking_id' => 'TRK-R7S8T9U0',
            'customer_id' => $cust5->id,
            'device_brand' => 'Samsung',
            'device_model' => 'Galaxy A54',
            'problem_description' => 'Battery replacement needed',
            'estimated_cost' => 1500,
            'service_charge' => 200,
            'technician_id' => $techUser->id,
            'status' => 'closed',
            'is_locked' => true,
            'completed_at' => now()->subDays(5),
            'closed_at' => now()->subDays(3),
            'record_type' => 'original',
        ]);
        RepairStatusHistory::create(['repair_id' => $rep5->id, 'status' => 'received', 'notes' => 'Device received', 'updated_by' => 1]);
        RepairStatusHistory::create(['repair_id' => $rep5->id, 'status' => 'in_progress', 'notes' => 'Battery swap', 'updated_by' => $techUser->id]);
        RepairStatusHistory::create(['repair_id' => $rep5->id, 'status' => 'completed', 'notes' => 'Done', 'updated_by' => $techUser->id]);
        RepairStatusHistory::create(['repair_id' => $rep5->id, 'status' => 'payment', 'notes' => 'Payment collected', 'updated_by' => 1]);
        RepairStatusHistory::create(['repair_id' => $rep5->id, 'status' => 'closed', 'notes' => 'Delivered to customer', 'updated_by' => 1]);
        RepairPart::create(['repair_id' => $rep5->id, 'part_id' => $partBattery->id, 'quantity' => 1, 'cost_price' => 450]);
        RepairPayment::create(['repair_id' => $rep5->id, 'payment_type' => 'advance', 'amount' => 500, 'payment_method' => 'cash', 'direction' => 'IN']);
        RepairPayment::create(['repair_id' => $rep5->id, 'payment_type' => 'final', 'amount' => 150, 'payment_method' => 'upi', 'direction' => 'IN']);

        // Repair 6: Cancelled (with refund)
        $rep6 = Repair::create([
            'ticket_number' => 'RPR-000006',
            'tracking_id' => 'TRK-V1W2X3Y4',
            'customer_id' => $cust1->id,
            'device_brand' => 'Vivo',
            'device_model' => 'Y16',
            'problem_description' => 'Speaker not working',
            'estimated_cost' => 800,
            'status' => 'cancelled',
            'cancel_reason' => 'Customer decided to buy a new phone instead',
            'record_type' => 'original',
        ]);
        RepairStatusHistory::create(['repair_id' => $rep6->id, 'status' => 'received', 'notes' => 'Device received', 'updated_by' => 1]);
        RepairStatusHistory::create(['repair_id' => $rep6->id, 'status' => 'cancelled', 'notes' => 'Cancelled with refund of ₹200', 'updated_by' => 1]);
        RepairPayment::create(['repair_id' => $rep6->id, 'payment_type' => 'advance', 'amount' => 200, 'payment_method' => 'cash', 'direction' => 'IN']);
        RepairPayment::create(['repair_id' => $rep6->id, 'payment_type' => 'refund', 'amount' => 200, 'payment_method' => 'cash', 'direction' => 'OUT', 'notes' => 'Refund on cancellation']);

        // === RECHARGES ===
        Recharge::create(['customer_id' => $cust1->id, 'provider_id' => 1, 'mobile_number' => '9876500001', 'plan_name' => 'Jio 299 Plan', 'recharge_amount' => 299, 'commission' => 8.97, 'payment_method' => 'cash', 'status' => 'success']);
        Recharge::create(['customer_id' => $cust5->id, 'provider_id' => 2, 'mobile_number' => '9876500005', 'plan_name' => 'Airtel 199 Plan', 'recharge_amount' => 199, 'commission' => 5.97, 'payment_method' => 'upi', 'status' => 'success']);

        // === SERVICES ===
        Service::create(['service_type_id' => 1, 'customer_id' => $cust2->id, 'description' => 'Screen protector application', 'vendor_cost' => 0, 'customer_charge' => 200, 'profit' => 200, 'status' => 'completed']);

        // === EXPENSE CATEGORIES ===
        $expCat1 = ExpenseCategory::create(['name' => 'Rent', 'description' => 'Shop rent']);
        $expCat2 = ExpenseCategory::create(['name' => 'Utilities', 'description' => 'Electricity, water, internet']);
        $expCat3 = ExpenseCategory::create(['name' => 'Salary', 'description' => 'Employee salaries']);
        $expCat4 = ExpenseCategory::create(['name' => 'Miscellaneous', 'description' => 'Other expenses']);

        // === EXPENSES ===
        Expense::create(['category_id' => $expCat1->id, 'amount' => 25000, 'description' => 'Monthly shop rent', 'expense_date' => now()->startOfMonth(), 'created_by' => 1]);
        Expense::create(['category_id' => $expCat2->id, 'amount' => 3500, 'description' => 'Electricity bill', 'expense_date' => now()->subDays(5), 'created_by' => 1]);
        Expense::create(['category_id' => $expCat3->id, 'amount' => 15000, 'description' => 'Technician salary', 'expense_date' => now()->startOfMonth(), 'created_by' => 1]);

        // === LEDGER ===
        LedgerTransaction::create(['transaction_type' => 'sale', 'reference_module' => 'invoices', 'reference_id' => $inv1->id, 'amount' => 13998, 'payment_method' => 'split', 'direction' => 'IN', 'description' => 'Invoice INV-000001', 'created_by' => 1]);
        LedgerTransaction::create(['transaction_type' => 'sale', 'reference_module' => 'invoices', 'reference_id' => $inv2->id, 'amount' => 1799, 'payment_method' => 'card', 'direction' => 'IN', 'description' => 'Invoice INV-000002', 'created_by' => 1]);
        LedgerTransaction::create(['transaction_type' => 'purchase', 'reference_module' => 'purchases', 'reference_id' => $pur1->id, 'amount' => 85000, 'payment_method' => 'bank', 'direction' => 'OUT', 'description' => 'Purchase SUP-INV-001', 'created_by' => 1]);
        LedgerTransaction::create(['transaction_type' => 'expense', 'reference_module' => 'expenses', 'reference_id' => 1, 'amount' => 25000, 'payment_method' => 'bank', 'direction' => 'OUT', 'description' => 'Shop rent', 'created_by' => 1]);
        LedgerTransaction::create(['transaction_type' => 'repair', 'reference_module' => 'repairs', 'reference_id' => $rep1->id, 'amount' => 1500, 'payment_method' => 'cash', 'direction' => 'IN', 'description' => 'Repair advance RPR-000001', 'created_by' => 1]);
        LedgerTransaction::create(['transaction_type' => 'repair', 'reference_module' => 'repairs', 'reference_id' => $rep5->id, 'amount' => 650, 'payment_method' => 'split', 'direction' => 'IN', 'description' => 'Repair payment RPR-000005', 'created_by' => 1]);

        // === SETTINGS ===
        Setting::setValue('shop_name', 'RepairBox Mobile Shop');
        Setting::setValue('shop_phone', '+91 98765 43210');
        Setting::setValue('shop_email', 'info@repairbox.com');
        Setting::setValue('shop_address', '123, Tech Market, MG Road, Delhi - 110001');
        Setting::setValue('currency', 'INR');
        Setting::setValue('currency_symbol', '₹');
        Setting::setValue('tax_rate', '18');
        Setting::setValue('invoice_prefix', 'INV-');
        Setting::setValue('repair_prefix', 'RPR-');
        Setting::setValue('tracking_prefix', 'TRK-');

        // === EMAIL TEMPLATES ===
        EmailTemplate::create(['template_name' => 'Invoice Created', 'subject' => 'Your Invoice #{invoice_number}', 'body' => 'Dear {customer_name}, your invoice #{invoice_number} of {amount} has been generated. Thank you for your purchase!']);
        EmailTemplate::create(['template_name' => 'Repair Status Update', 'subject' => 'Repair Update - {ticket_number}', 'body' => 'Dear {customer_name}, your repair ticket {ticket_number} status has been updated to: {status}. Tracking ID: {tracking_id}']);
        EmailTemplate::create(['template_name' => 'Repair Completed', 'subject' => 'Repair Completed - {ticket_number}', 'body' => 'Dear {customer_name}, your device repair ({ticket_number}) is completed. Please collect from our shop.']);

        // === ACTIVITY LOGS ===
        ActivityLog::create(['user_id' => 1, 'action' => 'login', 'module' => 'auth', 'description' => 'Admin logged in']);
        ActivityLog::create(['user_id' => 1, 'action' => 'create', 'module' => 'invoices', 'reference_id' => $inv1->id, 'description' => 'Created invoice INV-000001']);
        ActivityLog::create(['user_id' => 1, 'action' => 'create', 'module' => 'repairs', 'reference_id' => $rep1->id, 'description' => 'Created repair ticket RPR-000001']);
    }
}
