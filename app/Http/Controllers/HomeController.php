<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\ServiceType;
use Carbon\Carbon;
use Illuminate\Http\Response;

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
        $shopFavicon = Setting::getValue('shop_favicon', '');
        $shopWhatsapp  = Setting::getValue('shop_whatsapp', $shopPhone);
        $shopOpenDays  = Setting::getValue('shop_open_days', '');
        $shopOpenTime  = Setting::getValue('shop_open_time', '');
        $shopCloseTime = Setting::getValue('shop_close_time', '');
        $shopHoliday   = Setting::getValue('shop_holiday', '');

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
            'shopSlogan', 'shopIcon', 'shopFavicon', 'shopWhatsapp', 'services', 'landing',
            'shopOpenDays', 'shopOpenTime', 'shopCloseTime', 'shopHoliday'
        ));
    }

    public function sitemap(): Response
    {
        $baseUrl = rtrim(config('app.url', url('/')), '/');
        $lastmod = Carbon::now()->toDateString();

        $urls = [
            ['loc' => $baseUrl . '/',       'priority' => '1.0', 'changefreq' => 'weekly'],
            ['loc' => $baseUrl . '/track',  'priority' => '0.8', 'changefreq' => 'monthly'],
        ];

        // Add services anchor if any service types exist
        try {
            $hasServices = ServiceType::orderBy('sort_order')->exists();
            if ($hasServices) {
                $urls[] = [
                    'loc'        => $baseUrl . '/#services',
                    'priority'   => '0.7',
                    'changefreq' => 'monthly',
                ];
            }
        } catch (\Throwable) {
            // Table or column may not exist yet; silently skip
        }

        // Deduplicate
        $seen = [];
        $urls = array_filter($urls, function ($u) use (&$seen) {
            if (isset($seen[$u['loc']])) return false;
            return $seen[$u['loc']] = true;
        });

        $xml  = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="https://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        foreach ($urls as $url) {
            $xml .= "  <url>\n";
            $xml .= "    <loc>" . htmlspecialchars($url['loc']) . "</loc>\n";
            $xml .= "    <lastmod>{$lastmod}</lastmod>\n";
            $xml .= "    <changefreq>{$url['changefreq']}</changefreq>\n";
            $xml .= "    <priority>{$url['priority']}</priority>\n";
            $xml .= "  </url>\n";
        }
        $xml .= '</urlset>';

        return response($xml, 200, [
            'Content-Type' => 'application/xml; charset=UTF-8',
            'X-Robots-Tag' => 'noindex',
        ]);
    }

    public function robots(): Response
    {
        $sitemapUrl = rtrim(config('app.url', url('/')), '/') . '/sitemap.xml';

        $content = "User-agent: *\n";
        $content .= "Allow: /\n";
        $content .= "Disallow: /admin/\n";
        $content .= "Disallow: /login\n";
        $content .= "Disallow: /setup/\n";
        $content .= "Disallow: /api/\n";
        $content .= "\n";
        $content .= "# Admin bots\n";
        $content .= "User-agent: AhrefsBot\n";
        $content .= "Crawl-delay: 10\n";
        $content .= "\n";
        $content .= "User-agent: SemrushBot\n";
        $content .= "Crawl-delay: 10\n";
        $content .= "\n";
        $content .= "Sitemap: {$sitemapUrl}\n";

        return response($content, 200, [
            'Content-Type' => 'text/plain; charset=UTF-8',
        ]);
    }
}
