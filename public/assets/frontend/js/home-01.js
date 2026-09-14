    /* ==========================================================================
       Go4Database - Main Application Scripts
       ========================================================================== */
    
    // --- Script Section 1 ---
    function filterFavCards(cat, btn) {
              document.querySelectorAll('.fav-tab-btn').forEach(b => b.classList.remove('active'));
              if (btn) btn.classList.add('active');
    
              const cards = document.querySelectorAll('.fav-banner-card');
              cards.forEach(card => {
                if (cat === 'all' || card.getAttribute('data-category') === cat) {
                  card.style.display = 'flex';
                  card.style.opacity = '0';
                  card.style.transform = 'translateY(8px)';
                  setTimeout(() => {
                    card.style.transition = 'opacity 0.25s ease, transform 0.25s ease';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                  }, 20);
                } else {
                  card.style.display = 'none';
                }
              });
            }
    
    
        // Input Filtering
        function setupFilterListeners() {
          const inputs = ['filter-title', 'filter-industry', 'filter-location'];

          // There is no Search button any more, so typing has to fetch. Ask the
          // API once the visitor stops typing rather than on every keystroke.
          let fetchTimer;
          const onType = () => {
            clearTimeout(fetchTimer);
            fetchTimer = setTimeout(buildParamsAndFetch, 550);
          };

          inputs.forEach(id => {
            const el = document.getElementById(id);
            if (!el) return;
            el.addEventListener('input', onType);
            el.addEventListener('keydown', (e) => {
              if (e.key === 'Enter') {
                e.preventDefault();
                clearTimeout(fetchTimer);
                buildParamsAndFetch();
              }
            });
          });
        }
    
        // Filter Modal
        function setupModal() {
          const modal = document.getElementById('filter-modal');
          const openBtn = document.getElementById('open-filter-btn');
          const closeBtn = document.getElementById('close-modal-x');
          const applyBtn = document.getElementById('apply-modal-btn');
          const resetBtn = document.getElementById('reset-modal-btn');
    
          if (openBtn && modal) openBtn.addEventListener('click', (e) => { e.preventDefault(); modal.classList.remove('hidden'); });
          if (closeBtn && modal) closeBtn.addEventListener('click', () => modal.classList.add('hidden'));
          if (modal) modal.addEventListener('click', (e) => { if (e.target === modal) modal.classList.add('hidden'); });
          if (resetBtn && modal) resetBtn.addEventListener('click', () => {
            modal.querySelectorAll('input').forEach(i => i.value = '');
          });
          // The modal's fields aren't sent to the API yet, so Apply re-runs the
          // current search instead of replacing real results with sample data.
          if (applyBtn && modal) applyBtn.addEventListener('click', () => {
            modal.classList.add('hidden');
            buildParamsAndFetch();
          });
        }
    
        // Category Tabs
        // function setupCategoryTabs() {
        //   const tabs = document.querySelectorAll('.category-tab');
        //   tabs.forEach(tab => {
        //     tab.addEventListener('click', () => {
        //       tabs.forEach(t => t.classList.remove('active'));
        //       tab.classList.add('active');
        //       renderLeads(leads);
        //     });
        //   });
        // }
    
        // 21st.dev Card Stacking Observer (Scale down card as next card overlays it)
        function setupCardStackingEffect() {
          const cards = document.querySelectorAll('.clay-card-stack');
          if (!cards.length) return;
    
          const updateStacking = () => {
            cards.forEach((card, idx) => {
              const nextCard = cards[idx + 1];
              if (nextCard) {
                const nextRect = nextCard.getBoundingClientRect();
                const triggerDistance = 300;
                if (nextRect.top <= triggerDistance && nextRect.top > 0) {
                  const progress = (triggerDistance - nextRect.top) / triggerDistance;
                  const scale = 1 - (progress * 0.05); // Smooth shrink to 95%
                  const opacity = 1 - (progress * 0.25);
                  card.style.transform = `scale(${scale})`;
                  card.style.opacity = opacity;
                } else if (nextRect.top <= 0) {
                  card.style.transform = `scale(0.95)`;
                  card.style.opacity = 0.75;
                } else {
                  card.style.transform = `scale(1)`;
                  card.style.opacity = 1;
                }
              }
            });
          };
    
          window.addEventListener('scroll', updateStacking, { passive: true });
          updateStacking();
        }
    
        // FAQ Accordion
        function setupFAQ() {
          const items = document.querySelectorAll('.faq-item');
          items.forEach(item => {
            const q = item.querySelector('.faq-question');
            if (!q) return;
            q.addEventListener('click', (e) => {
              e.preventDefault();
              const isOpen = item.classList.contains('open');
              // Close all FAQ items
              items.forEach(i => i.classList.remove('open'));
              // Toggle target item
              if (!isOpen) {
                item.classList.add('open');
              }
            });
          });
        }
    
        // Contact Form
        function setupContactForm() {
          const form = document.getElementById('contact-form');
          const btn = document.getElementById('contact-submit-btn');
          form.addEventListener('submit', (e) => {
            e.preventDefault();
            btn.textContent = "Trial Activated! Redirecting...";
            btn.style.background = "#14501e";
            setTimeout(() => {
              btn.textContent = "Talk to an Expert \u2192";
              btn.style.background = "#6fd943";
              form.reset();
            }, 2500);
          });
        }
    
        // Scroll Top
        function setupScrollTop() {
          const btn = document.getElementById('scroll-top-btn');
          window.addEventListener('scroll', () => {
            if (window.scrollY > 400) {
              btn.classList.remove('hidden');
            } else {
              btn.classList.add('hidden');
            }
          }, { passive: true });
        }
    
        // 21st.dev Simple Animated Testimonials JS (by Bankkroll)
      // 21st.dev Simple Animated Testimonials JS (by Bankkroll)
        function setupSimpleAnimatedTestimonials() {
          // Prefer live data injected by Blade (window.__TESTIMONIALS__).
          // Falls back to static data if a page doesn't inject any.
          const testimonials = (Array.isArray(window.__TESTIMONIALS__) && window.__TESTIMONIALS__.length)
            ? window.__TESTIMONIALS__
            : [
              {
                quote: '"Go4Database real-time email verification completely eliminated our bounce rates. Our SDR team booked 3x more meetings in the first month alone."',
                name: "Chris Wilmot",
                role: "VP of Outbound, CloudScale",
                metric: "$1,000,000+ Revenue",
                rating: "★★★★★"
              },
              {
                quote: '"The accuracy is night and day compared to ZoomInfo. The built-in email verification alone paid for our annual plan in the very first week."',
                name: "Jack Copeland",
                role: "Head of Revenue, Nimbus",
                metric: "1,567% ROI in 60 Days",
                rating: "★★★★★"
              },
              {
                quote: '"Technographic filters enabled us to target exact companies running HubSpot and Salesforce. Our cold email response rates jumped from 2% to 8.5%."',
                name: "Mike Giuffrida",
                role: "Growth Lead, Foundry Growth",
                metric: "$400k+ New Pipeline",
                rating: "★★★★★"
              }
            ];
    
          if (!testimonials.length) return;
    
          let currentIndex = 0;
          let timer = null;
          const updateTestimonial = (index) => {
            currentIndex = index;
            const data = testimonials[index];
            // Update Text & Badges
            const quoteEl = document.getElementById('anim-quote-text');
            const nameEl = document.getElementById('anim-author-name');
            const roleEl = document.getElementById('anim-author-role');
            const metricEl = document.getElementById('anim-metric-badge');
            const ratingEl = document.getElementById('anim-rating-text');
            if (quoteEl) {
              quoteEl.style.opacity = '0';
              quoteEl.style.transform = 'translateY(8px)';
              setTimeout(() => {
                quoteEl.textContent = data.quote;
                if (nameEl) nameEl.textContent = data.name;
                if (roleEl) roleEl.textContent = data.role;
                if (metricEl) metricEl.textContent = data.metric;
                if (ratingEl) ratingEl.textContent = data.rating;
                quoteEl.style.opacity = '1';
                quoteEl.style.transform = 'translateY(0)';
              }, 180);
            }
            // Update 3D Stack Cards
            [0, 1, 2].forEach(i => {
              const card = document.getElementById(`anim-card-${i}`);
              if (!card) return;
              const diff = (i - index + testimonials.length) % testimonials.length;
              if (diff === 0) {
                // Front Active Card
                card.style.zIndex = '3';
                card.style.transform = 'translate(0px, 0px) scale(1) rotate(0deg)';
                card.style.opacity = '1';
              } else if (diff === 1) {
                // Second Card
                card.style.zIndex = '2';
                card.style.transform = 'translate(20px, -14px) scale(0.93) rotate(5deg)';
                card.style.opacity = '0.85';
              } else {
                // Third Card
                card.style.zIndex = '1';
                card.style.transform = 'translate(-20px, -24px) scale(0.86) rotate(-5deg)';
                card.style.opacity = '0.7';
              }
            });
          };
          const next = () => {
            updateTestimonial((currentIndex + 1) % testimonials.length);
          };
          const prev = () => {
            updateTestimonial((currentIndex - 1 + testimonials.length) % testimonials.length);
          };
          document.getElementById('anim-next-btn')?.addEventListener('click', () => {
            next();
            resetTimer();
          });
          document.getElementById('anim-prev-btn')?.addEventListener('click', () => {
            prev();
            resetTimer();
          });
          const resetTimer = () => {
            if (timer) clearInterval(timer);
            timer = setInterval(next, 5000);
          };
          updateTestimonial(0);
          resetTimer();
        }
    
        function setupHeroWordRotation() {
          const words = [
            "AI Lead Generation",
            "High-Intent B2B Sales Leads",
            "Real-Time Verified B2B Emails",
            "Executive Decision Makers",
            "AI-Powered B2B Prospecting"
          ];
          let wordIndex = 0;
          let charIndex = 0;
          let isDeleting = false;
          const target = document.getElementById('hero-rotating-word');
          if (!target) return;
    
          function type() {
            const currentWord = words[wordIndex];
            if (isDeleting) {
              charIndex--;
            } else {
              charIndex++;
            }
    
            target.textContent = currentWord.substring(0, charIndex);
    
            let typeSpeed = isDeleting ? 30 : 60;
    
            if (!isDeleting && charIndex === currentWord.length) {
              typeSpeed = 2000; // Pause when word is completely typed
              isDeleting = true;
            } else if (isDeleting && charIndex === 0) {
              isDeleting = false;
              wordIndex = (wordIndex + 1) % words.length;
              typeSpeed = 500; // Pause before starting to type the next word
            }
    
            setTimeout(type, typeSpeed);
          }
    
          type();
        }
    /* ==========================================================================
   Go4Database - Leads Fetch + Render + Filter
   ========================================================================== */

