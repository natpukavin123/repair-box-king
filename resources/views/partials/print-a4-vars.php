<?php
/**
 * Common variables & helpers shared by all A4 print templates.
 *
 * Usage — each child sets $printKeys + $printDefaults BEFORE including:
 *   $printKeys = [
 *       'headerTitleEn'=>'receipt_header_title_en', 'headerTitleTa'=>'receipt_header_title_ta',
 *       'shopNameTa'=>'receipt_shop_name_ta', 'shopSloganTa'=>'receipt_shop_slogan_ta',
 *       'shopAddressTa'=>'receipt_shop_address_ta',
 *       'signLabelEn'=>'receipt_sign_label_en', 'signLabelTa'=>'receipt_sign_label_ta',
 *       'footerTextEn'=>'receipt_footer_text', 'footerTextTa'=>'receipt_footer_text_ta',
 *   ];
 *   $printDefaults = [
 *       'headerTitleEn'=>'Repair Receipt', 'headerTitleTa'=>'பழுதுபார்ப்பு ரசீது',
 *       'footerTextEn'=>'...', 'footerTextTa'=>'...',
 *   ];
 *   require_once resource_path('views/partials/print-a4-vars.php');
 */
use App\Models\Setting;

// ── Shop (General) ──
$shopName    = Setting::getValue('shop_name',    'RepairBox');
$shopAddress = Setting::getValue('shop_address', 'Your shop address');
$shopPhone   = Setting::getValue('shop_phone',   '');
$shopPhone2  = Setting::getValue('shop_phone2',  '');
$shopEmail   = Setting::getValue('shop_email',   '');
$shopSlogan  = Setting::getValue('shop_slogan',  'Your Trusted Mobile Partner');
$shopIcon    = Setting::getValue('shop_icon',    '');
$defaultLang = Setting::getValue('invoice_default_language', 'en');

// ── Template-specific settings (resolved from $printKeys / $printDefaults set by child) ──
$printKeys     = $printKeys ?? [];
$printDefaults = $printDefaults ?? [];

$headerTitleEn = !empty($printKeys['headerTitleEn'])
    ? Setting::getValue($printKeys['headerTitleEn'], $printDefaults['headerTitleEn'] ?? 'Invoice')
    : ($printDefaults['headerTitleEn'] ?? 'Invoice');
$headerTitleTa = !empty($printKeys['headerTitleTa'])
    ? Setting::getValue($printKeys['headerTitleTa'], $printDefaults['headerTitleTa'] ?? '')
    : ($printDefaults['headerTitleTa'] ?? '');
$shopNameTa = !empty($printKeys['shopNameTa'])
    ? (Setting::getValue($printKeys['shopNameTa'], '') ?: $shopName) : $shopName;
$shopSloganTa = !empty($printKeys['shopSloganTa'])
    ? (Setting::getValue($printKeys['shopSloganTa'], '') ?: $shopSlogan) : $shopSlogan;
$shopAddressTa = !empty($printKeys['shopAddressTa'])
    ? (Setting::getValue($printKeys['shopAddressTa'], '') ?: $shopAddress) : $shopAddress;
$signLabelEn = !empty($printKeys['signLabelEn'])
    ? Setting::getValue($printKeys['signLabelEn'], 'Authorised Signatory') : 'Authorised Signatory';
$signLabelTa = !empty($printKeys['signLabelTa'])
    ? Setting::getValue($printKeys['signLabelTa'], 'அங்கீகரிக்கப்பட்ட கையொப்பம்') : 'அங்கீகரிக்கப்பட்ட கையொப்பம்';

// Preview titles (always match header)
$previewTitleEn = $headerTitleEn;
$previewTitleTa = $headerTitleTa;

// Edit-mode setting keys (derived from $printKeys)
$shopNameTaKey    = $printKeys['shopNameTa'] ?? '';
$shopSloganTaKey  = $printKeys['shopSloganTa'] ?? '';
$headerTitleEnKey = $printKeys['headerTitleEn'] ?? '';
$headerTitleTaKey = $printKeys['headerTitleTa'] ?? '';
$signLabelEnKey   = $printKeys['signLabelEn'] ?? '';
$signLabelTaKey   = $printKeys['signLabelTa'] ?? '';

// ── Tamil payment method names ──
$methodsTa = ['cash'=>'பணம்','card'=>'அட்டை','upi'=>'UPI','bank_transfer'=>'வங்கி மாற்றம்','cheque'=>'காசோலை'];

