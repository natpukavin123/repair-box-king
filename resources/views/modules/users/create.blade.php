@extends('layouts.app')
@section('page-title', 'Add User')

@section('content')
<div x-data="createUserPage()" class="max-w-2xl mx-auto">
    <div class="flex items-center justify-between mb-5">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Add User</h2>
            <p class="text-sm text-gray-500 mt-0.5">Create a new user account</p>
        </div>
        <a href="/users" class="btn-secondary inline-flex items-center gap-1.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="space-y-4">
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Name *</label><input x-model="form.name" type="text" class="form-input-custom"></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Email *</label><input x-model="form.email" type="email" class="form-input-custom"></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Password *</label><input x-model="form.password" type="password" class="form-input-custom"></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label><input x-model="form.password_confirmation" type="password" class="form-input-custom"></div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Role *</label>
                    <select x-model="form.role_id" class="form-select-custom">
                        <option value="">Select Role</option>
                        @foreach($roles as $r)
                            <option value="{{ $r->id }}">{{ $r->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select x-model="form.status" class="form-select-custom">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="card-footer flex justify-end gap-3">
            <a href="/users" class="btn-secondary">Cancel</a>
            <button @click="save()" class="btn-primary inline-flex items-center gap-2" :disabled="saving">
                <span x-show="saving" class="spinner"></span>
                Create User
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function createUserPage() {
    return {
        saving: false,
        form: { name: '', email: '', password: '', password_confirmation: '', role_id: '', status: 'active' },
        async save() {
            this.saving = true;
            const r = await RepairBox.ajax('/users', 'POST', this.form);
            this.saving = false;
            if (r.success !== false) { RepairBox.toast('User created', 'success'); window.location.href = '/users'; }
        }
    };
}
</script>
@endpush
