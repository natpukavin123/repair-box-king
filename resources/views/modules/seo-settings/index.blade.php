@extends('layouts.app')
@section('page-title', 'SEO Settings')
@section('content-class', 'flex flex-col')

@section('content')
<div x-data="seoSettingsPage()" x-init="load()" class="page-list">
    <div class="flex items-center justify-between mb-4">
        <p class="text-sm text-gray-500">Configure global SEO settings, analytics, schema markup, and tracking scripts.</p>
        <button @click="save()" class="btn-primary" :disabled="saving">
            <span x-show="saving" class="spinner mr-1" style="width:16px;height:16px;border-width:2px"></span>
            Save Settings
        </button>
    </div>

    <div class="space-y-6">
        {{-- Global SEO Defaults --}}
        <div class="card">
            <div class="card-header"><h3 class="font-semibold text-gray-700 text-sm uppercase tracking-wider">Global SEO Defaults</h3></div>
            <div class="card-body space-y-4">
                <div>
                    <label class="form-label">Title Suffix <span class="text-gray-400 font-normal">(appended to all page titles)</span></label>
                    <input x-model="form.seo_global_title_suffix" type="text" class="form-input-custom" placeholder="e.g. | KingInternet">
                </div>
                <div>
                    <label class="form-label">Default Meta Description <span class="text-gray-400 font-normal">(fallback when page has no description)</span></label>
                    <textarea x-model="form.seo_global_description" rows="2" class="form-input-custom" placeholder="Your shop's default meta description..."></textarea>
                </div>
                <div>
                    <label class="form-label">Default Keywords</label>
                    <input x-model="form.seo_global_keywords" type="text" class="form-input-custom" placeholder="mobile repair, phone fix, screen replacement...">
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">Default OG Image URL</label>
                        <input x-model="form.seo_og_default_image" type="text" class="form-input-custom" placeholder="https://...">
                    </div>
                    <div>
                        <label class="form-label">Twitter Handle</label>
                        <input x-model="form.seo_twitter_handle" type="text" class="form-input-custom" placeholder="@yourhandle">
                    </div>
                </div>
            </div>
        </div>

        {{-- Search Engine Verification --}}
        <div class="card">
            <div class="card-header"><h3 class="font-semibold text-gray-700 text-sm uppercase tracking-wider">Search Engine Verification</h3></div>
            <div class="card-body space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">Google Verification Code</label>
                        <input x-model="form.seo_google_verification" type="text" class="form-input-custom" placeholder="google-site-verification=...">
                    </div>
                    <div>
                        <label class="form-label">Bing Verification Code</label>
                        <input x-model="form.seo_bing_verification" type="text" class="form-input-custom" placeholder="...">
                    </div>
                </div>
            </div>
        </div>

        {{-- Analytics & Tracking --}}
        <div class="card">
            <div class="card-header"><h3 class="font-semibold text-gray-700 text-sm uppercase tracking-wider">Analytics & Tracking</h3></div>
            <div class="card-body space-y-4">
                <div>
                    <label class="form-label">Google Analytics ID <span class="text-gray-400 font-normal">(e.g. G-XXXXXXXXXX)</span></label>
                    <input x-model="form.seo_google_analytics" type="text" class="form-input-custom" placeholder="G-XXXXXXXXXX">
                </div>
                <div>
                    <label class="form-label">Google Tag Manager ID <span class="text-gray-400 font-normal">(e.g. GTM-XXXXXXX)</span></label>
                    <input x-model="form.seo_google_tag_manager" type="text" class="form-input-custom" placeholder="GTM-XXXXXXX">
                </div>
            </div>
        </div>

        {{-- Local Business Schema --}}
        <div class="card">
            <div class="card-header"><h3 class="font-semibold text-gray-700 text-sm uppercase tracking-wider">Local Business Schema (Schema.org)</h3></div>
            <div class="card-body space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">Business Type</label>
                        <select x-model="form.seo_schema_business_type" class="form-select-custom">
                            <option value="LocalBusiness">LocalBusiness</option>
                            <option value="Store">Store</option>
                            <option value="ElectronicsStore">ElectronicsStore</option>
                            <option value="MobilePhoneStore">MobilePhoneStore</option>
                            <option value="ProfessionalService">ProfessionalService</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Price Range</label>
                        <input x-model="form.seo_schema_price_range" type="text" class="form-input-custom" placeholder="$ or $$ or $$$">
                    </div>
                </div>
                <div>
                    <label class="form-label">Opening Hours <span class="text-gray-400 font-normal">(Schema format: Mo-Sa 09:00-20:00)</span></label>
                    <input x-model="form.seo_schema_opening_hours" type="text" class="form-input-custom" placeholder="Mo-Sa 09:00-20:00">
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">Geo Latitude</label>
                        <input x-model="form.seo_schema_geo_lat" type="text" class="form-input-custom" placeholder="12.2253">
                    </div>
                    <div>
                        <label class="form-label">Geo Longitude</label>
                        <input x-model="form.seo_schema_geo_lng" type="text" class="form-input-custom" placeholder="79.0747">
                    </div>
                </div>
            </div>
        </div>

        {{-- Custom Scripts --}}
        <div class="card">
            <div class="card-header"><h3 class="font-semibold text-gray-700 text-sm uppercase tracking-wider">Custom Scripts</h3></div>
            <div class="card-body space-y-4">
                <div>
                    <label class="form-label">Head Scripts <span class="text-gray-400 font-normal">(injected before &lt;/head&gt;)</span></label>
                    <textarea x-model="form.seo_head_scripts" rows="4" class="form-input-custom font-mono text-xs" placeholder="<script>...</script> or <meta> tags..."></textarea>
                </div>
                <div>
                    <label class="form-label">Body Scripts <span class="text-gray-400 font-normal">(injected before &lt;/body&gt;)</span></label>
                    <textarea x-model="form.seo_body_scripts" rows="4" class="form-input-custom font-mono text-xs" placeholder="<script>...</script>..."></textarea>
                </div>
            </div>
        </div>

        {{-- Custom Robots.txt --}}
        <div class="card">
            <div class="card-header"><h3 class="font-semibold text-gray-700 text-sm uppercase tracking-wider">Custom Robots.txt Rules</h3></div>
            <div class="card-body space-y-4">
                <div>
                    <label class="form-label">Additional Rules <span class="text-gray-400 font-normal">(appended to default robots.txt)</span></label>
                    <textarea x-model="form.seo_robots_custom" rows="5" class="form-input-custom font-mono text-xs" placeholder="User-agent: *
