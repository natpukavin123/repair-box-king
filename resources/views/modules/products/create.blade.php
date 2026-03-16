@extends('layouts.app')
@section('page-title', 'Add Product')

@section('content')
<div x-data="createProductPage()" class="max-w-3xl mx-auto">
    <div class="flex items-center justify-between mb-5">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Add Product</h2>
            <p class="text-sm text-gray-500 mt-0.5">Create a new product in inventory</p>
        </div>
        <a href="/products" class="btn-secondary inline-flex items-center gap-1.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Name *</label><input x-model="form.name" type="text" class="form-input-custom"></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">SKU</label><input x-model="form.sku" type="text" class="form-input-custom"></div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                    <select x-model="form.category_id" @change="loadSubcategories()" class="form-select-custom">
                        <option value="">Select</option>
                        @foreach($categories as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Subcategory</label>
                    <select x-model="form.subcategory_id" class="form-select-custom">
                        <option value="">Select</option>
                        <template x-for="s in subcategories" :key="s.id"><option :value="s.id" x-text="s.name"></option></template>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Brand</label>
                    <select x-model="form.brand_id" class="form-select-custom">
                        <option value="">Select</option>
                        @foreach($brands as $b)
                            <option value="{{ $b->id }}">{{ $b->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Purchase Price *</label><input x-model="form.purchase_price" type="number" step="0.01" class="form-input-custom"></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">MRP *</label><input x-model="form.mrp" type="number" step="0.01" class="form-input-custom"></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Selling Price *</label><input x-model="form.selling_price" type="number" step="0.01" class="form-input-custom"></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Barcode</label><input x-model="form.barcode" type="text" class="form-input-custom"></div>

                <div class="md:col-span-2"><label class="block text-sm font-medium text-gray-700 mb-1">Description</label><textarea x-model="form.description" class="form-input-custom" rows="2"></textarea></div>

                {{-- Image Upload --}}
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Product Images</label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        {{-- Main Image --}}
                        <div>
                            <p class="text-xs text-gray-500 mb-1.5 font-medium">Main Image <span class="text-gray-400 font-normal">(full size)</span></p>
                            <div class="relative group border-2 border-dashed border-gray-300 rounded-xl p-4 text-center cursor-pointer hover:border-primary-400 hover:bg-primary-50 transition-all"
                                 @click="$refs.imageInput.click()" @dragover.prevent @drop.prevent="handleFileDrop('image', $event)">
                                <template x-if="!imagePreview">
                                    <div class="py-4">
                                        <svg class="w-10 h-10 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        <p class="text-sm text-gray-500">Click or drag to upload</p>
                                        <p class="text-xs text-gray-400 mt-0.5">PNG, JPG, WEBP up to 4 MB</p>
                                    </div>
                                </template>
                                <template x-if="imagePreview">
                                    <div class="relative">
                                        <img :src="imagePreview" class="max-h-40 mx-auto rounded-lg object-contain">
                                        <button type="button" @click.stop="imageFile=null; imagePreview=null; $refs.imageInput.value=''"
                                                class="absolute top-1 right-1 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs hover:bg-red-600 shadow">✕</button>
                                    </div>
                                </template>
                                <input x-ref="imageInput" type="file" accept="image/*" class="hidden"
                                       @change="handleFilePick('image', $event)">
                            </div>
                        </div>
                        {{-- Thumbnail --}}
                        <div>
                            <p class="text-xs text-gray-500 mb-1.5 font-medium">Thumbnail <span class="text-gray-400 font-normal">(auto-generated if not set)</span></p>
                            <div class="relative group border-2 border-dashed border-gray-300 rounded-xl p-4 text-center cursor-pointer hover:border-primary-400 hover:bg-primary-50 transition-all"
                                 @click="$refs.thumbInput.click()" @dragover.prevent @drop.prevent="handleFileDrop('thumb', $event)">
                                <template x-if="!thumbPreview">
                                    <div class="py-4">
                                        <svg class="w-10 h-10 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        <p class="text-sm text-gray-500">Click or drag to upload</p>
                                        <p class="text-xs text-gray-400 mt-0.5">PNG, JPG, WEBP up to 2 MB</p>
                                    </div>
                                </template>
                                <template x-if="thumbPreview">
                                    <div class="relative">
                                        <img :src="thumbPreview" class="max-h-40 mx-auto rounded-lg object-contain">
                                        <button type="button" @click.stop="thumbFile=null; thumbPreview=null; $refs.thumbInput.value=''"
                                                class="absolute top-1 right-1 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs hover:bg-red-600 shadow">✕</button>
                                    </div>
                                </template>
                                <input x-ref="thumbInput" type="file" accept="image/*" class="hidden"
                                       @change="handleFilePick('thumb', $event)">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer flex justify-end gap-3">
            <a href="/products" class="btn-secondary">Cancel</a>
            <button @click="save()" class="btn-primary inline-flex items-center gap-2" :disabled="saving">
                <span x-show="saving" class="spinner"></span>
                Create Product
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function createProductPage() {
    return {
        saving: false,
        subcategories: [],
        imageFile: null, imagePreview: null, thumbFile: null, thumbPreview: null,
        form: { name: '', sku: '', category_id: '', subcategory_id: '', brand_id: '', purchase_price: '', mrp: '', selling_price: '', barcode: '', description: '' },

        handleFilePick(type, e) {
            const file = e.target.files[0];
            if (!file) return;
            if (type === 'image') { this.imageFile = file; const r = new FileReader(); r.onload = ev => this.imagePreview = ev.target.result; r.readAsDataURL(file); }
            else { this.thumbFile = file; const r = new FileReader(); r.onload = ev => this.thumbPreview = ev.target.result; r.readAsDataURL(file); }
        },
        handleFileDrop(type, e) {
            const file = e.dataTransfer.files[0];
            if (!file || !file.type.startsWith('image/')) return;
            if (type === 'image') { this.imageFile = file; const r = new FileReader(); r.onload = ev => this.imagePreview = ev.target.result; r.readAsDataURL(file); }
            else { this.thumbFile = file; const r = new FileReader(); r.onload = ev => this.thumbPreview = ev.target.result; r.readAsDataURL(file); }
        },
        async loadSubcategories() {
            if (!this.form.category_id) { this.subcategories = []; return; }
            const r = await RepairBox.ajax(`/subcategories/by-category/${this.form.category_id}`);
            if (r.data) this.subcategories = r.data;
        },
        async save() {
            this.saving = true;
            const r = await RepairBox.ajax('/products', 'POST', this.form);
            if (r.success !== false && r.data) {
                const id = r.data.id;
                if (this.imageFile || this.thumbFile) {
                    const fd = new FormData();
                    if (this.imageFile) fd.append('image', this.imageFile);
                    if (this.thumbFile) fd.append('thumbnail', this.thumbFile);
                    await RepairBox.upload(`/products/${id}/upload-image`, fd);
                }
                RepairBox.toast('Product created', 'success');
                window.location.href = '/products';
            }
            this.saving = false;
        }
    };
}
</script>
@endpush
