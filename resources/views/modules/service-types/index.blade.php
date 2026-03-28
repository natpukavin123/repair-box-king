@extends('layouts.app')
@section('page-title', 'Service Types')
@section('content-class', 'workspace-content')

@section('content')
<style>
    .st-workspace { gap: 0.7rem; }

    .st-workspace .st-toolbar {
        border: 1px solid rgba(148, 163, 184, 0.18);
        border-radius: 1.2rem;
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.96), rgba(244, 247, 255, 0.88));
        box-shadow: 0 18px 42px -34px rgba(15, 23, 42, 0.34);
        backdrop-filter: blur(16px);
        padding: 0.55rem;
    }

    .st-workspace .st-search-input,
    .st-workspace .st-form-input {
        min-height: 2.7rem;
        border-radius: 0.95rem;
        border-color: rgba(148, 163, 184, 0.22);
        background: rgba(255, 255, 255, 0.94);
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.7), 0 12px 28px -24px rgba(15, 23, 42, 0.28);
    }

    .st-workspace .st-search-input {
        padding-top: 0.72rem;
        padding-bottom: 0.72rem;
    }

    .st-workspace .st-panel {
        border-radius: 1.35rem;
        border-color: rgba(148, 163, 184, 0.16);
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.9), rgba(250, 252, 255, 0.82));
        box-shadow: 0 26px 60px -42px rgba(15, 23, 42, 0.38);
    }

    .st-workspace .st-panel .card-header {
        padding: 0.9rem 1rem;
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.72), rgba(241, 245, 255, 0.48));
    }

    .st-workspace .st-panel .card-body { padding: 1rem; }

    .st-workspace .st-table-shell {
        padding: 0.35rem 0.4rem 0.15rem;
    }

    .st-workspace .st-table-shell .data-table thead {
        background: linear-gradient(180deg, rgba(248, 250, 252, 0.98), rgba(238, 242, 255, 0.9));
    }

    .st-workspace .st-table-shell .data-table th {
        padding: 0.75rem 0.9rem;
        font-size: 0.65rem;
        letter-spacing: 0.14em;
    }

    .st-workspace .st-table-shell .data-table td {
        padding: 0.8rem 0.9rem;
        font-size: 0.88rem;
    }

    .st-workspace .st-table-shell .data-table tbody tr {
        border-top-color: rgba(226, 232, 240, 0.92);
    }

    .st-workspace .st-table-shell .data-table tbody tr:hover {
        background: rgba(37, 99, 235, 0.04);
    }

    .st-workspace .st-form-scroll > div {
        padding: 0.95rem 1rem;
    }

    @media (max-width: 1023px) {
        .st-workspace { gap: 0.6rem; }
        .st-workspace .st-toolbar { padding: 0.45rem; }
        .st-workspace .st-panel .card-header,
        .st-workspace .st-panel .card-body,
        .st-workspace .st-form-scroll > div {
            padding-left: 0.85rem;
            padding-right: 0.85rem;
        }
        .st-workspace .st-table-shell .data-table th,
        .st-workspace .st-table-shell .data-table td {
            padding-left: 0.75rem;
            padding-right: 0.75rem;
        }
    }

    @media (max-width: 767px) {
        .st-workspace { gap: 0.5rem; }
        .st-workspace .st-toolbar { padding: 0.35rem; border-radius: 1rem; }
        .st-workspace .st-search-input,
        .st-workspace .st-form-input { min-height: 2.5rem; border-radius: 0.82rem; }
        .st-workspace .st-panel { border-radius: 1.1rem; }
        .st-workspace .st-panel .card-header,
        .st-workspace .st-panel .card-body,
        .st-workspace .st-form-scroll > div {
            padding-left: 0.72rem;
            padding-right: 0.72rem;
        }
        .st-workspace .st-table-shell .data-table th,
        .st-workspace .st-table-shell .data-table td {
            padding-left: 0.68rem;
            padding-right: 0.68rem;
        }
    }

    @media (min-width: 1024px) {
        .st-workspace .st-table-shell .data-table th { padding: 0.65rem 0.8rem; }
        .st-workspace .st-table-shell .data-table td { padding: 0.68rem 0.8rem; }
    }
</style>

