/**
 * ============================================
 * WELCOME PAGE - Enhanced Interactions
 * ============================================
 * Smooth, modern UX enhancements
 * ============================================
 */

// ============================================
// Mobile Menu Toggle
// ============================================
document.addEventListener('DOMContentLoaded', function() {
  const mobileMenuToggle = document.getElementById('mobileMenuToggle');
  const navbarLinks = document.getElementById('navbarLinks');
  const mobileMenuOverlay = document.getElementById('mobileMenuOverlay');

  if (mobileMenuToggle && navbarLinks) {
    // Toggle menu
    mobileMenuToggle.addEventListener('click', function() {
      navbarLinks.classList.toggle('active');
      if (mobileMenuOverlay) {
        mobileMenuOverlay.classList.toggle('active');
      }
      const icon = mobileMenuToggle.querySelector('i');
      if (navbarLinks.classList.contains('active')) {
        icon.classList.remove('fa-bars');
        icon.classList.add('fa-times');
      } else {
        icon.classList.remove('fa-times');
        icon.classList.add('fa-bars');
      }
    });

    // Close menu when clicking overlay
    if (mobileMenuOverlay) {
      mobileMenuOverlay.addEventListener('click', function() {
        navbarLinks.classList.remove('active');
        mobileMenuOverlay.classList.remove('active');
        const icon = mobileMenuToggle.querySelector('i');
        icon.classList.remove('fa-times');
        icon.classList.add('fa-bars');
      });
    }

    // Close menu when clicking a link
    navbarLinks.querySelectorAll('a').forEach(link => {
      link.addEventListener('click', function() {
        navbarLinks.classList.remove('active');
        if (mobileMenuOverlay) {
          mobileMenuOverlay.classList.remove('active');
        }
        const icon = mobileMenuToggle.querySelector('i');
        icon.classList.remove('fa-times');
        icon.classList.add('fa-bars');
      });
    });

    // Close menu on escape key
    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape' && navbarLinks.classList.contains('active')) {
        navbarLinks.classList.remove('active');
        if (mobileMenuOverlay) {
          mobileMenuOverlay.classList.remove('active');
        }
        const icon = mobileMenuToggle.querySelector('i');
        icon.classList.remove('fa-times');
        icon.classList.add('fa-bars');
      }
    });
  }
});

// ============================================
// Navbar Active State with Smooth Scrolling
// ============================================
  const sections = document.querySelectorAll("section");
  const navLinks = document.querySelectorAll(".navbar-links a");

// Smooth scroll function
function smoothScrollTo(target) {
  const element = document.querySelector(target);
  if (element) {
    const offsetTop = element.offsetTop - 20; // Small offset since navbar is not sticky
    window.scrollTo({
      top: offsetTop,
      behavior: 'smooth'
    });
  }
}

// Update active nav link on scroll
  window.addEventListener("scroll", () => {
    let current = "";
  const scrollPosition = window.scrollY + 200;

    sections.forEach(sec => {
    const top = sec.offsetTop;
      const height = sec.offsetHeight;
    if (scrollPosition >= top && scrollPosition < top + height) {
        current = sec.getAttribute("id");
      }
    });

    navLinks.forEach(link => {
      link.classList.remove("active");
    if (link.getAttribute("href") === `#${current}`) {
      link.classList.add("active");
    }
    });
  });

// Add click handlers for smooth scrolling
navLinks.forEach(link => {
  link.addEventListener('click', (e) => {
    e.preventDefault();
    const target = link.getAttribute("href");
    smoothScrollTo(target);
  });
});

// ============================================
// About and Footer Animation with Intersection Observer
// ============================================
  const aboutSection = document.querySelector(".about-section");
  const footer = document.querySelector("footer");

// Use Intersection Observer for better performance
const observerOptions = {
  threshold: 0.1,
  rootMargin: '0px 0px -50px 0px'
};

const observer = new IntersectionObserver((entries) => {
  entries.forEach(entry => {
    if (entry.isIntersecting) {
      if (entry.target === aboutSection && aboutSection) {
      aboutSection.classList.add("about-visible");
    }
      // Footer is always visible, but we can add animation if needed
      if (entry.target === footer && footer) {
      footer.classList.add("visible");
    }
    }
  });
}, observerOptions);

