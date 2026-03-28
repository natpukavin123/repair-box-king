@extends('layouts.app')
@section('page-title', 'Print Settings')

@section('content')
@php
    $shopName    = $settings['shop_name'] ?? 'RepairBox';
    $shopSlogan  = $settings['shop_slogan'] ?? 'Your Trusted Mobile Partner';
    $shopAddress = $settings['shop_address'] ?? 'Your shop address';
    $shopPhone   = $settings['shop_phone'] ?? '';
    $shopEmail   = $settings['shop_email'] ?? '';
    $shopIcon    = $settings['shop_icon'] ?? '';
@endphp

<div x-data="printSettingsApp()" x-init="init()">

    {{-- Toolbar --}}
    <div class="ps-toolbar">
        <div class="flex items-center gap-3">
            <a href="/admin/settings" class="ps-back-btn">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <div>
                <h2 class="text-lg font-bold text-gray-900 leading-tight">Print Settings</h2>
                <p class="text-[11px] text-gray-400">Edit on left, preview updates live on right</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            {{-- Language Toggle --}}
            <div class="ps-lang-toggle">
                <button @click="previewLang='en'" :class="previewLang==='en' ? 'active' : ''" class="ps-lang-btn">English</button>
                <button @click="previewLang='ta'" :class="previewLang==='ta' ? 'active' : ''" class="ps-lang-btn">தமிழ்</button>
            </div>
            <button @click="window.open('/admin/print-preview/'+activeTab, '_blank')" class="ps-sample-btn">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Sample
            </button>
            <button @click="save()" class="ps-save-btn" :disabled="saving">
                <span x-show="saving" class="spinner" style="width:14px;height:14px;"></span>
                <svg x-show="!saving" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Save
            </button>
        </div>
    </div>

    {{-- Template Tabs --}}
    <div class="ps-tab-bar">
        <template x-for="t in tabs" :key="t.key">
            <button @click="activeTab = t.key"
                :class="activeTab === t.key ? 'ps-tab active' : 'ps-tab'"
                class="flex items-center gap-1.5">
                <span x-html="t.icon"></span>
                <span x-text="t.label"></span>
            </button>
        </template>
        <template x-if="activeTab === 'sales-invoice'">
            <div class="ml-auto flex items-center gap-2">
                <span class="text-[10px] text-gray-400 font-medium">Paper:</span>
                <select x-model="s.invoice_paper_size" class="ps-mini-select">
                    <option value="A4_landscape">A4 Landscape</option>
                    <option value="A5">A5 Portrait</option>
                </select>
            </div>
        </template>
    </div>

    {{-- Split Layout --}}
    <div class="ps-split">

        {{-- ═══ LEFT: Fields for active language ═══ --}}
        <div class="ps-left">
            <div class="ps-left-inner">

                {{-- ── SALES INVOICE ── --}}
                <template x-if="activeTab === 'sales-invoice'">
                    <div class="ps-fields">
                        {{-- English --}}
                        <template x-if="previewLang === 'en'">
                            <div class="ps-fields-inner">
                                <div class="ps-field-group">
                                    <label class="ps-lbl">Invoice Title</label>
                                    <input type="text" x-model="s.invoice_header_title_en" class="ps-inp" placeholder="Sales Invoice">
                                </div>
                                <div class="ps-field-group">
                                    <label class="ps-lbl">Footer Text</label>
                                    <textarea x-model="s.invoice_footer_text" class="ps-inp" rows="3" placeholder="Subject to jurisdiction..."></textarea>
                                </div>
                                <div class="ps-field-group">
                                    <label class="ps-lbl">Signature Label</label>
                                    <input type="text" x-model="s.invoice_sign_label_en" class="ps-inp" placeholder="Authorised Signatory">
                                </div>
                                <div class="ps-field-group">
                                    <label class="ps-lbl">Default Print Language</label>
                                    <select x-model="s.invoice_default_language" class="ps-inp">
                                        <option value="en">English</option>
                                        <option value="ta">Tamil</option>
                                    </select>
                                </div>
                            </div>
                        </template>
                        {{-- Tamil --}}
                        <template x-if="previewLang === 'ta'">
                            <div class="ps-fields-inner">
                                <div class="ps-field-group">
                                    <label class="ps-lbl">Invoice Title</label>
                                    <input type="text" x-model="s.invoice_header_title_ta" class="ps-inp" placeholder="விற்பனை இரசீது">
                                </div>
                                <div class="ps-field-group">
                                    <label class="ps-lbl">Shop Name</label>
                                    <input type="text" x-model="s.invoice_shop_name_ta" class="ps-inp" placeholder="(English used if blank)">
                                </div>
                                <div class="ps-field-group">
                                    <label class="ps-lbl">Shop Slogan</label>
                                    <input type="text" x-model="s.invoice_shop_slogan_ta" class="ps-inp" placeholder="உங்கள் நம்பகமான மொபைல் பார்ட்னர்">
                                </div>
                                <div class="ps-field-group">
                                    <label class="ps-lbl">Shop Address</label>
                                    <input type="text" x-model="s.invoice_shop_address_ta" class="ps-inp" placeholder="(English used if blank)">
                                </div>
                                <div class="ps-field-group">
                                    <label class="ps-lbl">Footer Text</label>
                                    <textarea x-model="s.invoice_footer_text_ta" class="ps-inp" rows="3" placeholder="நீதிமன்ற அதிகார வரம்புக்கு..."></textarea>
                                </div>
                                <div class="ps-field-group">
                                    <label class="ps-lbl">Signature Label</label>
                                    <input type="text" x-model="s.invoice_sign_label_ta" class="ps-inp" placeholder="அங்கீகரிக்கப்பட்ட கையொப்பம்">
                                </div>
                            </div>
                        </template>
                    </div>
                </template>

                {{-- ── REPAIR RECEIPT ── --}}
                <template x-if="activeTab === 'repair-receipt'">
                    <div class="ps-fields">
                        <template x-if="previewLang === 'en'">
                            <div class="ps-fields-inner">
                                <div class="ps-field-group">
                                    <label class="ps-lbl">Receipt Title</label>
                                    <input type="text" x-model="s.receipt_header_title_en" class="ps-inp" placeholder="Repair Receipt">
                                </div>
                                <div class="ps-field-group">
                                    <label class="ps-lbl">Important Notes</label>
                                    <textarea x-model="s.receipt_notes_en" class="ps-inp" rows="4" placeholder="Keep this receipt to claim your device..."></textarea>
                                </div>
                                <div class="ps-field-group">
                                    <label class="ps-lbl">Footer Text</label>
                                    <textarea x-model="s.receipt_footer_text" class="ps-inp" rows="2" placeholder="Keep this receipt..."></textarea>
                                </div>
                                <div class="ps-field-group">
                                    <label class="ps-lbl">Signature Label</label>
                                    <input type="text" x-model="s.receipt_sign_label_en" class="ps-inp" placeholder="Authorised Signatory">
                                </div>
                            </div>
                        </template>
                        <template x-if="previewLang === 'ta'">
                            <div class="ps-fields-inner">
                                <div class="ps-field-group">
                                    <label class="ps-lbl">Receipt Title</label>
                                    <input type="text" x-model="s.receipt_header_title_ta" class="ps-inp" placeholder="பழுதுபார்ப்பு ரசீது">
                                </div>
                                <div class="ps-field-group">
                                    <label class="ps-lbl">Shop Name</label>
                                    <input type="text" x-model="s.receipt_shop_name_ta" class="ps-inp" placeholder="(English used if blank)">
                                </div>
                                <div class="ps-field-group">
                                    <label class="ps-lbl">Shop Slogan</label>
                                    <input type="text" x-model="s.receipt_shop_slogan_ta" class="ps-inp" placeholder="உங்கள் நம்பகமான மொபைல் பார்ட்னர்">
                                </div>
                                <div class="ps-field-group">
                                    <label class="ps-lbl">Shop Address</label>
                                    <input type="text" x-model="s.receipt_shop_address_ta" class="ps-inp" placeholder="(English used if blank)">
                                </div>
                                <div class="ps-field-group">
                                    <label class="ps-lbl">Important Notes</label>
                                    <textarea x-model="s.receipt_notes_ta" class="ps-inp" rows="4" placeholder="உங்கள் சாதனத்தை பெற..."></textarea>
                                </div>
                                <div class="ps-field-group">
                                    <label class="ps-lbl">Footer Text</label>
                                    <textarea x-model="s.receipt_footer_text_ta" class="ps-inp" rows="2" placeholder="உங்கள் சாதனத்தை பெற..."></textarea>
                                </div>
                                <div class="ps-field-group">
                                    <label class="ps-lbl">Signature Label</label>
                                    <input type="text" x-model="s.receipt_sign_label_ta" class="ps-inp" placeholder="அங்கீகரிக்கப்பட்ட கையொப்பம்">
                                </div>
                            </div>
                        </template>
                    </div>
                </template>

                {{-- ── REPAIR INVOICE ── --}}
                <template x-if="activeTab === 'repair-invoice'">
                    <div class="ps-fields">
                        <template x-if="previewLang === 'en'">
                            <div class="ps-fields-inner">
                                <div class="ps-field-group">
                                    <label class="ps-lbl">Invoice Title</label>
                                    <input type="text" x-model="s.repair_invoice_header_title_en" class="ps-inp" placeholder="Repair Invoice">
                                </div>
                                <div class="ps-field-group">
                                    <label class="ps-lbl">Footer Text</label>
                                    <textarea x-model="s.repair_invoice_footer_text" class="ps-inp" rows="3" placeholder="Subject to jurisdiction..."></textarea>
                                </div>
                            </div>
                        </template>
                        <template x-if="previewLang === 'ta'">
                            <div class="ps-fields-inner">
                                <div class="ps-field-group">
                                    <label class="ps-lbl">Invoice Title</label>
                                    <input type="text" x-model="s.repair_invoice_header_title_ta" class="ps-inp" placeholder="பழுதுபார்ப்பு இரசீது">
                                </div>
                                <div class="ps-field-group">
                                    <label class="ps-lbl">Footer Text</label>
                                    <textarea x-model="s.repair_invoice_footer_text_ta" class="ps-inp" rows="3" placeholder="நீதிமன்ற அதிகார வரம்புக்கு..."></textarea>
                                </div>
                            </div>
                        </template>
                    </div>
                </template>
            </div>
        </div>

        {{-- ═══ RIGHT: Live Preview ═══ --}}
        <div class="ps-right">
            <div class="ps-preview-wrap">

                {{-- SALES INVOICE --}}
                <div x-show="activeTab === 'sales-invoice'" class="ps-card" x-cloak>
                    <div class="ps-hdr">
                        <div class="ps-logo">@if($shopIcon)<img src="{{ asset('storage/'.$shopIcon) }}">@else<span class="ps-logo-text">REPAIR<br>BOX</span>@endif</div>
                        <div class="ps-shop">
                            <div class="ps-shop-name" x-text="previewLang==='ta' ? (s.invoice_shop_name_ta || '{{ e($shopName) }}') : '{{ e($shopName) }}'"></div>
                            <div class="ps-shop-slogan" x-text="previewLang==='ta' ? (s.invoice_shop_slogan_ta || '{{ e($shopSlogan) }}') : '{{ e($shopSlogan) }}'"></div>
                            <div class="ps-shop-contact"><span x-text="previewLang==='ta' ? (s.invoice_shop_address_ta || '{{ e($shopAddress) }}') : '{{ e($shopAddress) }}'"></span>@if($shopPhone)<br>{{ $shopPhone }}@endif</div>
                        </div>
                        <div class="ps-badge">
                            <div class="ps-type ps-hl" x-text="previewLang==='ta' ? (s.invoice_header_title_ta || 'விற்பனை இரசீது') : (s.invoice_header_title_en || 'Sales Invoice')"></div>
                            <div class="ps-num">#INV-0001</div>
                            <div class="ps-date">{{ now()->format('d M Y') }}</div>
                        </div>
                    </div>
                    <div class="ps-info-row">
                        <div class="ps-info-cell"><div class="ps-info-lbl" x-text="previewLang==='ta' ? 'வாடிக்கையாளர்' : 'Bill To'"></div><div class="ps-info-val">John Doe</div><div class="ps-info-sub">9876543210<br>123 Main Street</div></div>
                        <div class="ps-info-cell ps-border-l"><div class="ps-info-lbl" x-text="previewLang==='ta' ? 'இரசீது விவரங்கள்' : 'Invoice Details'"></div><div class="ps-info-sub"><span x-text="previewLang==='ta' ? 'தேதி' : 'Date'"></span>: {{ now()->format('d M Y, g:i A') }}<br><span x-text="previewLang==='ta' ? 'ஊழியர்' : 'Staff'"></span>: Admin</div></div>
                    </div>
                    <table class="ps-tbl"><thead><tr><th style="width:20px">#</th><th class="tl" x-text="previewLang==='ta' ? 'பொருள் / சேவை' : 'Product / Service'"></th><th style="width:28px" x-text="previewLang==='ta' ? 'எண்.' : 'Qty'"></th><th class="tr" style="width:52px" x-text="previewLang==='ta' ? 'அதி.வி' : 'MRP'"></th><th class="tr" style="width:52px" x-text="previewLang==='ta' ? 'விலை' : 'Price'"></th><th class="tr" style="width:58px" x-text="previewLang==='ta' ? 'தொகை' : 'Amount'"></th></tr></thead>
                    <tbody><tr><td>1</td><td class="tl">Phone Case</td><td>2</td><td class="tr">350.00</td><td class="tr">300.00</td><td class="tr">600.00</td></tr><tr><td>2</td><td class="tl">Screen Guard</td><td>1</td><td class="tr">200.00</td><td class="tr">150.00</td><td class="tr">150.00</td></tr><tr><td>3</td><td class="tl">Charging Cable</td><td>1</td><td class="tr">120.00</td><td class="tr">100.00</td><td class="tr">100.00</td></tr><tr class="ps-erow"><td></td><td></td><td></td><td></td><td></td><td></td></tr><tr class="ps-erow"><td></td><td></td><td></td><td></td><td></td><td></td></tr>
                    </tbody><tfoot><tr><td colspan="2" class="tl" style="font-weight:700" x-text="previewLang==='ta' ? 'மொத்தம்' : 'Total'"></td><td style="font-weight:700">4</td><td></td><td></td><td class="tr" style="font-weight:700">850.00</td></tr></tfoot></table>
                    <div class="ps-bottom-row">
                        <div class="ps-bottom-l">
                            <div class="ps-slbl" x-text="previewLang==='ta' ? 'தொகை சொற்களில்' : 'Amount in Words'"></div>
                            <div class="ps-words" x-text="previewLang==='ta' ? 'எண்ணூற்று ஐம்பது ரூபாய் மட்டும்' : 'EIGHT HUNDRED FIFTY RUPEES ONLY'"></div>
                            <div class="ps-slbl" style="margin-top:6px" x-text="previewLang==='ta' ? 'செலுத்திய விவரம்' : 'Payment Details'"></div>
                            <div class="ps-payline"><span x-text="previewLang==='ta' ? 'பணம்' : 'Cash'"></span><span>850.00</span></div>
                        </div>
                        <div class="ps-bottom-r">
                            <div class="ps-sumline"><span x-text="previewLang==='ta' ? 'கூட்டுத்தொகை' : 'Sub Total'"></span><span>850.00</span></div>
                            <div class="ps-sumline"><span x-text="previewLang==='ta' ? 'தள்ளுபடி' : 'Discount'"></span><span>0.00</span></div>
                            <div class="ps-sumline ps-grand"><span x-text="previewLang==='ta' ? 'மொத்த தொகை' : 'Grand Total'"></span><span>850.00</span></div>
                            <div class="ps-sumline"><span x-text="previewLang==='ta' ? 'செலுத்தியது' : 'Paid'"></span><span style="font-weight:700">850.00</span></div>
                            <div class="ps-sumline ps-fullpaid" x-text="previewLang==='ta' ? 'முழுமையாக செலுத்தப்பட்டது' : 'FULLY PAID'"></div>
                        </div>
                    </div>
                    <div class="ps-sign"><div class="ps-sign-line"></div><div class="ps-sign-shop">{{ $shopName }}</div><div class="ps-sign-lbl ps-hl" x-text="previewLang==='ta' ? (s.invoice_sign_label_ta || 'அங்கீகரிக்கப்பட்ட கையொப்பம்') : (s.invoice_sign_label_en || 'Authorised Signatory')"></div></div>
                    <div class="ps-foot"><div class="ps-foot-txt ps-hl" x-text="previewLang==='ta' ? (s.invoice_footer_text_ta || 'நீதிமன்ற அதிகார வரம்புக்கு உட்பட்டது.') : (s.invoice_footer_text || 'Subject to jurisdiction. Goods once sold will not be taken back.')"></div><div class="ps-foot-gen">RepairBox</div></div>
                </div>

                {{-- REPAIR RECEIPT --}}
                <div x-show="activeTab === 'repair-receipt'" class="ps-card" x-cloak>
                    <div class="ps-hdr">
                        <div class="ps-logo">@if($shopIcon)<img src="{{ asset('storage/'.$shopIcon) }}">@else<span class="ps-logo-text">REPAIR<br>BOX</span>@endif</div>
                        <div class="ps-shop">
                            <div class="ps-shop-name" x-text="previewLang==='ta' ? (s.receipt_shop_name_ta || '{{ e($shopName) }}') : '{{ e($shopName) }}'"></div>
                            <div class="ps-shop-slogan" x-text="previewLang==='ta' ? (s.receipt_shop_slogan_ta || '{{ e($shopSlogan) }}') : '{{ e($shopSlogan) }}'"></div>
                            <div class="ps-shop-contact"><span x-text="previewLang==='ta' ? (s.receipt_shop_address_ta || '{{ e($shopAddress) }}') : '{{ e($shopAddress) }}'"></span>@if($shopPhone)<br>{{ $shopPhone }}@endif</div>
                        </div>
                        <div class="ps-badge">
                            <div class="ps-type ps-hl" x-text="previewLang==='ta' ? (s.receipt_header_title_ta || 'பழுதுபார்ப்பு ரசீது') : (s.receipt_header_title_en || 'Repair Receipt')"></div>
                            <div class="ps-num">#REP-0001</div>
                            <div class="ps-date">{{ now()->format('d M Y') }}</div>
                        </div>
                    </div>
                    <div class="ps-info-row">
                        <div class="ps-info-cell"><div class="ps-info-lbl" x-text="previewLang==='ta' ? 'வாடிக்கையாளர்' : 'Customer'"></div><div class="ps-info-val">Jane Smith</div><div class="ps-info-sub">9876543210</div></div>
                        <div class="ps-info-cell ps-border-l"><div class="ps-info-lbl" x-text="previewLang==='ta' ? 'சாதனம்' : 'Device'"></div><div class="ps-info-val">Samsung Galaxy S24</div><div class="ps-info-sub">IMEI: 123456789012345<br><span x-text="previewLang==='ta' ? 'எதிர்பார்ப்பு' : 'Est'"></span>: {{ now()->addDays(5)->format('d M Y') }}</div></div>
                    </div>
                    <div class="ps-cost-row">
                        <div><div class="ps-slbl" x-text="previewLang==='ta' ? 'மதிப்பீட்டு பழுது செலவு' : 'Estimated Repair Cost'"></div><div class="ps-cost-big">&#8377;1,500.00</div></div>
                        <div class="text-center"><div class="ps-slbl" x-text="previewLang==='ta' ? 'நிலை' : 'Status'"></div><span class="ps-status" x-text="previewLang==='ta' ? 'பெறப்பட்டது' : 'RECEIVED'"></span></div>
                        <div class="text-right"><div class="ps-slbl" x-text="previewLang==='ta' ? 'முன்பணம்' : 'Advance Paid'"></div><div style="font-size:14px;font-weight:700;">&#8377;500.00</div></div>
                    </div>
                    <div class="ps-prob"><div class="ps-slbl" x-text="previewLang==='ta' ? 'சிக்கல் விவரணை' : 'Problem Description'"></div><div style="font-size:10px;line-height:1.6;">Screen cracked, touch not working properly</div></div>
                    <div class="ps-bottom-row" style="min-height:90px;">
                        <div class="ps-bottom-l">
                            <div class="ps-slbl" x-text="previewLang==='ta' ? 'முக்கிய குறிப்புகள்' : 'Important Notes'"></div>
                            <div class="ps-hl" style="font-size:8px;line-height:1.7;white-space:pre-line;padding:3px 5px;" x-text="previewLang==='ta' ? (s.receipt_notes_ta || 'உங்கள் சாதனத்தை பெற இந்த ரசீதை வைத்திருங்கள்.') : (s.receipt_notes_en || 'Keep this receipt to claim your device.\nEstimated cost may change upon diagnosis.\nData backup is customer\'s responsibility.\nUnclaimed devices after 30 days — not our liability.')"></div>
                        </div>
                        <div class="ps-bottom-r" style="width:155px;border-left:1.5px solid #000;padding:7px 10px;">
                            <div class="ps-slbl" x-text="previewLang==='ta' ? 'ஆன்லைன் கண்காணிப்பு' : 'Track Online'"></div>
                            <div style="border:2px solid #000;padding:6px 8px;text-align:center;margin-top:3px;">
                                <div style="font-family:'Playfair Display',Georgia,serif;font-size:18px;font-weight:900;letter-spacing:3px;">TRK001</div>
                                <div style="font-size:7px;margin-top:2px;" x-text="previewLang==='ta' ? '/track பக்கம் செல்க' : 'Visit /track'"></div>
                            </div>
                            <div class="ps-slbl" style="margin-top:8px;" x-text="previewLang==='ta' ? 'செலுத்திய விவரம்' : 'Payment'"></div>
                            <div class="ps-payline"><span x-text="previewLang==='ta' ? 'முன்பணம்' : 'Advance'"></span><span>500.00</span></div>
                        </div>
                    </div>
                    <div class="ps-sign"><div class="ps-sign-line"></div><div class="ps-sign-shop">{{ $shopName }}</div><div class="ps-sign-lbl ps-hl" x-text="previewLang==='ta' ? (s.receipt_sign_label_ta || 'அங்கீகரிக்கப்பட்ட கையொப்பம்') : (s.receipt_sign_label_en || 'Authorised Signatory')"></div></div>
                    <div class="ps-foot"><div class="ps-foot-txt ps-hl" x-text="previewLang==='ta' ? (s.receipt_footer_text_ta || 'உங்கள் சாதனத்தை பெற இந்த ரசீதை வைத்திருங்கள்.') : (s.receipt_footer_text || 'Keep this receipt to claim your device.')"></div><div class="ps-foot-gen">RepairBox</div></div>
                </div>

                {{-- REPAIR INVOICE --}}
                <div x-show="activeTab === 'repair-invoice'" class="ps-card" x-cloak>
                    <div class="ps-hdr">
                        <div class="ps-logo">@if($shopIcon)<img src="{{ asset('storage/'.$shopIcon) }}">@else<span class="ps-logo-text">REPAIR<br>BOX</span>@endif</div>
                        <div class="ps-shop">
                            <div class="ps-shop-name" x-text="previewLang==='ta' ? (s.receipt_shop_name_ta || '{{ e($shopName) }}') : '{{ e($shopName) }}'"></div>
                            <div class="ps-shop-slogan" x-text="previewLang==='ta' ? (s.receipt_shop_slogan_ta || '{{ e($shopSlogan) }}') : '{{ e($shopSlogan) }}'"></div>
                            <div class="ps-shop-contact"><span x-text="previewLang==='ta' ? (s.receipt_shop_address_ta || '{{ e($shopAddress) }}') : '{{ e($shopAddress) }}'"></span>@if($shopPhone)<br>{{ $shopPhone }}@endif</div>
                        </div>
                        <div class="ps-badge">
                            <div class="ps-type ps-hl" x-text="previewLang==='ta' ? (s.repair_invoice_header_title_ta || 'பழுதுபார்ப்பு இரசீது') : (s.repair_invoice_header_title_en || 'Repair Invoice')"></div>
                            <div class="ps-num">#REP-0001</div>
                            <div class="ps-date">{{ now()->format('d M Y') }}</div>
                        </div>
                    </div>
                    <div class="ps-info-row">
                        <div class="ps-info-cell"><div class="ps-info-lbl" x-text="previewLang==='ta' ? 'வாடிக்கையாளர்' : 'Customer'"></div><div class="ps-info-val">Jane Smith</div><div class="ps-info-sub">9876543210<br>123 Main Street</div></div>
                        <div class="ps-info-cell ps-border-l"><div class="ps-info-lbl" x-text="previewLang==='ta' ? 'பழுதுபார்ப்பு விவரங்கள்' : 'Repair Details'"></div><div class="ps-info-sub">Samsung Galaxy S24<br><span x-text="previewLang==='ta' ? 'நிலை: முடிந்தது' : 'Status: Completed'"></span><br>{{ now()->format('d M Y') }}</div></div>
                    </div>
                    <table class="ps-tbl"><thead><tr><th style="width:20px">#</th><th class="tl" x-text="previewLang==='ta' ? 'விவரம்' : 'Description'"></th><th style="width:28px" x-text="previewLang==='ta' ? 'எண்.' : 'Qty'"></th><th class="tr" style="width:55px" x-text="previewLang==='ta' ? 'விலை' : 'Rate'"></th><th class="tr" style="width:60px" x-text="previewLang==='ta' ? 'தொகை' : 'Amount'"></th></tr></thead>
                    <tbody><tr><td>1</td><td class="tl">Screen Replacement</td><td>1</td><td class="tr">800.00</td><td class="tr">800.00</td></tr><tr><td>2</td><td class="tl">Labour Charge</td><td>1</td><td class="tr">500.00</td><td class="tr">500.00</td></tr><tr class="ps-erow"><td></td><td></td><td></td><td></td><td></td></tr><tr class="ps-erow"><td></td><td></td><td></td><td></td><td></td></tr></tbody></table>
                    <div class="ps-bottom-row">
                        <div class="ps-bottom-l">
                            <div class="ps-slbl" x-text="previewLang==='ta' ? 'தொகை சொற்களில்' : 'Amount in Words'"></div>
                            <div class="ps-words" x-text="previewLang==='ta' ? 'ஒன்று ஆயிரம் முன்னூறு ரூபாய் மட்டும்' : 'ONE THOUSAND THREE HUNDRED RUPEES ONLY'"></div>
                            <div class="ps-slbl" style="margin-top:6px" x-text="previewLang==='ta' ? 'செலுத்திய விவரம்' : 'Payment Details'"></div>
                            <div class="ps-payline"><span x-text="previewLang==='ta' ? 'முன்பணம்' : 'Advance'"></span><span>500.00</span></div>
                            <div class="ps-payline"><span x-text="previewLang==='ta' ? 'பணம்' : 'Cash'"></span><span>800.00</span></div>
                        </div>
                        <div class="ps-bottom-r">
                            <div class="ps-sumline"><span x-text="previewLang==='ta' ? 'பாகங்கள்' : 'Parts'"></span><span>800.00</span></div>
                            <div class="ps-sumline"><span x-text="previewLang==='ta' ? 'சேவைகள்' : 'Services'"></span><span>500.00</span></div>
                            <div class="ps-sumline ps-grand"><span x-text="previewLang==='ta' ? 'மொத்த தொகை' : 'Grand Total'"></span><span>1,300.00</span></div>
                            <div class="ps-sumline"><span x-text="previewLang==='ta' ? 'முன்பணம்' : 'Advance'"></span><span>-500.00</span></div>
                            <div class="ps-sumline" style="font-weight:900;text-decoration:underline;"><span x-text="previewLang==='ta' ? 'இருப்பு' : 'Balance'"></span><span>800.00</span></div>
                        </div>
                    </div>
                    <div class="ps-sign"><div class="ps-sign-line"></div><div class="ps-sign-shop">{{ $shopName }}</div><div class="ps-sign-lbl" x-text="previewLang==='ta' ? 'அங்கீகரிக்கப்பட்ட கையொப்பம்' : 'Authorised Signatory'"></div></div>
                    <div class="ps-foot"><div class="ps-foot-txt ps-hl" x-text="previewLang==='ta' ? (s.repair_invoice_footer_text_ta || 'நீதிமன்ற அதிகார வரம்புக்கு உட்பட்டது.') : (s.repair_invoice_footer_text || 'Subject to jurisdiction.')"></div><div class="ps-foot-gen">RepairBox</div></div>
                </div>

            </div>
        </div>
    </div>
