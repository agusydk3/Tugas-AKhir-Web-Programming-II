@extends('admin.layouts.app')

@section('title', 'Edit User')

@section('content')
<div class="page-header">
    <h1 class="page-title">Edit User</h1>
    <p class="page-description">Update user information</p>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.users.update', $user) }}" method="POST">
            @csrf
            @method('PUT')
            
            @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
            
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" class="form-input" id="name" name="name" value="{{ old('name', $user->name) }}" required>
            </div>
            
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-input" id="email" name="email" value="{{ old('email', $user->email) }}" required>
            </div>
            
            <div class="mb-3">
                <label for="password" class="form-label">Password (leave blank to keep current)</label>
                <input type="password" class="form-input" id="password" name="password">
                <small class="form-text">Only fill this if you want to change the password</small>
            </div>
            
            <div class="mb-3">
                <label for="password_confirmation" class="form-label">Confirm Password</label>
                <input type="password" class="form-input" id="password_confirmation" name="password_confirmation">
            </div>
            
            <div class="mb-3">
                <label for="role" class="form-label">Role</label>
                <select class="form-select" id="role" name="role" required>
                    <option value="admin" {{ (old('role', $user->role) == 'admin') ? 'selected' : '' }}>Admin</option>
                    <option value="reseller" {{ (old('role', $user->role) == 'reseller') ? 'selected' : '' }}>Reseller</option>
                    <option value="customer" {{ (old('role', $user->role) == 'customer') ? 'selected' : '' }}>Customer</option>
                </select>
            </div>
            
            <div class="d-flex mt-4">
                <button type="submit" class="btn btn-primary me-2">Update User</button>
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection 