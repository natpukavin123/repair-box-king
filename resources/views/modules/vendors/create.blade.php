@extends('layouts.app')
@section('page-title', 'Add Vendor')

@section('content')
<div x-data="createVendorPage()" class="max-w-2xl mx-auto">
    <div class="flex flex-col gap-3 mb-5 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Add Vendor</h2>
            <p class="text-sm text-gray-500 mt-0.5">Create a new vendor record</p>
        </div>
        <a href="/vendors" class="btn-secondary inline-flex w-full items-center justify-center gap-1.5 sm:w-auto">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back
        </a>
    </div>

    <div class="card">
        <div class="card-body space-y-5">
            <x-ui.form-section title="Vendor Details" description="Capture the supplier-side contact and specialization details in the shared form layout.">
                <x-ui.input-field label="Name" x-model="form.name" required />
                <x-ui.input-field label="Phone" x-model="form.phone" />
                <x-ui.input-field label="Specialization" x-model="form.specialization" />
                <x-ui.textarea-field label="Address" x-model="form.address" rows="2" />
            </x-ui.form-section>

            {{-- Image Upload --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Vendor Image <span class="text-gray-400 font-normal">(optional)</span></label>
                <div class="border-2 border-dashed border-gray-300 rounded-xl p-4 text-center cursor-pointer hover:border-primary-400 hover:bg-primary-50 transition-all"
                     @click="$refs.imageInput.click()" @dragover.prevent @drop.prevent="handleDrop($event)">
                    <template x-if="imagePreview">
                        <div class="relative inline-block">
                            <img :src="imagePreview" class="max-h-28 mx-auto rounded-lg object-contain">
                            <button type="button" @click.stop="imageFile=null; imagePreview=null; $refs.imageInput.value=''"
                                    class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs hover:bg-red-600">&#x2715;</button>
                        </div>
                    </template>
                    <template x-if="!imagePreview">
                        <div class="py-3">
                            <svg class="w-8 h-8 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <p class="text-xs text-gray-400">Click or drag & drop to upload</p>
                        </div>
                    </template>
                    <input x-ref="imageInput" type="file" accept="image/*" class="hidden" @change="handlePick($event)">
                </div>
            </div>
        </div>
        <div class="card-footer flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
            <a href="/vendors" class="btn-secondary w-full text-center sm:w-auto">Cancel</a>
            <button @click="save()" class="btn-primary inline-flex w-full items-center justify-center gap-2 sm:w-auto" :disabled="saving">
                <span x-show="saving" class="spinner"></span>
                Create Vendor
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function createVendorPage() {
    return {
        saving: false,
        form: { name: '', phone: '', address: '', specialization: '' },
        imageFile: null, imagePreview: null,

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
            const r = await RepairBox.ajax('/admin/vendors', 'POST', this.form);
            if (r.success !== false && r.data) {
                if (this.imageFile) {
                    const fd = new FormData();
                    fd.append('image', this.imageFile);
                    await RepairBox.upload(`/admin/vendors/${r.data.id}/upload-image`, fd);
                }
                RepairBox.toast('Vendor created', 'success');
                window.location.href = '/admin/vendors';
            }
            this.saving = false;
        }
    };
}
</script>
@endpush
