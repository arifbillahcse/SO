# Phase 8 — Manual QA Checklist

The automated test suite (`tests/Feature/`) covers routing, validation, database
wiring, and the sitemap. It cannot check visual rendering, JavaScript
interactions, or cross-browser behavior — this checklist covers that part.

Run `php artisan test` first and confirm everything passes before starting this.

## 1. Every page loads and looks right

For each page below: visit it, compare against what you remember from the
static site, and confirm nothing looks broken.

- [ ] Home (`/`)
- [ ] Website Development (`/web-development`)
- [ ] SEO Optimization (`/seo`)
- [ ] Manage Cloud (`/manage-cloud`)
- [ ] Automation (`/automation`)
- [ ] eCommerce Solution (`/ecommerce-solution`)
- [ ] Cloud Solution (`/cloud-solution`)
- [ ] Automation Solution (`/automation-solution`)
- [ ] Our Works (`/our-works`)
- [ ] About Us (`/about-us`)
- [ ] Case Study (`/case-study`)
- [ ] Our Resources (`/our-resources`)
- [ ] Affiliate Program (`/affiliate-program`)
- [ ] Our Founders (`/our-founders`) — direct link only, confirm it's NOT in nav/footer

## 2. Navigation

- [ ] Logo links to home
- [ ] Desktop: "Services" dropdown opens on hover, stays open while moving mouse into it (no gap-closing bug)
- [ ] Desktop: "Solutions" dropdown opens on hover
- [ ] Desktop: "Resources" dropdown opens on hover
- [ ] Active page is highlighted in the nav (visit each page, confirm its own nav link is styled differently)
- [ ] "Get in Touch" button scrolls to/links to the contact section
- [ ] Footer links all go to the correct pages
- [ ] Footer copyright year shows the current year automatically

## 3. Mobile navigation (resize browser to ~390px or use device emulation)

- [ ] Hamburger icon appears, "Get in Touch" button hides
- [ ] Tapping hamburger opens the mobile menu, animates to an X
- [ ] Mobile menu panel renders ABOVE page content (not hidden behind it)
- [ ] Tapping a dropdown trigger (Services/Solutions/Resources) expands it in place
- [ ] Tapping a link inside a dropdown navigates AND closes the mobile menu
- [ ] Menu scrolls internally if content is taller than the viewport

## 4. Homepage-specific interactions

- [ ] Hero stat counters animate upward when scrolled into view
- [ ] "Page Speed" and "SEO Score" floating cards render in FRONT of the browser mockup, not behind it
- [ ] Testimonials carousel: shows 3 cards on desktop, 2 on tablet, 1 on mobile
- [ ] Testimonials carousel: prev/next arrows work, dots are clickable, auto-advances every 5s
- [ ] Testimonials carousel: pauses auto-advance on mouse hover
- [ ] Client logo marquee scrolls continuously left with NO visible gap or jump at the loop point
- [ ] Works filter buttons (All/WordPress/eCommerce/Cloud/Automation/SEO) filter the portfolio grid correctly

## 5. Service/Solution page interactions

- [ ] FAQ accordion: clicking a question expands it, clicking another closes the first
- [ ] Pricing cards (where present) render correctly

## 6. Forms — test on at least 2 different pages (e.g. home + one service page)

- [ ] Submitting with empty required fields (name/email/message) shows a red border + does NOT submit
- [ ] Submitting with an invalid email format gets rejected
- [ ] Submitting a valid form shows "Sending..." then a success message, and clears the fields
- [ ] Check `storage/logs/laravel.log` (with `MAIL_MAILER=log`) — confirm BOTH the admin notification and the auto-reply email appear, with correct content
- [ ] Check the database record was created (`php artisan tinker` → `App\Models\ContactSubmission::latest()->first()`)
- [ ] Affiliate Program form specifically: confirm the submission lands in `affiliate_referrals`, NOT `contact_submissions`
- [ ] Submit the same form 6 times quickly — the 6th should be rate-limited (fails gracefully, doesn't crash the page)

## 7. Admin panel (once Filament is installed — see Phase 4 notes)

- [ ] Can log in at `/admin`
- [ ] Each of the 8 resources (Services, Testimonials, Case Studies, Portfolio, Articles, Team Members, Contact Submissions, Affiliate Referrals) loads without error
- [ ] Editing a Service and saving reflects on the homepage immediately (no cache issue)
- [ ] Contact Submissions / Affiliate Referrals: confirm there's no "Create" button (list/edit only, by design)
- [ ] Changing a submission's `status` field saves correctly

## 8. Cross-browser (if accessible to you)

- [ ] Chrome
- [ ] Safari
- [ ] Firefox
- [ ] Mobile Safari (actual iPhone, or at minimum Safari's device emulation) — this is the browser most likely to render dropdown/carousel CSS differently

## 9. SEO sanity check

- [ ] View source on 2-3 pages, confirm `<title>`, meta description, canonical URL, and Open Graph tags are present and page-specific (not all identical)
- [ ] Visit `/sitemap.xml` directly, confirm it lists 13 URLs and excludes `/our-founders`
- [ ] Visit `/robots.txt`, confirm it disallows `/admin`, `/our-founders`, and points to the sitemap

---

**If anything in this checklist fails**, tell me exactly what page, what you did, and what you saw (or a screenshot) — that's far more useful than "it's broken."
