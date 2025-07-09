@extends('admin.layouts.app')

@section('title', 'Stock Management')

@section('content')
<div class="page-header">
    <h1 class="page-title">Stock Management</h1>
    <p class="page-description">Manage product stock entries and inventory</p>
</div>

<div class="table-container">
    <div class="table-header">
        <h3 class="table-title">Stock Overview</h3>
        <div class="search-box">
            <input type="text" class="search-input" placeholder="Search products..." id="stockSearch">
            <button class="btn btn-success" onclick="showStockForm()">
                <i class="fas fa-plus"></i> Add Stock Entry
            </button>
        </div>
    </div>
    <table>
        <thead>
            <tr>
                <th>Product Name</th>
                <th>Total Stock</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="stockTableBody">
            @forelse($products as $product)
            <tr data-id="{{ $product->id }}" data-name="{{ $product->name }}">
                <td>{{ $product->name }}</td>
                <td>
                    <span class="status-badge 
                        @if($product->stockCount > 10) status-success 
                        @elseif($product->stockCount > 0) status-pending 
                        @else status-inactive @endif">
                        {{ $product->stockCount }} entries
                    </span>
                </td>
                <td>
                    <div class="action-buttons">
                        <button class="btn btn-primary btn-sm" onclick="viewStockDetails({{ $product->id }})">
                            <i class="fas fa-eye"></i> View Details
                        </button>
                        <button class="btn btn-success btn-sm" onclick="addStockEntry({{ $product->id }}, '{{ $product->name }}')">
                            <i class="fas fa-plus"></i> Add Entry
                        </button>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="3" class="text-center">No products found</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Stock Entry Form -->
<div class="form-container" id="stockForm" style="display: none;">
    <h3 class="form-title">Add Stock Entries</h3>
    <form id="addStockForm">
        @csrf
        <div class="form-group">
            <label class="form-label">Select Product</label>
            <select class="form-select" id="productId" name="product_id" required>
                <option value="">-- Select Product --</option>
                @foreach($products as $product)
                <option value="{{ $product->id }}">{{ $product->name }} (Current Stock: {{ $product->stockCount }})</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label class="form-label">Stock Entries</label>
            <div class="dynamic-rows" id="stockEntries">
                <div class="dynamic-row">
                    <input type="text" class="form-input" name="entries[0][serial_number]" placeholder="Serial Number (optional)" style="flex: 1;">
                    <button type="button" class="remove-row" onclick="removeStockRow(this)" style="display: none;">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <button type="button" class="btn btn-secondary" onclick="addStockRow()" style="margin-top: 10px;">
                <i class="fas fa-plus"></i> Add Row
            </button>
        </div>
        <div style="display: flex; gap: 10px; margin-top: 20px;">
            <button type="button" class="btn btn-success" onclick="saveStock()">Save Stock Entries</button>
            <button type="button" class="btn btn-secondary" onclick="hideStockForm()">Cancel</button>
        </div>
    </form>
</div>

