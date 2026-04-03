@extends('layouts.app')
@section('page-title', 'FAQ Management')
@section('content-class', 'flex flex-col')

@section('content')
<div x-data="faqPage()" x-init="load()" class="page-list">
    {{-- Tab switcher --}}
    <div class="flex items-center justify-between mb-4">
        <div class="flex gap-2">
            <button @click="tab='faqs'" class="px-3 py-1.5 rounded-lg text-sm font-medium transition"
                    :class="tab==='faqs' ? 'bg-primary-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'">FAQs</button>
            <button @click="tab='categories'; loadCategories()" class="px-3 py-1.5 rounded-lg text-sm font-medium transition"
                    :class="tab==='categories' ? 'bg-primary-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'">Categories</button>
        </div>
        <button @click="tab === 'faqs' ? openCreateFaq() : openCreateCategory()" class="btn-primary">
            <svg class="w-4 h-4 mr-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span x-text="tab === 'faqs' ? 'Add FAQ' : 'Add Category'"></span>
        </button>
    </div>

    {{-- FAQs List --}}
    <div x-show="tab === 'faqs'" class="card">
        <div class="card-body p-0">
            <div class="table-scroll">
                <table class="data-table">
                    <thead class="sticky top-0 z-10 bg-gray-50">
                        <tr><th>#</th><th>Question</th><th>Category</th><th>Page</th><th>Active</th><th>Order</th><th>Actions</th></tr>
                    </thead>
                    <tbody>
                        <template x-for="(item, i) in faqs" :key="item.id">
                            <tr>
                                <td x-text="i+1"></td>
                                <td class="font-medium max-w-sm truncate" x-text="item.question"></td>
                                <td class="text-xs text-gray-500" x-text="item.category?.name || '—'"></td>
                                <td class="text-xs text-gray-500" x-text="item.page_slug || '—'"></td>
                                <td>
                                    <span class="badge text-xs" :class="item.is_active ? 'badge-success' : 'badge-secondary'"
                                          x-text="item.is_active ? 'Yes' : 'No'"></span>
                                </td>
                                <td class="text-xs" x-text="item.sort_order"></td>
                                <td class="whitespace-nowrap">
                                    <button @click="openEditFaq(item)" class="text-primary-600 hover:text-primary-800 mr-2">
                                        <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                    <button @click="removeFaq(item)" class="text-red-600 hover:text-red-800">
                                        <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </td>
                            </tr>
                        </template>
                        <tr x-show="faqs.length === 0 && !loading"><td colspan="7" class="text-center text-gray-400 py-8">No FAQs yet</td></tr>
                        <template x-if="loading">
                            <template x-for="i in 5" :key="'sk'+i">
                                <tr><td colspan="7"><div class="skeleton h-4 w-full my-1"></div></td></tr>
                            </template>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Categories List --}}
    <div x-show="tab === 'categories'" class="card">
        <div class="card-body p-0">
            <div class="table-scroll">
                <table class="data-table">
                    <thead class="sticky top-0 z-10 bg-gray-50">
                        <tr><th>#</th><th>Name</th><th>Slug</th><th>FAQs</th><th>Active</th><th>Actions</th></tr>
                    </thead>
                    <tbody>
                        <template x-for="(cat, i) in categories" :key="cat.id">
                            <tr>
                                <td x-text="i+1"></td>
                                <td class="font-medium" x-text="cat.name"></td>
                                <td class="text-xs text-gray-500" x-text="cat.slug"></td>
                                <td class="text-xs" x-text="cat.faqs_count"></td>
                                <td>
                                    <span class="badge text-xs" :class="cat.is_active ? 'badge-success' : 'badge-secondary'"
                                          x-text="cat.is_active ? 'Yes' : 'No'"></span>
                                </td>
                                <td class="whitespace-nowrap">
                                    <button @click="openEditCategory(cat)" class="text-primary-600 hover:text-primary-800 mr-2">
                                        <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                    <button @click="removeCategory(cat)" class="text-red-600 hover:text-red-800">
                                        <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </td>
                            </tr>
                        </template>
                        <tr x-show="categories.length === 0 && !loading"><td colspan="6" class="text-center text-gray-400 py-8">No categories yet</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- FAQ Create/Edit Modal --}}
    <div x-show="showFaqModal" class="modal-overlay" x-cloak @keydown.escape.window="showFaqModal = false">
        <div class="modal-container max-w-2xl">
            <div class="modal-header">
                <h3 class="text-lg font-semibold" x-text="editingFaq ? 'Edit FAQ' : 'Add FAQ'"></h3>
                <button @click="showFaqModal = false" class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
            </div>
            <div class="modal-body">
                <div class="space-y-4">
                    <div>
                        <label class="form-label">Question *</label>
                        <input x-model="faqForm.question" type="text" class="form-input-custom" placeholder="e.g. How long does screen replacement take?">
                    </div>
                    <div>
                        <label class="form-label">Answer * <span class="text-gray-400 font-normal">(HTML supported)</span></label>
                        <textarea x-model="faqForm.answer" rows="5" class="form-input-custom" placeholder="Detailed answer..."></textarea>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="form-label">Category</label>
                            <select x-model="faqForm.faq_category_id" class="form-select-custom">
                                <option value="">None</option>
                                <template x-for="cat in categories" :key="cat.id">
                                    <option :value="cat.id" x-text="cat.name"></option>
                                </template>
                            </select>
                        </div>
                        <div>
                            <label class="form-label">Page Slug <span class="text-gray-400 font-normal">(optional)</span></label>
                            <input x-model="faqForm.page_slug" type="text" class="form-input-custom" placeholder="e.g. screen-replacement">
                        </div>
                        <div>
                            <label class="form-label">Sort Order</label>
                            <input x-model="faqForm.sort_order" type="number" class="form-input-custom" min="0">
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <input x-model="faqForm.is_active" type="checkbox" class="rounded border-gray-300 text-primary-600" id="faq_active">
                        <label for="faq_active" class="text-sm text-gray-700">Active</label>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button @click="showFaqModal = false" class="btn-secondary">Cancel</button>
                <button @click="saveFaq()" class="btn-primary" :disabled="saving || !faqForm.question || !faqForm.answer">
                    <span x-show="saving" class="spinner mr-1" style="width:16px;height:16px;border-width:2px"></span>
                    <span x-text="editingFaq ? 'Update' : 'Create'"></span>
                </button>
            </div>
        </div>
    </div>

    {{-- Category Create/Edit Modal --}}
    <div x-show="showCatModal" class="modal-overlay" x-cloak @keydown.escape.window="showCatModal = false">
        <div class="modal-container">
            <div class="modal-header">
                <h3 class="text-lg font-semibold" x-text="editingCat ? 'Edit Category' : 'Add Category'"></h3>
                <button @click="showCatModal = false" class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
            </div>
            <div class="modal-body">
                <div class="space-y-4">
                    <div>
                        <label class="form-label">Name *</label>
                        <input x-model="catForm.name" type="text" class="form-input-custom" placeholder="Category name">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="form-label">Sort Order</label>
                            <input x-model="catForm.sort_order" type="number" class="form-input-custom" min="0">
                        </div>
                        <div class="flex items-center gap-2 pt-6">
                            <input x-model="catForm.is_active" type="checkbox" class="rounded border-gray-300 text-primary-600" id="cat_active">
                            <label for="cat_active" class="text-sm text-gray-700">Active</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button @click="showCatModal = false" class="btn-secondary">Cancel</button>
                <button @click="saveCategory()" class="btn-primary" :disabled="saving || !catForm.name">
                    <span x-show="saving" class="spinner mr-1" style="width:16px;height:16px;border-width:2px"></span>
                    <span x-text="editingCat ? 'Update' : 'Create'"></span>
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function faqPage() {
    return {
        tab: 'faqs', faqs: [], categories: [], loading: true, saving: false,
        showFaqModal: false, editingFaq: null,
        showCatModal: false, editingCat: null,
        faqForm: { question: '', answer: '', faq_category_id: '', sort_order: 0, is_active: true, page_slug: '' },
        catForm: { name: '', sort_order: 0, is_active: true },

        async load() {
            this.loading = true;
            const [faqRes, catRes] = await Promise.all([
                RepairBox.ajax('/admin/faqs'),
                RepairBox.ajax('/admin/faqs/categories'),
            ]);
            this.faqs = faqRes.data || [];
            this.categories = catRes.data || [];
            this.loading = false;
        },

        async loadCategories() {
            const r = await RepairBox.ajax('/admin/faqs/categories');
            this.categories = r.data || [];
        },

        // FAQ CRUD
        openCreateFaq() {
            this.editingFaq = null;
            this.faqForm = { question: '', answer: '', faq_category_id: '', sort_order: 0, is_active: true, page_slug: '' };
            this.showFaqModal = true;
        },
        openEditFaq(item) {
            this.editingFaq = item.id;
            this.faqForm = { ...item, faq_category_id: item.faq_category_id || '' };
            this.showFaqModal = true;
        },
        async saveFaq() {
            this.saving = true;
            const url = this.editingFaq ? `/admin/faqs/${this.editingFaq}` : '/admin/faqs';
            const method = this.editingFaq ? 'PUT' : 'POST';
            const data = { ...this.faqForm };
            if (!data.faq_category_id) data.faq_category_id = null;
            const r = await RepairBox.ajax(url, method, data);
            this.saving = false;
            if (r.success !== false) {
                RepairBox.toast(this.editingFaq ? 'FAQ updated' : 'FAQ created', 'success');
                this.showFaqModal = false;
                this.load();
            }
        },
        async removeFaq(item) {
            if (!await RepairBox.confirm('Delete this FAQ?')) return;
            const r = await RepairBox.ajax(`/admin/faqs/${item.id}`, 'DELETE');
            if (r.success !== false) { RepairBox.toast('FAQ deleted', 'success'); this.load(); }
        },

        // Category CRUD
        openCreateCategory() {
            this.editingCat = null;
            this.catForm = { name: '', sort_order: 0, is_active: true };
            this.showCatModal = true;
        },
        openEditCategory(cat) {
            this.editingCat = cat.id;
            this.catForm = { name: cat.name, sort_order: cat.sort_order, is_active: cat.is_active };
            this.showCatModal = true;
        },
        async saveCategory() {
            this.saving = true;
            const url = this.editingCat ? `/admin/faqs/categories/${this.editingCat}` : '/admin/faqs/categories';
            const method = this.editingCat ? 'PUT' : 'POST';
            const r = await RepairBox.ajax(url, method, this.catForm);
            this.saving = false;
            if (r.success !== false) {
                RepairBox.toast(this.editingCat ? 'Category updated' : 'Category created', 'success');
                this.showCatModal = false;
                this.load();
            }
        },
        async removeCategory(cat) {
            if (!await RepairBox.confirm('Delete this category? FAQs in it will become uncategorized.')) return;
            const r = await RepairBox.ajax(`/admin/faqs/categories/${cat.id}`, 'DELETE');
            if (r.success !== false) { RepairBox.toast('Category deleted', 'success'); this.load(); }
        },
    };
}
</script>
@endpush
