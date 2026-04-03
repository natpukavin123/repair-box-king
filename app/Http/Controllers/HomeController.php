<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\ServiceType;
use App\Models\BlogPost;
use App\Models\Faq;
use App\Models\FaqCategory;
use App\Models\SeoPage;
use Carbon\Carbon;
use Illuminate\Http\Response;

class HomeController extends Controller
{
    public const SERVICE_PAGES = [
        'screen-replacement' => [
            'title'       => 'Screen Replacement',
            'seoTitle'    => 'Screen Replacement in {city} | Same-Day Mobile Screen Repair',
            'seoDesc'     => 'Professional mobile screen replacement in {city}, Tamil Nadu. Same-day service for iPhone, Samsung, OnePlus, Vivo, Oppo & all brands. Genuine screens with warranty at kinginternet.',
            'keywords'    => 'screen replacement {city}, mobile screen repair {city}, phone screen fix {city}, broken screen repair Tamil Nadu, display replacement {city}',
            'content'     => '<h2>Professional Screen Replacement in {city}</h2><p>Cracked or broken screen? Get your phone screen replaced in under 60 minutes at kinginternet, {city}. Serving Avur and Tiruvannamalai District for over 12 years, we use genuine OEM-quality screens for all major brands including Apple iPhone, Samsung Galaxy, OnePlus, Vivo, Oppo, Realme, and Xiaomi.</p><h2>Why Choose kinginternet for Screen Replacement?</h2><ul><li>12+ years of trusted service in {city}, Tamil Nadu</li><li>Same-day screen replacement — most repairs done in 30–60 minutes</li><li>Genuine OEM-quality LCD and AMOLED screens</li><li>Warranty on all screen replacements</li><li>Transparent pricing with no hidden charges</li><li>Experienced technicians trusted by thousands of customers</li></ul><h2>Brands We Service</h2><p>We replace screens for all major smartphone brands: Apple iPhone (all models from iPhone 7 to iPhone 16), Samsung Galaxy S and A series, OnePlus, Vivo, Oppo, Realme, Xiaomi/Redmi, Motorola, Nokia, Google Pixel, and more.</p><h2>Screen Replacement Process</h2><p>1. Visit us at 37, Main Road, Avur Village, Tiruvannamalai District or contact us on WhatsApp.<br>2. We diagnose the issue and provide an instant quote.<br>3. Screen is replaced using professional tools in a dust-free environment.<br>4. Quality check and testing before handover.<br>5. You walk out with a perfectly working device!</p>',
            'faqs' => [
                ['q' => 'How long does screen replacement take in {city}?', 'a' => 'Most screen replacements are completed within 30–60 minutes at kinginternet. For rare models, it may take up to 2 hours if the screen needs to be ordered.'],
                ['q' => 'Do you use original screens?', 'a' => 'We use genuine OEM-quality screens that match the original display quality, brightness, and touch sensitivity. 12+ years of trusted service guarantees quality.'],
                ['q' => 'What is the cost of screen replacement in {city}?', 'a' => 'Prices vary by model. Basic screen replacements start from ₹500 for budget phones. iPhone and Samsung flagship screens start from ₹2,500. Contact us for an exact quote.'],
                ['q' => 'Is there a warranty on screen replacement?', 'a' => 'Yes, all screen replacements at kinginternet come with a warranty covering manufacturing defects in the replacement screen.'],
            ],
        ],
        'battery-replacement' => [
            'title'       => 'Battery Replacement',
            'seoTitle'    => 'Battery Replacement in {city} | Mobile Battery Repair Service',
            'seoDesc'     => 'Fast mobile battery replacement in {city}, Tamil Nadu. Fix battery drain, swollen battery & charging issues for iPhone, Samsung & all brands. Genuine batteries with warranty at kinginternet.',
            'keywords'    => 'battery replacement {city}, mobile battery repair {city}, phone battery change {city}, battery drain fix Tamil Nadu, swollen battery repair',
            'content'     => '<h2>Mobile Battery Replacement in {city}</h2><p>Is your phone battery draining too fast, swelling, or not holding charge? Get a genuine battery replacement at kinginternet, {city}. With 12+ years of trusted service in Tiruvannamalai District, we stock batteries for all major brands and most replacements are done within 30 minutes.</p><h2>Signs You Need a Battery Replacement</h2><ul><li>Battery drains within a few hours even with minimal use</li><li>Phone shuts down randomly at 20-30% battery</li><li>Battery is visibly swollen or bulging</li><li>Phone gets excessively hot during charging</li><li>Charging takes much longer than usual</li></ul><h2>Our Battery Replacement Service</h2><p>We use genuine OEM-quality batteries that match your phone\'s original specifications. Every battery is tested for capacity, voltage, and safety before installation. Our technicians ensure proper calibration after replacement for optimal battery life.</p><h2>Visit Us</h2><p>Located at 37, Main Road, Avur Village, Tiruvannamalai District, Tamil Nadu — 606755. Walk in or message us on WhatsApp for instant support.</p>',
            'faqs' => [
                ['q' => 'How much does battery replacement cost in {city}?', 'a' => 'Battery replacement starts from ₹400 for budget phones. iPhone batteries start from ₹1,500 and Samsung flagships from ₹1,200. Contact kinginternet for your exact model.'],
                ['q' => 'How long does battery replacement take?', 'a' => 'Most battery replacements are completed in 20–30 minutes. You can wait at our shop in {city} while we replace it.'],
                ['q' => 'Will replacing the battery erase my data?', 'a' => 'No, battery replacement does not affect your data. All your photos, apps, and settings remain intact.'],
                ['q' => 'Do you provide warranty on new batteries?', 'a' => 'Yes, all replacement batteries at kinginternet come with a warranty covering defects and performance issues.'],
            ],
        ],
        'iphone-repair' => [
            'title'       => 'iPhone Repair',
            'seoTitle'    => 'iPhone Repair in {city} | Apple iPhone Screen, Battery & Board Repair',
            'seoDesc'     => 'Expert iPhone repair service in {city}, Tamil Nadu. Screen replacement, battery change, charging port fix, water damage repair for all iPhone models. 12+ years trusted service at kinginternet.',
            'keywords'    => 'iPhone repair {city}, Apple repair {city}, iPhone screen replacement {city}, iPhone battery replacement Tamil Nadu, iPhone water damage repair {city}',
            'content'     => '<h2>Expert iPhone Repair in {city}</h2><p>Looking for reliable iPhone repair in {city}? kinginternet has been the trusted name for mobile repairs in Tiruvannamalai District for over 12 years. Our experienced technicians handle screen replacements, battery changes, charging port repairs, camera fixes, and motherboard-level repairs for all iPhone models.</p><h2>iPhone Services We Offer</h2><ul><li>iPhone Screen Replacement (LCD & OLED)</li><li>iPhone Battery Replacement</li><li>Charging Port / Lightning / USB-C Repair</li><li>Water Damage Diagnosis & Repair</li><li>Camera Module Replacement</li><li>Speaker & Microphone Repair</li><li>Software Issues & iOS Troubleshooting</li><li>Back Glass Replacement</li></ul><h2>All iPhone Models Supported</h2><p>We service every iPhone model: iPhone 7, 7 Plus, 8, 8 Plus, X, XR, XS, XS Max, 11, 11 Pro, 11 Pro Max, 12 Mini, 12, 12 Pro, 12 Pro Max, 13 Mini, 13, 13 Pro, 13 Pro Max, 14, 14 Plus, 14 Pro, 14 Pro Max, 15, 15 Plus, 15 Pro, 15 Pro Max, 16, 16 Plus, 16 Pro, and 16 Pro Max.</p><h2>Visit Us</h2><p>Located at 37, Main Road, Avur Village, Tiruvannamalai District, Tamil Nadu — 606755. Over 12 years of trusted service.</p>',
            'faqs' => [
                ['q' => 'Where can I get my iPhone repaired in {city}?', 'a' => 'Visit kinginternet at 37, Main Road, Avur Village, Tiruvannamalai District for expert iPhone repair. We handle all models and all types of issues including screen, battery, charging, and water damage.'],
                ['q' => 'How much does iPhone screen replacement cost in {city}?', 'a' => 'iPhone screen replacement costs vary by model. Older models start from ₹2,500 while newer Pro Max models can go up to ₹15,000+. Contact us for an exact quote for your model.'],
                ['q' => 'Do you use genuine Apple parts?', 'a' => 'We use premium OEM-quality parts that match Apple\'s original specifications for display quality, touch response, and durability.'],
                ['q' => 'Can you fix water-damaged iPhones?', 'a' => 'Yes, we offer water damage diagnosis and repair at kinginternet. Success depends on the extent of damage. We provide honest assessment and only charge if the repair is successful.'],
            ],
        ],
    ];