// Website-only search endpoint. It uses the app's Normal filter rules, returns
// at most 5 leads plus a total, and never sends emails or phone numbers, only
// has_email / has_phone flags.
// A local copy of the site sets G4D_LEADS_API to its own proxy route, because
// the API only accepts calls from www.go4database.com.
const API_BASE = window.G4D_LEADS_API || 'https://app.go4database.com/api/website/leads';

// Lead fields go into innerHTML, so escape them rather than trusting the data.
function escapeHtml(value) {
  return String(value ?? '').replace(/[&<>"']/g, ch => ({
    '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
  })[ch]);
}

  // Render Leads in the original grid-row layout
function renderLeads(leadsArr) {
  const container = document.getElementById('results-container');
  const rowsContainer = document.getElementById('leads-rows');
  if (!container || !rowsContainer) return;

  if (!leadsArr || leadsArr.length === 0) {
    rowsContainer.innerHTML = `
      <div style="padding:32px;text-align:center;color:#64748b;font-size:14px">
        No results found.
      </div>`;
    container.classList.remove('hidden');
    return;
  }

  container.classList.remove('hidden');

  rowsContainer.innerHTML = leadsArr.map(lead => {
    const hasEmail = !!lead.has_email;
    // The data uses "0000" and "" for unknown values, so show N/A for those too.
    const founded = lead.founded_year && lead.founded_year !== '0000' ? lead.founded_year : 'N/A';
    return `
    <div style="display:grid;grid-template-columns:36px 1.7fr 1.3fr 1.4fr 1.1fr 1.1fr;align-items:center;padding:16px 20px;border-bottom:1px solid #f1f5f9;font-size:13.5px;color:#333;min-width:860px;background:#fff">
      <span style="width:16px;height:16px;border:1.5px solid #cbd5e1;border-radius:3px;display:inline-block"></span>

      <div style="overflow:hidden;padding-right:12px">
        <div style="font-weight:700;color:#1e293b;font-size:14px;line-height:1.3;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">${escapeHtml(lead.company)}</div>
        <div style="display:flex;align-items:center;gap:6px;margin:4px 0 2px">
          <span style="color:#1f7a2e;font-size:12px">🌐</span>
          <span style="background:#1f7a2e;color:#fff;font-size:9px;font-weight:800;padding:1px 4px;border-radius:3px;display:inline-flex;align-items:center;justify-content:center;line-height:1">in</span>
          <span style="background:#1f7a2e;color:#fff;font-size:9px;font-weight:800;width:14px;height:14px;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;line-height:1">f</span>
        </div>
        <div style="font-size:11.5px;color:#64748b">Founded: ${escapeHtml(founded)}, Turnover: ${escapeHtml(lead.turnover || 'N/A')}</div>
      </div>

      <div style="overflow:hidden;padding-right:12px">
        <div style="font-weight:700;color:#1e293b;font-size:14px">${escapeHtml(lead.person_name)}</div>
        <div style="margin-top:3px">
          <span style="background:#1f7a2e;color:#fff;font-size:9px;font-weight:800;padding:1px 4px;border-radius:3px;display:inline-flex;align-items:center;justify-content:center;line-height:1">in</span>
        </div>
      </div>

      <div style="color:#475569;font-size:13.5px;font-weight:500;padding-right:12px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">
        ${escapeHtml(lead.title)}
      </div>

      <div>
        <button class="verify-email-btn view-btn" data-type="email"
          style="border:1px solid #cbd5e1;background:#fff;border-radius:6px;padding:7px 12px;font-size:12.5px;font-weight:600;color:#334155;display:inline-flex;align-items:center;gap:6px;cursor:pointer;box-shadow:0 1px 2px rgba(0,0,0,0.04)">
          View email
          <span style="display:inline-flex;align-items:center;justify-content:center;background:${hasEmail ? '#10b981' : '#ef4444'};color:#fff;width:16px;height:14px;border-radius:3px;font-size:10px">${hasEmail ? '✉✓' : '✉✕'}</span>
        </button>
      </div>

      <div>
        <button class="view-btn" data-type="contact" style="border:1px solid #cbd5e1;background:#fff;border-radius:6px;padding:7px 12px;font-size:12.5px;font-weight:600;color:#334155;display:inline-flex;align-items:center;gap:6px;cursor:pointer;box-shadow:0 1px 2px rgba(0,0,0,0.04)">
          View Contact <span style="color:#10b981;font-size:13px">📞</span>
        </button>
      </div>
    </div>
    `;
  }).join('');
}

