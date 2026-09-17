/**
 * Main Application Logic - Landing Page
 * Interaksi UI, Accordion FAQ, Mobile Drawer, Entrance & Scroll Reveal Animations
 */

document.addEventListener('DOMContentLoaded', () => {
  // 1. Inisialisasi Lucide Icons jika library tersedia
  if (window.lucide) {
    window.lucide.createIcons();
  }

  // 2. Mobile Menu Toggle
  const mobileBtn = document.getElementById('mobile-menu-btn');
  const mobileDrawer = document.getElementById('mobile-nav-drawer');
  if (mobileBtn && mobileDrawer) {
    mobileBtn.addEventListener('click', () => {
      mobileDrawer.classList.toggle('open');
    });

    // Tutup saat link di drawer diklik
    mobileDrawer.querySelectorAll('.nav-link').forEach(link => {
      link.addEventListener('click', () => {
        mobileDrawer.classList.remove('open');
      });
    });
  }

  // 3. FAQ Accordion Interaction
  const faqItems = document.querySelectorAll('.faq-item');
  faqItems.forEach(item => {
    const question = item.querySelector('.faq-question');
    if (question) {
      question.addEventListener('click', () => {
        const isActive = item.classList.contains('active');
        // Tutup semua FAQ lainnya
        faqItems.forEach(i => i.classList.remove('active'));
        if (!isActive) {
          item.classList.add('active');
        }
      });
    }
  });

  // 4. Scroll Reveal Observer (Smooth entrance on scroll)
  const revealElements = document.querySelectorAll('.reveal-on-scroll');
  if (revealElements.length > 0) {
    if ('IntersectionObserver' in window) {
      const revealObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            entry.target.classList.add('is-revealed');
            observer.unobserve(entry.target);
          }
        });
      }, {
        threshold: 0.12,
        rootMargin: '0px 0px -40px 0px'
      });

      revealElements.forEach(el => revealObserver.observe(el));
    } else {
      // Fallback jika browser lawas
      revealElements.forEach(el => el.classList.add('is-revealed'));
    }
  }

  // 5. Toast Helper (Clean tanpa emoji)
  window.showToast = function(msg) {
    let toast = document.querySelector('.toast');
    if (!toast) {
      toast = document.createElement('div');
      toast.className = 'toast';
      document.body.appendChild(toast);
    }
    toast.innerHTML = `
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="color:var(--accent-purple);">
        <circle cx="12" cy="12" r="10"></circle>
        <line x1="12" y1="16" x2="12" y2="12"></line>
        <line x1="12" y1="8" x2="12.01" y2="8"></line>
      </svg>
      <span>${msg}</span>
    `;
    toast.classList.add('show');
    setTimeout(() => {
      toast.classList.remove('show');
    }, 3500);
  };
});

