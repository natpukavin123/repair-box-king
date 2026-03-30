<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\ServiceType;

class HomeController extends Controller
{
    public function index()
    {
        $shopName    = Setting::getValue('shop_name', 'RepairBox');
        $shopPhone   = Setting::getValue('shop_phone', '');
        $shopEmail   = Setting::getValue('shop_email', '');
        $shopAddress = Setting::getValue('shop_address', '');
        $shopSlogan  = Setting::getValue('shop_slogan', 'Your Trusted Mobile Partner');
        $shopIcon    = Setting::getValue('shop_icon', '');
        $shopWhatsapp = Setting::getValue('shop_whatsapp', $shopPhone);

        // Load service types for the services section (up to 8)
        $services = [];
        try {
            $services = ServiceType::orderBy('sort_order')->limit(8)->get();
        } catch (\Throwable) {
            // Table may not exist yet; silently skip
        }

        // Load landing page customizable content
        $landingJson = Setting::getValue('landing_page', '{}');
        $landing = json_decode($landingJson, true) ?: [];

        return view('public.home', compact(
            'shopName', 'shopPhone', 'shopEmail', 'shopAddress',
            'shopSlogan', 'shopIcon', 'shopWhatsapp', 'services', 'landing'
        ));
    }
}