function showLoadingRow() {
  const container = document.getElementById('results-container');
  const rowsContainer = document.getElementById('leads-rows');
  if (!container || !rowsContainer) return;
  container.classList.remove('hidden');
  rowsContainer.innerHTML = `
    <div style="padding:32px;text-align:center;color:#64748b;font-size:14px">
      Loading...
    </div>`;
}

// Typing and tab clicks can overlap requests; only the newest one may render,
// so a slow earlier response can't overwrite the results of a later search.
let latestLeadsRequest = 0;

function fetchLeads(params) {
  const requestId = ++latestLeadsRequest;
  const query = new URLSearchParams(params).toString();
  showLoadingRow();

  fetch(`${API_BASE}?${query}`, {
    method: 'GET',
    headers: { 'Accept': 'application/json' }
  })
    .then(res => {
      if (!res.ok) throw new Error('Network response was not ok');
      return res.json();
    })
    .then(json => {
      if (requestId !== latestLeadsRequest) return;
      let leadsArr = [];
      if (Array.isArray(json)) {
        leadsArr = json;
      } else if (Array.isArray(json.data)) {
        leadsArr = json.data;
      }
      renderLeads(leadsArr);
    })
    .catch(err => {
      if (requestId !== latestLeadsRequest) return;
      console.error('Lead fetch failed:', err);
      const rowsContainer = document.getElementById('leads-rows');
      if (rowsContainer) {
        rowsContainer.innerHTML = `
          <div style="padding:32px;text-align:center;color:#ef4444;font-size:14px">
            Something went wrong. Please try again.
          </div>`;
      }
    });
}

