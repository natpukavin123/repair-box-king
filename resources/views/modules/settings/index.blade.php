@extends('layouts.app')
@section('page-title', 'Settings')

@section('content')
<div x-data="settingsPage()" x-init="init()">
    {{-- Tabs --}}
    <div class="flex flex-wrap gap-2 mb-6">
        <button @click="tab='general'; updateUrl()" :class="tab==='general' ? 'btn-primary' : 'btn-secondary'" class="text-sm">General</button>
        <button @click="tab='service-types'; updateUrl()" :class="tab==='service-types' ? 'btn-primary' : 'btn-secondary'" class="text-sm">Service Types</button>
        <button @click="tab='recharge-providers'; updateUrl()" :class="tab==='recharge-providers' ? 'btn-primary' : 'btn-secondary'" class="text-sm">Recharge Providers</button>
        <button @click="tab='email-templates'; updateUrl()" :class="tab==='email-templates' ? 'btn-primary' : 'btn-secondary'" class="text-sm">Email Templates</button>
        <button @click="tab='notifications'; updateUrl(); loadNotifications()" :class="tab==='notifications' ? 'btn-primary' : 'btn-secondary'" class="text-sm">Notifications</button>
        <button @click="tab='invoice-layout'; updateUrl()" :class="tab==='invoice-layout' ? 'btn-primary' : 'btn-secondary'" class="text-sm">Invoice Layout</button>
        <button @click="tab='backups'; updateUrl()" :class="tab==='backups' ? 'btn-primary' : 'btn-secondary'" class="text-sm">Backups</button>
    </div>

    {{-- General Settings --}}
    <div x-show="tab==='general'" class="card">
        <div class="card-header"><h3 class="text-lg font-semibold">General Settings</h3></div>
        <div class="card-body">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <template x-for="key in settingKeys" :key="key">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1" x-text="formatLabel(key)"></label>
                        <input :value="settings[key] || ''" @input="settings[key] = $event.target.value" type="text" class="form-input-custom">
                    </div>
                </template>
            </div>

            {{-- Shop Icon Upload --}}
            <div class="mt-6 pt-6 border-t">
                <h4 class="text-md font-semibold mb-4">Shop Logo</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Upload Icon</label>
                        <div class="flex items-center gap-4">
                            <div class="flex-1">
                                <input type="file" @change="handleIconUpload" accept="image/*" class="form-input-custom">
                                <p class="text-xs text-gray-500 mt-1">Max 2MB. PNG, JPG, GIF, SVG</p>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center justify-center">
                        <div x-show="previewIcon" class="w-24 h-24 border rounded-lg overflow-hidden bg-gray-50 flex items-center justify-center">
                            <img :src="previewIcon" class="w-full h-full object-contain" alt="Preview">
                        </div>
                        <div x-show="!previewIcon && settings.shop_icon" class="w-24 h-24 border rounded-lg overflow-hidden bg-gray-50 flex items-center justify-center">
                            <img :src="getIconUrl()" class="w-full h-full object-contain" alt="Shop Icon" x-on:error="$el.parentElement.style.display='none'" @load="console.log('Icon loaded:', getIconUrl())">
                        </div>
                        <div x-show="!previewIcon && !settings.shop_icon" class="w-24 h-24 border-2 border-dashed rounded-lg bg-gray-50 flex items-center justify-center">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-6">
                <button @click="saveSettings()" class="btn-primary" :disabled="saving"><span x-show="saving" class="spinner mr-1"></span> Save Settings</button>
            </div>
        </div>
    </div>

    {{-- Service Types --}}
    <div x-show="tab==='service-types'" class="card">
        <div class="card-header flex items-center justify-between">
            <h3 class="text-lg font-semibold">Service Types</h3>
            <button @click="stForm={name:'',default_price:'',description:''}; stEditing=null; stImageFile=null; stImagePreview=null; stThumbFile=null; stThumbPreview=null; showStModal=true" class="btn-primary text-sm">Add Type</button>
        </div>
        <div class="card-body p-0">
            <table class="data-table">
                <thead><tr><th>Image</th><th>Name</th><th>Default Price</th><th>Status</th><th>Actions</th></tr></thead>
                <tbody>
                    <template x-for="st in serviceTypes" :key="st.id">
                        <tr>
                            <td>
                                <template x-if="st.thumbnail">
                                    <img :src="'/storage/' + st.thumbnail" class="w-10 h-10 rounded-lg object-cover border border-gray-200 shadow-sm">
                                </template>
                                <template x-if="!st.thumbnail">
                                    <div class="w-10 h-10 rounded-lg bg-gray-100 border border-gray-200 flex items-center justify-center">
                                        <svg class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    </div>
                                </template>
                            </td>
                            <td class="font-medium" x-text="st.name"></td>
                            <td x-text="st.default_price ? RepairBox.formatCurrency(st.default_price) : '-'"></td>
                            <td><span class="badge" :class="st.status==='active' ? 'badge-success' : 'badge-danger'" x-text="st.status || 'active'"></span></td>
                            <td><button @click="openEditSt(st)" class="text-primary-600 hover:text-primary-800"><svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></button></td>
                        </tr>
                    </template>
                    <tr x-show="serviceTypes.length===0"><td colspan="5" class="text-center text-gray-400 py-6">No service types</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- Recharge Providers --}}
    <div x-show="tab==='recharge-providers'" class="card">
        <div class="card-header flex items-center justify-between">
            <h3 class="text-lg font-semibold">Recharge Providers</h3>
            <button @click="rpForm={name:'',provider_type:'',commission_percentage:''}; showRpModal=true" class="btn-primary text-sm">Add Provider</button>
        </div>
        <div class="card-body p-0">
            <table class="data-table">
                <thead><tr><th>Name</th><th>Type</th><th>Commission %</th></tr></thead>
                <tbody>
                    <template x-for="rp in rechargeProviders" :key="rp.id"><tr><td class="font-medium" x-text="rp.name"></td><td x-text="rp.provider_type"></td><td x-text="rp.commission_percentage + '%'"></td></tr></template>
                    <tr x-show="rechargeProviders.length===0"><td colspan="3" class="text-center text-gray-400 py-6">No providers</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- Email Templates --}}
    <div x-show="tab==='email-templates'" class="card">
        <div class="card-header"><h3 class="text-lg font-semibold">Email Templates</h3></div>
        <div class="card-body p-0">
            <table class="data-table">
                <thead><tr><th>Name</th><th>Subject</th><th>Status</th><th>Actions</th></tr></thead>
                <tbody>
                    <template x-for="et in emailTemplates" :key="et.id">
                        <tr>
                            <td class="font-medium" x-text="et.template_name"></td>
                            <td x-text="et.subject"></td>
                            <td><span class="badge" :class="et.status==='active' ? 'badge-success' : 'badge-danger'" x-text="et.status"></span></td>
                            <td><button @click="etEditing=et; etForm={subject:et.subject,body:et.body||'',status:et.status}; showEtModal=true" class="text-primary-600 hover:text-primary-800"><svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></button></td>
                        </tr>
                    </template>
                    <tr x-show="emailTemplates.length===0"><td colspan="4" class="text-center text-gray-400 py-6">No templates</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════
         Notifications
    ══════════════════════════════════════════════════════════ --}}
    <div x-show="tab==='notifications'" x-cloak>

        {{-- ─── Email Notifications ─────────────────────── --}}
        <div class="card mb-6">
            <div class="card-header flex items-center gap-3">
                <div class="w-9 h-9 flex items-center justify-center rounded-lg bg-indigo-100">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                </div>
                <h3 class="text-lg font-semibold">Email Notifications</h3>
            </div>
            <div class="card-body space-y-5">
                <p class="text-sm text-gray-500">Automatically send emails to customers when their repair status changes. Configure SMTP settings in <code class="bg-gray-100 px-1 rounded">.env</code> or under your hosting mail settings.</p>

                {{-- Toggles --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <label class="flex items-start gap-3 p-4 border rounded-xl cursor-pointer hover:bg-gray-50 transition" :class="settings.notify_email_received === '1' ? 'border-indigo-300 bg-indigo-50' : 'border-gray-200'">
                        <input type="checkbox" :checked="settings.notify_email_received === '1'" @change="settings.notify_email_received = $event.target.checked ? '1' : '0'" class="mt-0.5 h-4 w-4 accent-indigo-600">
                        <div>
                            <p class="font-medium text-gray-800 text-sm">Order Received</p>
                            <p class="text-xs text-gray-500 mt-0.5">Send email when a repair ticket is created.</p>
                        </div>
                    </label>
                    <label class="flex items-start gap-3 p-4 border rounded-xl cursor-pointer hover:bg-gray-50 transition" :class="settings.notify_email_completed === '1' ? 'border-indigo-300 bg-indigo-50' : 'border-gray-200'">
                        <input type="checkbox" :checked="settings.notify_email_completed === '1'" @change="settings.notify_email_completed = $event.target.checked ? '1' : '0'" class="mt-0.5 h-4 w-4 accent-indigo-600">
                        <div>
                            <p class="font-medium text-gray-800 text-sm">Repair Completed</p>
                            <p class="text-xs text-gray-500 mt-0.5">Send email when a repair is marked as completed.</p>
                        </div>
                    </label>
                </div>

                {{-- Variable Reference --}}
                <details class="text-sm">
                    <summary class="cursor-pointer text-indigo-600 font-medium select-none">Available template variables</summary>
                    <div class="mt-2 p-3 bg-gray-50 rounded-lg border border-gray-200">
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-x-4 gap-y-1 font-mono text-xs text-gray-600">
                            <span>{customer_name}</span><span>{ticket_number}</span><span>{tracking_id}</span>
                            <span>{tracking_url}</span><span>{device_brand}</span><span>{device_model}</span>
                            <span>{estimated_cost}</span><span>{service_charge}</span><span>{grand_total}</span>
                            <span>{expected_delivery_date}</span><span>{technician_name}</span><span>{status}</span>
                            <span>{shop_name}</span><span>{shop_phone}</span>
                        </div>
                    </div>
                </details>

                {{-- Email Templates quick-edit --}}
                <div>
                    <h4 class="font-semibold text-gray-700 mb-3">Email Templates</h4>
                    <div class="space-y-4">
                        <template x-for="et in emailTemplates.filter(t => ['repair_received','repair_completed'].includes(t.template_name))" :key="et.id">
                            <div class="border border-gray-200 rounded-xl overflow-hidden">
                                <div class="flex items-center justify-between px-4 py-3 bg-gray-50 border-b border-gray-200">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full" :class="et.template_name === 'repair_received' ? 'bg-blue-500' : 'bg-emerald-500'"></span>
                                        <span class="font-medium text-sm" x-text="et.template_name === 'repair_received' ? '📥 Order Received' : '✅ Repair Completed'"></span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="badge" :class="et.status==='active' ? 'badge-success' : 'badge-danger'" x-text="et.status"></span>
                                        <button @click="etEditing=et; etForm={subject:et.subject,body:et.body||'',status:et.status}; showEtModal=true" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">Edit</button>
                                    </div>
                                </div>
                                <div class="px-4 py-3">
                                    <p class="text-xs text-gray-500 mb-1 uppercase tracking-wide font-semibold">Subject</p>
                                    <p class="text-sm text-gray-700 font-mono" x-text="et.subject || '(no subject)'"></p>
                                </div>
                            </div>
                        </template>
                        <p x-show="emailTemplates.filter(t => ['repair_received','repair_completed'].includes(t.template_name)).length === 0"
                           class="text-sm text-gray-400">No repair email templates found. Run migrations to seed default templates.</p>
                    </div>
                </div>

                <div><button @click="saveNotificationSettings()" class="btn-primary" :disabled="saving"><span x-show="saving" class="spinner mr-1"></span> Save Email Settings</button></div>
            </div>
        </div>

        {{-- ─── WhatsApp Notifications ─────────────────────── --}}
        <div class="card">
            <div class="card-header flex items-center gap-3">
                <div class="w-9 h-9 flex items-center justify-center rounded-lg bg-green-100">
                    <svg class="w-5 h-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                </div>
                <h3 class="text-lg font-semibold">WhatsApp Notifications</h3>
            </div>
            <div class="card-body space-y-5">
                <div class="flex items-start gap-2 p-3 bg-amber-50 border border-amber-200 rounded-lg text-sm text-amber-800">
                    <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Works with any HTTP WhatsApp gateway like <strong>Ultramsg</strong>, <strong>2chat</strong>, or <strong>WA-Gateway</strong>. Enter the API URL, token, and your sender number below.</span>
                </div>

                {{-- Toggles --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <label class="flex items-start gap-3 p-4 border rounded-xl cursor-pointer hover:bg-gray-50 transition" :class="settings.notify_whatsapp_received === '1' ? 'border-green-300 bg-green-50' : 'border-gray-200'">
                        <input type="checkbox" :checked="settings.notify_whatsapp_received === '1'" @change="settings.notify_whatsapp_received = $event.target.checked ? '1' : '0'" class="mt-0.5 h-4 w-4 accent-green-600">
                        <div>
                            <p class="font-medium text-gray-800 text-sm">Order Received</p>
                            <p class="text-xs text-gray-500 mt-0.5">Send WhatsApp when a repair ticket is created.</p>
                        </div>
                    </label>
                    <label class="flex items-start gap-3 p-4 border rounded-xl cursor-pointer hover:bg-gray-50 transition" :class="settings.notify_whatsapp_completed === '1' ? 'border-green-300 bg-green-50' : 'border-gray-200'">
                        <input type="checkbox" :checked="settings.notify_whatsapp_completed === '1'" @change="settings.notify_whatsapp_completed = $event.target.checked ? '1' : '0'" class="mt-0.5 h-4 w-4 accent-green-600">
                        <div>
                            <p class="font-medium text-gray-800 text-sm">Repair Completed</p>
                            <p class="text-xs text-gray-500 mt-0.5">Send WhatsApp when a repair is marked as completed.</p>
                        </div>
                    </label>
                </div>

                {{-- API Config --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">API Endpoint URL</label>
                        <input x-model="settings.whatsapp_api_url" type="url" class="form-input-custom" placeholder="https://api.ultramsg.com/instanceXXXX">
                        <p class="text-xs text-gray-400 mt-1">Base URL – the system appends <code class="bg-gray-100 px-1 rounded">/sendMessage</code> automatically.</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">API Token / Secret</label>
                        <input x-model="settings.whatsapp_api_token" type="password" class="form-input-custom" placeholder="••••••••••">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">From Number / Instance ID <span class="text-gray-400 font-normal">(optional)</span></label>
                        <input x-model="settings.whatsapp_from_number" type="text" class="form-input-custom" placeholder="919876543210">
                        <p class="text-xs text-gray-400 mt-1">Some providers need the sender number or instance ID.</p>
                    </div>
                </div>

                {{-- WhatsApp Templates --}}
                <div class="space-y-4">
                    <h4 class="font-semibold text-gray-700">Message Templates</h4>

                    <div class="border border-gray-200 rounded-xl overflow-hidden">
                        <div class="flex items-center gap-2 px-4 py-3 bg-gray-50 border-b border-gray-200">
                            <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                            <span class="font-medium text-sm">📥 Order Received Message</span>
                        </div>
                        <div class="p-4">
                            <textarea x-model="settings.whatsapp_template_received" class="form-input-custom font-mono text-sm" rows="7"
                                      placeholder="Hello {customer_name}! Your device has been received..."></textarea>
                            <p class="text-xs text-gray-400 mt-1">Use the same <code class="bg-gray-100 px-1 rounded">{variable}</code> placeholders as email templates.</p>
                        </div>
                    </div>

                    <div class="border border-gray-200 rounded-xl overflow-hidden">
                        <div class="flex items-center gap-2 px-4 py-3 bg-gray-50 border-b border-gray-200">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            <span class="font-medium text-sm">✅ Repair Completed Message</span>
                        </div>
                        <div class="p-4">
                            <textarea x-model="settings.whatsapp_template_completed" class="form-input-custom font-mono text-sm" rows="7"
                                      placeholder="Hello {customer_name}! Your device is ready for pickup..."></textarea>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <button @click="saveNotificationSettings()" class="btn-primary" :disabled="saving"><span x-show="saving" class="spinner mr-1"></span> Save WhatsApp Settings</button>
                    <button @click="showTestNotifyModal=true" class="btn-secondary text-sm">🧪 Send Test Message</button>
                </div>
            </div>
        </div>
    </div>
    {{-- /Notifications --}}

    {{-- Invoice Layout --}}
    <div x-show="tab==='invoice-layout'" class="card">
        <div class="card-header"><h3 class="text-lg font-semibold">Invoice Layout</h3></div>
        <div class="card-body">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Paper Size</label>
                    <select x-model="settings.invoice_paper_size" class="form-select-custom w-full">
                        <option value="80mm auto">80mm<br>auto</option>
                        <option value="80mm 200mm">80mm x 200mm</option>
                        <option value="A4">A4</option>
                        <option value="A5">A5</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Design Variant</label>
                    <select x-model="settings.invoice_design_variant" class="form-select-custom w-full">
                        <option value="default">Default</option>
                        <option value="modern">Modern</option>
                        <option value="minimal">Minimal</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Header Title</label>
                    <input type="text" x-model="settings.invoice_header_title" class="form-input-custom" placeholder="RepairBox">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Header Subtitle</label>
                    <input type="text" x-model="settings.invoice_header_subtitle" class="form-input-custom" placeholder="Mobile Shop Management">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Footer Text</label>
                    <input type="text" x-model="settings.invoice_footer_text" class="form-input-custom" placeholder="Thank you for your business">
                </div>
            </div>
            <div class="mt-4"><button @click="saveSettings()" class="btn-primary" :disabled="saving"><span x-show="saving" class="spinner mr-1"></span> Save Invoice Layout</button></div>
        </div>
    </div>

    {{-- Backups --}}
    <div x-show="tab==='backups'" class="card">
        <div class="card-header flex items-center justify-between">
            <h3 class="text-lg font-semibold">Backups</h3>
            <button @click="createBackup()" class="btn-primary text-sm" :disabled="saving"><span x-show="saving" class="spinner mr-1"></span> Create Backup</button>
        </div>
        <div class="card-body p-0">
            <table class="data-table">
                <thead><tr><th>Type</th><th>File</th><th>Size</th><th>Status</th><th>Date</th></tr></thead>
                <tbody>
                    <template x-for="b in backups" :key="b.id">
                        <tr>
                            <td x-text="b.backup_type"></td>
                            <td class="text-sm" x-text="b.file_path"></td>
                            <td x-text="b.file_size ? (b.file_size / 1024).toFixed(1)+' KB' : '-'"></td>
                            <td><span class="badge badge-success" x-text="b.status"></span></td>
                            <td x-text="new Date(b.created_at).toLocaleString()"></td>
                        </tr>
                    </template>
                    <tr x-show="backups.length===0"><td colspan="5" class="text-center text-gray-400 py-6">No backups</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- Service Type Modal --}}
    <div x-show="showStModal" class="modal-overlay" x-cloak @click.self="showStModal=false">
        <div class="modal-container">
            <div class="modal-header">
                <h3 class="text-lg font-semibold" x-text="stEditing ? 'Edit Service Type' : 'Add Service Type'"></h3>
                <button @click="showStModal=false" class="text-gray-400 hover:text-gray-600">&times;</button>
            </div>
            <div class="modal-body space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Name *</label>
                    <input x-model="stForm.name" type="text" class="form-input-custom">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Default Price</label>
                    <input x-model="stForm.default_price" type="number" step="0.01" class="form-input-custom">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea x-model="stForm.description" class="form-input-custom" rows="2"></textarea>
                </div>

                {{-- Image Upload --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Service Images</label>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <p class="text-xs text-gray-500 mb-1.5 font-medium">Main Image</p>
                            <div class="border-2 border-dashed border-gray-300 rounded-xl p-3 text-center cursor-pointer hover:border-primary-400 hover:bg-primary-50 transition-all"
                                 @click="$refs.stImageInput.click()" @dragover.prevent @drop.prevent="stHandleFileDrop('image', $event)">
                                <template x-if="stImagePreview">
                                    <div class="relative">
                                        <img :src="stImagePreview" class="max-h-28 mx-auto rounded-lg object-contain">
                                        <button type="button" @click.stop="stImageFile=null; stImagePreview=null; $refs.stImageInput.value=''"
                                                class="absolute top-0 right-0 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs hover:bg-red-600">✕</button>
                                    </div>
                                </template>
                                <template x-if="!stImagePreview">
                                    <div class="py-3">
                                        <svg class="w-8 h-8 text-gray-300 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        <p class="text-xs text-gray-400">Click to upload</p>
                                    </div>
                                </template>
                                <input x-ref="stImageInput" type="file" accept="image/*" class="hidden" @change="stHandleFilePick('image', $event)">
                            </div>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 mb-1.5 font-medium">Thumbnail <span class="text-gray-400">(auto if not set)</span></p>
                            <div class="border-2 border-dashed border-gray-300 rounded-xl p-3 text-center cursor-pointer hover:border-primary-400 hover:bg-primary-50 transition-all"
                                 @click="$refs.stThumbInput.click()" @dragover.prevent @drop.prevent="stHandleFileDrop('thumb', $event)">
                                <template x-if="stThumbPreview">
                                    <div class="relative">
                                        <img :src="stThumbPreview" class="max-h-28 mx-auto rounded-lg object-contain">
                                        <button type="button" @click.stop="stThumbFile=null; stThumbPreview=null; $refs.stThumbInput.value=''"
                                                class="absolute top-0 right-0 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs hover:bg-red-600">✕</button>
                                    </div>
                                </template>
                                <template x-if="!stThumbPreview">
                                    <div class="py-3">
                                        <svg class="w-8 h-8 text-gray-300 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        <p class="text-xs text-gray-400">Click to upload</p>
                                    </div>
                                </template>
                                <input x-ref="stThumbInput" type="file" accept="image/*" class="hidden" @change="stHandleFilePick('thumb', $event)">
                            </div>
                        </div>
                    </div>
                </div>
                <template x-if="stEditing">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select x-model="stForm.status" class="form-select-custom">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </template>
            </div>
            <div class="modal-footer">
                <button @click="showStModal=false" class="btn-secondary">Cancel</button>
                <button @click="saveServiceType()" class="btn-primary" :disabled="saving">
                    <span x-show="saving" class="spinner mr-1"></span> Save
                </button>
            </div>
        </div>
    </div>

    {{-- Test Notification Modal --}}
    <div x-show="showTestNotifyModal" class="modal-overlay" x-cloak @click.self="showTestNotifyModal=false">
        <div class="modal-container">
            <div class="modal-header">
                <h3 class="text-lg font-semibold">🧪 Send Test Notification</h3>
                <button @click="showTestNotifyModal=false" class="text-gray-400 hover:text-gray-600">&times;</button>
            </div>
            <div class="modal-body space-y-4">
                <p class="text-sm text-gray-600">Enter a repair ticket number to fire a test notification right now (bypasses the enabled/disabled toggles).</p>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ticket Number</label>
                    <input x-model="testTicket" type="text" class="form-input-custom" placeholder="e.g. REP-0001">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Notification Type</label>
                    <select x-model="testType" class="form-select-custom">
                        <option value="received">📥 Order Received</option>
                        <option value="completed">✅ Repair Completed</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Channel</label>
                    <select x-model="testChannel" class="form-select-custom">
                        <option value="email">📧 Email only</option>
                        <option value="whatsapp">💬 WhatsApp only</option>
                        <option value="both">📧+💬 Both</option>
                    </select>
                </div>
                <div x-show="testResult" class="p-3 rounded-lg text-sm" :class="testResult?.success ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-red-50 text-red-700 border border-red-200'" x-text="testResult?.message"></div>
            </div>
            <div class="modal-footer">
                <button @click="showTestNotifyModal=false" class="btn-secondary">Close</button>
                <button @click="sendTestNotification()" class="btn-primary" :disabled="saving">
                    <span x-show="saving" class="spinner mr-1"></span> Send Test
                </button>
            </div>
        </div>
    </div>

    {{-- Recharge Provider Modal --}}
    <div x-show="showRpModal" class="modal-overlay" x-cloak @click.self="showRpModal=false">
        <div class="modal-container">
            <div class="modal-header"><h3 class="text-lg font-semibold">Add Recharge Provider</h3><button @click="showRpModal=false" class="text-gray-400 hover:text-gray-600">&times;</button></div>
            <div class="modal-body space-y-4">
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Name *</label><input x-model="rpForm.name" type="text" class="form-input-custom"></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Type *</label>
                    <select x-model="rpForm.provider_type" class="form-select-custom"><option value="">Select</option><option value="mobile">Mobile</option><option value="dth">DTH</option><option value="data_card">Data Card</option><option value="electricity">Electricity</option></select>
                </div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Commission % *</label><input x-model="rpForm.commission_percentage" type="number" step="0.01" min="0" max="100" class="form-input-custom" placeholder="e.g. 3.5"></div>
            </div>
            <div class="modal-footer"><button @click="showRpModal=false" class="btn-secondary">Cancel</button><button @click="saveRechargeProvider()" class="btn-primary" :disabled="saving"><span x-show="saving" class="spinner mr-1"></span> Save</button></div>
        </div>
    </div>

    {{-- Email Template Modal --}}
    <div x-show="showEtModal" class="modal-overlay" x-cloak @click.self="showEtModal=false">
        <div class="modal-container modal-lg">
            <div class="modal-header"><h3 class="text-lg font-semibold">Edit Email Template</h3><button @click="showEtModal=false" class="text-gray-400 hover:text-gray-600">&times;</button></div>
            <div class="modal-body space-y-4">
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Subject</label><input x-model="etForm.subject" type="text" class="form-input-custom"></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Body</label><textarea x-model="etForm.body" class="form-input-custom" rows="8"></textarea></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select x-model="etForm.status" class="form-select-custom"><option value="active">Active</option><option value="inactive">Inactive</option></select>
                </div>
            </div>
            <div class="modal-footer"><button @click="showEtModal=false" class="btn-secondary">Cancel</button><button @click="saveEmailTemplate()" class="btn-primary" :disabled="saving"><span x-show="saving" class="spinner mr-1"></span> Save</button></div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function settingsPage() {
    return {
        tab: 'general', saving: false, iconFile: null, previewIcon: '',
        settings: {}, settingKeys: ['shop_name','shop_address','shop_phone','shop_email','currency_symbol','invoice_prefix','repair_prefix','low_stock_threshold','invoice_paper_size','invoice_design_variant','invoice_header_title','invoice_header_subtitle','invoice_footer_text'],
        notificationSettingKeys: ['notify_email_received','notify_email_completed','notify_whatsapp_received','notify_whatsapp_completed','whatsapp_api_url','whatsapp_api_token','whatsapp_from_number','whatsapp_template_received','whatsapp_template_completed'],
        serviceTypes: [], showStModal: false, stEditing: null, stForm: {},
        stImageFile: null, stImagePreview: null, stThumbFile: null, stThumbPreview: null,
        rechargeProviders: [], showRpModal: false, rpForm: {},
        emailTemplates: [], showEtModal: false, etEditing: null, etForm: {},
        backups: [],
        showTestNotifyModal: false, testTicket: '', testType: 'received', testChannel: 'email', testResult: null,
        init() {
            const p = new URLSearchParams(window.location.search);
            if (p.has('tab')) this.tab = p.get('tab');
            this.load();
        },
        updateUrl() {
            const params = new URLSearchParams();
            if (this.tab !== 'general') params.set('tab', this.tab);
            const qs = params.toString();
            history.replaceState(null, '', window.location.pathname + (qs ? '?' + qs : ''));
        },
        async load() {
            const [s, st, rp, et, b] = await Promise.all([
                RepairBox.ajax('/settings'), RepairBox.ajax('/service-types'),
                RepairBox.ajax('/recharge-providers'), RepairBox.ajax('/email-templates'),
                RepairBox.ajax('/backups')
            ]);
            if(s.data) this.settings = s.data; if(st.data) this.serviceTypes = st.data;
            if(rp.data) this.rechargeProviders = rp.data; if(et.data) this.emailTemplates = et.data;
            if(b.data) this.backups = b.data;
        },
        loadNotifications() {
            // already loaded in load() — just ensure email templates are present
            if (this.emailTemplates.length === 0) {
                RepairBox.ajax('/email-templates').then(r => { if(r.data) this.emailTemplates = r.data; });
            }
        },
        async saveNotificationSettings() {
            this.saving = true;
            try {
                const payload = {};
                this.notificationSettingKeys.forEach(k => { if (this.settings[k] !== undefined) payload['settings['+k+']'] = this.settings[k]; });
                const formData = new FormData();
                formData.append('_method', 'PUT');
                this.notificationSettingKeys.forEach(k => {
                    if (this.settings[k] !== undefined && this.settings[k] !== null)
                        formData.append('settings['+k+']', this.settings[k]);
                });
                const r = await fetch('/settings', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
                    body: formData
                });
                const data = await r.json();
                if (data.success !== false) RepairBox.toast('Notification settings saved', 'success');
                else RepairBox.toast(data.message || 'Error', 'error');
            } catch(e) { RepairBox.toast('Error: '+e.message, 'error'); }
            this.saving = false;
        },
        async sendTestNotification() {
            if (!this.testTicket.trim()) { RepairBox.toast('Enter a ticket number', 'warning'); return; }
            this.saving = true; this.testResult = null;
            const r = await RepairBox.ajax('/notifications/test', 'POST', { ticket: this.testTicket, type: this.testType, channel: this.testChannel });
            this.saving = false;
            this.testResult = { success: r.success !== false, message: r.message || (r.success !== false ? 'Sent successfully!' : 'Failed to send.') };
        },
        formatLabel(key) { return key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase()); },
        getIconUrl() {
            const icon = this.settings.shop_icon;
            if (!icon) return '';
            if (icon.startsWith('http') || icon.startsWith('data:')) return icon;
            // Construct the correct storage URL
            return '/storage/' + (icon.startsWith('/') ? icon.substring(1) : icon);
        },
        handleIconUpload(e) {
            const file = e.target.files[0];
            if (file) {
                this.iconFile = file;
                const reader = new FileReader();
                reader.onload = (evt) => {
                    this.previewIcon = evt.target.result;
                };
                reader.readAsDataURL(file);
            }
        },
        async saveSettings() {
            this.saving = true;
            try {
                const formData = new FormData();
                // Laravel method spoofing: POST + _method=PUT so PHP parses multipart/form-data
                formData.append('_method', 'PUT');
                // Append each setting individually with array notation
                Object.keys(this.settings).forEach(key => {
                    if (key !== 'shop_icon' && this.settings[key] !== null) {
                        formData.append(`settings[${key}]`, this.settings[key]);
                    }
                });
                if (this.iconFile) {
                    formData.append('shop_icon', this.iconFile);
                }

                const response = await fetch('/settings', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: formData
                });

                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }

                const r = await response.json();
                this.saving = false;
                this.iconFile = null;
                if (r.success !== false) {
                    RepairBox.toast('Settings saved', 'success');
                    this.previewIcon = '';
                    this.iconFile = null;
                    setTimeout(() => this.load(), 500);
                } else {
                    RepairBox.toast(r.message || 'Error saving settings', 'error');
                }
            } catch (err) {
                this.saving = false;
                console.error('Save error:', err);
                RepairBox.toast('Error saving settings: ' + err.message, 'error');
            }
        },
        clearIconPreview() {
            this.previewIcon = '';
            this.iconFile = null;
        },
        async saveServiceType() {
            this.saving = true;
            const r = await RepairBox.ajax(this.stEditing ? `/service-types/${this.stEditing}` : '/service-types', this.stEditing ? 'PUT' : 'POST', this.stForm);
            if (r.success !== false && r.data) {
                const id = r.data.id || this.stEditing;
                if (this.stImageFile || this.stThumbFile) {
                    const fd = new FormData();
                    if (this.stImageFile) fd.append('image', this.stImageFile);
                    if (this.stThumbFile) fd.append('thumbnail', this.stThumbFile);
                    await RepairBox.upload(`/service-types/${id}/upload-image`, fd);
                }
                RepairBox.toast('Saved', 'success'); this.showStModal = false;
                const st = await RepairBox.ajax('/service-types'); if(st.data) this.serviceTypes = st.data;
            }
            this.saving = false;
        },
        openEditSt(st) {
            this.stEditing = st.id;
            this.stForm = { name: st.name, default_price: st.default_price || '', description: st.description || '', status: st.status || 'active' };
            this.stImageFile = null; this.stThumbFile = null;
            this.stImagePreview = st.image ? '/storage/' + st.image : null;
            this.stThumbPreview = st.thumbnail ? '/storage/' + st.thumbnail : null;
            this.showStModal = true;
        },
        stHandleFilePick(type, e) {
            const file = e.target.files[0]; if (!file) return;
            if (type === 'image') { this.stImageFile = file; const r = new FileReader(); r.onload = ev => this.stImagePreview = ev.target.result; r.readAsDataURL(file); }
            else { this.stThumbFile = file; const r = new FileReader(); r.onload = ev => this.stThumbPreview = ev.target.result; r.readAsDataURL(file); }
        },
        stHandleFileDrop(type, e) {
            const file = e.dataTransfer.files[0]; if (!file || !file.type.startsWith('image/')) return;
            if (type === 'image') { this.stImageFile = file; const r = new FileReader(); r.onload = ev => this.stImagePreview = ev.target.result; r.readAsDataURL(file); }
            else { this.stThumbFile = file; const r = new FileReader(); r.onload = ev => this.stThumbPreview = ev.target.result; r.readAsDataURL(file); }
        },
        async saveRechargeProvider() {
            this.saving = true;
            const r = await RepairBox.ajax('/recharge-providers', 'POST', this.rpForm);
            this.saving = false; if(r.success !== false) { RepairBox.toast('Saved', 'success'); this.showRpModal = false; const rp = await RepairBox.ajax('/recharge-providers'); if(rp.data) this.rechargeProviders = rp.data; }
        },
        async saveEmailTemplate() {
            this.saving = true;
            const r = await RepairBox.ajax(`/email-templates/${this.etEditing.id}`, 'PUT', this.etForm);
            this.saving = false; if(r.success !== false) { RepairBox.toast('Saved', 'success'); this.showEtModal = false; const et = await RepairBox.ajax('/email-templates'); if(et.data) this.emailTemplates = et.data; }
        },
        async createBackup() {
            this.saving = true;
            const r = await RepairBox.ajax('/backups', 'POST');
            this.saving = false; if(r.success !== false) { RepairBox.toast('Backup created', 'success'); const b = await RepairBox.ajax('/backups'); if(b.data) this.backups = b.data; }
        }
    };
}
</script>
@endpush
