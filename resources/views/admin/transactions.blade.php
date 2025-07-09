@extends('admin.layouts.app')

@section('title', 'Transaction History')

@section('content')
<div class="page-header">
    <h1 class="page-title">Transaction History</h1>
    <p class="page-description">View and manage all transactions</p>
</div>

<div class="table-container">
    <div class="table-header">
        <h3 class="table-title">Transactions</h3>
        <div class="search-box">
            <input type="text" class="search-input" placeholder="Search by Transaction ID..." id="transactionSearch">
        </div>
    </div>
    <table>
        <thead>
            <tr>
                <th>Transaction ID</th>
                <th>User Name</th>
                <th>Product</th>
                <th>Quantity</th>
                <th>Total</th>
                <th>Payment Method</th>
                <th>Status</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="transactionsTableBody">
            @forelse($transactions as $transaction)
            <tr data-transaction-id="{{ $transaction->id }}">
                <td>{{ $transaction->transaction_id }}</td>
                <td>{{ $transaction->user->name }}</td>
                <td>{{ $transaction->product->name }}</td>
                <td>{{ $transaction->quantity }}</td>
                <td>{{ $transaction->formatted_total }}</td>
                <td>
                    @if($transaction->payment_method == 'qris')
                    <span class="status-badge" style="background: #e0f2fe; color: #0277bd;">QRIS</span>
                    @else
                    <span class="status-badge" style="background: #f3e5f5; color: #7b1fa2;">Saldo</span>
                    @endif
                </td>
                <td>
                    @if($transaction->status == 'completed')
                    <span class="status-badge status-success">Completed</span>
                    @elseif($transaction->status == 'pending')
                    <span class="status-badge status-pending">Pending</span>
                    @elseif($transaction->status == 'failed')
                    <span class="status-badge status-failed">Failed</span>
                    @elseif($transaction->status == 'cancelled')
                    <span class="status-badge status-cancelled">Cancelled</span>
                    @endif
                </td>
                <td>{{ $transaction->created_at->format('Y-m-d H:i') }}</td>
                <td>
                    <div class="action-buttons">
                        <a href="{{ route('admin.transactions.show', $transaction->id) }}" class="btn btn-secondary view-transaction-btn">
                            <i class="fas fa-eye"></i>
                        </a>
                        <button class="btn btn-primary update-status-btn" data-id="{{ $transaction->id }}" 
                                data-status="{{ $transaction->status }}" data-toggle="modal" data-target="#updateStatusModal">
                            <i class="fas fa-edit"></i>
                        </button>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="9" class="text-center">No transactions found</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    
    <div class="pagination-container">
        {{ $transactions->links() }}
    </div>
</div>

<!-- Update Status Modal -->
<div class="modal" id="updateStatusModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Transaction Status</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="updateStatusForm">
                    @csrf
                    <input type="hidden" id="transactionId" name="transaction_id">
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select class="form-control" id="status" name="status">
                            <option value="pending">Pending</option>
                            <option value="completed">Completed</option>
                            <option value="failed">Failed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="saveStatus">Save changes</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Search functionality
        const searchInput = document.getElementById('transactionSearch');
        const tableRows = document.querySelectorAll('#transactionsTableBody tr');
        
        searchInput.addEventListener('keyup', function() {
            const searchTerm = this.value.toLowerCase();
            
            tableRows.forEach(row => {
                const transactionId = row.querySelector('td:first-child').textContent.toLowerCase();
                const userName = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
                
                if (transactionId.includes(searchTerm) || userName.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
        
        // Update status modal
        const updateStatusBtns = document.querySelectorAll('.update-status-btn');
        const transactionIdInput = document.getElementById('transactionId');
        const statusSelect = document.getElementById('status');
        const saveStatusBtn = document.getElementById('saveStatus');
        
        updateStatusBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const transactionId = this.getAttribute('data-id');
                const currentStatus = this.getAttribute('data-status');
                
                transactionIdInput.value = transactionId;
                statusSelect.value = currentStatus;
                
                // Show the modal
                $('#updateStatusModal').modal('show');
            });
        });
        
        // Save status changes
        saveStatusBtn.addEventListener('click', function() {
            const transactionId = transactionIdInput.value;
            const newStatus = statusSelect.value;
            
            fetch(`/admin/transactions/${transactionId}/status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    status: newStatus
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Close the modal
                    $('#updateStatusModal').modal('hide');
                    
                    // Reload the page to show updated data
                    window.location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while updating the status');
            });
        });
    });
</script>
@endsection 