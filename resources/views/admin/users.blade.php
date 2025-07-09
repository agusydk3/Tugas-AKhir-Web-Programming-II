@extends('admin.layouts.app')

@section('title', 'User Management')

@section('content')
<div class="page-header">
    <h1 class="page-title">User Management</h1>
    <p class="page-description">Manage users, roles, and accounts</p>
</div>

@if(session('success'))
<div class="alert alert-success">
    {{ session('success') }}
</div>
@endif

@if(session('error'))
<div class="alert alert-danger">
    {{ session('error') }}
</div>
@endif

<div class="table-container">
    <div class="table-header">
        <h3 class="table-title">Users</h3>
        <div class="search-box">
            <input type="text" class="search-input" placeholder="Search users..." id="userSearch">
            <select class="form-select" style="width: 120px; margin-left: 10px;" id="roleFilter">
                <option value="">All Roles</option>
                <option value="admin">Admin</option>
                <option value="customer">Customer</option>
                <option value="reseller">Reseller</option>
            </select>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                <i class="fas fa-plus"></i> Add User
            </button>
        </div>
    </div>
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Balance</th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="usersTableBody">
            @forelse($users as $user)
            <tr>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>
                    @if($user->isAdmin())
                    <span class="status-badge" style="background: #e3f2fd; color: #0d47a1;">Admin</span>
                    @elseif($user->isReseller())
                    <span class="status-badge" style="background: #fff3e0; color: #e65100;">Reseller</span>
                    @else
                    <span class="status-badge" style="background: #e8f5e8; color: #2d5a2d;">Customer</span>
                    @endif
                </td>
                <td>{{ $user->getFormattedBalance() }}</td>
                <td>{{ $user->created_at->format('d M Y') }}</td>
                <td>
                    <div class="action-buttons">
                        <button class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#editUserModal" 
                            data-user-id="{{ $user->id }}"
                            data-user-name="{{ $user->name }}"
                            data-user-email="{{ $user->email }}"
                            data-user-role="{{ $user->role }}">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#balanceModal"
                            data-user-id="{{ $user->id }}"
                            data-user-name="{{ $user->name }}"
                            data-user-email="{{ $user->email }}"
                            data-user-balance="{{ $user->balance }}">
                            <i class="fas fa-wallet"></i>
                        </button>
                        @if(auth()->id() !== $user->id)
                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this user?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center">No users found</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addUserModalLabel">Add New User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
                
                <form action="{{ route('admin.users.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-input" id="name" name="name" value="{{ old('name') }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-input" id="email" name="email" value="{{ old('email') }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-input" id="password" name="password" required>
                    </div>
                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Confirm Password</label>
                        <input type="password" class="form-input" id="password_confirmation" name="password_confirmation" required>
                    </div>
                    <div class="mb-3">
                        <label for="role" class="form-label">Role</label>
                        <select class="form-select" id="role" name="role" required>
                            <option value="">Select Role</option>
                            <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="reseller" {{ old('role') == 'reseller' ? 'selected' : '' }}>Reseller</option>
                            <option value="customer" {{ old('role') == 'customer' ? 'selected' : '' }}>Customer</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Save User</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editUserForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label for="edit_name" class="form-label">Name</label>
                        <input type="text" class="form-input" id="edit_name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_email" class="form-label">Email</label>
                        <input type="email" class="form-input" id="edit_email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_password" class="form-label">Password (leave blank to keep current)</label>
                        <input type="password" class="form-input" id="edit_password" name="password">
                    </div>
                    <div class="mb-3">
                        <label for="edit_password_confirmation" class="form-label">Confirm Password</label>
                        <input type="password" class="form-input" id="edit_password_confirmation" name="password_confirmation">
                    </div>
                    <div class="mb-3">
                        <label for="edit_role" class="form-label">Role</label>
                        <select class="form-select" id="edit_role" name="role" required>
                            <option value="admin">Admin</option>
                            <option value="reseller">Reseller</option>
                            <option value="customer">Customer</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Update User</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Balance Modal -->
<div class="modal fade" id="balanceModal" tabindex="-1" aria-labelledby="balanceModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="balanceModalLabel">Manage User Balance</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="user-info mb-4 p-3 bg-light rounded">
                    <p><strong>User:</strong> <span id="balance_user_name"></span></p>
                    <p><strong>Email:</strong> <span id="balance_user_email"></span></p>
                    <p><strong>Current Balance:</strong> <span id="balance_user_current"></span></p>
                </div>
                
                <form id="balanceForm" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="operation" class="form-label">Operation</label>
                        <select class="form-select" id="operation" name="operation" required>
                            <option value="add">Add to balance</option>
                            <option value="subtract">Subtract from balance</option>
                            <option value="set">Set balance to</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="amount" class="form-label">Amount (Rp)</label>
                        <input type="number" min="0" step="1000" class="form-input" id="amount" name="amount" required>
                    </div>
                    <button type="submit" class="btn btn-success">Update Balance</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Show modal on validation errors
        @if ($errors->any())
            var addUserModal = new bootstrap.Modal(document.getElementById('addUserModal'));
            addUserModal.show();
        @endif
        
        // Setup edit user modal
        const editUserModal = document.getElementById('editUserModal');
        if (editUserModal) {
            editUserModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const userId = button.getAttribute('data-user-id');
                const userName = button.getAttribute('data-user-name');
                const userEmail = button.getAttribute('data-user-email');
                const userRole = button.getAttribute('data-user-role');
                
                const form = document.getElementById('editUserForm');
                form.action = "{{ url('admin/users') }}/" + userId;
                
                document.getElementById('edit_name').value = userName;
                document.getElementById('edit_email').value = userEmail;
                document.getElementById('edit_role').value = userRole;
                
                // Clear password fields
                document.getElementById('edit_password').value = '';
                document.getElementById('edit_password_confirmation').value = '';
            });
        }
        
        // Setup balance modal
        const balanceModal = document.getElementById('balanceModal');
        if (balanceModal) {
            balanceModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const userId = button.getAttribute('data-user-id');
                const userName = button.getAttribute('data-user-name');
                const userEmail = button.getAttribute('data-user-email');
                const userBalance = button.getAttribute('data-user-balance');
                
                const form = document.getElementById('balanceForm');
                form.action = "{{ url('admin/users') }}/" + userId + "/balance";
                
                document.getElementById('balance_user_name').textContent = userName;
                document.getElementById('balance_user_email').textContent = userEmail;
                document.getElementById('balance_user_current').textContent = "Rp " + new Intl.NumberFormat('id-ID').format(userBalance);
                
                // Set default amount
                document.getElementById('amount').value = 0;
            });
        }
        
        // User search functionality
        const userSearch = document.getElementById('userSearch');
        const roleFilter = document.getElementById('roleFilter');
        const tableRows = document.querySelectorAll('#usersTableBody tr');
        
        function filterTable() {
            const searchTerm = userSearch.value.toLowerCase();
            const roleValue = roleFilter.value.toLowerCase();
            
            tableRows.forEach(row => {
                const name = row.cells[0].textContent.toLowerCase();
                const email = row.cells[1].textContent.toLowerCase();
                const role = row.cells[2].textContent.toLowerCase();
                
                const matchesSearch = name.includes(searchTerm) || email.includes(searchTerm);
                const matchesRole = roleValue === '' || role.includes(roleValue);
                
                row.style.display = matchesSearch && matchesRole ? '' : 'none';
            });
        }
        
        if (userSearch) userSearch.addEventListener('input', filterTable);
        if (roleFilter) roleFilter.addEventListener('change', filterTable);
    });
</script>
@endsection 