function buildParamsAndFetch() {
  // Same three boxes as the app's Normal filter. Each box takes comma separated
  // values, e.g. "CEO, CTO".
  const params = {
    title: document.getElementById('filter-title')?.value.trim() || '',
    industry_business: document.getElementById('filter-industry')?.value.trim() || '',
    location: document.getElementById('filter-location')?.value.trim() || '',
  };

  // drop empty params
  Object.keys(params).forEach(k => !params[k] && delete params[k]);

  // The API returns nothing for an empty search, so skip the round trip.
  if (!Object.keys(params).length) {
    latestLeadsRequest++; // discard any request still in flight
    renderLeads([]);
    return;
  }

  fetchLeads(params);
}


        function switchSearchTab(element, presetKey) {
          document.querySelectorAll('#search-section .category-tab').forEach(tab => tab.classList.remove('active'));
          element.classList.add('active');
    
          const titleInput = document.getElementById('filter-title');
          if (titleInput) {
            // Comma separated, matching the API. The CEO tab must search "CEO"
            // because an empty search returns nothing.
            if (presetKey === 'CEO') titleInput.value = 'CEO';
            else if (presetKey === 'VP') titleInput.value = 'VP, Director';
            else if (presetKey === 'TECH') titleInput.value = 'CTO, IT Head';
            else if (presetKey === 'HEALTH') titleInput.value = 'Healthcare Executive';
            else if (presetKey === 'SALES') titleInput.value = 'VP Sales, CMO';
            else if (presetKey === 'FINANCE') titleInput.value = 'CFO';
          }
          buildParamsAndFetch();
        }
    
        // Blog Category Filter & Interactive Reader Modal
        function filterBlog(category, btn) {
          const buttons = document.querySelectorAll('.blog-cat-btn');
          buttons.forEach(b => b.classList.remove('active'));
          if (btn) btn.classList.add('active');
    
          const items = document.querySelectorAll('.blog-item');
          items.forEach(item => {
            if (category === 'all' || item.getAttribute('data-category') === category) {
              item.style.display = 'flex';
            } else {
              item.style.display = 'none';
            }
          });
        }
    
        function openBlogArticle(id) {
          const modal = document.getElementById('blog-reader-modal');
          const data = blogArticlesData[id];
          if (!modal || !data) return;
    
          document.getElementById('modal-blog-cat').textContent = data.cat;
          document.getElementById('modal-blog-title').textContent = data.title;
          document.getElementById('modal-blog-meta').textContent = data.meta;
          document.getElementById('modal-blog-img').src = data.img;
          document.getElementById('modal-blog-body').innerHTML = data.content;
    
          modal.style.display = 'flex';
          document.body.style.overflow = 'hidden';
        }
    
        function closeBlogArticle() {
          const modal = document.getElementById('blog-reader-modal');
          if (modal) modal.style.display = 'none';
          document.body.style.overflow = 'auto';
        }
    
        document.getElementById('blog-reader-modal')?.addEventListener('click', (e) => {
          if (e.target.id === 'blog-reader-modal') {
            closeBlogArticle();
          }
        });
    
        // Sticky Navbar Scroll Shadow
        function setupStickyNav() {
          const nav = document.querySelector('.g4d-sticky-nav');
          if (!nav) return;
          const onScroll = () => {
            if (window.scrollY > 20) {
              nav.classList.add('scrolled');
            } else {
              nav.classList.remove('scrolled');
            }
          };
          window.addEventListener('scroll', onScroll, { passive: true });
          onScroll();
        }
    
        // Nav Dropdown Click & Hover Toggle Handling
        function setupNavDropdowns() {
          const dropdownWrappers = document.querySelectorAll('.nav-dropdown-wrapper');
    
          dropdownWrappers.forEach(wrapper => {
            const trigger = wrapper.querySelector('.nav-link-item');
            if (!trigger) return;
    
            trigger.addEventListener('click', (e) => {
              e.preventDefault();
              e.stopPropagation();
    
              const isOpen = wrapper.classList.contains('active');
    
              // Close all other dropdowns
              dropdownWrappers.forEach(w => w.classList.remove('active'));
    
              if (!isOpen) {
                wrapper.classList.add('active');
              }
            });
          });
    
          // Close when clicking anywhere outside
          document.addEventListener('click', (e) => {
            if (!e.target.closest('.nav-dropdown-wrapper')) {
              dropdownWrappers.forEach(w => w.classList.remove('active'));
            }
          });
    
          // Close dropdown on escape key
          document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
              dropdownWrappers.forEach(w => w.classList.remove('active'));
            }
          });
        }
    
        // Init
        document.addEventListener('DOMContentLoaded', () => {
  setupStickyNav();
  setupNavDropdowns();

  // Auto-fill "CEO" in the title filter since CEO tab is active by default
  const titleInput = document.getElementById('filter-title');
  if (titleInput) {
    titleInput.value = 'CEO';
  }

  showLoadingRow();          // show "Loading..." immediately
  buildParamsAndFetch();     // fetch leads filtered by "CEO" (uses the pre-filled input)

  setupFilterListeners();
  setupModal();
  setupCardStackingEffect();
  setupFAQ();
  setupSimpleAnimatedTestimonials();
  setupHeroWordRotation();
  setupContactForm();
  setupScrollTop();
});

// View email / View Contact -> Register, with tracking
document.addEventListener('click', function (e) {
  const btn = e.target.closest('#leads-rows .view-btn');
  if (!btn) return;

  const action = btn.dataset.type === 'contact' ? 'view_contact' : 'view_email';
  const registerUrl = 'https://app.go4database.com/register?utm_source=Homepage&utm_medium=Internal&utm_campaign=' + action;

  // Leaving the page straight away can drop the GA4 hit, so wait for gtag to
  // confirm it, but never longer than 800ms (ad blockers stop the callback).
  let navigated = false;
  const go = () => {
    if (navigated) return;
    navigated = true;
    window.location.href = registerUrl;
  };

  if (typeof gtag === 'function') {
    gtag('event', action, { location: 'homepage_search', event_callback: go });
    setTimeout(go, 800);
  } else {
    go();
  }
});
    