</div>

<style>
/* ═══ TOOLBAR ═══ */
.ps-toolbar { display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;margin-bottom:12px; }
.ps-back-btn { width:32px;height:32px;display:inline-flex;align-items:center;justify-content:center;border-radius:8px;border:1px solid #e2e8f0;background:#fff;color:#64748b;transition:.15s; }
.ps-back-btn:hover { background:#f1f5f9;color:#1e293b; }
.ps-lang-toggle { display:inline-flex;border-radius:8px;overflow:hidden;border:2px solid #1e293b; }
.ps-lang-btn { padding:5px 14px;font-size:11px;font-weight:700;border:none;background:#fff;color:#1e293b;cursor:pointer;transition:.15s; }
.ps-lang-btn:hover { background:#f1f5f9; }
.ps-lang-btn.active { background:#1e293b;color:#fff; }
.ps-sample-btn { display:inline-flex;align-items:center;gap:5px;padding:6px 14px;font-size:11px;font-weight:700;border-radius:8px;border:1.5px solid #d1d5db;background:#fff;color:#374151;cursor:pointer;transition:.15s; }
.ps-sample-btn:hover { background:#f9fafb;border-color:#9ca3af; }
.ps-save-btn { display:inline-flex;align-items:center;gap:5px;padding:6px 16px;font-size:11px;font-weight:700;border-radius:8px;border:none;background:#2563eb;color:#fff;cursor:pointer;transition:.15s; }
.ps-save-btn:hover { background:#1d4ed8; }
.ps-save-btn:disabled { opacity:.6;cursor:not-allowed; }

/* ═══ TABS ═══ */
.ps-tab-bar { display:flex;align-items:center;gap:4px;background:#f1f5f9;padding:4px;border-radius:12px;margin-bottom:12px; }
.ps-tab { padding:7px 16px;font-size:12px;font-weight:500;border-radius:9px;border:none;background:transparent;color:#64748b;cursor:pointer;transition:.15s; }
.ps-tab:hover { color:#1e293b; }
.ps-tab.active { background:#fff;color:#1e293b;font-weight:700;box-shadow:0 1px 4px rgba(0,0,0,.08); }
.ps-mini-select { font-size:11px;padding:3px 8px;border:1px solid #d1d5db;border-radius:6px;background:#fff; }

/* ═══ SPLIT ═══ */
.ps-split { display:grid;grid-template-columns:1fr 1fr;gap:16px;align-items:start; }
@media(max-width:1024px){ .ps-split { grid-template-columns:1fr; } }

/* ═══ LEFT ═══ */
.ps-left { background:#fff;border:1px solid #e2e8f0;border-radius:14px;overflow:hidden; }
.ps-left-inner { max-height:calc(100vh - 220px);overflow-y:auto;padding:16px; }
.ps-fields { }
.ps-fields-inner { display:flex;flex-direction:column;gap:14px; }
.ps-field-group { }
.ps-lbl { display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;text-transform:uppercase;letter-spacing:.5px; }
.ps-inp { width:100%;padding:9px 12px;font-size:13px;border:1.5px solid #e2e8f0;border-radius:10px;background:#f8fafc;transition:all .2s;font-family:inherit;resize:vertical; }
.ps-inp:focus { outline:none;border-color:#3b82f6;background:#fff;box-shadow:0 0 0 3px rgba(59,130,246,.1); }

/* ═══ RIGHT / PREVIEW ═══ */
.ps-right { position:sticky;top:12px; }
.ps-preview-wrap { background:linear-gradient(145deg,#e2e5ea,#cdd1d8);border-radius:14px;padding:20px;box-shadow:inset 0 2px 8px rgba(0,0,0,.05);overflow-y:auto;max-height:calc(100vh - 220px); }

/* ═══ CARD ═══ */
.ps-card { background:#fff;border:2.5px solid #000;font-family:'DM Sans',Arial,sans-serif;font-size:11px;color:#000;overflow:hidden;box-shadow:0 10px 40px rgba(0,0,0,.12); }
.ps-hl { background:rgba(59,130,246,.06);border:1.5px dashed rgba(59,130,246,.45);border-radius:3px;padding:1px 5px;position:relative; }

.ps-hdr { padding:12px 14px 10px;display:flex;align-items:center;gap:12px;border-bottom:2.5px solid #000; }
.ps-logo { width:44px;height:44px;border:2px solid #000;border-radius:50%;overflow:hidden;flex-shrink:0;display:flex;align-items:center;justify-content:center;background:#fff; }
.ps-logo img { width:100%;height:100%;object-fit:cover;border-radius:50%; }
.ps-logo-text { font-size:7px;font-weight:700;text-align:center;line-height:1.3; }
.ps-shop { flex:1;min-width:0; }
.ps-shop-name { font-family:'Playfair Display',Georgia,serif;font-size:20px;font-weight:900;line-height:1;white-space:nowrap;overflow:hidden;text-overflow:ellipsis; }
.ps-shop-slogan { font-size:8px;letter-spacing:1.5px;text-transform:uppercase;margin-top:2px; }
.ps-shop-contact { font-size:8px;margin-top:3px;line-height:1.7; }
.ps-badge { text-align:right;flex-shrink:0; }
.ps-type { display:inline-block;border:1.5px solid #000;font-size:8px;font-weight:700;letter-spacing:2px;text-transform:uppercase;padding:3px 8px;margin-bottom:3px; }
.ps-num { font-family:'Playfair Display',Georgia,serif;font-size:16px;font-weight:900;line-height:1; }
.ps-date { font-size:8px;margin-top:2px; }

.ps-info-row { display:flex;border-bottom:2.5px solid #000; }
.ps-info-cell { flex:1;padding:8px 12px; }
.ps-border-l { border-left:1.5px solid #000; }
.ps-info-lbl { font-size:7px;font-weight:700;letter-spacing:1.2px;text-transform:uppercase;margin-bottom:2px; }
.ps-info-val { font-size:12px;font-weight:700;line-height:1.3; }
.ps-info-sub { font-size:8px;margin-top:2px;line-height:1.7; }

.ps-tbl { width:100%;border-collapse:collapse;border-bottom:2.5px solid #000; }
.ps-tbl th { background:#fff;font-size:7px;font-weight:700;letter-spacing:.7px;text-transform:uppercase;padding:4px 6px;border:1.5px solid #000;text-align:center; }
.ps-tbl th.tl { text-align:left; }
.ps-tbl td { padding:4px 6px;font-size:10px;border:1px solid #000;text-align:center; }
.ps-tbl td.tl { text-align:left; }
.ps-tbl td.tr,.ps-tbl th.tr { text-align:right; }
.ps-tbl tfoot td { font-size:10px;padding:4px 6px;border:1.5px solid #000; }
.ps-erow { height:16px; }

.ps-bottom-row { display:flex;border-bottom:2.5px solid #000; }
.ps-bottom-l { flex:1;border-right:1.5px solid #000;padding:7px 10px; }
.ps-bottom-r { width:155px;display:flex;flex-direction:column; }
.ps-slbl { font-size:7px;font-weight:700;letter-spacing:1.2px;text-transform:uppercase;margin-bottom:2px; }
.ps-words { border:1.5px solid #000;padding:4px 8px;font-size:8px;font-weight:600;line-height:1.5;margin-top:2px; }
.ps-payline { display:flex;justify-content:space-between;font-size:9px;padding:2px 0;border-bottom:1px solid #000; }
.ps-payline:last-child { border-bottom:none; }
.ps-sumline { display:flex;justify-content:space-between;padding:4px 9px;font-size:9px;border-bottom:1px solid #000; }
.ps-sumline:last-child { border-bottom:none; }
.ps-grand { background:#fff;font-family:'Playfair Display',Georgia,serif;font-size:11px;font-weight:900;border:1.5px solid #000;padding:5px 9px; }
.ps-fullpaid { text-align:center;font-size:8px;font-weight:900;padding:4px 9px; }

.ps-cost-row { border-bottom:2.5px solid #000;padding:10px 12px;display:flex;align-items:center;justify-content:space-between; }
.ps-cost-big { font-family:'Playfair Display',Georgia,serif;font-size:26px;font-weight:900;line-height:1; }
.ps-status { display:inline-block;padding:3px 10px;border:1.5px solid #000;font-size:8px;font-weight:800;letter-spacing:.8px;text-transform:uppercase; }
.ps-prob { border-bottom:2.5px solid #000;padding:7px 12px; }

.ps-sign { padding:5px 10px 7px;text-align:center;border-bottom:2.5px solid #000; }
.ps-sign-line { border-top:1.5px solid #000;margin:20px 14px 2px; }
.ps-sign-shop { font-family:'Playfair Display',Georgia,serif;font-size:10px;font-weight:700; }
.ps-sign-lbl { font-size:7px;letter-spacing:1px;text-transform:uppercase;margin-top:1px; }

.ps-foot { padding:4px 12px;display:flex;justify-content:space-between;align-items:center; }
.ps-foot-txt { font-size:7px;line-height:1.5;flex:1;margin-right:8px; }
.ps-foot-gen { font-size:7px;white-space:nowrap; }
</style>
@endsection

@push('scripts')
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@400;500;600;700&family=Noto+Sans+Tamil:wght@400;500;600;700&display=swap" rel="stylesheet">
<script>
function printSettingsApp() {
    return {
        activeTab: 'sales-invoice',
        previewLang: @json($settings['invoice_default_language'] ?? 'en'),
        saving: false,
        s: @json($settings ?? []),
        tabs: [
            { key: 'sales-invoice', label: 'Sales Invoice', icon: '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>' },
            { key: 'repair-receipt', label: 'Repair Receipt', icon: '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>' },
            { key: 'repair-invoice', label: 'Repair Invoice', icon: '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0"/></svg>' },
        ],
        init() {},
        async save() {
            this.saving = true;
            try {
                const fd = new FormData();
                fd.append('_method', 'PUT');
                fd.append('section', 'print');
                Object.keys(this.s).forEach(k => {
                    if (this.s[k] !== null && this.s[k] !== undefined) fd.append('settings['+k+']', this.s[k]);
                });
                const r = await fetch('/admin/settings', { method:'POST', headers:{ 'X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content, 'Accept':'application/json' }, body:fd });
                if (!r.ok) throw new Error('HTTP '+r.status);
                const d = await r.json();
                if (d.success !== false) RepairBox.toast('Saved!', 'success');
                else RepairBox.toast(d.message || 'Error', 'error');
            } catch(e) { RepairBox.toast('Error: '+e.message, 'error'); }
            this.saving = false;
        }
    };
}
</script>
@endpush