    public function index()
    {
        $shopName    = Setting::getValue('shop_name', 'kinginternet');
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

        // Add SEO landing pages
        foreach (array_keys(self::SERVICE_PAGES) as $slug) {
            $urls[] = [
                'loc'        => $baseUrl . '/' . $slug,
                'priority'   => '0.9',
                'changefreq' => 'monthly',
            ];
        }

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

        // Add blog posts
        try {
            $blogPosts = BlogPost::published()->orderByDesc('published_at')->get();
            if ($blogPosts->isNotEmpty()) {
                $urls[] = [
                    'loc'        => $baseUrl . '/blog',
                    'priority'   => '0.8',
                    'changefreq' => 'weekly',
                ];
                foreach ($blogPosts as $post) {
                    $urls[] = [
                        'loc'        => $baseUrl . '/blog/' . $post->slug,
                        'priority'   => '0.7',
                        'changefreq' => 'monthly',
                        'lastmod'    => $post->updated_at->toDateString(),
                    ];
                }
            }
        } catch (\Throwable) {}

        // Add FAQ page
        try {
            if (Faq::active()->exists()) {
                $urls[] = [
                    'loc'        => $baseUrl . '/faq',
                    'priority'   => '0.7',
                    'changefreq' => 'monthly',
                ];
            }
        } catch (\Throwable) {}

        // Add dynamic SEO pages
        try {
            $seoPages = SeoPage::published()->orderBy('sort_order')->get();
            foreach ($seoPages as $seoPage) {
                $urls[] = [
                    'loc'        => $baseUrl . '/page/' . $seoPage->slug,
                    'priority'   => '0.8',
                    'changefreq' => 'weekly',
                    'lastmod'    => $seoPage->updated_at->toDateString(),
                ];
            }
        } catch (\Throwable) {}

        // Deduplicate
        $seen = [];
        $urls = array_filter($urls, function ($u) use (&$seen) {
            if (isset($seen[$u['loc']])) return false;
            return $seen[$u['loc']] = true;
        });

        $xml  = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="https://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        foreach ($urls as $url) {
            $urlLastmod = $url['lastmod'] ?? $lastmod;
            $xml .= "  <url>\n";
            $xml .= "    <loc>" . htmlspecialchars($url['loc']) . "</loc>\n";
            $xml .= "    <lastmod>{$urlLastmod}</lastmod>\n";
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

        // Append custom robots rules from SEO settings
        $customRobots = Setting::getValue('seo_robots_custom', '');
        if ($customRobots) {
            $content .= "# Custom rules\n";
            $content .= $customRobots . "\n\n";
        }

        $content .= "Sitemap: {$sitemapUrl}\n";

        return response($content, 200, [
            'Content-Type' => 'text/plain; charset=UTF-8',
        ]);
    }

