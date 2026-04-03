@extends('layouts.app')
@section('page-title', 'SEO Landing Pages')
@section('content-class', 'flex flex-col')

@section('content')
<div x-data="seoPagesPage()" x-init="load()" class="page-list">
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-2">
            <select x-model="filterStatus" @change="load()" class="form-select-custom text-sm">
                <option value="">All Status</option>
                <option value="draft">Draft</option>
                <option value="published">Published</option>
                <option value="archived">Archived</option>
            </select>
        </div>
        <button @click="openCreate()" class="btn-primary">
            <svg class="w-4 h-4 mr-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            New SEO Page
        </button>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-scroll">
                <table class="data-table">
                    <thead class="sticky top-0 z-10 bg-gray-50">
                        <tr><th>#</th><th>Title</th><th>Slug</th><th>Schema</th><th>Status</th><th>Actions</th></tr>
                    </thead>
                    <tbody>
                        <template x-for="(item, i) in items" :key="item.id">
                            <tr>
                                <td x-text="i+1"></td>
                                <td class="font-medium max-w-xs truncate" x-text="item.title"></td>
                                <td class="text-xs text-gray-500" x-text="'/page/' + item.slug"></td>
                                <td class="text-xs text-gray-500" x-text="item.schema_type || 'WebPage'"></td>
                                <td>
                                    <span class="badge text-xs"
                                          :class="item.status === 'published' ? 'badge-success' : (item.status === 'draft' ? 'badge-warning' : 'badge-secondary')"
                                          x-text="item.status"></span>
                                </td>
                                <td class="whitespace-nowrap">
                                    <button @click="openEdit(item)" class="text-primary-600 hover:text-primary-800 mr-2">
                                        <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                    <template x-if="item.status === 'published'">
                                        <a :href="'/page/' + item.slug" target="_blank" class="text-gray-500 hover:text-gray-800 mr-2">
                                            <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                        </a>
                                    </template>
                                    <button @click="remove(item)" class="text-red-600 hover:text-red-800">
                                        <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </td>
                            </tr>
                        </template>
                        <tr x-show="items.length === 0 && !loading"><td colspan="6" class="text-center text-gray-400 py-8">No SEO pages yet</td></tr>
                        <template x-if="loading">
                            <template x-for="i in 5" :key="'sk'+i">
                                <tr><td colspan="6"><div class="skeleton h-4 w-full my-1"></div></td></tr>
                            </template>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Create/Edit Modal --}}
    <div x-show="showModal" class="modal-overlay" x-cloak @keydown.escape.window="showModal = false">
        <div class="modal-container max-w-4xl">
            <div class="modal-header">
                <h3 class="text-lg font-semibold" x-text="editing ? 'Edit SEO Page' : 'New SEO Page'"></h3>
                <button @click="showModal = false" class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
            </div>
            <div class="modal-body max-h-[70vh] overflow-y-auto">
                <div class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="sm:col-span-2">
                            <label class="form-label">Page Title *</label>
                            <input x-model="form.title" type="text" class="form-input-custom" placeholder="e.g. Laptop Repair Service" @input="if(!editing) form.slug = slugify(form.title)">
                        </div>
                        <div>
                            <label class="form-label">Slug</label>
                            <input x-model="form.slug" type="text" class="form-input-custom" placeholder="auto-generated">
                        </div>
                        <div>
                            <label class="form-label">Status</label>
                            <select x-model="form.status" class="form-select-custom">
                                <option value="draft">Draft</option>
                                <option value="published">Published</option>
                                <option value="archived">Archived</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="form-label">Content * <span class="text-gray-400 font-normal">(HTML — use {city} for dynamic city substitution)</span></label>
                        <textarea x-model="form.content" rows="12" class="form-input-custom font-mono text-sm" placeholder="<h2>Heading</h2><p>Page content...</p>"></textarea>
                    </div>

                    {{-- SEO Section --}}
                    <details class="border border-gray-200 rounded-lg" open>
                        <summary class="px-4 py-3 cursor-pointer font-medium text-sm text-gray-700 bg-gray-50 rounded-lg">
                            <svg class="w-4 h-4 inline mr-1 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            SEO Settings
                        </summary>
                        <div class="p-4 space-y-3">
                            <div>
                                <label class="form-label">Meta Title <span class="text-gray-400 font-normal" x-text="'(' + (form.meta_title||'').length + '/60)'"></span></label>
                                <input x-model="form.meta_title" type="text" class="form-input-custom" maxlength="60" placeholder="SEO title">
                            </div>
                            <div>
                                <label class="form-label">Meta Description <span class="text-gray-400 font-normal" x-text="'(' + (form.meta_description||'').length + '/160)'"></span></label>
                                <textarea x-model="form.meta_description" rows="2" class="form-input-custom" maxlength="160" placeholder="SEO description"></textarea>
                            </div>
                            <div>
                                <label class="form-label">Meta Keywords</label>
                                <input x-model="form.meta_keywords" type="text" class="form-input-custom" placeholder="keyword1, keyword2, keyword3">
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="form-label">Canonical URL</label>
                                    <input x-model="form.canonical_url" type="url" class="form-input-custom" placeholder="https://...">
                                </div>
                                <div>
                                    <label class="form-label">Robots</label>
                                    <select x-model="form.robots" class="form-select-custom">
                                        <option value="index,follow">Index, Follow</option>
                                        <option value="noindex,follow">No Index, Follow</option>
                                        <option value="index,nofollow">Index, No Follow</option>
                                        <option value="noindex,nofollow">No Index, No Follow</option>
                                    </select>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="form-label">Schema Type</label>
                                    <select x-model="form.schema_type" class="form-select-custom">
                                        <option value="WebPage">WebPage</option>
                                        <option value="Service">Service</option>
                                        <option value="FAQPage">FAQPage</option>
                                        <option value="Article">Article</option>
                                        <option value="LocalBusiness">LocalBusiness</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="form-label">Sort Order</label>
                                    <input x-model="form.sort_order" type="number" class="form-input-custom" min="0">
                                </div>
                            </div>
                            <div>
                                <label class="form-label">OG Title</label>
                                <input x-model="form.og_title" type="text" class="form-input-custom">
                            </div>
                            <div>
                                <label class="form-label">OG Description</label>
                                <textarea x-model="form.og_description" rows="2" class="form-input-custom"></textarea>
                            </div>
                        </div>
                    </details>
                </div>
            </div>
            <div class="modal-footer">
                <button @click="showModal = false" class="btn-secondary">Cancel</button>
                <button @click="save()" class="btn-primary" :disabled="saving || !form.title || !form.content">
                    <span x-show="saving" class="spinner mr-1" style="width:16px;height:16px;border-width:2px"></span>
                    <span x-text="editing ? 'Update' : 'Create'"></span>
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function seoPagesPage() {
    return {
        items: [], showModal: false, editing: null, saving: false, loading: true,
        filterStatus: '',
        form: {},

        defaultForm() {
            return {
                title: '', slug: '', content: '',
                meta_title: '', meta_description: '', meta_keywords: '',
                canonical_url: '', og_title: '', og_description: '',
                robots: 'index,follow', schema_type: 'WebPage',
                status: 'draft', sort_order: 0,
            };
        },

        slugify(text) {
            return text.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g, '');
        },

        async load() {
            this.loading = true;
            let url = '/admin/seo-pages';
            if (this.filterStatus) url += '?status=' + this.filterStatus;
            const r = await RepairBox.ajax(url);
            this.items = r.data || [];
            this.loading = false;
        },

        openCreate() {
            this.editing = null;
            this.form = this.defaultForm();
            this.showModal = true;
        },

        openEdit(item) {
            this.editing = item.id;
            this.form = { ...item };
            this.showModal = true;
        },

        async save() {
            this.saving = true;
            const url = this.editing ? `/admin/seo-pages/${this.editing}` : '/admin/seo-pages';
            const method = this.editing ? 'PUT' : 'POST';
            const r = await RepairBox.ajax(url, method, this.form);
            if (r.success !== false) {
                RepairBox.toast(this.editing ? 'Page updated' : 'Page created', 'success');
                this.showModal = false;
                this.load();
            }
            this.saving = false;
        },

        async remove(item) {
            if (!await RepairBox.confirm('Delete this SEO page?')) return;
            const r = await RepairBox.ajax(`/admin/seo-pages/${item.id}`, 'DELETE');
            if (r.success !== false) { RepairBox.toast('Page deleted', 'success'); this.load(); }
        }
    };
}
</script>
@endpush
