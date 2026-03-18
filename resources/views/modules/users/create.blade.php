@extends('layouts.app')
@section('page-title', 'Add User')

@section('content')
<div x-data="createUserPage()" class="max-w-2xl mx-auto">
    <div class="flex flex-col gap-3 mb-5 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Add User</h2>
            <p class="text-sm text-gray-500 mt-0.5">Create a new user account</p>
        </div>
        <a href="/users" class="btn-secondary inline-flex w-full items-center justify-center gap-1.5 sm:w-auto">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back
        </a>
    </div>

    <div class="card">
        <div class="card-body space-y-5">
            <x-ui.form-section title="Account Details" description="Create team access with the same structured fields used in other admin screens.">
                <x-ui.input-field label="Name" x-model="form.name" required />
                <x-ui.input-field label="Email" x-model="form.email" type="email" required />
                <x-ui.input-field label="Password" x-model="form.password" type="password" required />
                <x-ui.input-field label="Confirm Password" x-model="form.password_confirmation" type="password" />
                <x-ui.select-field label="Role" x-model="form.role_id" required>
                    <option value="">Select Role</option>
                    @foreach($roles as $r)
                        <option value="{{ $r->id }}">{{ $r->name }}</option>
                    @endforeach
                </x-ui.select-field>
                <x-ui.select-field label="Status" x-model="form.status">
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </x-ui.select-field>
            </x-ui.form-section>
        </div>
        <div class="card-footer flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
            <a href="/users" class="btn-secondary w-full text-center sm:w-auto">Cancel</a>
            <button @click="save()" class="btn-primary inline-flex w-full items-center justify-center gap-2 sm:w-auto" :disabled="saving">
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