<div x-data="serviceTypesPage()" x-init="init()" class="workspace-screen st-workspace w-full">
    <div class="grid w-full lg:flex-1 lg:min-h-0 grid-cols-1 gap-2 lg:grid-cols-3 lg:grid-rows-1">

        {{-- ===== LEFT: Service Types List (table) ===== --}}
        <div class="flex lg:min-h-0 flex-col lg:overflow-hidden lg:col-span-2">

            {{-- Search toolbar --}}
            <div class="st-toolbar mb-1 flex shrink-0 flex-col gap-2 sm:flex-row sm:items-center">
                <div class="relative flex-1">
                    <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input x-model="filters.search" @input.debounce.400ms="load()" type="text"
                        class="form-input-custom st-search-input pl-10 pr-10 w-full text-sm" placeholder="Search service types...">
                    <button x-show="filters.search" @click="filters.search = ''; load()" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>

            {{-- Table --}}
            <div class="card st-panel relative flex min-h-0 flex-1 flex-col" style="z-index:0;">
                {{-- Overlay loader --}}
                <div x-show="loading && !firstLoad" x-cloak x-transition.opacity
                    class="absolute inset-0 bg-white/70 flex items-center justify-center rounded-xl" style="z-index:10">
                    <div class="flex flex-col items-center gap-2">
                        <svg class="w-7 h-7 text-primary-500 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                        <span class="text-xs text-gray-400 font-medium">Loading...</span>
                    </div>
                </div>

                <div class="card-header flex shrink-0 items-center justify-between py-1.5">
                    <h3 class="font-semibold text-gray-800 text-sm">
                        Service Types (<span x-text="total"></span>)
                    </h3>
                    <button @click="load()" class="text-xs text-primary-600 hover:text-primary-800 font-medium">Refresh</button>
                </div>

                <div class="st-table-shell min-h-0 flex-1 overflow-hidden">
                    <div class="h-full overflow-y-auto overscroll-contain">
                    <table class="data-table w-full">
                        <thead class="sticky top-0 z-10">
                            <tr class="bg-gray-50">
                                <th class="px-3 py-2 text-left text-[11px] font-semibold text-gray-600 uppercase">Image</th>
                                <th class="px-3 py-2 text-left text-[11px] font-semibold text-gray-600 uppercase">Name</th>
                                <th class="px-3 py-2 text-left text-[11px] font-semibold text-gray-600 uppercase">Default Price</th>
                                <th class="px-3 py-2 text-left text-[11px] font-semibold text-gray-600 uppercase">Status</th>
                                <th class="px-3 py-2 text-center text-[11px] font-semibold text-gray-600 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <template x-for="item in items" :key="item.id">
                                <tr class="hover:bg-gray-50/50 transition-colors cursor-pointer" @click="editItem(item)">
                                    <td class="px-3 py-2">
                                        <template x-if="item.thumbnail">
                                            <img :src="RepairBox.imageUrl(item.thumbnail)" class="w-10 h-10 rounded-lg object-cover border border-gray-200 shadow-sm">
                                        </template>
                                        <template x-if="!item.thumbnail">
                                            <div class="w-10 h-10 rounded-lg bg-gray-100 border border-gray-200 flex items-center justify-center">
                                                <svg class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                            </div>
                                        </template>
                                    </td>
                                    <td class="px-3 py-2">
                                        <div class="font-medium text-gray-800 text-sm" x-text="item.name"></div>
                                        <div class="text-[11px] leading-tight text-gray-400 truncate max-w-[200px]" x-text="item.description || ''"></div>
                                    </td>
                                    <td class="px-3 py-2">
                                        <span class="text-sm" x-text="item.default_price ? RepairBox.formatCurrency(item.default_price) : '-'"></span>
                                    </td>
                                    <td class="px-3 py-2">
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold"
                                            :class="(item.status || 'active') === 'active' ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-600'"
                                            x-text="(item.status || 'active')"></span>
                                    </td>
                                    <td class="px-3 py-2 text-center" @click.stop>
                                        <button @click="editItem(item)" class="p-1.5 rounded-lg text-gray-400 hover:text-primary-600 hover:bg-primary-50 transition" title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                            <tr x-show="items.length === 0 && !loading">
                                <td colspan="5" class="text-center py-12">
                                    <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                                    <p class="text-gray-400 font-medium">No service types found</p>
                                    <p class="text-gray-300 text-sm mt-1">Create one using the form on the right</p>
                                </td>
                            </tr>
                            <template x-if="loading && firstLoad">
                                <template x-for="i in 6" :key="'sk'+i">
                                    <tr>
                                        <td class="px-3 py-2"><div class="skeleton h-10 w-10 rounded-lg"></div></td>
                                        <td class="px-3 py-2"><div class="skeleton h-3 w-28"></div></td>
                                        <td class="px-3 py-2"><div class="skeleton h-3 w-16"></div></td>
                                        <td class="px-3 py-2"><div class="skeleton h-3 w-14 rounded-full"></div></td>
                                        <td class="px-3 py-2"><div class="skeleton h-3 w-10"></div></td>
                                    </tr>
                                </template>
                            </template>
                        </tbody>
                    </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- ===== RIGHT: Add/Edit Service Type Form ===== --}}
        <div class="relative order-first flex lg:min-h-0 flex-col gap-1.5 lg:order-none" style="z-index:10;">

            <div class="card st-panel relative flex min-h-0 flex-1 flex-col">
                <div class="card-header flex shrink-0 items-center justify-between py-1.5">
                    <h3 class="font-semibold text-gray-800 text-sm">
                        <svg class="w-4 h-4 inline mr-1 -mt-0.5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                        <span x-text="editing ? 'Edit Service Type' : 'New Service Type'"></span>
                    </h3>
                    <button x-show="editing || form.name" @click="resetForm()" class="text-xs text-red-400 hover:text-red-600">Clear</button>
                </div>

                <div class="st-form-scroll min-h-0 flex-1 overflow-y-auto overscroll-contain">
                    <div class="px-4 py-2 space-y-3">
                        <div>
                            <label class="text-xs font-medium text-gray-600 mb-1 block">Name *</label>
                            <input x-model="form.name" type="text" class="form-input-custom st-form-input text-sm" placeholder="e.g. Screen Replacement">
                        </div>
                        <div>
                            <label class="text-xs font-medium text-gray-600 mb-1 block">Default Price</label>
                            <input x-model="form.default_price" type="number" step="0.01" class="form-input-custom st-form-input text-sm" placeholder="0.00">
                        </div>
                        <div>
                            <label class="text-xs font-medium text-gray-600 mb-1 block">Description</label>
                            <textarea x-model="form.description" rows="2" class="form-input-custom st-form-input text-sm"
                                placeholder="Brief description of this service type..."></textarea>
                        </div>

                        {{-- Image Upload --}}
                        <div>
                            <label class="text-xs font-medium text-gray-600 mb-2 block">Service Images</label>
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <p class="text-[11px] text-gray-500 mb-1 font-medium">Main Image</p>
                                    <div class="border-2 border-dashed border-gray-300 rounded-xl p-2 text-center cursor-pointer hover:border-primary-400 hover:bg-primary-50 transition-all"
                                         @click="$refs.imageInput.click()" @dragover.prevent @drop.prevent="handleDrop('image', $event)">
                                        <template x-if="imagePreview">
                                            <div class="relative">
                                                <img :src="imagePreview" class="max-h-20 mx-auto rounded-lg object-contain">
                                                <button type="button" @click.stop="imageFile=null; imagePreview=null; $refs.imageInput.value=''"
                                                        class="absolute top-0 right-0 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs hover:bg-red-600">&#x2715;</button>
                                            </div>
                                        </template>
                                        <template x-if="!imagePreview">
                                            <div class="py-2">
                                                <svg class="w-6 h-6 text-gray-300 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                <p class="text-[10px] text-gray-400">Click to upload</p>
                                            </div>
                                        </template>
                                        <input x-ref="imageInput" type="file" accept="image/*" class="hidden" @change="handlePick('image', $event)">
                                    </div>
                                </div>
                                <div>
                                    <p class="text-[11px] text-gray-500 mb-1 font-medium">Thumbnail <span class="text-gray-400">(auto)</span></p>
                                    <div class="border-2 border-dashed border-gray-300 rounded-xl p-2 text-center cursor-pointer hover:border-primary-400 hover:bg-primary-50 transition-all"
                                         @click="$refs.thumbInput.click()" @dragover.prevent @drop.prevent="handleDrop('thumb', $event)">
                                        <template x-if="thumbPreview">
                                            <div class="relative">
                                                <img :src="thumbPreview" class="max-h-20 mx-auto rounded-lg object-contain">
                                                <button type="button" @click.stop="thumbFile=null; thumbPreview=null; $refs.thumbInput.value=''"
                                                        class="absolute top-0 right-0 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs hover:bg-red-600">&#x2715;</button>
                                            </div>
                                        </template>
                                        <template x-if="!thumbPreview">
                                            <div class="py-2">
                                                <svg class="w-6 h-6 text-gray-300 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                <p class="text-[10px] text-gray-400">Click to upload</p>
                                            </div>
                                        </template>
                                        <input x-ref="thumbInput" type="file" accept="image/*" class="hidden" @change="handlePick('thumb', $event)">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <template x-if="editing">
                            <div>
                                <label class="text-xs font-medium text-gray-600 mb-1 block">Status</label>
                                <select x-model="form.status" class="form-select-custom st-form-input text-sm">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                        </template>
                    </div>
                </div>

                <div class="shrink-0 border-t px-4 py-3">
                    <button @click="save()" class="btn-primary w-full py-3 text-base font-semibold" :disabled="saving">
                        <span x-show="saving" class="spinner mr-2"></span>
                        <svg x-show="!saving" class="w-4 h-4 inline mr-1.5 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span x-text="editing ? 'Update Service Type' : 'Save Service Type'"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function serviceTypesPage() {
    return {
        items: [],
        loading: false,
        firstLoad: true,
        saving: false,
        editing: null,
        total: 0,
        filters: { search: '' },
        form: { name: '', default_price: '', description: '' },

        // Image upload
        imageFile: null, imagePreview: null,
        thumbFile: null, thumbPreview: null,

        async init() {
            await this.load();
        },

        resetForm() {
            this.form = { name: '', default_price: '', description: '' };
            this.editing = null;
            this.imageFile = null; this.imagePreview = null;
            this.thumbFile = null; this.thumbPreview = null;
        },

        editItem(item) {
            this.editing = item.id;
            this.form = {
                name: item.name,
                default_price: item.default_price || '',
                description: item.description || '',
                status: item.status || 'active'
            };
            this.imageFile = null;
            this.thumbFile = null;
            this.imagePreview = RepairBox.imageUrl(item.image);
            this.thumbPreview = RepairBox.imageUrl(item.thumbnail);
        },

        // File handling
        handlePick(type, e) {
            const file = e.target.files[0];
            if (!file) return;
            const reader = new FileReader();
            if (type === 'image') {
                this.imageFile = file;
                reader.onload = ev => this.imagePreview = ev.target.result;
            } else {
                this.thumbFile = file;
                reader.onload = ev => this.thumbPreview = ev.target.result;
            }
            reader.readAsDataURL(file);
        },

        handleDrop(type, e) {
            const file = e.dataTransfer.files[0];
            if (!file || !file.type.startsWith('image/')) return;
            const reader = new FileReader();
            if (type === 'image') {
                this.imageFile = file;
                reader.onload = ev => this.imagePreview = ev.target.result;
            } else {
                this.thumbFile = file;
                reader.onload = ev => this.thumbPreview = ev.target.result;
            }
            reader.readAsDataURL(file);
        },

        // Data
        async load() {
            this.loading = true;
            const r = await RepairBox.ajax('/admin/service-types', 'GET', {
                search: this.filters.search || undefined
            });
            if (Array.isArray(r)) {
                this.items = r;
                this.total = r.length;
            } else if (r.data) {
                this.items = Array.isArray(r.data) ? r.data : [];
                this.total = this.items.length;
            }
            this.loading = false;
            this.firstLoad = false;
        },

        async save() {
            if (!this.form.name.trim()) {
                return RepairBox.toast('Name is required', 'error');
            }
            this.saving = true;
            const url = this.editing ? `/service-types/${this.editing}` : '/admin/service-types';
            const method = this.editing ? 'PUT' : 'POST';
            const r = await RepairBox.ajax(url, method, this.form);

            if (r.success !== false && r.data) {
                const id = r.data.id || this.editing;

                // Upload images if any
                if (this.imageFile || this.thumbFile) {
                    const fd = new FormData();
                    if (this.imageFile) fd.append('image', this.imageFile);
                    if (this.thumbFile) fd.append('thumbnail', this.thumbFile);
                    await RepairBox.upload(`/admin/service-types/${id}/upload-image`, fd);
                }

                RepairBox.toast(this.editing ? 'Service type updated' : 'Service type added', 'success');
                this.resetForm();
                await this.load();
            }
            this.saving = false;
        }
    };
}
</script>
@endpush