Disallow: /private/
..."></textarea>
                </div>
            </div>
        </div>
    </div>

    {{-- Save button bottom --}}
    <div class="flex justify-end mt-6">
        <button @click="save()" class="btn-primary" :disabled="saving">
            <span x-show="saving" class="spinner mr-1" style="width:16px;height:16px;border-width:2px"></span>
            Save All SEO Settings
        </button>
    </div>
</div>
@endsection

@push('scripts')
<script>
function seoSettingsPage() {
    return {
        saving: false, loading: true,
        form: {
            seo_global_title_suffix: '',
            seo_global_description: '',
            seo_global_keywords: '',
            seo_google_analytics: '',
            seo_google_tag_manager: '',
            seo_google_verification: '',
            seo_bing_verification: '',
            seo_schema_business_type: 'LocalBusiness',
            seo_schema_price_range: '',
            seo_schema_opening_hours: '',
            seo_schema_geo_lat: '',
            seo_schema_geo_lng: '',
            seo_og_default_image: '',
            seo_twitter_handle: '',
            seo_robots_custom: '',
            seo_head_scripts: '',
            seo_body_scripts: '',
        },

        async load() {
            this.loading = true;
            const r = await RepairBox.ajax('/admin/seo-settings');
            if (r) {
                for (const key in this.form) {
                    if (r[key] !== undefined) this.form[key] = r[key];
                }
            }
            this.loading = false;
        },

        async save() {
            this.saving = true;
            const r = await RepairBox.ajax('/admin/seo-settings', 'PUT', this.form);
            this.saving = false;
            if (r.success !== false) {
                RepairBox.toast('SEO settings saved', 'success');
            }
        },
    };
}
</script>
@endpush
