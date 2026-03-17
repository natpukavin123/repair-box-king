<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\Setting;

return new class extends Migration
{
    public function up(): void
    {
        $defaults = [
            // Invoice Print Settings
            'invoice_header_title_en'    => 'Sales Invoice',
            'invoice_header_title_ta'    => 'விற்பனை இரசீது',
            'invoice_shop_name_ta'       => '',
            'invoice_shop_slogan_ta'     => '',
            'invoice_shop_address_ta'    => '',
            'invoice_sign_label_en'      => 'Authorised Signatory',
            'invoice_sign_label_ta'      => 'அங்கீகரிக்கப்பட்ட கையொப்பம்',
            'invoice_footer_text'        => 'Subject to jurisdiction. Our responsibility ceases as soon as goods leave our premises. Goods once sold will not be taken back.',
            'invoice_footer_text_ta'     => 'நீதிமன்ற அதிகார வரம்புக்கு உட்பட்டது. பொருட்கள் எங்கள் வளாகத்தை விட்டு வெளியேறியவுடன் எங்கள் பொறுப்பு முடிவடைகிறது. விற்கப்பட்ட பொருட்கள் திரும்ப ஏற்றுக்கொள்ளப்படாது.',

            // Receipt (Repair) Print Settings
            'receipt_header_title_en'    => 'Repair Receipt',
            'receipt_header_title_ta'    => 'பழுதுபார்ப்பு ரசீது',
            'receipt_shop_name_ta'       => '',
            'receipt_shop_slogan_ta'     => '',
            'receipt_shop_address_ta'    => '',
            'receipt_sign_label_en'      => 'Authorised Signatory',
            'receipt_sign_label_ta'      => 'அங்கீகரிக்கப்பட்ட கையொப்பம்',
            'receipt_footer_text'        => 'Keep this receipt to claim your device. Unclaimed devices after 30 days are not our responsibility.',
            'receipt_footer_text_ta'     => 'உங்கள் சாதனத்தை பெற இந்த ரசீதை வைத்திருங்கள். 30 நாட்களுக்குப் பிறகு உரிமை கோரப்படாத சாதனங்களுக்கு நாங்கள் பொறுப்பல்ல.',
            'receipt_notes_en'           => "Keep this receipt to claim your device.\nEstimated cost may change upon diagnosis.\nData backup is customer's responsibility.\nUnclaimed devices after 30 days — not our liability.",
            'receipt_notes_ta'           => "உங்கள் சாதனத்தை பெற இந்த ரசீதை வைத்திருங்கள்.\nமதிப்பீட்டுச் செலவு ஆய்வுக்குப் பிறகு மாறலாம்.\nதரவு காப்புப்பிரதி வாடிக்கையாளரின் பொறுப்பு.\n30 நாட்களுக்குப் பிறகு உரிமை கோரப்படாத சாதனங்கள் — எங்கள் பொறுப்பல்ல.",

            // Repair Invoice Print Settings
            'repair_invoice_header_title_en' => 'Repair Invoice',
            'repair_invoice_header_title_ta' => 'பழுதுபார்ப்பு இரசீது',
            'repair_invoice_footer_text'     => 'Subject to jurisdiction. Our responsibility ceases as soon as goods leave our premises.',
            'repair_invoice_footer_text_ta'  => 'நீதிமன்ற அதிகார வரம்புக்கு உட்பட்டது. பொருட்கள் எங்கள் வளாகத்தை விட்டு வெளியேறியவுடன் எங்கள் பொறுப்பு முடிவடைகிறது.',

            // Default language
            'invoice_default_language'   => 'en',
        ];

        foreach ($defaults as $key => $value) {
            // Only set if key doesn't already exist
            if (!Setting::where('setting_key', $key)->exists()) {
                Setting::setValue($key, $value);
            }
        }
    }

    public function down(): void
    {
        $keys = [
            'invoice_header_title_en', 'invoice_header_title_ta',
            'invoice_shop_name_ta', 'invoice_shop_slogan_ta', 'invoice_shop_address_ta',
            'invoice_sign_label_en', 'invoice_sign_label_ta',
            'receipt_header_title_en', 'receipt_header_title_ta',
            'receipt_shop_name_ta', 'receipt_shop_slogan_ta', 'receipt_shop_address_ta',
            'receipt_sign_label_en', 'receipt_sign_label_ta',
            'receipt_notes_en', 'receipt_notes_ta',
            'repair_invoice_header_title_en', 'repair_invoice_header_title_ta',
            'repair_invoice_footer_text', 'repair_invoice_footer_text_ta',
        ];
        Setting::whereIn('setting_key', $keys)->delete();
    }
};
