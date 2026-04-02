@extends('layouts.app')
@section('page-title', 'Parts')
@section('content-class', 'flex flex-col')

@section('content')
<div x-data="partsPage()" x-init="init()" class="page-list">
    <div class="flex items-center justify-between mb-4">
        <input x-model="search" @input.debounce.300ms="load()" type="text" class="form-input-custom w-64" placeholder="Search parts...">
        <a href="/parts/create" class="btn-primary">
            <svg class="w-4 h-4 mr-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg> Add Part
        </a>
    </div>
    <div class="card">
        <div class="card-body p-0">
            <div class="table-scroll">
                <table class="data-table">
                    <thead class="sticky top-0 z-10 bg-gray-50"><tr><th>#</th><th>Name</th><th>Image</th><th>SKU</th><th>Cost Price</th><th>Selling Price</th><th>Status</th><th>Actions</th></tr></thead>
                    <tbody>
                        <template x-for="(item, i) in items" :key="item.id">
                            <tr>
                                <td x-text="i+1"></td>
                                <td class="font-medium" x-text="item.name"></td>
                                <td>
                                    <template x-if="item.thumbnail || item.image">
                                        <img :src="RepairBox.imageUrl(item.thumbnail || item.image)" class="w-8 h-8 rounded object-cover">
                                    </template>
                                    <template x-if="!item.thumbnail && !item.image">
                                        <span class="text-gray-400">-</span>
                                    </template>
                                </td>
                                <td x-text="item.sku || '-'"></td>
                                <td x-text="'₹' + Number(item.cost_price).toFixed(2)"></td>
                                <td x-text="'₹' + Number(item.selling_price).toFixed(2)"></td>
                                <td><span class="badge" :class="item.status === 'active' ? 'badge-success' : 'badge-danger'" x-text="item.status"></span></td>
                                <td class="whitespace-nowrap">
                                    <button @click="edit(item)" class="text-primary-600 hover:text-primary-800 mr-2" title="Edit"><svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></button>
                                    <button @click="remove(item)" class="text-red-600 hover:text-red-800" title="Delete"><svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
                                </td>
                            </tr>
                        </template>
                        <tr x-show="items.length === 0 && !loading"><td colspan="8" class="text-center text-gray-400 py-8">No parts found</td></tr>
                        <template x-if="loading">
                            <template x-for="i in 10" :key="'sk'+i">
                                <tr>
                                    <td><div class="skeleton h-3 w-8"></div></td>
                                    <td><div class="skeleton h-3 w-32"></div></td>
                                    <td><div class="skeleton h-3 w-24"></div></td>
                                    <td><div class="skeleton h-3 w-20"></div></td>
                                    <td><div class="skeleton h-3 w-20"></div></td>
                                    <td><div class="skeleton h-3 w-20 rounded-full"></div></td>
                                    <td><div class="skeleton h-3 w-16"></div></td>
                                </tr>
                            </template>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div x-show="showModal" class="modal-overlay" x-cloak>
        <div class="modal-container">
            <div class="modal-header">
                <h3 class="text-lg font-semibold">Edit Part</h3>
                <button @click="showModal = false" class="text-gray-400 hover:text-gray-600">&times;</button>
            </div>
            <div class="modal-body">
                <div class="space-y-4">
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Name *</label><input x-model="form.name" type="text" class="form-input-custom"></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">SKU</label><input x-model="form.sku" type="text" class="form-input-custom"></div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div><label class="block text-sm font-medium text-gray-700 mb-1">Cost Price</label><input x-model="form.cost_price" type="number" step="0.01" class="form-input-custom"></div>
                        <div><label class="block text-sm font-medium text-gray-700 mb-1">Selling Price</label><input x-model="form.selling_price" type="number" step="0.01" class="form-input-custom"></div>
                    </div>

                    {{-- Image Upload --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Part Image <span class="text-gray-400 font-normal">(optional)</span></label>
                        <div class="border-2 border-dashed border-gray-300 rounded-xl p-3 text-center cursor-pointer hover:border-primary-400 hover:bg-primary-50 transition-all"
                             @click="$refs.editImageInput.click()" @dragover.prevent @drop.prevent="handleDrop($event)">
                            <template x-if="imagePreview">
                                <div class="relative inline-block">
                                    <img :src="imagePreview" class="max-h-24 mx-auto rounded-lg object-contain">
                                    <button type="button" @click.stop="imageFile=null; imagePreview=null; $refs.editImageInput.value=''"
                                            class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs hover:bg-red-600">&#x2715;</button>
                                </div>
                            </template>
                            <template x-if="!imagePreview">
                                <div class="py-2">
                                    <svg class="w-6 h-6 text-gray-300 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    <p class="text-[10px] text-gray-400">Click or drag & drop</p>
                                </div>
                            </template>
                            <input x-ref="editImageInput" type="file" accept="image/*" class="hidden" @change="handlePick($event)">
                        </div>
                    </div>

                    <div x-show="editing">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select x-model="form.status" class="form-select-custom">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button @click="showModal = false" class="btn-secondary">Cancel</button>
                <button @click="save()" class="btn-primary" :disabled="saving">
                    <span x-show="saving" class="spinner mr-1"></span>
                    Update
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function partsPage() {
    return {
        items: [], showModal: false, editing: null, saving: false, search: '', loading: true,
        form: { name: '', sku: '', cost_price: '', selling_price: '' },
        imageFile: null, imagePreview: null,

        init() {
            const p = new URLSearchParams(window.location.search);
            if (p.has('search')) this.search = p.get('search');
            this.load();
        },
        updateUrl() {
            const params = new URLSearchParams();
            if (this.search) params.set('search', this.search);
            const qs = params.toString();
            history.replaceState(null, '', window.location.pathname + (qs ? '?' + qs : ''));
        },
        async load() {
            this.loading = true;
            const r = await RepairBox.ajax('/admin/parts?search=' + encodeURIComponent(this.search));
            if (r.data) this.items = r.data;
            this.updateUrl();
            this.loading = false;
        },
        edit(item) {
            this.editing = item.id;
            this.form = {
                name: item.name,
                sku: item.sku || '',
                cost_price: item.cost_price,
                selling_price: item.selling_price,
                status: item.status,
            };
            this.imageFile = null;
            this.imagePreview = RepairBox.imageUrl(item.thumbnail || item.image);
            this.showModal = true;
        },

        handlePick(e) {
            const file = e.target.files[0];
            if (!file) return;
            this.imageFile = file;
            const reader = new FileReader();
            reader.onload = ev => this.imagePreview = ev.target.result;
            reader.readAsDataURL(file);
        },
        handleDrop(e) {
            const file = e.dataTransfer.files[0];
            if (!file || !file.type.startsWith('image/')) return;
            this.imageFile = file;
            const reader = new FileReader();
            reader.onload = ev => this.imagePreview = ev.target.result;
            reader.readAsDataURL(file);
        },

        async save() {
            this.saving = true;
            const r = await RepairBox.ajax(`/admin/parts/${this.editing}`, 'PUT', this.form);
            if (r.success !== false) {
                if (this.imageFile) {
                    const fd = new FormData();
                    fd.append('image', this.imageFile);
                    await RepairBox.upload(`/admin/parts/${this.editing}/upload-image`, fd);
                }
                RepairBox.toast('Updated', 'success');
                this.showModal = false;
                this.load();
            }
            this.saving = false;
        },
        async remove(item) {
            if (!await RepairBox.confirm('Delete this part?')) return;
            const r = await RepairBox.ajax(`/admin/parts/${item.id}`, 'DELETE');
            if (r.success !== false) { RepairBox.toast('Deleted', 'success'); this.load(); }
        }
    };
}
</script>
@endpush
