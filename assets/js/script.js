
// assets/js/script.js

// Toast Notification
function showToast(message, type = 'success') {
    // Create toast container if it doesn't exist
    let toastContainer = document.getElementById('toast-container');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.id = 'toast-container';
        toastContainer.style.position = 'fixed';
        toastContainer.style.top = '20px';
        toastContainer.style.right = '20px';
        toastContainer.style.zIndex = '10000';
        document.body.appendChild(toastContainer);
    }

    // Create Toast
    const toast = document.createElement('div');
    const bgColor = type === 'success' ? '#00b74a' : (type === 'error' ? '#f93154' : '#39c0ed');

    toast.style.background = 'rgba(20, 20, 20, 0.9)';
    toast.style.color = '#fff';
    toast.style.padding = '15px 25px';
    toast.style.marginBottom = '10px';
    toast.style.borderRadius = '12px';
    toast.style.borderLeft = `5px solid ${bgColor}`;
    toast.style.backdropFilter = 'blur(10px)';
    toast.style.boxShadow = '0 5px 15px rgba(0,0,0,0.3)';
    toast.style.display = 'flex';
    toast.style.alignItems = 'center';
    toast.style.gap = '10px';
    toast.style.opacity = '0';
    toast.style.transform = 'translateX(20px)';
    toast.style.transition = 'all 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55)';

    toast.innerHTML = `
        <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-info-circle'}" style="color: ${bgColor}"></i>
        <span style="font-weight: 500;">${message}</span>
    `;

    toastContainer.appendChild(toast);

    // Animate In
    requestAnimationFrame(() => {
        toast.style.opacity = '1';
        toast.style.transform = 'translateX(0)';
    });

    // Remove after 3s
    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateX(20px)';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

// Add to Cart AJAX
function addToCart(event, productId) {
    if (event) event.preventDefault();

    const qtyInput = document.querySelector('input[name="quantity"]');
    const quantity = qtyInput ? qtyInput.value : 1;

    const formData = new FormData();
    formData.append('action', 'add');
    formData.append('product_id', productId);
    formData.append('quantity', quantity);

    fetch('../src/cart.php', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('Product added to cart!', 'success');
                updateCartCount(data.cart_count);
            } else {
                showToast('Failed to add product', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            // Fallback for non-AJAX or errors
            // location.reload(); 
        });
}

// Update Cart Quantity AJAX
function updateCartQty(btn, delta) {
    const form = btn.closest('form');
    const input = form.querySelector('input[name="quantity"]');
    let newQty = parseInt(input.value) + delta;
    if (newQty < 1) newQty = 1;

    // Optimistic UI Update
    input.value = newQty;

    const formData = new FormData(form);

    // We manually set validation for the form data since we aren't submitting normally
    formData.set('quantity', newQty);

    fetch('cart.php', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateCartCount(data.cart_count);
                // Update Prices on DOM
                // Assuming we have distinct IDs for these elements in the new design
                // For now, we might just reload or partial update. 
                // Better to update DOM elements if we add IDs to them.
                location.reload(); // Reload for now to ensure totals are correct specific to PHP logic
            }
        })
        .catch(error => console.error('Error:', error));
}

function updateCartCount(count) {
    const badge = document.getElementById('cart-count-badge');
    if (badge) {
        badge.textContent = count;
        badge.style.display = count > 0 ? 'inline-block' : 'none';

        // Animation
        badge.style.transform = 'scale(1.2)';
        setTimeout(() => badge.style.transform = 'scale(1)', 200);
    }
}

// Password Visibility Toggle
function togglePasswordVisibility(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById(iconId);

    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}
