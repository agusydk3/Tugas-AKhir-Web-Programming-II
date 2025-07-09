// Global variables
let sidebarCollapsed = false;

// Initialize the dashboard
document.addEventListener('DOMContentLoaded', function() {
    initializeEventListeners();
    updateUI();
    checkScreenSize();
});

// Check screen size on resize
window.addEventListener('resize', function() {
    checkScreenSize();
});

// Event Listeners
function initializeEventListeners() {
    // Sidebar toggle
    document.getElementById('sidebarToggle').addEventListener('click', toggleSidebar);
    
    // Sidebar close button
    document.getElementById('sidebarClose')?.addEventListener('click', closeSidebar);
    
    // Sidebar overlay for mobile
    document.getElementById('sidebarOverlay')?.addEventListener('click', closeSidebar);
    
    // Search functionality
    setupSearchListeners();
}

function setupSearchListeners() {
    // Product search
    document.getElementById('productSearch')?.addEventListener('input', function() {
        filterTable('productsTableBody', this.value);
    });

    // Stock search
    document.getElementById('stockSearch')?.addEventListener('input', function() {
        filterTable('stockTableBody', this.value);
    });

    // Transaction search
    document.getElementById('transactionSearch')?.addEventListener('input', function() {
        filterTable('transactionsTableBody', this.value);
    });

    // User search
    document.getElementById('userSearch')?.addEventListener('input', function() {
        filterTable('usersTableBody', this.value);
    });
}

// Check screen size and update UI accordingly
function checkScreenSize() {
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.getElementById('mainContent');
    
    if (window.innerWidth <= 1024) {
        // On mobile/tablet, always collapse sidebar
        if (!sidebar.classList.contains('collapsed') && !sidebar.classList.contains('open')) {
            sidebar.classList.remove('collapsed'); // Reset state
            mainContent.classList.remove('expanded');
            sidebarCollapsed = false;
        }
    }
}

// Sidebar functions
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.getElementById('mainContent');
    const overlay = document.getElementById('sidebarOverlay');
    const closeBtn = document.getElementById('sidebarClose');

    if (window.innerWidth <= 1024) {
        // Mobile: slide sidebar
        sidebar.classList.toggle('open');
        overlay.classList.toggle('active');
        closeBtn.style.display = sidebar.classList.contains('open') ? 'block' : 'none';
    } else {
        // Desktop: collapse/expand
        if (sidebar.classList.contains('collapsed')) {
            sidebar.classList.remove('collapsed');
            mainContent.classList.remove('expanded');
            sidebarCollapsed = false;
        } else {
            sidebar.classList.add('collapsed');
            mainContent.classList.add('expanded');
            sidebarCollapsed = true;
        }
    }
}

function closeSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const closeBtn = document.getElementById('sidebarClose');
    
    sidebar.classList.remove('open');
    overlay.classList.remove('active');
    closeBtn.style.display = 'none';
}

// Search and filter functions
function filterTable(tableBodyId, searchValue) {
    const tableBody = document.getElementById(tableBodyId);
    if (!tableBody) return;
    
    const rows = tableBody.getElementsByTagName('tr');

    for (let row of rows) {
        const text = row.textContent.toLowerCase();
        const shouldShow = text.includes(searchValue.toLowerCase());
        row.style.display = shouldShow ? '' : 'none';
    }
}

// Toast notification system
function showToast(message, type = 'success') {
    const toastContainer = document.getElementById('toastContainer');
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;

    const icon = type === 'success' ? 'fa-check' : 'fa-exclamation-triangle';
    
    toast.innerHTML = `
        <div class="toast-icon">
            <i class="fas ${icon}"></i>
        </div>
        <div>
            <strong>${type === 'success' ? 'Success!' : 'Error!'}</strong>
            <div>${message}</div>
        </div>
    `;

    toastContainer.appendChild(toast);

    // Auto remove after 4 seconds
    setTimeout(() => {
        toast.remove();
    }, 4000);
}

// Product Management Functions
function showProductForm() {
    const form = document.getElementById('productForm');
    if (!form) return;
    
    form.style.display = 'block';
    form.scrollIntoView({ behavior: 'smooth' });
}

function hideProductForm() {
    const form = document.getElementById('productForm');
    if (!form) return;
    
    form.style.display = 'none';
}

function saveProduct() {
    showToast('Product saved successfully!', 'success');
    hideProductForm();
}

function editProduct(id) {
    showProductForm();
    showToast('Edit mode activated for product #' + id, 'success');
}

function deleteProduct(id) {
    if (confirm('Are you sure you want to delete this product?')) {
        showToast('Product deleted successfully!', 'success');
    }
}

// Stock Management Functions
function showStockForm() {
    const form = document.getElementById('stockForm');
    if (!form) return;
    
    form.style.display = 'block';
    form.scrollIntoView({ behavior: 'smooth' });
}

function hideStockForm() {
    const form = document.getElementById('stockForm');
    if (!form) return;
    
    form.style.display = 'none';
}

function addStockRow() {
    const container = document.getElementById('stockEntries');
    if (!container) return;
    
    const newRow = document.createElement('div');
    newRow.className = 'dynamic-row';
    newRow.innerHTML = `
        <input type="text" class="form-input" placeholder="Serial Number, Batch Code, etc." style="flex: 1;">
        <button type="button" class="remove-row" onclick="removeStockRow(this)">
            <i class="fas fa-times"></i>
        </button>
    `;
    container.appendChild(newRow);
    
    // Show remove buttons when there are multiple rows
    updateRemoveButtons();
}

function removeStockRow(button) {
    button.parentElement.remove();
    updateRemoveButtons();
}

function updateRemoveButtons() {
    const rows = document.querySelectorAll('#stockEntries .dynamic-row');
    rows.forEach((row, index) => {
        const removeBtn = row.querySelector('.remove-row');
        removeBtn.style.display = rows.length > 1 ? 'block' : 'none';
    });
}

function saveStock() {
    showToast('Stock data added successfully!', 'success');
    hideStockForm();
}

function viewStockDetails(id) {
    showToast('Viewing stock details for product #' + id, 'success');
}

function addStockEntry(id) {
    showStockForm();
    showToast('Adding stock entry for product #' + id, 'success');
}

// User Management Functions
function showUserForm() {
    const form = document.getElementById('userForm');
    if (!form) return;
    
    form.style.display = 'block';
    form.scrollIntoView({ behavior: 'smooth' });
}

function hideUserForm() {
    const form = document.getElementById('userForm');
    if (!form) return;
    
    form.style.display = 'none';
}

function saveUser() {
    showToast('User created successfully!', 'success');
    hideUserForm();
}

function editUser(id) {
    showUserForm();
    showToast('Edit mode activated for user #' + id, 'success');
}

function deleteUser(id) {
    if (confirm('Are you sure you want to delete this user?')) {
        showToast('User deleted successfully!', 'success');
    }
}

function showBalanceForm(userId) {
    const form = document.getElementById('balanceForm');
    if (!form) return;
    
    form.style.display = 'block';
    form.scrollIntoView({ behavior: 'smooth' });
}

function hideBalanceForm() {
    const form = document.getElementById('balanceForm');
    if (!form) return;
    
    form.style.display = 'none';
}

function addBalance() {
    showToast('Balance added successfully!', 'success');
    hideBalanceForm();
}

// Update UI based on current state
function updateUI() {
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.getElementById('mainContent');
    
    // Apply correct sidebar state on page load
    if (sidebarCollapsed) {
        sidebar.classList.add('collapsed');
        mainContent.classList.add('expanded');
    }
} 