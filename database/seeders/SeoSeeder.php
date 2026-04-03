<?php

namespace Database\Seeders;

use App\Models\BlogPost;
use App\Models\Faq;
use App\Models\FaqCategory;
use App\Models\SeoPage;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;

class SeoSeeder extends Seeder
{
    public function run(): void
    {
        $authorId = User::first()?->id ?? 1;

        // ─── Blog Posts ────────────────────────────────────────────
        $blogs = [
            [
                'title' => '10 Signs Your Phone Screen Needs Replacement',
                'slug' => '10-signs-phone-screen-needs-replacement',
                'excerpt' => 'Not sure if your cracked screen warrants a full replacement? Here are the top 10 warning signs every smartphone owner should know.',
                'content' => '<h2>1. Spider-Web Cracks Across the Display</h2>
<p>Even if your phone still works, spider-web cracks weaken the structural integrity of the glass and can cut your fingers. Over time, moisture and dust seep through microscopic gaps, damaging the digitizer beneath.</p>

<h2>2. Unresponsive Touch Zones</h2>
<p>If certain areas of your screen no longer register taps, the digitizer layer has likely been damaged by impact. This is a clear signal that a screen replacement is necessary rather than a simple repair.</p>

<h2>3. Visible LCD Bleeding or Black Spots</h2>
<p>Dark patches or colour bleeding indicate internal LCD damage. These spots typically grow larger over time and cannot be fixed without a full display assembly replacement.</p>

<h2>4. Ghost Touches and Phantom Inputs</h2>
<p>When your phone starts opening apps or typing on its own, the touch layer is sending erratic signals. This can lead to unintended calls, messages, or even factory resets if left unchecked.</p>

<h2>5. Screen Flickering or Lines</h2>
<p>Horizontal or vertical lines across the display, or intermittent flickering, point to a failing connection between the screen and the motherboard. A replacement screen resolves this in most cases.</p>

<h2>6. Brightness Irregularities</h2>
<p>If one half of your screen appears brighter or dimmer than the other, the backlight panel or OLED layer is damaged. Adjusting brightness settings won\'t help — the hardware needs replacing.</p>

<h2>7. Burn-In on OLED Screens</h2>
<p>Persistent ghost images of navigation bars or keyboards are signs of pixel degradation. While mild burn-in is cosmetic, severe cases affect readability and usability.</p>

<h2>8. Slow or Laggy Touch Response</h2>
<p>A noticeable delay between touching the screen and the phone responding can indicate digitizer fatigue, especially after a drop or pressure damage.</p>

<h2>9. Lifting or Separating Glass</h2>
<p>If the glass layer is lifting away from the frame, the adhesive seal is broken. This exposes internal components to dust, moisture, and further damage.</p>

<h2>10. You\'ve Already Repaired It Multiple Times</h2>
<p>Repeated DIY fixes with screen protectors and UV glue only mask the problem. If you\'ve patched it more than twice, a professional replacement is the most cost-effective long-term solution.</p>

<h2>When to Visit a Professional</h2>
<p>If you notice any two or more of these signs, we recommend bringing your device to our repair centre for a free diagnostic. Our technicians use OEM-quality screens and offer a warranty on every replacement.</p>',
                'meta_title' => '10 Signs Your Phone Screen Needs Replacement | Expert Guide',
                'meta_description' => 'Discover the top 10 warning signs that indicate your phone screen needs professional replacement. From ghost touches to LCD bleeding — know when it\'s time.',
                'meta_keywords' => 'phone screen replacement, cracked screen signs, broken phone display, screen repair guide',
                'status' => 'published',
                'published_at' => now()->subDays(2),
                'sort_order' => 1,
            ],
            [
                'title' => 'iPhone vs Samsung Battery Replacement: What You Need to Know',
                'slug' => 'iphone-vs-samsung-battery-replacement-guide',
                'excerpt' => 'A comprehensive comparison of battery replacement processes, costs, and what to expect for iPhone and Samsung Galaxy devices.',
                'content' => '<h2>Understanding Battery Degradation</h2>
<p>All lithium-ion batteries lose capacity over time. After roughly 500 full charge cycles, most smartphone batteries retain only 80% of their original capacity. Apple displays battery health in Settings, while Samsung users can check via the Samsung Members app.</p>

<h2>iPhone Battery Replacement</h2>
<p>Apple designs iPhones with adhesive pull-tabs holding the battery in place. While this makes professional replacement straightforward, DIY attempts risk puncturing the battery. Key points:</p>
<ul>
<li><strong>Models supported:</strong> iPhone 8 through iPhone 16 Pro Max</li>
<li><strong>Time required:</strong> 30–45 minutes for a trained technician</li>
<li><strong>Warranty:</strong> We provide a 6-month warranty on all iPhone battery replacements</li>
<li><strong>Data safety:</strong> Your data remains untouched during the process</li>
</ul>

<h2>Samsung Galaxy Battery Replacement</h2>
<p>Samsung phones use strong adhesive and glass backs, requiring heat guns to safely open. The process is slightly more involved:</p>
<ul>
<li><strong>Models supported:</strong> Galaxy S20 series through S24 Ultra, A-series, and Note series</li>
<li><strong>Time required:</strong> 45–60 minutes due to the glass-back disassembly</li>
<li><strong>Water resistance:</strong> We re-seal the device to maintain its IP68 rating</li>
<li><strong>Quality:</strong> Only genuine-grade cells with proper BMS chips</li>
</ul>

<h2>Cost Comparison</h2>
<p>iPhone battery replacements typically range from ₹1,500 to ₹4,000 depending on the model. Samsung replacements range from ₹1,200 to ₹3,500. Flagship models with larger cells cost more due to component pricing.</p>

<h2>Signs You Need a Battery Replacement</h2>
<p>Common indicators include: draining from 100% to 0% in under 4 hours of screen time, unexpected shutdowns at 20-30% charge, the phone becoming excessively hot during normal use, or visible battery swelling (a safety hazard that requires immediate attention).</p>

<h2>Book Your Battery Replacement</h2>
<p>Visit our shop or book online for a quick diagnostic. We\'ll test your battery health and give you an honest assessment — no upselling, no hidden charges.</p>',
                'meta_title' => 'iPhone vs Samsung Battery Replacement Guide | Costs & Process',
                'meta_description' => 'Compare iPhone and Samsung battery replacement processes, costs, and timelines. Expert guide from certified repair technicians.',
                'meta_keywords' => 'iPhone battery replacement, Samsung battery replacement, phone battery cost, battery health',
                'status' => 'published',
                'published_at' => now()->subDays(5),
                'sort_order' => 2,
            ],
            [
                'title' => 'How to Protect Your Phone After a Screen Repair',
                'slug' => 'protect-phone-after-screen-repair',
                'excerpt' => 'Just got your screen replaced? Follow these essential tips to keep your new display in perfect condition and avoid another costly repair.',
                'content' => '<h2>Invest in a Quality Screen Protector</h2>
<p>A tempered glass screen protector is the single best investment after a screen repair. Look for 9H hardness rating and oleophobic coating. We recommend applying it within 24 hours of your repair to protect the fresh display.</p>

<h2>Choose the Right Case</h2>
<p>Not all cases are created equal. For maximum drop protection, consider cases with:</p>
<ul>
<li><strong>Raised bezels:</strong> At least 1.5mm lip above the screen to prevent face-down contact</li>
<li><strong>Corner air cushions:</strong> Absorb impact energy at the most vulnerable points</li>
<li><strong>MIL-STD-810G certification:</strong> Tested to survive drops from 4 feet onto concrete</li>
</ul>

<h2>Handle with Care for the First 48 Hours</h2>
<p>The adhesive used to bond the new screen needs time to fully cure. Avoid exposing your phone to extreme heat, water, or heavy pressure during the first two days after repair.</p>

<h2>Enable Auto-Lock and Face-Down Detection</h2>
<p>Reduce unnecessary screen-on time by setting a shorter auto-lock timer. Many phones also detect when placed face-down and turn off the display to prevent accidental touches and reduce wear.</p>

<h2>Regular Cleaning</h2>
<p>Use a microfiber cloth slightly dampened with screen-safe cleaner. Avoid household glass cleaners — they contain ammonia that degrades oleophobic coatings over time.</p>

<h2>Visit Us for Free Check-Ups</h2>
<p>We offer complimentary screen health check-ups for customers who\'ve had repairs done with us. Drop by any time and we\'ll inspect the display, adhesion, and touch responsiveness.</p>',
                'meta_title' => 'How to Protect Your Phone After Screen Repair | Pro Tips',
                'meta_description' => 'Essential tips to protect your phone after screen repair. Learn about screen protectors, cases, and care routines from repair experts.',
                'meta_keywords' => 'phone protection after repair, screen protector tips, phone case guide, screen care',
                'status' => 'published',
                'published_at' => now()->subDays(8),
                'sort_order' => 3,
            ],
            [
                'title' => 'Water Damage Repair: Can Your Phone Be Saved?',
                'slug' => 'water-damage-repair-can-phone-be-saved',
                'excerpt' => 'Dropped your phone in water? Don\'t panic — and definitely don\'t put it in rice. Here\'s what actually works and when professional help is needed.',
                'content' => '<h2>The Rice Myth — Debunked</h2>
<p>Putting your phone in rice does <strong>not</strong> effectively dry internal components. In fact, rice dust and starch particles can clog ports and cause additional corrosion. Professional ultrasonic cleaning is far more effective.</p>

<h2>Immediate Steps After Water Exposure</h2>
<ol>
<li><strong>Power off immediately</strong> — Do NOT try to charge it or turn it on to "check if it works"</li>
<li><strong>Remove the SIM card and any cases</strong></li>
<li><strong>Gently shake out excess water</strong> from ports</li>
<li><strong>Bring it to a repair shop within 24 hours</strong> for professional drying and cleaning</li>
</ol>

<h2>What Happens During Professional Water Damage Repair</h2>
<p>Our technicians follow a systematic process:</p>
<ul>
<li>Complete disassembly of all internal components</li>
<li>Ultrasonic bath cleaning with specialized isopropyl solution</li>
<li>Individual component inspection under microscope</li>
<li>Corrosion treatment and contact cleaning</li>
<li>Controlled drying in a dehumidification chamber</li>
<li>Reassembly and full functional testing</li>
</ul>

<h2>Success Rates</h2>
<p>When treated within 24 hours, we achieve a 75-85% success rate for water-damaged phones. After 48 hours, corrosion sets in rapidly and the success rate drops to around 40-50%. The sooner you act, the better.</p>

<h2>Prevention Tips</h2>
<p>Even IP68-rated phones aren\'t immune to water damage — the rating degrades over time. Consider a waterproof pouch for beach trips or pool days, and always check your pockets before laundry day.</p>',
                'meta_title' => 'Water Damage Phone Repair — What Actually Works | Expert Guide',
                'meta_description' => 'Learn what to do (and what NOT to do) when your phone gets water damaged. Professional repair tips and success rates explained.',
                'meta_keywords' => 'water damage phone repair, phone dropped in water, water damaged iPhone, wet phone fix',
                'status' => 'published',
                'published_at' => now()->subDays(12),
                'sort_order' => 4,
            ],
            [
                'title' => '5 Reasons to Choose a Local Repair Shop Over the Manufacturer',
                'slug' => '5-reasons-choose-local-repair-shop',
                'excerpt' => 'Thinking about sending your phone to the manufacturer for repair? Here\'s why a trusted local repair shop might be the smarter choice.',
                'content' => '<h2>1. Faster Turnaround Time</h2>
<p>Manufacturer repairs typically take 7–14 business days between shipping, processing, and return delivery. A local repair shop can handle most repairs same-day — often within an hour while you wait.</p>

<h2>2. Lower Cost</h2>
<p>Without the overhead of corporate service centres and shipping logistics, local shops pass significant savings to customers. Screen replacements can cost 30-50% less than manufacturer pricing while using equivalent quality parts.</p>

<h2>3. Personal Service and Transparency</h2>
<p>At a local shop, you can watch your device being repaired, ask questions, and get honest advice. There\'s no anonymous ticket number — you deal directly with the technician working on your phone.</p>

<h2>4. Data Privacy</h2>
<p>Manufacturers often require you to factory reset your phone before sending it in. Local repair shops never need access to your personal data. Your photos, messages, and apps stay untouched.</p>

<h2>5. Supporting Your Community</h2>
<p>Every rupee spent at a local repair shop stays in your community. You\'re supporting local jobs, local families, and local expertise — not a distant corporate headquarters.</p>

<h2>What About Warranty?</h2>
<p>Reputable local shops offer their own warranties on parts and labour. We offer a 90-day warranty on all repairs and a 6-month warranty on battery replacements. This often matches or exceeds manufacturer repair warranties for out-of-warranty devices.</p>',
                'meta_title' => '5 Reasons to Choose a Local Phone Repair Shop | Save Time & Money',
                'meta_description' => 'Discover why local repair shops offer faster, cheaper, and more transparent phone repair than manufacturer service centres.',
                'meta_keywords' => 'local phone repair, repair shop vs manufacturer, fast phone repair, affordable phone repair',
                'status' => 'published',
                'published_at' => now()->subDays(15),
                'sort_order' => 5,
            ],
            [
                'title' => 'The Complete Guide to Phone Data Backup Before Repair',
                'slug' => 'complete-guide-phone-data-backup-before-repair',
                'excerpt' => 'Backing up your phone before a repair is crucial. This step-by-step guide covers iCloud, Google backup, and manual methods to keep your data safe.',
                'content' => '<h2>Why Backup Before Repair?</h2>
<p>While most repairs don\'t affect your data, certain procedures (motherboard repair, software recovery) may require a factory reset. Having a fresh backup means zero risk of data loss, regardless of what happens.</p>

<h2>iPhone Backup Methods</h2>
<h3>iCloud Backup (Easiest)</h3>
<ol>
<li>Connect to Wi-Fi</li>
<li>Go to Settings → [Your Name] → iCloud → iCloud Backup</li>
<li>Tap "Back Up Now" and wait for completion</li>
</ol>
<h3>iTunes/Finder Backup (Most Complete)</h3>
<ol>
<li>Connect your iPhone to a computer via cable</li>
<li>Open Finder (Mac) or iTunes (Windows)</li>
<li>Select your device and click "Back Up Now"</li>
<li>Check "Encrypt local backup" to include passwords and health data</li>
</ol>

<h2>Android Backup Methods</h2>
<h3>Google Backup</h3>
<ol>
<li>Go to Settings → System → Backup</li>
<li>Ensure "Back up to Google Drive" is enabled</li>
<li>Tap "Back up now"</li>
</ol>
<h3>Samsung Smart Switch</h3>
<p>Samsung users can use Smart Switch to create a complete backup to a computer, including app data that Google Backup may miss.</p>

<h2>Don\'t Forget These</h2>
<ul>
<li><strong>WhatsApp:</strong> Settings → Chats → Chat Backup → Back Up Now</li>
<li><strong>Photos:</strong> Ensure Google Photos or iCloud Photos sync is complete</li>
<li><strong>Authenticator apps:</strong> Export codes or ensure cloud sync is enabled</li>
<li><strong>Notes and contacts:</strong> Verify they\'re syncing to your cloud account</li>
</ul>

<h2>Pro Tip</h2>
<p>Take a screenshot of your home screen layout before the repair. If a reset is needed, you can quickly reorganize your apps exactly as they were.</p>',
                'meta_title' => 'Complete Phone Data Backup Guide Before Repair | iPhone & Android',
                'meta_description' => 'Step-by-step guide to backing up your iPhone or Android phone before repair. Covers iCloud, Google, WhatsApp, photos, and more.',
                'meta_keywords' => 'phone backup before repair, iCloud backup guide, Google backup Android, data backup phone',
                'status' => 'draft',
                'published_at' => null,
                'sort_order' => 6,
            ],
        ];

        foreach ($blogs as $blog) {
            BlogPost::firstOrCreate(['slug' => $blog['slug']], array_merge($blog, ['author_id' => $authorId]));
        }

        // ─── FAQ Categories ───────────────────────────────────────
        $categories = [
            ['name' => 'General', 'slug' => 'general', 'sort_order' => 1, 'is_active' => true],
            ['name' => 'Screen Repair', 'slug' => 'screen-repair', 'sort_order' => 2, 'is_active' => true],
            ['name' => 'Battery', 'slug' => 'battery', 'sort_order' => 3, 'is_active' => true],
            ['name' => 'Pricing & Payment', 'slug' => 'pricing-payment', 'sort_order' => 4, 'is_active' => true],
            ['name' => 'Warranty & Returns', 'slug' => 'warranty-returns', 'sort_order' => 5, 'is_active' => true],
        ];

        $catMap = [];
        foreach ($categories as $cat) {
            $catMap[$cat['slug']] = FaqCategory::firstOrCreate(['slug' => $cat['slug']], $cat)->id;
        }

        // ─── FAQs ─────────────────────────────────────────────────
        $faqs = [
            // General
            ['faq_category_id' => $catMap['general'], 'question' => 'How long does a typical phone repair take?', 'answer' => 'Most repairs are completed within 30–60 minutes. Complex issues like motherboard repair or water damage treatment may take 1–3 business days. We\'ll always give you an estimated time before starting the work.', 'sort_order' => 1, 'is_active' => true],
            ['faq_category_id' => $catMap['general'], 'question' => 'Do I need an appointment or can I walk in?', 'answer' => 'Walk-ins are always welcome! However, booking an appointment online guarantees priority service and ensures we have the right parts ready for your device.', 'sort_order' => 2, 'is_active' => true],
            ['faq_category_id' => $catMap['general'], 'question' => 'Will my data be safe during the repair?', 'answer' => 'Absolutely. We never access, copy, or view your personal data. Most repairs don\'t require unlocking your phone. In rare cases where a factory reset is needed, we\'ll inform you beforehand so you can back up your data.', 'sort_order' => 3, 'is_active' => true],
            ['faq_category_id' => $catMap['general'], 'question' => 'Which phone brands do you repair?', 'answer' => 'We repair all major brands including Apple iPhone, Samsung Galaxy, OnePlus, Xiaomi, Oppo, Vivo, Realme, Google Pixel, and more. If you have a less common brand, give us a call and we\'ll let you know if we can help.', 'sort_order' => 4, 'is_active' => true],

            // Screen Repair
            ['faq_category_id' => $catMap['screen-repair'], 'question' => 'What quality of screen do you use for replacements?', 'answer' => 'We offer multiple options: OEM-grade screens (same quality as manufacturer originals), high-quality aftermarket screens, and budget-friendly alternatives. We\'ll explain the differences and let you choose what fits your budget and expectations.', 'sort_order' => 1, 'is_active' => true],
            ['faq_category_id' => $catMap['screen-repair'], 'question' => 'Can you fix just the glass without replacing the whole screen?', 'answer' => 'For most modern smartphones, the glass and display are fused together. A "glass-only" repair is possible for some older models, but it carries a higher risk of display damage. We\'ll assess your device and recommend the best approach.', 'sort_order' => 2, 'is_active' => true],
            ['faq_category_id' => $catMap['screen-repair'], 'question' => 'Will my phone be waterproof after a screen replacement?', 'answer' => 'We re-apply water-resistant adhesive seals during every screen replacement. While this restores a good level of water resistance, we cannot guarantee the original IP rating, which is standard practice industry-wide.', 'sort_order' => 3, 'is_active' => true],

            // Battery
            ['faq_category_id' => $catMap['battery'], 'question' => 'How do I know if my battery needs replacing?', 'answer' => 'Common signs include: rapid battery drain, phone shutting off unexpectedly at 20-30%, excessive heat during normal use, battery health below 80% (check in Settings for iPhone), or visible swelling of the battery causing the back or screen to lift.', 'sort_order' => 1, 'is_active' => true],
            ['faq_category_id' => $catMap['battery'], 'question' => 'How long does a battery replacement take?', 'answer' => 'Most battery replacements are completed in 30–45 minutes. Samsung devices with glass backs may take up to an hour due to the careful removal process required.', 'sort_order' => 2, 'is_active' => true],
            ['faq_category_id' => $catMap['battery'], 'question' => 'What battery brands and quality do you use?', 'answer' => 'We use genuine-grade battery cells with original-spec capacity and proper BMS (Battery Management System) chips. All our batteries are tested for safety and come with a 6-month warranty.', 'sort_order' => 3, 'is_active' => true],

            // Pricing & Payment
            ['faq_category_id' => $catMap['pricing-payment'], 'question' => 'How much does a screen repair cost?', 'answer' => 'Pricing depends on your phone model and the screen quality you choose. For example, iPhone screen replacements start from ₹2,500 and Samsung from ₹2,000. Contact us with your model for an exact quote — no surprises, no hidden fees.', 'sort_order' => 1, 'is_active' => true],
            ['faq_category_id' => $catMap['pricing-payment'], 'question' => 'Do you offer free diagnostics?', 'answer' => 'Yes! We offer completely free diagnostics for all devices. Bring your phone in, and we\'ll identify the issue and provide a transparent quote before any work begins. If you choose not to proceed, there\'s no charge.', 'sort_order' => 2, 'is_active' => true],
            ['faq_category_id' => $catMap['pricing-payment'], 'question' => 'What payment methods do you accept?', 'answer' => 'We accept cash, all major UPI apps (Google Pay, PhonePe, Paytm), credit/debit cards, and net banking. We also offer EMI options for repairs above ₹5,000 on select cards.', 'sort_order' => 3, 'is_active' => true],

            // Warranty & Returns
            ['faq_category_id' => $catMap['warranty-returns'], 'question' => 'What warranty do you provide on repairs?', 'answer' => 'All repairs come with a 90-day parts and labour warranty. Battery replacements receive a 6-month warranty. If the same issue recurs within the warranty period, we\'ll fix it at no additional cost.', 'sort_order' => 1, 'is_active' => true],
            ['faq_category_id' => $catMap['warranty-returns'], 'question' => 'What does the warranty cover?', 'answer' => 'Our warranty covers defects in the replacement parts and any workmanship issues. It does not cover new physical damage (drops, water exposure) or issues unrelated to the original repair.', 'sort_order' => 2, 'is_active' => true],
            ['faq_category_id' => $catMap['warranty-returns'], 'question' => 'What if I\'m not satisfied with the repair?', 'answer' => 'Customer satisfaction is our priority. If you\'re not happy with the repair quality, bring your device back within 7 days and we\'ll re-examine it. If the issue is related to our work, we\'ll fix it for free or offer a full refund on the repair.', 'sort_order' => 3, 'is_active' => true],
        ];

        foreach ($faqs as $faq) {
            Faq::firstOrCreate(
                ['question' => $faq['question']],
                $faq
            );
        }

        // ─── SEO Pages (Dynamic Landing Pages) ───────────────────
        $seoPages = [
            [
                'title' => 'Mobile Screen Replacement',
                'slug' => 'mobile-screen-replacement',
                'content' => '<h2>Professional Mobile Screen Replacement Service</h2>
<p>Is your phone screen cracked, shattered, or unresponsive? Our expert technicians provide fast and reliable screen replacement services for all major smartphone brands.</p>

<h3>Why Choose Our Screen Replacement Service?</h3>
<ul>
<li><strong>Same-Day Service:</strong> Most screen replacements completed within 30–60 minutes</li>
<li><strong>OEM-Quality Parts:</strong> We use display assemblies that match original manufacturer specifications</li>
<li><strong>90-Day Warranty:</strong> Every screen replacement backed by our comprehensive warranty</li>
<li><strong>Free Diagnostic:</strong> We\'ll assess your device at no charge before any work begins</li>
<li><strong>All Brands Supported:</strong> iPhone, Samsung, OnePlus, Xiaomi, Oppo, Vivo, and more</li>
</ul>

<h3>Our Screen Replacement Process</h3>
<ol>
<li><strong>Inspection:</strong> We examine your device to determine the extent of damage</li>
<li><strong>Quote:</strong> Transparent pricing with no hidden charges — you approve before we start</li>
<li><strong>Repair:</strong> Skilled technicians replace the screen in a dust-free environment</li>
<li><strong>Testing:</strong> Full touch, display, and sensor testing before handover</li>
<li><strong>Handover:</strong> Your phone, good as new, with warranty documentation</li>
</ol>

<h3>Common Screen Issues We Fix</h3>
<p>Cracked glass, LCD/OLED damage, unresponsive touch, white/black screen, display flickering, dead pixels, ghost touches, and screen burn-in. Whatever the issue, we have the solution.</p>

<h3>Visit Us Today</h3>
<p>Walk in for a free assessment or book an appointment online. We\'re committed to getting your phone back to perfect condition at a fair price.</p>',
                'meta_title' => 'Mobile Screen Replacement Service | Same-Day Repair | Quality Guaranteed',
                'meta_description' => 'Professional mobile screen replacement for iPhone, Samsung & all brands. Same-day service, OEM-quality parts, 90-day warranty. Free diagnostic — visit today!',
                'meta_keywords' => 'mobile screen replacement, phone screen repair, cracked screen fix, iPhone screen replacement, Samsung screen repair',
                'schema_type' => 'Service',
                'status' => 'published',
                'sort_order' => 1,
            ],
            [
                'title' => 'Phone Battery Replacement Service',
                'slug' => 'phone-battery-replacement',
                'content' => '<h2>Fast & Affordable Phone Battery Replacement</h2>
<p>Is your phone dying too quickly, overheating, or shutting down unexpectedly? A battery replacement can bring your phone back to life for a fraction of the cost of a new device.</p>

<h3>Signs You Need a Battery Replacement</h3>
<ul>
<li>Battery draining from 100% to 0% within a few hours</li>
<li>Phone shutting off at 20% or 30% charge</li>
<li>Device getting unusually hot during normal use</li>
<li>Battery health below 80% (shown in iPhone settings)</li>
<li>Physical swelling — back panel or screen starting to lift</li>
</ul>

<h3>Our Battery Replacement Service</h3>
<p>We use genuine-grade battery cells with proper Battery Management System (BMS) chips to ensure safety, longevity, and optimal performance. Every battery is tested before installation and comes with a <strong>6-month warranty</strong>.</p>

<h3>Supported Devices</h3>
<p>We replace batteries for all iPhone models (iPhone 8 through iPhone 16 Pro Max), Samsung Galaxy S and A series, OnePlus, Xiaomi, Oppo, Vivo, Realme, Google Pixel, and more.</p>

<h3>Quick Turnaround</h3>
<p>Most battery replacements are completed in 30–45 minutes while you wait. No need to leave your phone overnight.</p>',
                'meta_title' => 'Phone Battery Replacement | 6-Month Warranty | All Brands',
                'meta_description' => 'Professional phone battery replacement service with 6-month warranty. Fast 30-minute service for iPhone, Samsung, OnePlus & more. Genuine-grade cells used.',
                'meta_keywords' => 'phone battery replacement, iPhone battery replacement, Samsung battery replacement, battery repair near me',
                'schema_type' => 'Service',
                'status' => 'published',
                'sort_order' => 2,
            ],
            [
                'title' => 'Water Damage Repair Service',
                'slug' => 'water-damage-repair',
                'content' => '<h2>Professional Water Damage Repair for Phones & Tablets</h2>
<p>Dropped your phone in water, spilled a drink on it, or caught in the rain? Time is critical — bring your device to us as soon as possible for the best chance of recovery.</p>

<h3>Our Water Damage Repair Process</h3>
<ol>
<li><strong>Immediate Assessment:</strong> We inspect the extent of water ingress and corrosion</li>
<li><strong>Complete Disassembly:</strong> Every component is carefully removed for individual treatment</li>
<li><strong>Ultrasonic Cleaning:</strong> Professional-grade ultrasonic bath with IPA solution removes all moisture and corrosion</li>
<li><strong>Microscope Inspection:</strong> Board-level examination to identify damaged components</li>
<li><strong>Controlled Drying:</strong> Dehumidification chamber ensures zero residual moisture</li>
<li><strong>Component Repair:</strong> Damaged ICs, connectors, or flex cables are repaired or replaced</li>
<li><strong>Full Testing:</strong> Complete functional test of all systems before handover</li>
</ol>

<h3>Success Rates</h3>
<p>When treated within 24 hours: <strong>75–85% recovery rate</strong>. After 48 hours, corrosion begins to cause irreversible damage, reducing success rates significantly. The sooner you act, the better.</p>

<h3>What NOT to Do</h3>
<ul>
<li>Do NOT put your phone in rice — it doesn\'t work and can cause additional damage</li>
<li>Do NOT try to charge or turn on a wet phone</li>
<li>Do NOT use a hair dryer — excessive heat can damage components</li>
</ul>

<h3>Emergency? Contact Us Now</h3>
<p>We prioritize water damage cases. Walk in directly or call us for immediate guidance while you\'re on your way.</p>',
                'meta_title' => 'Water Damage Phone Repair | Emergency Service | 85% Recovery Rate',
                'meta_description' => 'Emergency water damage phone repair with 75-85% recovery rate. Professional ultrasonic cleaning, board-level repair. Bring your phone in within 24 hours!',
                'meta_keywords' => 'water damage phone repair, phone dropped in water, wet phone repair, water damage recovery',
                'schema_type' => 'Service',
                'status' => 'published',
                'sort_order' => 3,
            ],
            [
                'title' => 'Laptop Repair & Service',
                'slug' => 'laptop-repair-service',
                'content' => '<h2>Expert Laptop Repair & Maintenance</h2>
<p>From cracked screens to slow performance, our technicians handle all laptop issues with precision and care. We service all brands including Dell, HP, Lenovo, ASUS, Acer, Apple MacBook, and more.</p>

<h3>Services We Offer</h3>
<ul>
<li><strong>Screen Replacement:</strong> LCD and LED panel replacements for cracked or dim displays</li>
<li><strong>Keyboard Replacement:</strong> Individual key repair or full keyboard swap</li>
<li><strong>Battery Replacement:</strong> Restore your laptop\'s portability with a new battery</li>
<li><strong>SSD/RAM Upgrade:</strong> Dramatically improve performance with storage and memory upgrades</li>
<li><strong>Virus & Malware Removal:</strong> Deep cleaning of infected systems with data preservation</li>
<li><strong>OS Installation:</strong> Fresh Windows, macOS, or Linux installation with driver setup</li>
<li><strong>Motherboard Repair:</strong> Component-level repair for power, charging, and boot issues</li>
<li><strong>Hinge Repair:</strong> Fix loose, cracked, or broken laptop hinges</li>
</ul>

<h3>Why Choose Us?</h3>
<p>We combine the technical expertise of an authorized service centre with the personal attention and fair pricing of a local repair shop. Most repairs are completed within 1–3 business days.</p>',
                'meta_title' => 'Laptop Repair Service | Screen, Battery, SSD Upgrade | All Brands',
                'meta_description' => 'Professional laptop repair for Dell, HP, Lenovo, MacBook & more. Screen replacement, SSD upgrades, battery replacement, motherboard repair. Quick turnaround.',
                'meta_keywords' => 'laptop repair service, laptop screen replacement, laptop battery replacement, SSD upgrade, laptop repair near me',
                'schema_type' => 'Service',
                'status' => 'published',
                'sort_order' => 4,
            ],
        ];

        foreach ($seoPages as $page) {
            SeoPage::firstOrCreate(['slug' => $page['slug']], $page);
        }

        // ─── Link some FAQs to SEO pages via page_slug ───────────
        Faq::where('question', 'LIKE', '%screen%')->update(['page_slug' => 'mobile-screen-replacement']);
        Faq::where('question', 'LIKE', '%battery%')->update(['page_slug' => 'phone-battery-replacement']);

        // ─── Default SEO Settings ─────────────────────────────────
        $seoDefaults = [
            'seo_global_title_suffix' => ' | Professional Phone Repair',
            'seo_global_description' => 'Expert mobile phone repair services including screen replacement, battery replacement, water damage repair, and more. Same-day service with warranty.',
            'seo_global_keywords' => 'phone repair, mobile repair, screen replacement, battery replacement, phone repair near me',
            'seo_schema_business_type' => 'LocalBusiness',
            'seo_schema_price_range' => '₹₹',
        ];

        foreach ($seoDefaults as $key => $value) {
            if (!Setting::getValue($key)) {
                Setting::setValue($key, $value);
            }
        }
    }
}
