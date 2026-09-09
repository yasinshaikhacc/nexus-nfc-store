/**
 * NEXUS Storefront & Micro-App Controller
 * Handles nav tab switching, 3D stacked card peek-through & swap switcher,
 * dynamic customization builder, shopping cart state management, and WhatsApp / Email order compiler.
 */

document.addEventListener('DOMContentLoaded', () => {

  // ==========================================
  // 1. DATA STRUCTURES & CONFIG
  // ==========================================

  const TIER_DATA = {
    normal: { id: 'normal', name: 'Normal Print', price: 600, desc: 'Flat acrylic review card with embedded NFC chip & printed QR code. (Without stand).' },
    stand: { id: 'stand', name: 'Normal Print + Base Stand', price: 850, desc: 'Acrylic review card complete with matching slotted acrylic pedestal stand for counter placement.' },
    custom: { id: 'custom', name: 'Custom Full Print', price: 1200, desc: 'Fully bespoke brand layout & logo print — matching acrylic pedestal stand included.' }
  };

  // Strictly 2 Editions: Royal Blue & White (First / Default Front) and Black (Peeping Back)
  const FINISH_VARIANTS = [
    {
      id: 'blue',
      name: 'Royal Blue & White Edition',
      imageSrc: 'assets/google_card_blue.png',
      desc: 'Vibrant royal blue header with signature cyan wave line, NFC tap zone, and crisp QR code.',
      glowColor: 'rgba(56, 189, 248, 0.45)'
    },
    {
      id: 'black',
      name: 'Black Edition',
      imageSrc: 'assets/google_card_black.jpg',
      desc: 'Classic black header layout with high-contrast Google G emblem, NFC tap zone, and QR code.',
      glowColor: 'rgba(30, 58, 138, 0.4)'
    }
  ];

  // Application State
  const state = {
    activeTab: 'review',
    currentFinishIndex: 0,
    selectedTier: 'stand',
    uploadedFile: null,
    nfcEnabled: true,
    qrEnabled: true,
    theme: localStorage.getItem('nexus_theme') || 'dark',
    cart: JSON.parse(localStorage.getItem('nexus_nfc_cart') || '[]')
  };

  // Theme Toggle Controller
  const themeToggleBtn = document.getElementById('theme-toggle-btn');
  const iconSun = themeToggleBtn?.querySelector('.icon-sun');
  const iconMoon = themeToggleBtn?.querySelector('.icon-moon');

  function applyTheme(themeName) {
    state.theme = themeName;
    document.body.setAttribute('data-theme', themeName);
    localStorage.setItem('nexus_theme', themeName);

    if (themeName === 'light') {
      if (iconSun) iconSun.style.display = 'none';
      if (iconMoon) iconMoon.style.display = 'block';
    } else {
      if (iconSun) iconSun.style.display = 'block';
      if (iconMoon) iconMoon.style.display = 'none';
    }
  }

  // Initialize Theme
  applyTheme(state.theme);

  themeToggleBtn?.addEventListener('click', () => {
    themeToggleBtn.classList.add('animating');
    const nextTheme = state.theme === 'dark' ? 'light' : 'dark';
    applyTheme(nextTheme);
    setTimeout(() => {
      themeToggleBtn.classList.remove('animating');
    }, 550);
  });



  // ==========================================
  // 3. STACKED CARD PEEK-THROUGH & 3D SWAP LOGIC
  // ==========================================

  const cardStack = document.getElementById('card-stack');
  const cardBlue = document.getElementById('card-blue');
  const cardBlack = document.getElementById('card-black');
  const heroImgDisplay = document.getElementById('hero-img-display');
  const variantNameDisplay = document.getElementById('variant-name-display');
  const variantDescDisplay = document.getElementById('variant-desc-display');
  const pedestalGlow = document.getElementById('pedestal-glow');
  const prevFinishBtn = document.getElementById('prev-finish-btn');
  const nextFinishBtn = document.getElementById('next-finish-btn');
  const swatchBtns = document.querySelectorAll('.swatch-btn');

  let isSwapping = false;

  function updateFinishDisplay(index) {
    if (isSwapping) return;
    state.currentFinishIndex = index;
    const activeVariant = FINISH_VARIANTS[index];

    // Smooth Hero Card Image crossfade
    if (heroImgDisplay) {
      heroImgDisplay.style.opacity = '0.2';
      setTimeout(() => {
        heroImgDisplay.src = activeVariant.imageSrc;
        heroImgDisplay.alt = `NEXUS ${activeVariant.name}`;
        heroImgDisplay.style.opacity = '1';
      }, 180);
    }

    if (cardStack) {
      isSwapping = true;
      cardStack.classList.add('swapping');

      setTimeout(() => {
        // Toggle Stack Classes based on active edition
        if (activeVariant.id === 'blue') {
          if (cardBlue) {
            cardBlue.className = 'stacked-card card-blue active-front';
          }
          if (cardBlack) {
            cardBlack.className = 'stacked-card card-black peeping-back';
          }
        } else {
          if (cardBlack) {
            cardBlack.className = 'stacked-card card-black active-front';
          }
          if (cardBlue) {
            cardBlue.className = 'stacked-card card-blue peeping-back';
          }
        }

        if (variantNameDisplay) variantNameDisplay.textContent = activeVariant.name;
        if (variantDescDisplay) variantDescDisplay.textContent = activeVariant.desc;
        if (pedestalGlow) {
          pedestalGlow.style.background = `radial-gradient(circle, ${activeVariant.glowColor} 0%, rgba(0,0,0,0) 70%)`;
        }

        cardStack.classList.remove('swapping');
        isSwapping = false;
      }, 320);
    } else {
      if (variantNameDisplay) variantNameDisplay.textContent = activeVariant.name;
      if (variantDescDisplay) variantDescDisplay.textContent = activeVariant.desc;
    }

    // Update Swatch Buttons
    swatchBtns.forEach((btn) => {
      const isSelected = btn.dataset.finish === activeVariant.id;
      btn.classList.toggle('active', isSelected);
      btn.setAttribute('aria-checked', isSelected ? 'true' : 'false');
    });
  }

  // ==========================================
  // AUTO ROTATE / AUTOMATIC CARD SWAPPER (3.5s)
  // ==========================================
  let autoRotateTimer = null;

  function startAutoRotate() {
    stopAutoRotate();
    autoRotateTimer = setInterval(() => {
      const nextIdx = (state.currentFinishIndex + 1) % FINISH_VARIANTS.length;
      updateFinishDisplay(nextIdx);
    }, 3500);
  }

  function stopAutoRotate() {
    if (autoRotateTimer) {
      clearInterval(autoRotateTimer);
      autoRotateTimer = null;
    }
  }

  // Start automatic self-changing cards on launch
  startAutoRotate();

  // Pause auto-rotate when user hovers or interacts, resume on leave
  const showcaseCardContainer = document.querySelector('.large-rectangle-showcase');
  const heroMockupContainer = document.querySelector('.acrylic-card-mockup-wrapper');

  [showcaseCardContainer, heroMockupContainer].forEach(container => {
    if (!container) return;
    container.addEventListener('mouseenter', stopAutoRotate);
    container.addEventListener('mouseleave', startAutoRotate);
  });

  // Click on the Stack itself to Swap Front / Peeping Back cards manually!
  cardStack?.addEventListener('click', () => {
    stopAutoRotate();
    const nextIdx = (state.currentFinishIndex + 1) % FINISH_VARIANTS.length;
    updateFinishDisplay(nextIdx);
    startAutoRotate();
  });

  swatchBtns.forEach((btn) => {
    btn.addEventListener('click', (e) => {
      e.stopPropagation();
      stopAutoRotate();
      const finishId = btn.dataset.finish;
      const index = FINISH_VARIANTS.findIndex(v => v.id === finishId);
      if (index !== -1 && index !== state.currentFinishIndex) {
        updateFinishDisplay(index);
      }
      startAutoRotate();
    });
  });

  prevFinishBtn?.addEventListener('click', (e) => {
    e.stopPropagation();
    stopAutoRotate();
    const nextIdx = (state.currentFinishIndex - 1 + FINISH_VARIANTS.length) % FINISH_VARIANTS.length;
    updateFinishDisplay(nextIdx);
    startAutoRotate();
  });

  nextFinishBtn?.addEventListener('click', (e) => {
    e.stopPropagation();
    stopAutoRotate();
    const nextIdx = (state.currentFinishIndex + 1) % FINISH_VARIANTS.length;
    updateFinishDisplay(nextIdx);
    startAutoRotate();
  });

  // ==========================================
  // 4. CUSTOMIZATION & TIER SELECTION
  // ==========================================

  const tierCards = document.querySelectorAll('.tier-card');
  const tierInputs = document.querySelectorAll('.tier-radio-input');
  const computedPriceText = document.getElementById('computed-price-text');
  const toggleNfc = document.getElementById('toggle-nfc');
  const toggleQr = document.getElementById('toggle-qr');
  const googleUrlInput = document.getElementById('google-url-input');
  const artworkUploadStep = document.getElementById('artwork-upload-step');
  const techGoogleStepNum = document.getElementById('tech-google-step-num');

  function updatePriceDisplay() {
    const selectedRadio = document.querySelector('.tier-radio-input:checked');
    if (selectedRadio) {
      const tierId = selectedRadio.value;
      state.selectedTier = tierId;
      const price = TIER_DATA[tierId].price;
      if (computedPriceText) computedPriceText.textContent = `₹${price}`;

      // Upload print option should ONLY appear when custom print (tierId === 'custom') is selected!
      if (tierId === 'custom') {
        if (artworkUploadStep) {
          artworkUploadStep.style.display = 'block';
          artworkUploadStep.removeAttribute('hidden');
        }
        if (techGoogleStepNum) techGoogleStepNum.textContent = '3';
      } else {
        if (artworkUploadStep) {
          artworkUploadStep.style.display = 'none';
          artworkUploadStep.setAttribute('hidden', 'true');
        }
        if (techGoogleStepNum) techGoogleStepNum.textContent = '2';
      }
    }

    tierCards.forEach(card => {
      const input = card.querySelector('.tier-radio-input');
      if (input && input.checked) {
        card.classList.add('active');
      } else {
        card.classList.remove('active');
      }
    });
  }

  // Initial call on page load
  updatePriceDisplay();

  tierInputs.forEach(input => {
    input.addEventListener('change', updatePriceDisplay);
  });

  toggleNfc?.addEventListener('change', (e) => {
    state.nfcEnabled = e.target.checked;
  });

  toggleQr?.addEventListener('change', (e) => {
    state.qrEnabled = e.target.checked;
  });

  googleUrlInput?.addEventListener('input', (e) => {
    state.googleUrl = e.target.value;
  });

  // Artwork File Upload Drag-and-Drop
  const dropzone = document.getElementById('dropzone');
  const artworkFileInput = document.getElementById('artwork-file-input');
  const dropzonePrompt = document.getElementById('dropzone-prompt');
  const uploadPreview = document.getElementById('upload-preview');
  const previewImgElement = document.getElementById('preview-img-element');
  const previewFilename = document.getElementById('preview-filename');
  const removeFileBtn = document.getElementById('remove-file-btn');

  function handleFileSelect(file) {
    if (!file) return;
    state.uploadedFile = file;

    if (previewFilename) previewFilename.textContent = file.name;

    if (file.type.startsWith('image/')) {
      const reader = new FileReader();
      reader.onload = (e) => {
        if (previewImgElement) previewImgElement.src = e.target.result;
      };
      reader.readAsDataURL(file);
    } else {
      if (previewImgElement) previewImgElement.src = 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="%2338BDF8" stroke-width="2"%3E%3Cpath d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"%3E%3C/path%3E%3Cpolyline points="14 2 14 8 20 8"%3E%3C/polyline%3E%3C/svg%3E';
    }

    if (dropzone) {
      dropzone.hidden = true;
      dropzone.style.display = 'none';
    }
    if (uploadPreview) {
      uploadPreview.hidden = false;
      uploadPreview.style.display = 'flex';
    }
  }

  artworkFileInput?.addEventListener('change', (e) => {
    if (e.target.files && e.target.files[0]) {
      handleFileSelect(e.target.files[0]);
    }
  });

  dropzone?.addEventListener('dragover', (e) => {
    e.preventDefault();
    dropzone.classList.add('dragover');
  });

  dropzone?.addEventListener('dragleave', () => {
    dropzone.classList.remove('dragover');
  });

  dropzone?.addEventListener('drop', (e) => {
    e.preventDefault();
    dropzone.classList.remove('dragover');
    if (e.dataTransfer.files && e.dataTransfer.files[0]) {
      handleFileSelect(e.dataTransfer.files[0]);
    }
  });

  removeFileBtn?.addEventListener('click', (e) => {
    e.preventDefault();
    e.stopPropagation();
    state.uploadedFile = null;
    if (artworkFileInput) artworkFileInput.value = '';
    if (previewImgElement) previewImgElement.src = '';
    if (previewFilename) previewFilename.textContent = '';
    if (uploadPreview) {
      uploadPreview.hidden = true;
      uploadPreview.style.display = 'none';
    }
    if (dropzone) {
      dropzone.hidden = false;
      dropzone.style.display = 'block';
    }
  });

  // ==========================================
  // 5. SHOPPING CART & DRAWER
  // ==========================================

  const cartDrawerToggle = document.getElementById('cart-drawer-toggle');
  const cartDrawer = document.getElementById('cart-drawer');
  const cartDrawerOverlay = document.getElementById('cart-drawer-overlay');
  const cartCloseBtn = document.getElementById('cart-close-btn');
  const cartCountBadge = document.getElementById('cart-count-badge');
  const cartItemsList = document.getElementById('cart-items-list');
  const emptyCartMsg = document.getElementById('empty-cart-msg');
  const cartSubtotalPrice = document.getElementById('cart-subtotal-price');
  const addToCartBtn = document.getElementById('add-to-cart-btn');

  function saveCart() {
    localStorage.setItem('nexus_nfc_cart', JSON.stringify(state.cart));
    renderCart();
  }

  function toggleCartDrawer(open) {
    const isOpen = open !== undefined ? open : !cartDrawer.classList.contains('open');
    cartDrawer.classList.toggle('open', isOpen);
    cartDrawer.setAttribute('aria-hidden', (!isOpen).toString());
    cartDrawerToggle.setAttribute('aria-expanded', isOpen.toString());
    if (cartDrawerOverlay) cartDrawerOverlay.hidden = !isOpen;
  }

  cartDrawerToggle?.addEventListener('click', () => toggleCartDrawer(true));
  cartCloseBtn?.addEventListener('click', () => toggleCartDrawer(false));
  cartDrawerOverlay?.addEventListener('click', () => toggleCartDrawer(false));

  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && cartDrawer?.classList.contains('open')) {
      toggleCartDrawer(false);
    }
  });

  function showToast(message) {
    let toast = document.querySelector('.toast-notification');
    if (!toast) {
      toast = document.createElement('div');
      toast.className = 'toast-notification';
      document.body.appendChild(toast);
    }
    toast.innerHTML = `<span class="toast-icon">✓</span> <span>${message}</span>`;
    toast.classList.add('show');

    setTimeout(() => {
      toast.classList.remove('show');
    }, 2800);
  }

  // Add Item to Cart Action
  addToCartBtn?.addEventListener('click', () => {
    const reviewUrl = googleUrlInput?.value.trim() || 'Not specified yet';
    const tierInfo = TIER_DATA[state.selectedTier];
    const currentFinish = FINISH_VARIANTS[state.currentFinishIndex];

    const newItem = {
      id: 'item_' + Date.now(),
      tierId: state.selectedTier,
      tierName: tierInfo.name,
      price: tierInfo.price,
      finishId: currentFinish.id,
      finishName: currentFinish.name,
      nfc: state.nfcEnabled,
      qr: state.qrEnabled,
      googleUrl: reviewUrl,
      fileName: state.uploadedFile ? state.uploadedFile.name : 'None',
      qty: 1
    };

    state.cart.push(newItem);
    saveCart();
    showToast(`Added ${tierInfo.name} (${currentFinish.name}) to cart!`);
    setTimeout(() => toggleCartDrawer(true), 300);
  });

  function renderCart() {
    const totalCount = state.cart.reduce((sum, item) => sum + item.qty, 0);
    const subtotal = state.cart.reduce((sum, item) => sum + (item.price * item.qty), 0);

    if (cartCountBadge) cartCountBadge.textContent = totalCount;
    if (cartSubtotalPrice) cartSubtotalPrice.textContent = `₹${subtotal}`;

    if (!cartItemsList) return;

    if (state.cart.length === 0) {
      if (emptyCartMsg) emptyCartMsg.hidden = false;
      const items = cartItemsList.querySelectorAll('.cart-item');
      items.forEach(el => el.remove());
      return;
    }

    if (emptyCartMsg) emptyCartMsg.hidden = true;

    const existingCards = cartItemsList.querySelectorAll('.cart-item');
    existingCards.forEach(el => el.remove());

    state.cart.forEach((item) => {
      const itemEl = document.createElement('div');
      itemEl.className = 'cart-item';
      itemEl.innerHTML = `
        <div class="item-main-row">
          <div class="item-info">
            <span class="item-title">${item.tierName}</span>
            <span class="item-meta">Edition: <strong>${item.finishName}</strong></span>
          </div>
          <span class="item-price">₹${item.price * item.qty}</span>
        </div>

        <div class="item-tech-tags">
          ${item.nfc ? '<span class="tech-tag">⚡ NFC Chip</span>' : ''}
          ${item.qr ? '<span class="tech-tag">📱 QR Code</span>' : ''}
          ${item.fileName !== 'None' ? `<span class="tech-tag">🎨 File: ${item.fileName}</span>` : ''}
        </div>

        <div class="item-controls-row">
          <div class="qty-controls">
            <button class="qty-btn minus-btn" data-id="${item.id}">-</button>
            <span class="qty-num">${item.qty}</span>
            <button class="qty-btn plus-btn" data-id="${item.id}">+</button>
          </div>
          <button class="btn-remove-item" data-id="${item.id}">Remove</button>
        </div>
      `;

      cartItemsList.appendChild(itemEl);
    });

    cartItemsList.querySelectorAll('.minus-btn').forEach(btn => {
      btn.addEventListener('click', (e) => {
        const id = e.target.dataset.id;
        const item = state.cart.find(i => i.id === id);
        if (item) {
          if (item.qty > 1) {
            item.qty--;
          } else {
            state.cart = state.cart.filter(i => i.id !== id);
          }
          saveCart();
        }
      });
    });

    cartItemsList.querySelectorAll('.plus-btn').forEach(btn => {
      btn.addEventListener('click', (e) => {
        const id = e.target.dataset.id;
        const item = state.cart.find(i => i.id === id);
        if (item) {
          item.qty++;
          saveCart();
        }
      });
    });

    cartItemsList.querySelectorAll('.btn-remove-item').forEach(btn => {
      btn.addEventListener('click', (e) => {
        const id = e.target.dataset.id;
        state.cart = state.cart.filter(i => i.id !== id);
        saveCart();
      });
    });
  }

  // ==========================================
  // 6. CHECKOUT COMPILERS (WhatsApp & Email)
  // ==========================================

  const checkoutWhatsappBtn = document.getElementById('checkout-whatsapp-btn');
  const checkoutEmailBtn = document.getElementById('checkout-email-btn');

  function generateOrderSummaryText() {
    if (state.cart.length === 0) return '';

    let text = `Hello NEXUS, I would like to place an order for Google Review Display Cards:\n\n`;

    let total = 0;
    state.cart.forEach((item, index) => {
      const itemTotal = item.price * item.qty;
      total += itemTotal;
      text += `*Item ${index + 1}:* ${item.tierName}\n`;
      text += `• Edition: ${item.finishName}\n`;
      text += `• Features: ${item.nfc ? 'NFC Enabled' : 'No NFC'}, ${item.qr ? 'QR Code' : 'No QR'}\n`;
      text += `• Google URL: ${item.googleUrl}\n`;
      if (item.fileName !== 'None') {
        text += `• Artwork File: ${item.fileName}\n`;
      }
      text += `• Qty: ${item.qty} x ₹${item.price} = ₹${itemTotal}\n\n`;
    });

    text += `*Total Amount:* ₹${total} (GST & Shipping Included)\n`;
    text += `Please confirm payment details to finalize printing!`;

    return text;
  }

  checkoutWhatsappBtn?.addEventListener('click', () => {
    if (state.cart.length === 0) {
      alert('Your cart is empty! Add an item before checking out.');
      return;
    }
    const orderText = generateOrderSummaryText();
    const whatsappPhone = '919876543210';
    const url = `https://wa.me/${whatsappPhone}?text=${encodeURIComponent(orderText)}`;
    window.open(url, '_blank');
  });

  checkoutEmailBtn?.addEventListener('click', () => {
    if (state.cart.length === 0) {
      alert('Your cart is empty! Add an item before checking out.');
      return;
    }
    const orderText = generateOrderSummaryText();
    const mailto = `mailto:orders@nexuscards.com?subject=${encodeURIComponent('New NEXUS Review Card Order Request')}&body=${encodeURIComponent(orderText)}`;
    window.location.href = mailto;
  });

  // ==========================================
  // 7. FAQ ACCORDION LOGIC
  // ==========================================

  const faqBtns = document.querySelectorAll('.faq-question-btn');

  faqBtns.forEach((btn) => {
    btn.addEventListener('click', () => {
      const isExpanded = btn.getAttribute('aria-expanded') === 'true';
      const answerPanel = btn.nextElementSibling;

      faqBtns.forEach(otherBtn => {
        if (otherBtn !== btn) {
          otherBtn.setAttribute('aria-expanded', 'false');
          if (otherBtn.nextElementSibling) otherBtn.nextElementSibling.hidden = true;
        }
      });

      btn.setAttribute('aria-expanded', (!isExpanded).toString());
      if (answerPanel) answerPanel.hidden = isExpanded;
    });
  });

  // Initialize view (Royal Blue & White Edition is index 0)
  updateFinishDisplay(0);
  updatePriceDisplay();
  renderCart();

});