<!-- Stock Details Modal -->
<div class="modal" id="stockDetailsModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Stock Details for <span id="modalProductName"></span></h3>
            <span class="modal-close" onclick="closeStockModal()">&times;</span>
        </div>
        <div class="modal-body">
            <div class="stock-info">
                <p>Total Stock: <span id="modalStockCount" class="status-badge"></span></p>
                <div id="modalProductNotes" style="margin-top: 10px; background-color: #f9fafb; padding: 10px; border-radius: 4px; border: 1px solid #e5e7eb; display: none;"></div>
            </div>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Serial Number</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="stockDetailTableBody">
                        <!-- Stock details will be populated here -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    // Search functionality
    document.getElementById('stockSearch').addEventListener('keyup', function() {
        const searchValue = this.value.toLowerCase();
        const rows = document.querySelectorAll('#stockTableBody tr');
        
        rows.forEach(row => {
            const productName = row.querySelector('td:first-child')?.textContent.toLowerCase() || '';
            if (productName.includes(searchValue)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
    
    // Stock form functions
    let stockRowCount = 1;
    
    function showStockForm() {
        document.getElementById('stockForm').style.display = 'block';
    }
    
    function hideStockForm() {
        document.getElementById('stockForm').style.display = 'none';
        document.getElementById('addStockForm').reset();
        resetStockRows();
    }
    
    function resetStockRows() {
        const container = document.getElementById('stockEntries');
        container.innerHTML = `
            <div class="dynamic-row">
                <input type="text" class="form-input" name="entries[0][serial_number]" placeholder="Serial Number (optional)" style="flex: 1;">
                <button type="button" class="remove-row" onclick="removeStockRow(this)" style="display: none;">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
        stockRowCount = 1;
    }
    
    function addStockRow() {
        const container = document.getElementById('stockEntries');
        const newRow = document.createElement('div');
        newRow.className = 'dynamic-row';
        newRow.innerHTML = `
            <input type="text" class="form-input" name="entries[${stockRowCount}][serial_number]" placeholder="Serial Number (optional)" style="flex: 1;">
            <button type="button" class="remove-row" onclick="removeStockRow(this)">
                <i class="fas fa-times"></i>
            </button>
        `;
        container.appendChild(newRow);
        
        // Show remove buttons if there's more than one row
        const removeButtons = document.querySelectorAll('.remove-row');
        if (removeButtons.length > 1) {
            removeButtons.forEach(button => {
                button.style.display = 'block';
            });
        }
        
        stockRowCount++;
    }
    
    function removeStockRow(button) {
        const row = button.parentNode;
        row.parentNode.removeChild(row);
        
        // Hide remove buttons if only one row left
        const removeButtons = document.querySelectorAll('.remove-row');
        if (removeButtons.length === 1) {
            removeButtons[0].style.display = 'none';
        }
        
        // Reindex names
        const rows = document.querySelectorAll('#stockEntries .dynamic-row');
        rows.forEach((row, index) => {
            const serialInput = row.querySelector('input[name*="[serial_number]"]');
            
            serialInput.name = `entries[${index}][serial_number]`;
        });
        
        stockRowCount = rows.length;
    }
    
    function addStockEntry(productId, productName) {
        // Pilih produk di dropdown
        const selectElement = document.getElementById('productId');
        if (selectElement) {
            selectElement.value = productId;
        }
        resetStockRows();
        showStockForm();
        
        // Scroll to product in dropdown
        if (selectElement) {
            selectElement.focus();
        }
    }
    
    function saveStock() {
        const form = document.getElementById('addStockForm');
        const formData = new FormData(form);
        
        // Validate form - ensure at least one entry
        if (document.querySelectorAll('#stockEntries .dynamic-row').length < 1) {
            showToast('Please add at least one stock entry', 'error');
            return;
        }
        
        fetch('{{ route("admin.stocks.store") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                hideStockForm();
                showToast(data.message, 'success');
                updateStockCount(data.product.id, data.stockCount);
            } else {
                showToast(data.message || 'An error occurred', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('An error occurred while saving stock entries', 'error');
        });
    }
    
    function updateStockCount(productId, count) {
        const productRow = document.querySelector(`#stockTableBody tr[data-id="${productId}"]`);
        if (productRow) {
            const countCell = productRow.querySelector('td:nth-child(2) .status-badge');
            countCell.textContent = `${count} entries`;
            
            // Update badge class based on count
            countCell.className = 'status-badge';
            if (count > 10) {
                countCell.classList.add('status-success');
            } else if (count > 0) {
                countCell.classList.add('status-pending');
            } else {
                countCell.classList.add('status-inactive');
            }
        }
    }
    
    // Stock details modal functions
    function viewStockDetails(productId) {
        fetch('{{ url("admin/stocks/product") }}/' + productId)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayStockDetails(data.product, data.stocks, data.stockCount);
            } else {
                showToast(data.message || 'An error occurred', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('An error occurred while fetching stock details', 'error');
        });
    }
    
    function displayStockDetails(product, stocks, stockCount) {
        document.getElementById('modalProductName').textContent = product.name;
        
        const countBadge = document.getElementById('modalStockCount');
        countBadge.textContent = `${stockCount} entries`;
        countBadge.className = 'status-badge';
        if (stockCount > 10) {
            countBadge.classList.add('status-success');
        } else if (stockCount > 0) {
            countBadge.classList.add('status-pending');
        } else {
            countBadge.classList.add('status-inactive');
        }
        
        // Tambahkan notes produk jika ada
        const productNotes = document.getElementById('modalProductNotes');
        if (product.notes) {
            productNotes.style.display = 'block';
            productNotes.textContent = product.notes;
        } else {
            productNotes.style.display = 'none';
        }
        
        const tableBody = document.getElementById('stockDetailTableBody');
        tableBody.innerHTML = '';
        
        if (stocks.length === 0) {
            tableBody.innerHTML = '<tr><td colspan="5" class="text-center">No stock entries found</td></tr>';
        } else {
            stocks.forEach(stock => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${stock.id}</td>
                    <td>${stock.serial_number || 'N/A'}</td>
                    <td>
                        <span class="status-badge ${stock.is_sold ? 'status-inactive' : 'status-success'}">
                            ${stock.is_sold ? 'Sold' : 'Available'}
                        </span>
                    </td>
                    <td>${new Date(stock.created_at).toLocaleDateString()}</td>
                    <td>
                        <div class="action-buttons">
                            <button class="btn btn-warning btn-sm" onclick="toggleStockStatus(${stock.id}, ${!stock.is_sold})">
                                <i class="fas fa-exchange-alt"></i> ${stock.is_sold ? 'Mark Available' : 'Mark Sold'}
                            </button>
                            <button class="btn btn-danger btn-sm" onclick="deleteStockEntry(${stock.id})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                `;
                tableBody.appendChild(row);
            });
        }
        
        document.getElementById('stockDetailsModal').style.display = 'block';
    }
    
    function closeStockModal() {
        document.getElementById('stockDetailsModal').style.display = 'none';
    }
    
    function toggleStockStatus(stockId, isStockSold) {
        fetch('{{ url("admin/stocks") }}/' + stockId, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                is_sold: isStockSold
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast(data.message, 'success');
                // Refresh stock details and update count
                viewStockDetails(data.product.id);
                updateStockCount(data.product.id, data.stockCount);
            } else {
                showToast(data.message || 'An error occurred', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('An error occurred while updating stock status', 'error');
        });
    }
    
    function deleteStockEntry(stockId) {
        if (!confirm('Are you sure you want to delete this stock entry?')) return;
        
        fetch('{{ url("admin/stocks") }}/' + stockId, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast(data.message, 'success');
                // Refresh stock details and update count
                viewStockDetails(data.product.id);
                updateStockCount(data.product.id, data.stockCount);
            } else {
                showToast(data.message || 'An error occurred', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('An error occurred while deleting stock entry', 'error');
        });
    }
    
    // Close modal when clicking outside
    window.addEventListener('click', function(event) {
        const modal = document.getElementById('stockDetailsModal');
        if (event.target === modal) {
            closeStockModal();
        }
    });
</script>
@endsection 