    public function serviceLanding(string $slug)
    {
        $page = self::SERVICE_PAGES[$slug] ?? null;
        if (!$page) {
            abort(404);
        }

        $shopName    = Setting::getValue('shop_name', 'kinginternet');
        $shopPhone   = Setting::getValue('shop_phone', '');
        $shopEmail   = Setting::getValue('shop_email', '');
        $shopAddress = Setting::getValue('shop_address', '');
        $shopIcon    = Setting::getValue('shop_icon', '');
        $shopFavicon = Setting::getValue('shop_favicon', '');
        $shopWhatsapp = Setting::getValue('shop_whatsapp', $shopPhone);

        $landingJson = Setting::getValue('landing_page', '{}');
        $landing     = json_decode($landingJson, true) ?: [];
        $cityName    = ($landing['city'] ?? '') ?: 'Tiruvannamalai';

        return view('public.service-landing', [
            'slug'           => $slug,
            'serviceTitle'   => $page['title'],
            'seoTitle'       => str_replace('{city}', $cityName, $page['seoTitle']) . ' | ' . $shopName,
            'seoDescription' => str_replace('{city}', $cityName, $page['seoDesc']),
            'seoKeywords'    => str_replace('{city}', $cityName, $page['keywords']),
            'contentHtml'    => str_replace('{city}', $cityName, $page['content']),
            'faqs'           => array_map(fn($f) => [
                'q' => str_replace('{city}', $cityName, $f['q']),
                'a' => str_replace('{city}', $cityName, $f['a']),
            ], $page['faqs']),
            'cityName'       => $cityName,
            'shopName'       => $shopName,
            'shopPhone'      => $shopPhone,
            'shopEmail'      => $shopEmail,
            'shopAddress'    => $shopAddress,
            'shopIcon'       => $shopIcon,
            'shopFavicon'    => $shopFavicon,
            'shopWhatsapp'   => $shopWhatsapp,
        ]);
    }

