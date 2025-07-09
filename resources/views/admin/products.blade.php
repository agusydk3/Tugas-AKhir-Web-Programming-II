@extends('admin.layouts.app')

@section('title', 'Product Management')

@section('content')
<div class="page-header">
    <h1 class="page-title">Product Management</h1>
    <p class="page-description">Manage your products and inventory</p>
</div>

<div class="table-container">
    <div class="table-header">
        <h3 class="table-title">Products</h3>
        <div class="search-box">
            <input type="text" class="search-input" placeholder="Search products..." id="productSearch">
            <button class="btn btn-primary" onclick="showProductForm()">
                <i class="fas fa-plus"></i> Add Product
            </button>
        </div>
    </div>
    <table>
        <thead>
            <tr>
                <th>Image</th>
                <th>Product Name</th>
                <th>Price</th>
                <th>Stock</th>
                <th>Description</th>
                <th>Notes</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="productsTableBody">
            @forelse($products as $product)
            <tr data-product-id="{{ $product->id }}">
                <td>
                    @if($product->image)
                        <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}" class="image-preview">
                    @else
                        <img src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHZpZXdCb3g9IjAgMCA0MCA0MCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHJlY3Qgd2lkdGg9IjQwIiBoZWlnaHQ9IjQwIiByeD0iOCIgZmlsbD0iI0YzRjRGNiIvPgo8cGF0aCBkPSJNMTMgMTVIMjdWMjVIMTNWMTVaIiBzdHJva2U9IiM5Q0EzQUYiIHN0cm9rZS13aWR0aD0iMiIgZmlsbD0ibm9uZSIvPgo8Y2lyY2xlIGN4PSIxNyIgY3k9IjE5IiByPSIyIiBmaWxsPSIjOUNBM0FGIi8+Cjwvc3ZnPgo=" alt="{{ $product->name }}" class="image-preview">
                    @endif
                </td>
                <td>{{ $product->name }}</td>
                <td>{{ $product->formatted_price }}</td>
                <td>
                    <span class="status-badge 
                        @if($product->stockCount > 10) status-success 
                        @elseif($product->stockCount > 0) status-pending 
                        @else status-inactive @endif">
                        {{ $product->stockCount }} items
                    </span>
                </td>
                <td>{{ Str::limit($product->description, 100) }}</td>
                <td>{{ Str::limit($product->notes, 100) }}</td>
                <td>
                    <div class="action-buttons">
                        <button class="btn btn-secondary edit-product-btn" data-id="{{ $product->id }}">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-danger delete-product-btn" data-id="{{ $product->id }}">
                            <i class="fas fa-trash"></i>
                        </button>
                        <button class="btn btn-info manage-stock-btn" onclick="window.location.href='/admin/stock#product-{{ $product->id }}'">
                            <i class="fas fa-cubes"></i>
                        </button>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center">No products found</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="form-container" id="productForm" style="display: none;">
    <h3 class="form-title" id="formTitle">Add Product</h3>
    <form id="productFormElement" enctype="multipart/form-data">
        @csrf
        <input type="hidden" id="productId" name="product_id">
        <input type="hidden" id="methodField" name="_method" value="POST">
        
        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">Product Name</label>
                <input type="text" id="productName" name="name" class="form-input" placeholder="Enter product name" required>
            </div>
            <div class="form-group">
                <label class="form-label">Price</label>
                <input type="number" id="productPrice" name="price" class="form-input" placeholder="Enter price" min="0" step="0.01" required>
            </div>
            <div class="form-group">
                <label class="form-label">Upload Image</label>
                <input type="file" id="productImage" name="image" class="form-input" accept="image/*">
                <div id="currentImage" style="margin-top: 10px; display: none;">
                    <p>Current image:</p>
                    <img id="currentImagePreview" src="" alt="Current Product Image" style="max-width: 100px; max-height: 100px;">
                </div>
            </div>
        </div>
        <div class="form-group">
            <label class="form-label">Product Description</label>
            <textarea id="productDescription" name="description" class="form-textarea" placeholder="Enter product description"></textarea>
        </div>
        <div class="form-group">
            <label class="form-label">Notes (Internal)</label>
            <textarea id="productNotes" name="notes" class="form-textarea" placeholder="Enter internal notes for this product"></textarea>
        </div>
        <div style="display: flex; gap: 10px;">
            <button type="submit" class="btn btn-primary">Save Product</button>
            <button type="button" class="btn btn-secondary" onclick="hideProductForm()">Cancel</button>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Setup product form submission
        const productForm = document.getElementById('productFormElement');
        if (productForm) {
            productForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                const productId = document.getElementById('productId').value;
                const method = document.getElementById('methodField').value;
                
                let url = '{{ route("admin.products.store") }}';
                
                if (method === 'PUT') {
                    url = '/admin/products/' + productId;
                }
                
                fetch(url, {
                    method: method === 'PUT' ? 'POST' : 'POST', // Always POST for FormData
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showToast(data.message, 'success');
                        hideProductForm();
                        // Reload page to show updated data
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    } else {
                        showToast('Error: ' + data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('An error occurred', 'error');
                });
            });
        }
        
        // Setup edit product buttons
        document.querySelectorAll('.edit-product-btn').forEach(button => {
            button.addEventListener('click', function() {
                const productId = this.getAttribute('data-id');
                editProduct(productId);
            });
        });
        
        // Setup delete product buttons
        document.querySelectorAll('.delete-product-btn').forEach(button => {
            button.addEventListener('click', function() {
                const productId = this.getAttribute('data-id');
                deleteProduct(productId);
            });
        });
    });
    
    function showProductForm() {
        // Reset form for new product
        document.getElementById('formTitle').textContent = 'Add Product';
        document.getElementById('productFormElement').reset();
        document.getElementById('productId').value = '';
        document.getElementById('methodField').value = 'POST';
        document.getElementById('currentImage').style.display = 'none';
        
        // Show form
        document.getElementById('productForm').style.display = 'block';
        document.getElementById('productForm').scrollIntoView({ behavior: 'smooth' });
    }
    
    function editProduct(productId) {
        // Fetch product data
        fetch(`/admin/products/${productId}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const product = data.product;
                
                // Set form title and method
                document.getElementById('formTitle').textContent = 'Edit Product';
                document.getElementById('methodField').value = 'PUT';
                
                // Populate form fields
                document.getElementById('productId').value = product.id;
                document.getElementById('productName').value = product.name;
                document.getElementById('productPrice').value = product.price;
                document.getElementById('productDescription').value = product.description || '';
                document.getElementById('productNotes').value = product.notes || '';
                
                // Handle image preview if exists
                if (product.image) {
                    document.getElementById('currentImage').style.display = 'block';
                    document.getElementById('currentImagePreview').src = `/storage/${product.image}`;
                } else {
                    document.getElementById('currentImage').style.display = 'none';
                }
                
                // Show form
                document.getElementById('productForm').style.display = 'block';
                document.getElementById('productForm').scrollIntoView({ behavior: 'smooth' });
            } else {
                showToast('Error: ' + data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('An error occurred', 'error');
        });
    }
    
    function hideProductForm() {
        document.getElementById('productForm').style.display = 'none';
    }
    
    function deleteProduct(productId) {
        if (confirm('Are you sure you want to delete this product?')) {
            fetch(`/admin/products/${productId}`, {
                method: 'DELETE',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast(data.message, 'success');
                    // Remove row from table
                    document.querySelector(`tr[data-product-id="${productId}"]`).remove();
                    
                    // Check if table is empty
                    if (document.querySelectorAll('#productsTableBody tr').length === 0) {
                        document.getElementById('productsTableBody').innerHTML = `
                            <tr>
                                <td colspan="7" class="text-center">No products found</td>
                            </tr>
                        `;
                    }
                } else {
                    showToast('Error: ' + data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('An error occurred', 'error');
            });
        }
    }
    
    // Search functionality
    document.getElementById('productSearch').addEventListener('keyup', function() {
        const searchValue = this.value.toLowerCase();
        const rows = document.querySelectorAll('#productsTableBody tr');
        
        rows.forEach(row => {
            if (row.querySelector('td:first-child')) { // Skip the "No products found" row
                const productName = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
                const productDescription = row.querySelector('td:nth-child(5)').textContent.toLowerCase();
                const productNotes = row.querySelector('td:nth-child(6)').textContent.toLowerCase();
                
                if (productName.includes(searchValue) || 
                    productDescription.includes(searchValue) || 
                    productNotes.includes(searchValue)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            }
        });
    });
</script>
@endsection 