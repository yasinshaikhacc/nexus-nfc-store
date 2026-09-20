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

    // Update Swatch Buttons & Color Option Cards
    swatchBtns.forEach((btn) => {
      const isSelected = btn.dataset.finish === activeVariant.id;
      btn.classList.toggle('active', isSelected);
      btn.setAttribute('aria-checked', isSelected ? 'true' : 'false');
    });

    const colorOptionCards = document.querySelectorAll('.color-option-card');
    colorOptionCards.forEach((card) => {
      const isSelected = card.dataset.color === activeVariant.id;
      card.classList.toggle('active', isSelected);
      const radio = card.querySelector('.color-radio-input');
      if (radio) radio.checked = isSelected;
    });
  }

  // Click on the Stack itself to Swap Front / Peeping Back cards manually!
  cardStack?.addEventListener('click', () => {
    const nextIdx = (state.currentFinishIndex + 1) % FINISH_VARIANTS.length;
    updateFinishDisplay(nextIdx);
  });

  swatchBtns.forEach((btn) => {
    btn.addEventListener('click', (e) => {
      e.stopPropagation();
      const finishId = btn.dataset.finish;
      const index = FINISH_VARIANTS.findIndex(v => v.id === finishId);
      if (index !== -1 && index !== state.currentFinishIndex) {
        updateFinishDisplay(index);
      }
    });
  });

  prevFinishBtn?.addEventListener('click', (e) => {
    e.stopPropagation();
    const nextIdx = (state.currentFinishIndex - 1 + FINISH_VARIANTS.length) % FINISH_VARIANTS.length;
    updateFinishDisplay(nextIdx);
  });

  nextFinishBtn?.addEventListener('click', (e) => {
    e.stopPropagation();
    const nextIdx = (state.currentFinishIndex + 1) % FINISH_VARIANTS.length;
    updateFinishDisplay(nextIdx);
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
  const businessNameInput = document.getElementById('business-name-input');
  const contactNameInput = document.getElementById('contact-name-input');
  const deliveryAddressInput = document.getElementById('delivery-address-input');
  const whatsappDirectOrderBtn = document.getElementById('whatsapp-direct-order-btn');

  function updatePriceDisplay() {
    const selectedRadio = document.querySelector('.tier-radio-input:checked');
    if (selectedRadio) {
      const tierId = selectedRadio.value;
      state.selectedTier = tierId;
      const price = TIER_DATA[tierId].price;
      if (computedPriceText) computedPriceText.textContent = `₹${price}`;
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

  const colorRadioInputs = document.querySelectorAll('.color-radio-input');
  colorRadioInputs.forEach(input => {
    input.addEventListener('change', (e) => {
      const colorId = e.target.value;
      const index = FINISH_VARIANTS.findIndex(v => v.id === colorId);
      if (index !== -1) {
        updateFinishDisplay(index);
      }
    });
  });

  // ==========================================
  // 5. ARTWORK MODAL & DIRECT WHATSAPP ORDER COMPILER
  // ==========================================

  const artworkModalOverlay = document.getElementById('artwork-modal-overlay');
  const artworkModalCloseBtn = document.getElementById('artwork-modal-close-btn');
  const modalSubmitWhatsappBtn = document.getElementById('modal-submit-whatsapp-btn');

  const dropzone = document.getElementById('dropzone');
  const artworkFileInput = document.getElementById('artwork-file-input');
  const uploadPreview = document.getElementById('upload-preview');
  const previewImgElement = document.getElementById('preview-img-element');
  const previewFilename = document.getElementById('preview-filename');
  const removeFileBtn = document.getElementById('remove-file-btn');

  function openArtworkModal() {
    if (!artworkModalOverlay) return;
    artworkModalOverlay.hidden = false;
    artworkModalOverlay.classList.add('open');
  }

  function closeArtworkModal() {
    if (!artworkModalOverlay) return;
    artworkModalOverlay.classList.remove('open');
    setTimeout(() => {
      artworkModalOverlay.hidden = true;
    }, 300);
  }

  artworkModalCloseBtn?.addEventListener('click', closeArtworkModal);
  
  artworkModalOverlay?.addEventListener('click', (e) => {
    if (e.target === artworkModalOverlay) {
      closeArtworkModal();
    }
  });

  // Handle File Selection inside Modal Dropzone
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

  function sendOrderToWhatsApp() {
    const tierInfo = TIER_DATA[state.selectedTier] || TIER_DATA.stand;
    const finishInfo = FINISH_VARIANTS[state.currentFinishIndex] || FINISH_VARIANTS[0];
    
    const googleUrl = googleUrlInput?.value.trim() || '';
    const businessName = businessNameInput?.value.trim() || '';
    const contactInfo = contactNameInput?.value.trim() || '';
    const deliveryAddress = deliveryAddressInput?.value.trim() || '';

    let orderText = `Hello NEXUS, I would like to place an order for a Google Review NFC Acrylic Display:\n\n`;
    orderText += `*ORDER SELECTION*\n`;
    orderText += `• Package: ${tierInfo.name} (₹${tierInfo.price})\n`;
    orderText += `• Edition: ${finishInfo.name}\n`;
    orderText += `• Material: 4mm Imported Acrylic Sheet\n`;
    orderText += `• Features: ${state.nfcEnabled ? 'NFC Chip Enabled' : 'No NFC'}, ${state.qrEnabled ? 'Printed QR Code Included' : 'No QR'}\n`;
    
    if (state.selectedTier === 'custom') {
      if (state.uploadedFile) {
        orderText += `• Custom Artwork File: ${state.uploadedFile.name}\n`;
      } else {
        orderText += `• Custom Artwork: Will attach directly in WhatsApp chat\n`;
      }
    }
    orderText += `\n`;

    let detailsList = [];
    if (businessName) detailsList.push(`• Business Name: ${businessName}`);
    if (contactInfo) detailsList.push(`• Contact Person: ${contactInfo}`);
    if (deliveryAddress) detailsList.push(`• Delivery Address: ${deliveryAddress}`);
    if (googleUrl) detailsList.push(`• Google Review Page URL: ${googleUrl}`);

    if (detailsList.length > 0) {
      orderText += `*BUSINESS & DELIVERY DETAILS*\n` + detailsList.join('\n') + `\n\n`;
    }

    orderText += `*TOTAL AMOUNT:* ₹${tierInfo.price} (GST & Shipping Included)\n\n`;
    orderText += `Please send payment confirmation details to finalize printing!`;

    const whatsappPhone = '918850938139';
    const waUrl = `https://wa.me/${whatsappPhone}?text=${encodeURIComponent(orderText)}`;
    window.open(waUrl, '_blank');
  }

  // Primary Get Quote button action
  whatsappDirectOrderBtn?.addEventListener('click', () => {
    if (state.selectedTier === 'custom') {
      openArtworkModal();
    } else {
      sendOrderToWhatsApp();
    }
  });

  // Modal proceed button action
  modalSubmitWhatsappBtn?.addEventListener('click', () => {
    sendOrderToWhatsApp();
    closeArtworkModal();
  });

  // ==========================================
  // 6. FAQ ACCORDION LOGIC
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

  // ==========================================
  // 7. SPOTLIGHT AUTO FADE SLIDESHOW (Swaps every 2.2s)
  // ==========================================
  const spotlightImg1 = document.getElementById('spotlight-img-1');
  const spotlightImg2 = document.getElementById('spotlight-img-2');

  if (spotlightImg1 && spotlightImg2) {
    let activeImg = 1;
    setInterval(() => {
      if (activeImg === 1) {
        spotlightImg1.classList.remove('active');
        spotlightImg2.classList.add('active');
        activeImg = 2;
      } else {
        spotlightImg2.classList.remove('active');
        spotlightImg1.classList.add('active');
        activeImg = 1;
      }
    }, 2200);
  }

  // Initialize view (Royal Blue & White Edition is index 0)
  updateFinishDisplay(0);
  updatePriceDisplay();

});