// Only observe if elements exist
if (aboutSection) {
  observer.observe(aboutSection);
  // Make visible immediately if already in viewport
  const rect = aboutSection.getBoundingClientRect();
  if (rect.top < window.innerHeight) {
    aboutSection.classList.add("about-visible");
  }
}

if (footer) {
  // Footer is always visible, just add visible class for any animations
  footer.classList.add("visible");
}

// ============================================
// Hero Background Switching (Enhanced)
// ============================================
  const heroBgs = document.querySelectorAll(".hero-bg");
  const leftBtn = document.querySelector(".hero-arrow.left");
  const rightBtn = document.querySelector(".hero-arrow.right");
  let currentIndex = 0;
let autoSwitchInterval;

// Function to change background with smooth transition
  function changeBackground(next = true) {
  // Remove active from current
    heroBgs[currentIndex].classList.remove("active");
  
  // Calculate new index
    if (next) {
      currentIndex = (currentIndex + 1) % heroBgs.length;
    } else {
      currentIndex = (currentIndex - 1 + heroBgs.length) % heroBgs.length;
    }
  
  // Add active to new background
  setTimeout(() => {
    heroBgs[currentIndex].classList.add("active");
  }, 50);
  }

// Manual arrow controls
if (rightBtn) {
  rightBtn.addEventListener("click", () => {
    changeBackground(true);
    resetAutoSwitch();
  });
}

if (leftBtn) {
  leftBtn.addEventListener("click", () => {
    changeBackground(false);
    resetAutoSwitch();
  });
}

// Auto-switch function
function startAutoSwitch() {
  autoSwitchInterval = setInterval(() => {
    changeBackground(true);
  }, 6000);
}

function resetAutoSwitch() {
  clearInterval(autoSwitchInterval);
  startAutoSwitch();
}

// Start auto-switching
if (heroBgs.length > 1) {
  startAutoSwitch();
  
  // Pause on hover
  const hero = document.querySelector('.hero');
  if (hero) {
    hero.addEventListener('mouseenter', () => {
      clearInterval(autoSwitchInterval);
    });
    
    hero.addEventListener('mouseleave', () => {
      startAutoSwitch();
    });
  }
}

// Keyboard navigation
document.addEventListener('keydown', (e) => {
  if (e.key === 'ArrowRight') {
    changeBackground(true);
    resetAutoSwitch();
  } else if (e.key === 'ArrowLeft') {
    changeBackground(false);
    resetAutoSwitch();
  }
});

// ============================================
// Toggle "More Details" Section (Enhanced)
// ============================================
const toggleBtn = document.getElementById("toggle-details");
const details = document.getElementById("details-content");
let visible = false;

if (toggleBtn && details) {
toggleBtn.addEventListener("click", () => {
  visible = !visible;
  details.classList.toggle("show");
    
    // Smooth scroll to details when opening
    if (visible) {
      setTimeout(() => {
        details.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
      }, 300);
    }
    
    // Update button text and icon
  toggleBtn.innerHTML = visible
    ? '<i class="fa-solid fa-circle-xmark ms-2"></i> إخفاء التفاصيل'
    : '<i class="fa-solid fa-circle-info ms-2"></i> المزيد من التفاصيل';
    
    // Add animation class
    toggleBtn.classList.toggle('active', visible);
  });
}

// ============================================
// Enhanced Button Interactions
// ============================================
const buttons = document.querySelectorAll('.btn');
buttons.forEach(btn => {
  btn.addEventListener('mouseenter', function() {
    this.style.transform = 'translateY(-4px) scale(1.05)';
  });
  
  btn.addEventListener('mouseleave', function() {
    this.style.transform = 'translateY(0) scale(1)';
  });
  
  btn.addEventListener('mousedown', function() {
    this.style.transform = 'translateY(-2px) scale(1.02)';
  });
  
  btn.addEventListener('mouseup', function() {
    this.style.transform = 'translateY(-4px) scale(1.05)';
  });
});

// Navbar scroll effect removed - navbar is no longer sticky