// ── Number-to-words: English ──
if (!function_exists('numWords')) {
    function numWords(float $n): string {
        $o=['','ONE','TWO','THREE','FOUR','FIVE','SIX','SEVEN','EIGHT','NINE','TEN',
            'ELEVEN','TWELVE','THIRTEEN','FOURTEEN','FIFTEEN','SIXTEEN','SEVENTEEN','EIGHTEEN','NINETEEN'];
        $t=['','','TWENTY','THIRTY','FORTY','FIFTY','SIXTY','SEVENTY','EIGHTY','NINETY'];
        $c=function(int $x)use($o,$t,&$c):string{
            if($x<20)return $o[$x];if($x<100)return $t[(int)($x/10)].($x%10?' '.$o[$x%10]:'');
            if($x<1000)return $o[(int)($x/100)].' HUNDRED'.($x%100?' '.$c($x%100):'');
            if($x<100000)return $c((int)($x/1000)).' THOUSAND'.($x%1000?' '.$c($x%1000):'');
            if($x<10000000)return $c((int)($x/100000)).' LAKH'.($x%100000?' '.$c($x%100000):'');
            return $c((int)($x/10000000)).' CRORE'.($x%10000000?' '.$c($x%10000000):'');
        };
        return $c((int)$n).' RUPEES ONLY';
    }
}

// ── Number-to-words: Tamil (simple — used by sales invoice) ──
if (!function_exists('numWordsTamil')) {
    function numWordsTamil(float $n): string {
        $o=['','ஒன்று','இரண்டு','மூன்று','நான்கு','ஐந்து','ஆறு','ஏழு','எட்டு','ஒன்பது','பத்து',
            'பதினொன்று','பன்னிரண்டு','பதிமூன்று','பதினான்கு','பதினைந்து','பதினாறு','பதினேழு','பதினெட்டு','பத்தொன்பது'];
        $t=['','','இருபது','முப்பது','நாற்பது','ஐம்பது','அறுபது','எழுபது','எண்பது','தொண்ணூறு'];
        $c=function(int $x)use($o,$t,&$c):string{
            if($x<20)return $o[$x];
            if($x<100)return $t[(int)($x/10)].($x%10?' '.$o[$x%10]:'');
            if($x<1000)return $o[(int)($x/100)].' நூறு'.($x%100?' '.$c($x%100):'');
            if($x<100000)return $c((int)($x/1000)).' ஆயிரம்'.($x%1000?' '.$c($x%1000):'');
            if($x<10000000)return $c((int)($x/100000)).' லட்சம்'.($x%100000?' '.$c($x%100000):'');
            return $c((int)($x/10000000)).' கோடி'.($x%10000000?' '.$c($x%10000000):'');
        };
        return $c((int)$n).' ரூபாய் மட்டும்';
    }
}

// ── Number-to-words: Tamil (proper hundred forms — used by repair invoice) ──
if (!function_exists('numWordsTa')) {
    function numWordsTa(float $n): string {
        $u=['','ஒன்று','இரண்டு','மூன்று','நான்கு','ஐந்து',
            'ஆறு','ஏழு','எட்டு','ஒன்பது','பத்து',
            'பதினொன்று','பன்னிரண்டு','பதின்மூன்று','பதினான்கு','பதினைந்து',
            'பதினாறு','பதினேழு','பதினெட்டு','பத்தொன்பது'];
        $t=['','','இருபது','முப்பது','நாற்பது','ஐம்பது',
            'அறுபது','எழுபது','எண்பது','தொண்ணூறு'];
        $h=['','நூறு','இருநூறு','முந்நூறு','நானூறு','ஐந்நூறு',
            'அறுநூறு','எழுநூறு','எண்ணூறு','தொள்ளாயிரம்'];
        $c=function(int $x)use($u,$t,$h,&$c):string{
            if($x<20)return $u[$x];
            if($x<100)return $t[(int)($x/10)].($x%10?' '.$u[$x%10]:'');
            if($x<1000){$hr=(int)($x/100);$rem=$x%100;return $h[$hr].($rem?' '.$c($rem):'');}
            if($x<100000){$th=(int)($x/1000);$rem=$x%1000;
                $thBase=$th===1?'ஆயிரம்':$c($th).' ஆயிரம்';
                $thConn=$th===1?'ஆயிரத்து':$c($th).' ஆயிரத்து';
                return ($rem?$thConn.' '.$c($rem):$thBase);}
            if($x<10000000){$l=(int)($x/100000);$rem=$x%100000;return($l===1?'ஒரு இலட்சம்':$c($l).' இலட்சம்').($rem?' '.$c($rem):'');}
            $cr=(int)($x/10000000);$rem=$x%10000000;return($cr===1?'ஒரு கோடி':$c($cr).' கோடி').($rem?' '.$c($rem):'');
        };
        return $c((int)$n).' ரூபாய் மட்டுமே';
    }
}