    // ─── Public Blog ─────────────────────────────────────────────────────────
    public function blogIndex()
    {
        $posts = BlogPost::published()
            ->orderByDesc('published_at')
            ->paginate(12);

        $shopName    = Setting::getValue('shop_name', 'kinginternet');
        $shopIcon    = Setting::getValue('shop_icon', '');
        $shopFavicon = Setting::getValue('shop_favicon', '');
        $shopPhone   = Setting::getValue('shop_phone', '');
        $shopWhatsapp = Setting::getValue('shop_whatsapp', $shopPhone);
        $titleSuffix = Setting::getValue('seo_global_title_suffix', $shopName);

        return view('public.blog-index', compact('posts', 'shopName', 'shopIcon', 'shopFavicon', 'shopWhatsapp', 'titleSuffix'));
    }

    public function blogShow(string $slug)
    {
        $post = BlogPost::published()->where('slug', $slug)->firstOrFail();
        $post->load('author:id,name');

        $related = BlogPost::published()
            ->where('id', '!=', $post->id)
            ->orderByDesc('published_at')
            ->limit(3)
            ->get();

        $shopName    = Setting::getValue('shop_name', 'kinginternet');
        $shopIcon    = Setting::getValue('shop_icon', '');
        $shopFavicon = Setting::getValue('shop_favicon', '');
        $shopPhone   = Setting::getValue('shop_phone', '');
        $shopWhatsapp = Setting::getValue('shop_whatsapp', $shopPhone);

        return view('public.blog-show', compact('post', 'related', 'shopName', 'shopIcon', 'shopFavicon', 'shopWhatsapp'));
    }

    // ─── Public FAQ ──────────────────────────────────────────────────────────
    public function faqPage()
    {
        $categories = FaqCategory::active()
            ->with(['faqs' => fn($q) => $q->active()->orderBy('sort_order')])
            ->orderBy('sort_order')
            ->get();

        $uncategorized = Faq::active()
            ->whereNull('faq_category_id')
            ->orderBy('sort_order')
            ->get();

        $shopName    = Setting::getValue('shop_name', 'kinginternet');
        $shopIcon    = Setting::getValue('shop_icon', '');
        $shopFavicon = Setting::getValue('shop_favicon', '');
        $shopPhone   = Setting::getValue('shop_phone', '');
        $shopWhatsapp = Setting::getValue('shop_whatsapp', $shopPhone);
        $titleSuffix = Setting::getValue('seo_global_title_suffix', $shopName);

        return view('public.faq', compact('categories', 'uncategorized', 'shopName', 'shopIcon', 'shopFavicon', 'shopWhatsapp', 'titleSuffix'));
    }

    // ─── Dynamic SEO Pages ───────────────────────────────────────────────────
    public function dynamicPage(string $slug)
    {
        $page = SeoPage::published()->where('slug', $slug)->firstOrFail();

        $faqs = Faq::active()->forPage($slug)->orderBy('sort_order')->get();

        $shopName    = Setting::getValue('shop_name', 'kinginternet');
        $shopPhone   = Setting::getValue('shop_phone', '');
        $shopEmail   = Setting::getValue('shop_email', '');
        $shopAddress = Setting::getValue('shop_address', '');
        $shopIcon    = Setting::getValue('shop_icon', '');
        $shopFavicon = Setting::getValue('shop_favicon', '');
        $shopWhatsapp = Setting::getValue('shop_whatsapp', $shopPhone);

        $landingJson = Setting::getValue('landing_page', '{}');
        $landing     = json_decode($landingJson, true) ?: [];
        $cityName    = ($landing['city'] ?? '') ?: 'Tiruvannamalai';

        return view('public.dynamic-page', compact(
            'page', 'faqs', 'shopName', 'shopPhone', 'shopEmail', 'shopAddress',
            'shopIcon', 'shopFavicon', 'shopWhatsapp', 'cityName'
        ));
    }
}
