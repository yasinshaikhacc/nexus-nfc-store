/**
 * VOLTIX Storefront & Micro-App Controller (voltixtap.com)
 * Handles nav tab switching, 3D stacked card peek-through & swap switcher,
 * dynamic customization builder, shopping cart state management, and WhatsApp / Email order compiler.
 */

document.addEventListener('DOMContentLoaded', () => {

  // ==========================================
  // 1. DATA STRUCTURES & CONFIG
  // ==========================================

  const TIER_DATA = {
    normal: { 
      id: 'normal', 
      name: 'Normal Print', 
      singlePrice: 750, 
      bothPrice: 1350, 
      originalBothPrice: 1500,
      discount: 150,
      desc: 'Pre-designed Google or Instagram card with embedded NFC chip & printed QR code. Includes high-strength double-sided tape — use as a counter card or stick anywhere.' 
    },
    custom: { 
      id: 'custom', 
      name: 'Custom Full Print', 
      singlePrice: 1200, 
      bothPrice: 2250, 
      originalBothPrice: 2400,
      discount: 150,
      desc: 'Fully bespoke brand layout & logo print with embedded NFC chip & printed QR code. Includes high-strength double-sided tape — use as a counter card or stick anywhere.' 
    }
  };

  // Strictly 2 Editions: Royal Blue & White (Google Review) and Instagram Follow Edition (Sunset Gradient)
  const FINISH_VARIANTS = [
    {
      id: 'blue',
      name: 'Google Review Edition (Royal Blue)',
      imageSrc: 'assets/google_card_blue.png',
      desc: 'Vibrant royal blue header with signature wave line, contactless NFC tap zone, and crisp QR code to boost 5-star Google reviews.',
      glowColor: 'rgba(56, 189, 248, 0.45)'
    },
    {
      id: 'instagram',
      name: 'Instagram Follow Edition',
      imageSrc: 'assets/instagram_card.jpg',
      desc: 'Signature Instagram sunset gradient layout with instant NFC tap zone and QR code to skyrocket your Instagram followers & customer engagement.',
      glowColor: 'rgba(236, 72, 153, 0.55)'
    }
  ];

  // Application State
  const state = {
    activeTab: 'review',
    currentFinishIndex: 0,
    selectedTier: 'normal',
    selectedEdition: 'blue',
    uploadedFile: null,
    nfcEnabled: true,
    qrEnabled: true,
    theme: localStorage.getItem('voltix_theme') || localStorage.getItem('nexus_theme') || 'dark',
    cart: JSON.parse(localStorage.getItem('voltix_nfc_cart') || localStorage.getItem('nexus_nfc_cart') || '[]')
  };

  // Theme Toggle Controller
  const themeToggleBtn = document.getElementById('theme-toggle-btn');
  const iconSun = themeToggleBtn?.querySelector('.icon-sun');
  const iconMoon = themeToggleBtn?.querySelector('.icon-moon');

  function applyTheme(themeName) {
    state.theme = themeName;
    document.body.setAttribute('data-theme', themeName);
    localStorage.setItem('voltix_theme', themeName);

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
  const heroMockupCard = document.getElementById('hero-mockup-card');
  const heroImgBlue = document.getElementById('hero-img-blue');
  const heroImgInstagram = document.getElementById('hero-img-instagram');
  const heroImgDisplay = document.getElementById('hero-img-display');
  const variantNameDisplay = document.getElementById('variant-name-display');
  const variantDescDisplay = document.getElementById('variant-desc-display');
  const pedestalGlow = document.getElementById('pedestal-glow');
  const prevFinishBtn = document.getElementById('prev-finish-btn');
  const nextFinishBtn = document.getElementById('next-finish-btn');
  const swatchBtns = document.querySelectorAll('.swatch-btn');

  let isSwapping = false;

  // ==========================================
  // A. THE ABOVE CARD (HERO MOCKUP): Auto-changes every 5-6 seconds
  // ==========================================
  let currentHeroIndex = 0;
  let heroAutoCycleTimer = null;
  const HERO_CYCLE_INTERVAL = 5500; // 5.5 seconds (cycles every 5-6 sec automatically)

  function updateHeroDisplay(index) {
    currentHeroIndex = index;
    const variant = FINISH_VARIANTS[index];

    if (heroImgBlue && heroImgInstagram) {
      if (variant.id === 'blue') {
        heroImgBlue.classList.remove('inactive');
        heroImgInstagram.classList.remove('active');
        if (heroMockupCard) {
          heroMockupCard.classList.remove('edition-instagram');
        }
      } else {
        heroImgBlue.classList.add('inactive');
        heroImgInstagram.classList.add('active');
        if (heroMockupCard) {
          heroMockupCard.classList.add('edition-instagram');
        }
      }
    } else if (heroImgDisplay) {
      heroImgDisplay.style.opacity = '0.2';
      setTimeout(() => {
        heroImgDisplay.src = variant.imageSrc;
        heroImgDisplay.alt = `VOLTIX ${variant.name}`;
        heroImgDisplay.style.opacity = '1';
      }, 180);
    }
  }

  function startHeroAutoCycle() {
    stopHeroAutoCycle();
    heroAutoCycleTimer = setInterval(() => {
      if (document.hidden) return;
      const nextIdx = (currentHeroIndex + 1) % FINISH_VARIANTS.length;
      updateHeroDisplay(nextIdx);
    }, HERO_CYCLE_INTERVAL);
  }

  function stopHeroAutoCycle() {
    if (heroAutoCycleTimer) {
      clearInterval(heroAutoCycleTimer);
      heroAutoCycleTimer = null;
    }
  }

  // Allow clicking on the Hero card to toggle immediately
  heroMockupCard?.addEventListener('click', () => {
    const nextIdx = (currentHeroIndex + 1) % FINISH_VARIANTS.length;
    updateHeroDisplay(nextIdx);
    startHeroAutoCycle();
  });

  // Pause hero cycle on hover so user can inspect details without interruption
  heroMockupCard?.addEventListener('mouseenter', stopHeroAutoCycle);
  heroMockupCard?.addEventListener('mouseleave', startHeroAutoCycle);


  // ==========================================
  // B. THE BELOW CARD (SHOWCASE 3D STACK): ONLY changes on tap or click
  // ==========================================
  function updateShowcaseDisplay(index) {
    if (isSwapping) return;
    state.currentFinishIndex = index;
    const activeVariant = FINISH_VARIANTS[index];

    if (cardStack) {
      isSwapping = true;
      cardStack.classList.add('swapping');

      setTimeout(() => {
        // Toggle 3D Stack Classes based on active edition
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

    // Update Swatch Buttons in Showcase
    swatchBtns.forEach((btn) => {
      const isSelected = btn.dataset.finish === activeVariant.id;
      btn.classList.toggle('active', isSelected);
      btn.setAttribute('aria-checked', isSelected ? 'true' : 'false');
    });

    // Synchronize selected edition with storefront
    state.selectedEdition = activeVariant.id;
    const colorOptionCards = document.querySelectorAll('.color-option-card');
    colorOptionCards.forEach((card) => {
      const isSelected = card.dataset.color === activeVariant.id;
      card.classList.toggle('active', isSelected);
      const radio = card.querySelector('.color-radio-input');
      if (radio) radio.checked = isSelected;
    });
    updatePriceDisplay();
  }

  // Left Arrow: ONLY changes when clicked or tapped
  prevFinishBtn?.addEventListener('click', (e) => {
    e.stopPropagation();
    const nextIdx = (state.currentFinishIndex - 1 + FINISH_VARIANTS.length) % FINISH_VARIANTS.length;
    updateShowcaseDisplay(nextIdx);
  });

  // Right Arrow: ONLY changes when clicked or tapped
  nextFinishBtn?.addEventListener('click', (e) => {
    e.stopPropagation();
    const nextIdx = (state.currentFinishIndex + 1) % FINISH_VARIANTS.length;
    updateShowcaseDisplay(nextIdx);
  });

  // Clicking on the Showcase Stack Card: changes edition
  cardStack?.addEventListener('click', () => {
    const nextIdx = (state.currentFinishIndex + 1) % FINISH_VARIANTS.length;
    updateShowcaseDisplay(nextIdx);
  });

  // Swatch Buttons in Showcase
  swatchBtns.forEach((btn) => {
    btn.addEventListener('click', (e) => {
      e.stopPropagation();
      const finishId = btn.dataset.finish;
      const index = FINISH_VARIANTS.findIndex(v => v.id === finishId);
      if (index !== -1 && index !== state.currentFinishIndex) {
        updateShowcaseDisplay(index);
      }
    });
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

  function getCurrentPrice() {
    const tier = TIER_DATA[state.selectedTier] || TIER_DATA.normal;
    if (state.selectedEdition === 'both') {
      return tier.bothPrice;
    }
    return tier.singlePrice;
  }

  function updatePriceDisplay() {
    const selectedRadio = document.querySelector('.tier-radio-input:checked');
    if (selectedRadio) {
      state.selectedTier = selectedRadio.value;
    }
    const currentPrice = getCurrentPrice();
    const isBoth = state.selectedEdition === 'both';
    const tier = TIER_DATA[state.selectedTier] || TIER_DATA.normal;

    if (computedPriceText) {
      if (isBoth) {
        computedPriceText.innerHTML = `<del class="cut-price-computed">₹${tier.originalBothPrice}</del><span class="active-combo-price">₹${tier.bothPrice}</span><span class="combo-discount-badge">Save ₹${tier.discount}</span>`;
      } else {
        computedPriceText.textContent = `₹${currentPrice}`;
      }
    }

    // Dynamic price labels on tier cards depending on whether Both is selected
    const normalPriceEl = document.querySelector('[data-tier-id="tier-normal"] .tier-price');
    const customPriceEl = document.querySelector('[data-tier-id="tier-custom"] .tier-price');
    if (normalPriceEl) {
      if (isBoth) {
        normalPriceEl.innerHTML = `<del class="cut-price">₹${TIER_DATA.normal.originalBothPrice}</del> ₹${TIER_DATA.normal.bothPrice}`;
      } else {
        normalPriceEl.textContent = `₹${TIER_DATA.normal.singlePrice}`;
      }
    }
    if (customPriceEl) {
      if (isBoth) {
        customPriceEl.innerHTML = `<del class="cut-price">₹${TIER_DATA.custom.originalBothPrice}</del> ₹${TIER_DATA.custom.bothPrice}`;
      } else {
        customPriceEl.textContent = `₹${TIER_DATA.custom.singlePrice}`;
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

  function selectEdition(editionId) {
    state.selectedEdition = editionId;

    const colorOptionCards = document.querySelectorAll('.color-option-card');
    colorOptionCards.forEach(card => {
      const isSelected = card.dataset.color === editionId;
      card.classList.toggle('active', isSelected);
      const radio = card.querySelector('.color-radio-input');
      if (radio) radio.checked = isSelected;
    });

    if (editionId === 'blue') {
      updateShowcaseDisplay(0);
    } else if (editionId === 'instagram') {
      updateShowcaseDisplay(1);
    } else if (editionId === 'both') {
      // In showcase, show both cards in stack
      if (cardBlue && cardBlack) {
        cardBlue.className = 'stacked-card card-blue active-front';
        cardBlack.className = 'stacked-card card-black peeping-back';
      }
      if (variantNameDisplay) {
        variantNameDisplay.textContent = 'Both Cards (Google Review + Instagram Follow)';
      }
      if (variantDescDisplay) {
        variantDescDisplay.textContent = 'Complete 2-card combo pack: 1x Google Review card + 1x Instagram Follow card with instant contactless NFC & QR codes.';
      }
    }

    updatePriceDisplay();
  }

  // Initial call on page load
  updatePriceDisplay();

  tierInputs.forEach(input => {
    input.addEventListener('change', updatePriceDisplay);
  });

  const colorOptionCards = document.querySelectorAll('.color-option-card');
  colorOptionCards.forEach(card => {
    card.addEventListener('click', (e) => {
      e.preventDefault();
      const colorId = card.dataset.color;
      selectEdition(colorId);
    });
  });

  // Pause hero cycle when browser tab is inactive, resume when active
  document.addEventListener('visibilitychange', () => {
    if (document.hidden) {
      stopHeroAutoCycle();
    } else {
      startHeroAutoCycle();
    }
  });

  // ==========================================
  // 5. PROCEED CONFIRMATION MODAL & WHATSAPP ORDER COMPILER
  // ==========================================

  const artworkModalOverlay = document.getElementById('artwork-modal-overlay');
  const artworkModalCloseBtn = document.getElementById('artwork-modal-close-btn');
  const modalSubmitWhatsappBtn = document.getElementById('modal-submit-whatsapp-btn');
  const modalOrderSubtitle = document.getElementById('modal-order-subtitle');
  const modalOrderDesc = document.getElementById('modal-order-desc');
  const modalPillPackage = document.getElementById('modal-pill-package');
  const modalPillEdition = document.getElementById('modal-pill-edition');
  const modalPillPrice = document.getElementById('modal-pill-price');
  const modalGuideContainer = document.getElementById('modal-guide-container');
  const modalReceiptShop = document.getElementById('modal-receipt-shop');
  const modalReceiptDest = document.getElementById('modal-receipt-dest');
  const modalSubmitBtnLabel = document.getElementById('modal-submit-btn-label');

  function updateModalContent() {
    const tierInfo = TIER_DATA[state.selectedTier] || TIER_DATA.normal;
    const isBoth = state.selectedEdition === 'both';
    const isCustom = state.selectedTier === 'custom';
    const currentPrice = getCurrentPrice();
    const enteredBusiness = businessNameInput?.value.trim();
    const enteredAddress = deliveryAddressInput?.value.trim();

    let editionName = 'Google Review';
    if (state.selectedEdition === 'instagram') {
      editionName = 'Instagram Follow';
    } else if (isBoth) {
      editionName = 'Both Cards (Combo)';
    }

    if (modalOrderSubtitle) {
      if (isBoth) {
        modalOrderSubtitle.textContent = '🔒 2-CARD COMBO PACK • VOLTIX CONCIERGE';
      } else if (isCustom) {
        modalOrderSubtitle.textContent = '🔒 BESPOKE CUSTOM PRINT • VOLTIX CONCIERGE';
      } else {
        modalOrderSubtitle.textContent = '🔒 256-BIT ORDER VERIFICATION • VOLTIX';
      }
    }

    if (modalPillPackage) modalPillPackage.textContent = tierInfo.name;
    if (modalPillEdition) modalPillEdition.textContent = editionName;
    if (modalPillPrice) {
      if (isBoth) {
        modalPillPrice.innerHTML = `<del class="cut-price" style="font-size:0.85rem;">₹${tierInfo.originalBothPrice}</del> ₹${tierInfo.bothPrice}`;
      } else {
        modalPillPrice.textContent = `₹${currentPrice}`;
      }
    }

    if (modalReceiptShop) {
      modalReceiptShop.textContent = enteredBusiness || 'To be confirmed on WhatsApp';
    }
    if (modalReceiptDest) {
      modalReceiptDest.textContent = enteredAddress || 'Pan-India Express Courier';
    }
    if (modalSubmitBtnLabel) {
      modalSubmitBtnLabel.textContent = `Confirm Order & Open WhatsApp Chat (₹${currentPrice})`;
    }

    if (modalGuideContainer) {
      if (isBoth) {
        modalGuideContainer.innerHTML = `
          <div class="guide-item">
            <span class="guide-icon">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
            </span>
            <div>
              <strong>Share Google & Instagram Links</strong>
              <p>Send both your Google Business link and Instagram profile link directly in our WhatsApp chat.</p>
            </div>
          </div>
          <div class="guide-item">
            <span class="guide-icon">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            </span>
            <div>
              <strong>${isCustom ? 'Share Shop Logo & Branding' : 'Combo Pre-Programmed & Tested'}</strong>
              <p>${isCustom ? 'Send your shop logo or branding references in chat for bespoke layout proofing.' : 'Both cards arrive pre-programmed with high-strength double-sided tape, ready to use!'}</p>
            </div>
          </div>
        `;
      } else if (isCustom) {
        modalGuideContainer.innerHTML = `
          <div class="guide-item">
            <span class="guide-icon">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            </span>
            <div>
              <strong>Share Shop Logo & Reference Images</strong>
              <p>Send your shop logo, reference photos, or graphics directly in our WhatsApp chat.</p>
            </div>
          </div>
          <div class="guide-item">
            <span class="guide-icon">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
            </span>
            <div>
              <strong>Share Destination Link</strong>
              <p>Send your Google Business or Instagram page link — our team will verify it and share a digital 3D proof before printing.</p>
            </div>
          </div>
        `;
      } else {
        modalGuideContainer.innerHTML = `
          <div class="guide-item">
            <span class="guide-icon">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
            </span>
            <div>
              <strong>Share Destination Link in Chat</strong>
              <p>Send your Google Business or Instagram link directly on WhatsApp — we will test and verify it for you.</p>
            </div>
          </div>
          <div class="guide-item">
            <span class="guide-icon">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            </span>
            <div>
              <strong>Instant Setup & Verified Pre-Programming</strong>
              <p>We program the contactless NFC chip and print the HD QR code with your verified link before shipping.</p>
            </div>
          </div>
        `;
      }
    }
  }

  function openArtworkModal() {
    if (!artworkModalOverlay) return;
    updateModalContent();
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

  function sendOrderToWhatsApp() {
    const tierInfo = TIER_DATA[state.selectedTier] || TIER_DATA.normal;
    const currentPrice = getCurrentPrice();
    const isBoth = state.selectedEdition === 'both';
    const businessName = businessNameInput?.value.trim() || '';
    const contactInfo = contactNameInput?.value.trim() || '';
    const deliveryAddress = deliveryAddressInput?.value.trim() || '';

    let orderText = '';

    if (isBoth) {
      orderText = `Hello VOLTIX, I would like to place an order for the *COMBO PACK (BOTH CARDS)*:\n\n`;
      orderText += `*ORDER SELECTION*\n`;
      orderText += `• Package: ${tierInfo.name} — 2-Card Combo Pack\n`;
      orderText += `• Editions: BOTH CARDS (1× Google Review + 1× Instagram Follow)\n`;
      orderText += `• Quantity: 2 Cards\n`;
      orderText += `• Price: ~₹${tierInfo.originalBothPrice}~ ₹${currentPrice} (Save ₹${tierInfo.discount}!)\n\n`;
    } else {
      const finishInfo = FINISH_VARIANTS.find(v => v.id === state.selectedEdition) || FINISH_VARIANTS[0];
      orderText = `Hello VOLTIX, I would like to place an order:\n\n`;
      orderText += `*ORDER SELECTION*\n`;
      orderText += `• Package: ${tierInfo.name}\n`;
      orderText += `• Edition: ${finishInfo.name}\n`;
      orderText += `• Quantity: 1 Card\n`;
      orderText += `• Price: ₹${currentPrice}\n\n`;
    }

    let detailsList = [];
    if (businessName) detailsList.push(`• Business Name: ${businessName}`);
    if (contactInfo) detailsList.push(`• Contact Person: ${contactInfo}`);
    if (deliveryAddress) detailsList.push(`• Delivery Address: ${deliveryAddress}`);

    if (detailsList.length > 0) {
      orderText += `*BUSINESS & DELIVERY DETAILS*\n` + detailsList.join('\n') + `\n\n`;
    }

    if (isBoth) {
      orderText += `*TOTAL AMOUNT:* ₹${currentPrice} (Discounted from ~₹${tierInfo.originalBothPrice}~ | GST & Shipping Included)\n\n`;
    } else {
      orderText += `*TOTAL AMOUNT:* ₹${currentPrice} (GST & Shipping Included)\n\n`;
    }
    orderText += `Please send payment confirmation details to finalize printing!`;

    const whatsappPhone = '918850938139';
    const waUrl = `https://wa.me/${whatsappPhone}?text=${encodeURIComponent(orderText)}`;
    window.open(waUrl, '_blank');
  }

  // Primary Order button action — Opens confirmation modal on EVERY purchase (Basic, Custom, or Combo)
  whatsappDirectOrderBtn?.addEventListener('click', () => {
    openArtworkModal();
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

  // Initialize view:
  // Above card (Hero Mockup): Starts auto-cycling every 5-6 sec
  updateHeroDisplay(0);
  startHeroAutoCycle();

  // Below card (Showcase 3D Stack): Set to Google Review Blue, ONLY changes when user taps/clicks arrows/cards
  updateShowcaseDisplay(0);
  updatePriceDisplay();

});