// ============================================
// Loading Animation
// ============================================
window.addEventListener('load', () => {
  document.body.classList.add('loaded');
  
  // Fade in hero content
  const heroContent = document.querySelector('.hero-content');
  if (heroContent) {
    heroContent.style.opacity = '0';
    heroContent.style.transform = 'translateY(40px)';
    
    setTimeout(() => {
      heroContent.style.transition = 'all 1.5s ease';
      heroContent.style.opacity = '1';
      heroContent.style.transform = 'translateY(0)';
    }, 100);
  }
});

// ============================================
// FAQ Accordion Functionality
// ============================================
const faqItems = document.querySelectorAll('.faq-item');

faqItems.forEach(item => {
  const question = item.querySelector('.faq-question');
  
  question.addEventListener('click', () => {
    const isActive = item.classList.contains('active');
    
    // Close all other items
    faqItems.forEach(otherItem => {
      if (otherItem !== item) {
        otherItem.classList.remove('active');
      }
    });
    
    // Toggle current item
    item.classList.toggle('active', !isActive);
  });
});

// ============================================
// Statistics Counter Animation
// ============================================
function animateCounter(element, target, duration = 2000) {
  let start = 0;
  const increment = target / (duration / 16);
  
  const timer = setInterval(() => {
    start += increment;
    if (start >= target) {
      element.textContent = target.toLocaleString('en-US');
      clearInterval(timer);
    } else {
      element.textContent = Math.floor(start).toLocaleString('en-US');
    }
  }, 16);
}

const statNumbers = document.querySelectorAll('.stat-number');
let statsAnimated = false;

function checkStatsVisibility() {
  const statsSection = document.querySelector('.statistics-section');
  if (!statsSection || statsAnimated) return;
  
  const rect = statsSection.getBoundingClientRect();
  const isVisible = rect.top < window.innerHeight && rect.bottom > 0;
  
  if (isVisible) {
    statNumbers.forEach(stat => {
      const target = parseInt(stat.getAttribute('data-target'));
      if (target) {
        animateCounter(stat, target);
      }
    });
    statsAnimated = true;
  }
}

// Check on scroll
window.addEventListener('scroll', checkStatsVisibility);
// Check on load
checkStatsVisibility();

// ============================================
// Section Visibility Animations
// ============================================
// Note: sections is already declared above, reusing it
const sectionHeaders = document.querySelectorAll('.section-header');
const featureCards = document.querySelectorAll('.feature-card');
const stepItems = document.querySelectorAll('.step-item');

const sectionObserver = new IntersectionObserver((entries) => {
  entries.forEach(entry => {
    if (entry.isIntersecting) {
      entry.target.classList.add('visible');
      
      // Animate feature cards with stagger
      if (entry.target.classList.contains('features-section')) {
        featureCards.forEach((card, index) => {
          setTimeout(() => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(30px)';
            card.style.transition = 'all 0.6s ease';
            
            setTimeout(() => {
              card.style.opacity = '1';
              card.style.transform = 'translateY(0)';
            }, 50);
          }, index * 100);
        });
      }
      
      // Animate step items with stagger
      if (entry.target.classList.contains('how-it-works-section')) {
        stepItems.forEach((step, index) => {
          setTimeout(() => {
            step.classList.add('visible');
          }, index * 200);
        });
      }
    }
  });
}, {
  threshold: 0.1,
  rootMargin: '0px 0px -100px 0px'
});

// Observe sections
sections.forEach(section => {
  sectionObserver.observe(section);
});

sectionHeaders.forEach(header => {
  sectionObserver.observe(header);
});

// ============================================
// Performance Optimization
// ============================================
// Throttle scroll events
function throttle(func, wait) {
  let timeout;
  return function executedFunction(...args) {
    const later = () => {
      clearTimeout(timeout);
      func(...args);
    };
    clearTimeout(timeout);
    timeout = setTimeout(later, wait);
  };
}

// Apply throttling to scroll handlers
const throttledScrollHandler = throttle(() => {
  checkStatsVisibility();
}, 16); // ~60fps

window.addEventListener('scroll', throttledScrollHandler);
