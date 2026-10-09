/* ==========================================================================
   Site behaviour.
   Everything here is progressive: the markup is complete and usable before
   this file runs, and every effect checks prefers-reduced-motion first.
   ========================================================================== */

(function () {
  'use strict';

  var reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  /* ------------------------------------------------------------------
     Scroll reveal. Children of a container are staggered by index.
     ------------------------------------------------------------------ */

  function initReveal() {
    var items = Array.prototype.slice.call(document.querySelectorAll('.reveal'));
    if (!items.length) return;

    if (!('IntersectionObserver' in window) || reduced) {
      items.forEach(function (el) { el.classList.add('is-in'); });
      return;
    }

    // Stagger siblings so a row of cards arrives in sequence, not all at once.
    var seen = new Map();
    items.forEach(function (el) {
      var parent = el.parentElement;
      var index = seen.get(parent) || 0;
      seen.set(parent, index + 1);
      if (index > 0 && index < 8) {
        el.style.setProperty('--delay', (index * 90) + 'ms');
      }
    });

    var observer = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (!entry.isIntersecting) return;
        entry.target.classList.add('is-in');
        observer.unobserve(entry.target);
      });
    }, { rootMargin: '0px 0px -8% 0px', threshold: 0.08 });

    items.forEach(function (el) { observer.observe(el); });
  }

  /* ------------------------------------------------------------------
     Ornament dividers animate with whichever block they sit in, so they
     are observed separately from .reveal.
     ------------------------------------------------------------------ */

  function initOrnaments() {
    var ornaments = Array.prototype.slice.call(document.querySelectorAll('.ornament'));
    if (!ornaments.length) return;

    if (!('IntersectionObserver' in window) || reduced) {
      ornaments.forEach(function (el) { el.classList.add('is-in'); });
      return;
    }

    var observer = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (!entry.isIntersecting) return;
        entry.target.classList.add('is-in');
        observer.unobserve(entry.target);
      });
    }, { threshold: 0.4 });

    ornaments.forEach(function (el) { observer.observe(el); });
  }

  /* ------------------------------------------------------------------
     Counters. The final figure is already in the DOM; this only animates
     up to it, so the number is correct at every point including failure.
     ------------------------------------------------------------------ */

  function initCounters() {
    var counters = Array.prototype.slice.call(document.querySelectorAll('.count'));
    if (!counters.length || reduced || !('IntersectionObserver' in window)) return;

    function run(el) {
      var target = parseFloat(el.getAttribute('data-count'));
      if (isNaN(target)) return;
      var duration = 1400;
      var start = null;

      function frame(timestamp) {
        if (start === null) start = timestamp;
        var progress = Math.min((timestamp - start) / duration, 1);
        // easeOutCubic
        var eased = 1 - Math.pow(1 - progress, 3);
        el.textContent = Math.round(target * eased).toLocaleString();
        if (progress < 1) {
          requestAnimationFrame(frame);
        } else {
          el.textContent = target.toLocaleString();
        }
      }
      requestAnimationFrame(frame);
    }

    var observer = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (!entry.isIntersecting) return;
        run(entry.target);
        observer.unobserve(entry.target);
      });
    }, { threshold: 0.5 });

    counters.forEach(function (el) { observer.observe(el); });
  }

  /* ------------------------------------------------------------------
     Header: shadow once scrolled, plus the thin reading-progress rule.
     ------------------------------------------------------------------ */

  function initHeader() {
    var head = document.getElementById('siteHead');
    var progress = document.getElementById('scrollProgress');
    var toTop = document.getElementById('toTop');
    if (!head) return;

    var ticking = false;

    function update() {
      var y = window.pageYOffset || document.documentElement.scrollTop;
      head.classList.toggle('is-stuck', y > 12);

      if (progress) {
        var height = document.documentElement.scrollHeight - window.innerHeight;
        var pct = height > 0 ? Math.min((y / height) * 100, 100) : 0;
        progress.style.setProperty('--progress', pct.toFixed(2) + '%');
      }
      if (toTop) {
        toTop.classList.toggle('is-visible', y > 700);
      }
      ticking = false;
    }

    window.addEventListener('scroll', function () {
      if (ticking) return;
      ticking = true;
      requestAnimationFrame(update);
    }, { passive: true });

    update();

    if (toTop) {
      toTop.addEventListener('click', function () {
        window.scrollTo({ top: 0, behavior: reduced ? 'auto' : 'smooth' });
      });
    }
  }

  /* ------------------------------------------------------------------
     Mobile navigation drawer + tap-to-open submenus.
     ------------------------------------------------------------------ */

  function initNav() {
    var burger = document.getElementById('burger');
    var nav = document.getElementById('primaryNav');
    if (!burger || !nav) return;

    var backdrop = document.createElement('div');
    backdrop.className = 'nav-backdrop';
    document.body.appendChild(backdrop);

    function setOpen(open) {
      burger.setAttribute('aria-expanded', open ? 'true' : 'false');
      burger.setAttribute('aria-label', open ? 'Close menu' : 'Open menu');
      nav.classList.toggle('is-open', open);
      backdrop.classList.toggle('is-open', open);
      document.body.style.overflow = open ? 'hidden' : '';
    }

    burger.addEventListener('click', function () {
      setOpen(burger.getAttribute('aria-expanded') !== 'true');
    });
    backdrop.addEventListener('click', function () { setOpen(false); });

    document.addEventListener('keydown', function (event) {
      if (event.key === 'Escape') setOpen(false);
    });

    // In the drawer the parent link toggles its submenu instead of navigating.
    // On desktop the submenu opens on hover, so the link keeps working.
    nav.querySelectorAll('.has-sub > .nav__link').forEach(function (link) {
      link.addEventListener('click', function (event) {
        if (!window.matchMedia('(max-width: 980px)').matches) return;
        var item = link.parentElement;
        event.preventDefault();
        if (item.classList.contains('is-open')) {
          item.classList.remove('is-open');
          link.setAttribute('aria-expanded', 'false');
        } else {
          nav.querySelectorAll('.nav__item.is-open').forEach(function (other) {
            if (other !== item) {
              other.classList.remove('is-open');
              var otherLink = other.querySelector('.nav__link');
              if (otherLink) otherLink.setAttribute('aria-expanded', 'false');
            }
          });
          item.classList.add('is-open');
          link.setAttribute('aria-expanded', 'true');
        }
      });
    });

    // Reset drawer state when the viewport grows past the breakpoint.
    window.addEventListener('resize', function () {
      if (!window.matchMedia('(max-width: 980px)').matches) {
        setOpen(false);
        nav.querySelectorAll('.nav__item.is-open').forEach(function (item) {
          item.classList.remove('is-open');
        });
      }
    });
  }

  /* ------------------------------------------------------------------
     Hero parallax. Layers carry data-depth; the deeper the layer the
     further it drifts.
     ------------------------------------------------------------------ */

  function initParallax() {
    var layers = Array.prototype.slice.call(document.querySelectorAll('.hero-art [data-depth]'));
    if (!layers.length || reduced) return;

    var hero = document.querySelector('.hero');
    if (!hero) return;

    var ticking = false;

    function update() {
      var rect = hero.getBoundingClientRect();
      if (rect.bottom < 0 || rect.top > window.innerHeight) { ticking = false; return; }
      var offset = -rect.top / Math.max(rect.height, 1);
      layers.forEach(function (layer) {
        var depth = parseFloat(layer.getAttribute('data-depth')) || 0;
        layer.setAttribute('transform', 'translate(0 ' + (offset * depth * 2.4).toFixed(2) + ')');
      });
      ticking = false;
    }

    window.addEventListener('scroll', function () {
      if (ticking) return;
      ticking = true;
      requestAnimationFrame(update);
    }, { passive: true });

    update();
  }

  /* ------------------------------------------------------------------
     Filter pills for the portfolio and testimonials grids.
     ------------------------------------------------------------------ */

  function initFilters() {
    document.querySelectorAll('[data-filter-group]').forEach(function (group) {
      var attribute = group.getAttribute('data-filter-attr') || 'data-tag';
      var targetSelector = group.getAttribute('data-filter-target');
      var items = Array.prototype.slice.call(document.querySelectorAll(targetSelector));
      var buttons = Array.prototype.slice.call(group.querySelectorAll('.filter'));
      var live = group.parentElement.querySelector('[data-filter-status]');

      buttons.forEach(function (button) {
        button.addEventListener('click', function () {
          var value = button.getAttribute('data-value');
          buttons.forEach(function (other) {
            other.classList.toggle('is-active', other === button);
            other.setAttribute('aria-pressed', other === button ? 'true' : 'false');
          });

          var shown = 0;
          items.forEach(function (item) {
            var match = value === 'all' || item.getAttribute(attribute) === value;
            item.classList.toggle('is-filtered-out', !match);
            if (match) {
              shown++;
              if (!reduced) {
                item.style.animation = 'none';
                /* force reflow so the animation restarts on each filter change */
                void item.offsetWidth;
                item.style.animation = 'unfold .45s cubic-bezier(.16,1,.3,1)';
              }
            }
          });

          if (live) {
            live.textContent = shown + (shown === 1 ? ' project shown' : ' items shown');
          }
        });
      });
    });
  }

  /* ------------------------------------------------------------------
     Multi-step quote form. Validation is client-side convenience only;
     quote.php validates every field again on the server.
     ------------------------------------------------------------------ */

  function initMultiStep() {
    var form = document.querySelector('[data-multistep]');
    if (!form) return;

    var steps = Array.prototype.slice.call(form.querySelectorAll('.fstep'));
    var navItems = Array.prototype.slice.call(form.querySelectorAll('.steps-nav__item'));
    var backBtn = form.querySelector('[data-step-back]');
    var nextBtn = form.querySelector('[data-step-next]');
    var submitBtn = form.querySelector('[data-step-submit]');
    if (steps.length < 2) return;

    var current = 0;

    function render() {
      steps.forEach(function (step, index) {
        step.classList.toggle('is-current', index === current);
      });
      navItems.forEach(function (item, index) {
        item.classList.toggle('is-current', index === current);
        item.classList.toggle('is-done', index < current);
      });
      if (backBtn) backBtn.style.visibility = current === 0 ? 'hidden' : 'visible';
      if (nextBtn) nextBtn.hidden = current === steps.length - 1;
      if (submitBtn) submitBtn.hidden = current !== steps.length - 1;

      var heading = steps[current].querySelector('h3, legend, label');
      if (heading && current > 0) {
        steps[current].setAttribute('tabindex', '-1');
        steps[current].focus({ preventScroll: true });
      }
    }

    function validateStep() {
      var fields = Array.prototype.slice.call(steps[current].querySelectorAll('input, select, textarea'));
      var firstBad = null;

      fields.forEach(function (field) {
        var holder = field.closest('.field') || field.closest('.fieldset');
        var error = holder ? holder.querySelector('.field__error') : null;
        var ok = field.checkValidity();
        field.setAttribute('aria-invalid', ok ? 'false' : 'true');
        if (error) error.textContent = ok ? '' : (field.validationMessage || 'Please check this field.');
        if (!ok && !firstBad) firstBad = field;
      });

      if (firstBad) {
        firstBad.focus();
        return false;
      }
      return true;
    }

    if (nextBtn) {
      nextBtn.addEventListener('click', function () {
        if (!validateStep()) return;
        current = Math.min(current + 1, steps.length - 1);
        render();
      });
    }
    if (backBtn) {
      backBtn.addEventListener('click', function () {
        current = Math.max(current - 1, 0);
        render();
      });
    }

    // Enter should advance rather than submit an incomplete form.
    form.addEventListener('keydown', function (event) {
      if (event.key !== 'Enter') return;
      if (event.target.tagName === 'TEXTAREA') return;
      if (current < steps.length - 1) {
        event.preventDefault();
        if (nextBtn) nextBtn.click();
      }
    });

    form.addEventListener('submit', function (event) {
      if (!validateStep()) event.preventDefault();
    });

    render();
  }

  /* ------------------------------------------------------------------
     Keep only one accordion panel open per group.
     ------------------------------------------------------------------ */

  function initAccordions() {
    document.querySelectorAll('.accordion').forEach(function (group) {
      var panels = Array.prototype.slice.call(group.querySelectorAll('details'));
      panels.forEach(function (panel) {
        panel.addEventListener('toggle', function () {
          if (!panel.open) return;
          panels.forEach(function (other) {
            if (other !== panel) other.open = false;
          });
        });
      });
    });
  }

  /* ------------------------------------------------------------------
     Testimonial strip: pause the auto-advance on hover or focus.
     ------------------------------------------------------------------ */

  function initQuoteRotator() {
    var rotator = document.querySelector('[data-rotator]');
    if (!rotator) return;
    var slides = Array.prototype.slice.call(rotator.querySelectorAll('[data-slide]'));
    if (slides.length < 2) return;

    var index = 0;
    var timer = null;

    function show(next) {
      slides.forEach(function (slide, i) {
        slide.hidden = i !== next;
      });
      index = next;
    }

    function start() {
      if (reduced || timer) return;
      timer = window.setInterval(function () {
        show((index + 1) % slides.length);
      }, 6500);
    }
    function stop() {
      if (!timer) return;
      window.clearInterval(timer);
      timer = null;
    }

    show(0);
    start();
    rotator.addEventListener('mouseenter', stop);
    rotator.addEventListener('mouseleave', start);
    rotator.addEventListener('focusin', stop);
    rotator.addEventListener('focusout', start);

    rotator.querySelectorAll('[data-rotator-dot]').forEach(function (dot, i) {
      dot.addEventListener('click', function () {
        stop();
        show(i);
        rotator.querySelectorAll('[data-rotator-dot]').forEach(function (other, j) {
          other.setAttribute('aria-current', j === i ? 'true' : 'false');
        });
        start();
      });
    });
  }

  /* ------------------------------------------------------------------ */

  /* ------------------------------------------------------------------
     Contact clicks are the site's main conversion. Taps on call, WhatsApp
     and email links are reported to analytics when it is configured.
     Nothing happens when no analytics tag is loaded.
     ------------------------------------------------------------------ */

  function initContactTracking() {
    document.addEventListener('click', function (event) {
      var link = event.target.closest ? event.target.closest('a[href]') : null;
      if (!link || typeof window.gtag !== 'function') return;

      var href = link.getAttribute('href') || '';
      var method = null;
      if (href.indexOf('tel:') === 0) method = 'phone';
      else if (href.indexOf('wa.me') !== -1 || href.indexOf('whatsapp') !== -1) method = 'whatsapp';
      else if (href.indexOf('mailto:') === 0) method = 'email';
      if (method === null) return;

      window.gtag('event', 'contact_click', {
        method: method,
        page_path: window.location.pathname,
        link_text: (link.textContent || '').trim().slice(0, 60)
      });
    });
  }

  /* ------------------------------------------------------------------
     Project slideshow on the home page. One slide visible at a time,
     advancing every few seconds. It pauses on hover or focus, can be
     paused by the visitor, and does not move for people who ask for
     reduced motion.
     ------------------------------------------------------------------ */

  function initSlideshows() {
    document.querySelectorAll('[data-slides]').forEach(function (root) {
      var slides = Array.prototype.slice.call(root.querySelectorAll('.slide'));
      var dots = Array.prototype.slice.call(root.querySelectorAll('[data-slide-go]'));
      var pause = root.querySelector('[data-slide-pause]');
      var interval = parseInt(root.getAttribute('data-interval'), 10) || 6500;
      if (slides.length < 2) return;

      var index = 0;
      var timer = null;
      var userPaused = false;

      function show(next) {
        index = (next + slides.length) % slides.length;
        slides.forEach(function (slide, i) { slide.hidden = i !== index; });
        dots.forEach(function (dot, i) {
          if (i === index) { dot.setAttribute('aria-current', 'true'); }
          else { dot.removeAttribute('aria-current'); }
        });
      }

      function stop() {
        if (timer) { window.clearInterval(timer); timer = null; }
      }

      function start() {
        if (reduced || userPaused || timer) return;
        if (root.matches(':hover') || root.contains(document.activeElement)) return;
        timer = window.setInterval(function () { show(index + 1); }, interval);
      }

      root.querySelector('[data-slide-prev]').addEventListener('click', function () { stop(); show(index - 1); start(); });
      root.querySelector('[data-slide-next]').addEventListener('click', function () { stop(); show(index + 1); start(); });
      dots.forEach(function (dot, i) {
        dot.addEventListener('click', function () { stop(); show(i); start(); });
      });
      root.addEventListener('keydown', function (event) {
        if (event.key === 'ArrowLeft') { stop(); show(index - 1); start(); }
        if (event.key === 'ArrowRight') { stop(); show(index + 1); start(); }
      });
      root.addEventListener('mouseenter', stop);
      root.addEventListener('mouseleave', start);
      root.addEventListener('focusin', stop);
      root.addEventListener('focusout', start);

      if (pause) {
        if (reduced) {
          pause.hidden = true;
        } else {
          pause.addEventListener('click', function () {
            userPaused = !userPaused;
            pause.setAttribute('aria-pressed', userPaused ? 'true' : 'false');
            pause.setAttribute('aria-label', userPaused ? 'Play slideshow' : 'Pause slideshow');
            pause.querySelector('span').textContent = userPaused ? 'Play' : 'Pause';
            if (userPaused) { stop(); } else { start(); }
          });
        }
      }

      show(0);
      start();
    });
  }

  function init() {
    initSlideshows();
    initContactTracking();
    initReveal();
    initOrnaments();
    initCounters();
    initHeader();
    initNav();
    initParallax();
    initFilters();
    initMultiStep();
    initAccordions();
    initQuoteRotator();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
