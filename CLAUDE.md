# Go4Database Website — Architecture Reference

Laravel 9 (PHP 8.1 in the running dev server; PHP 8.5 CLI also present via
Homebrew — use the same PHP the running server uses when in doubt, see
"Local dev environment" below), MySQL. This started life as a CodeCanyon
multi-purpose site-builder template (many industry-vertical demo homepages)
and is being customized/rebranded into the live Go4Database marketing site.
**~128 controllers, 85 models (all flat under `app/`, not `app/Models/`),
126 migrations, 212 frontend blade views, ~27 admin view sections.**

Read this file before touching frontend or backend code. The single most
important fact in this repo: **the live homepage has been hand-customized
into mostly-static HTML, which silently disconnected it from most of the
admin panel that theoretically controls it.** Section "Live homepage" below
explains exactly which admin screens still do something and which are now
dead controls. Always check that before assuming "edit it in the admin
panel" is the right answer, or before assuming a blade edit needs a backend
counterpart.

## Local dev environment

- App URL: `http://127.0.0.1:8002` (matches `APP_URL` in `.env`)
- Web server (PHP 8.1), run from the project root — check
  `lsof -nP -iTCP:8002 -sTCP:LISTEN` before starting a second one:
  `/opt/homebrew/opt/php@8.1/bin/php -S 127.0.0.1:8002 -t public server.php`
  **The `-t public` is required.** Without it the document root is the project
  root, every `/assets/...` CSS/JS/image 404s ("Invalid argument" in the
  server log) and pages render unstyled while still returning 200.
- MySQL runs on **port 3307** (not the default 3306), socket
  `/tmp/mysql_go4db.sock`, db `go4databasewebsite`, user `root`, no password.
  It's the Homebrew MySQL with data dir `/opt/homebrew/var/mysql`, separate
  from the system MySQL at `/usr/local/mysql` (which holds 3306 and 33060):
  `/opt/homebrew/opt/mysql/bin/mysqld_safe --datadir=/opt/homebrew/var/mysql --port=3307 --socket=/tmp/mysql_go4db.sock --mysqlx=OFF &`
- Admin login: `http://127.0.0.1:8002/login/admin`. One admin account exists:
  `admin@go4database.local` / username `admin` (table `admins`). Password is
  bcrypt-hashed and unrecoverable — reset it via `php artisan tinker` if
  needed (`Admin::find(1)->update(['password' => bcrypt('...')])`).

## Request → render pipeline

```
routes/web.php:409   Route::get('/', 'FrontendController@index')->name('homepage')
  → FrontendController@index()   (app/Http/Controllers/FrontendController.php, ~line 86)
      → view('frontend.frontend-home', $blade_data)
        resources/views/frontend/frontend-home.blade.php
          @extends('frontend.frontend-master')
          $page_partial = 'home-'.get_static_option('home_page_variant');   // e.g. '01'
          @include('frontend.home-pages.'.$page_partial)                    // → home-01.blade.php
            resources/views/frontend/frontend-master.blade.php  (MASTER LAYOUT)
              @include('frontend.partials.header')   <html><head>, favicon, CSS, preloader
              @yield('content')                        the home-XX partial itself
              @include('frontend.partials.footer')    footer + closing tags + JS bundles
```

Inner pages (about, contact, blog, pricing, login, etc.) use a **different**
wrapper: `resources/views/frontend/frontend-page-master.blade.php`, which
adds `frontend.partials.navbar-variant.navbar-{{navbar_variant}}` and a
breadcrumb, but shares the same `header`/`footer` partials.

`app/Http/Middleware/GlobalVariableMiddleware.php` runs a view composer on
`['frontend/*','components/*']` that injects `$global_static_field_data`,
`$all_social_item`, `$all_language`, `$primary_menu`, `$footer_widgets`,
`$all_usefull_links`/`$all_important_links`, `$popup_details` into **every**
frontend view automatically — don't be surprised these exist with no
visible `->with()` call.

## The `static_options` settings system

Generic key/value settings table, table `static_options`, model
`StaticOption`. Helpers in `app/Helpers/helpers.php`:
- `get_static_option($key, $default = null)` — cached read (`Cache::remember`,
  6400s). Now defensively returns `$default` if the table doesn't exist yet
  (guards fresh installs).
- `set_static_option($key, $value)` / `update_static_option($key, $value)` —
  write, used by every admin "settings" screen.
- `filter_static_option_value($key, $collection)` — reads a key out of a
  pre-fetched collection (`$static_field_data` / `$global_static_field_data`)
  instead of hitting the DB again, used heavily in blade files.

`app/Helpers/HomePageStaticSettings.php` → `get_home_field($variant)` returns
the list of option keys relevant to a given home page variant
(`default_settings()` shared keys + a per-variant `home_XX()` method). This
is how `FrontendController@index` decides which `static_options` rows to
pull for `$static_field_data`.

**Nearly all of this codebase's "is X admin-editable" questions reduce to:
find the `static_options` key in the relevant blade file, then find which
admin controller/route calls `update_static_option()` on that same key.**

## Live homepage (`home-01.blade.php`, variant `home_page_variant = '01'`)

This is the actual production homepage — not a generic template sample. It's
1718 lines, ~3-4x the size of the other home-XX variants, mentions
"Go4Database" 13+ times, and has the newest mtime of any file in
`home-pages/`. It was substantially hand-rewritten to hardcoded HTML/CSS,
which means **most of its own admin-panel section editors no longer do
anything visible**. Section order and data source, top to bottom:

| # | Section | Data source | Still admin-editable? |
|---|---|---|---|
| 1 | Top support bar | `frontend.partials.homesupportbar` → `supportbar` partial, gated on `navbar_variant` being empty + `home_page_support_bar_section_status` | Yes — Topbar Settings |
| 2 | Main nav | `frontend.partials.navbar-new` | Menu items **yes** — Menus Manage (same menu as inner pages); logo and Log in / Sign up buttons hardcoded. See "Navbar" below |
| 3 | Hero, search/demo card, stats strip, trust logos, credits promo, AI-verified tabs, feature grid, comparison table, integration screenshots | plain HTML | **No — hardcoded**, edit the blade file directly |
| 4 | Testimonials (`#case-studies`) | Eloquent `$all_testimonial` | Content via Testimonial CRUD (not this page's admin section); heading text is hardcoded |
| 5 | "Most Active Users" mailing list (`#favorite-lists`) | Eloquent `$all_work_category` / `$all_work` (Works model) | Content via Works CRUD; heading text hardcoded |
| 6 | Blog/News grid (`#blog`) | `$static_field_data` gated on `home_page_latest_news_section_status`; title/desc from `home_page_01_{lang}_latest_news_*`; cards from `$all_blog` | **Yes** — admin `/admin-home/home-page-01/latest-news` + Blog CRUD |
| 7 | FAQ accordion | plain HTML | **No — hardcoded**, no CMS backing at all |
| 8 | CTA banner | plain HTML | No |
| 9 | Decorative "Get Custom Prospecting" contact form (`#contact`) | plain `<form>`, no server wiring visible | No — pure HTML/CSS edit |
| 10 | `frontend.partials.contact-section` (stacked right after #9) | **Real, working** contact form: gated on `home_page_contact_section_status`, map from `home_page_01_contact_area_map_location`, fields from `get_in_touch_form_fields`, posts to `route('frontend.get.touch')` | **Yes** — admin `/admin-home/home-page-01/contact-area` |
| 11 | Floating chat bubble, `frontend.partials.chat-widget` (last include in the file) | `chat_widget_*` static options, gated on `chat_widget_status` | **Yes** — admin `/admin-home/general-settings/chat-widget-settings`, see "Chat widget" below |

Admin controller for this page: `app/Http/Controllers/HomePageController.php`
(`home_01_*` methods, `middleware('auth:admin')`). Routes under
`routes/admin.php`, prefix `/admin-home/home-page-01/...`
(`admin.homeone.*` route names), views in
`resources/views/backend/pages/home/home-01/*.blade.php`. **Of those admin
screens (about-us, quality-area, price-plan, case-study, team-member,
cta-area, brand-logo-area, testimonial-title, service-area), only
Latest News, Section Manage (the `*_section_status` toggles), and Contact
Area still have a live effect** — the rest write to `static_options` but
nothing on the page reads them anymore.

**When the user asks to change a home-01 section**: check the table above
first. If it's marked "hardcoded", the fix is a direct edit to
`home-01.blade.php` (or its CSS/JS at `public/assets/frontend/css/home.css`,
`public/assets/frontend/js/home-01.js`) — there's no backend counterpart to
touch. If it's marked "admin-editable", make the change through the
`HomePageController` route/`static_options` key (or the relevant CRUD
model), not by hardcoding text into the blade file, or the admin panel will
drift out of sync again.

## Lead search (homepage and list pages)

Both call the app (`app.go4database.com`) straight from the browser; the app's
CORS list only allows `https://www.go4database.com`, so local origins are
blocked. To test the homepage search locally, set `WEBSITE_LEADS_LOCAL_PROXY=true`
in `.env` (only honoured when `APP_ENV=local`): the page then searches through
`/local-dev/website-leads` and `/local-dev/website-leads/contact`
(`WebsiteLeadsLocalProxyController`), which forward to the API server-side. Run the dev server with `PHP_CLI_SERVER_WORKERS=4`,
since the built-in server is otherwise single-threaded and a 5-8s uncached
search stalls the whole page.

- **Homepage** (`home-01.js`): `GET /api/website/leads` with `title`,
  `industry_business`, `location` (comma separated values, e.g. `CEO, CTO`).
  Returns `{data: [...max 5], total}` with `has_email`/`has_phone` flags and a
  per-lead `contact_token`, but no email, phone, LinkedIn or id. With all three
  boxes empty the JS skips the request and hides the results table; "No results
  found." is only for a real search with no matches.
  Above the rows a summary reads "Showing 5 of 28,697 matching leads" with a
  "Sign up free to see all" link (`utm_campaign=see_all_leads`, GA4 event of the
  same name). The table's CSS is a `<style>` block next to `#results-container`
  in `home-01.blade.php`, not `home.css`, so it deploys without copying assets;
  below 900px the rows turn into cards.
- **Reveal** (same file): "View Email"/"View Phone" call
  `GET /api/website/leads/contact?token=…&type=email|contact` for that one lead
  and show the value as text (never innerHTML). 404 means the search's tokens
  are older than 30 minutes, so the page re-runs the search; 429 means the
  visitor passed 10/minute or 100/day, so the button becomes a "Sign up free to
  see more" link to Register (`utm_campaign=view_email_limit|view_contact_limit`).
  Leads flagged without an email/phone show "No … available" without a call.
  GA4 events: `view_email`, `view_contact`, `reveal_limit_reached`,
  `see_all_leads`.
- **List pages** (`list/list-single.blade.php`): on the same
  `/api/website/leads` + `/api/website/leads/contact` click-to-reveal API
  since commit a4d34c1 (2026-09-16), live as well. The old `/api/getleads`
  is no longer called from any list page.
- **Cache busting:** LiteSpeed serves JS with a one-year `immutable` cache, so
  bump the `?v=` on the `home-01.js` script tag in `home-01.blade.php` with
  every change to that file. Production's `public_html/assets` is a separate
  copy, so changed JS must also be copied there.

## Layout trap: `position: sticky` is dead site-wide by default

The theme sets **`overflow-x: hidden` on `body`**, which makes body a scroll
container and silently disables `position: sticky` anywhere on the page. There
is no error, the element just never sticks. The fix used on the FAQ page is
`body:has(.fq){overflow-x:clip;overflow-y:visible;}` — `clip` hides overflow
without creating the scroll container. Scope it to the page, never change body
globally, other pages rely on `hidden` to suppress horizontal scroll.

Two companions to the same bug, both only findable by measuring in a browser:
a sticky child needs its grid parent to span the row (`align-self:stretch`),
and grid items default to `min-width:auto`, so a horizontal scroll row inside
one will drag the whole grid wider than the viewport (`min-width:0` fixes it).

## FAQ page

`resources/views/frontend/pages/faq-page.blade.php`, served by
`FrontendController@faq_page`, assets at `public/assets/frontend/css/faq.css`
and `js/faq.js`. Rebuilt 2026-09-10 as a search-first help centre: live client
side filtering over question + answer text, a sticky topic rail with counts
that follow the search, scroll-spy highlighting, and an empty state pointing at
the contact page. Purely a front-end change, the controller, `Faq`/`FaqCategory`
models and admin CRUD are untouched, as is the schema.org markup.

The breadcrumb banner is suppressed for this page in
`frontend-page-master.blade.php` by **route name** (`frontend.faq`) rather than
path, because the slug is admin-configurable and differs between environments.

## Blog listings (`/blog`, `/blog/{category}`, `/blog-search`)

All three render one shared view, `pages/blog/partials/listing.blade.php`
(with `card`, `pagination` and `style` partials beside it; CSS and the
newsletter script are inline in `style`, so it deploys as views). Rebuilt
2026-10-25 as a magazine layout: header with search and topic chips, then on
the index's first page a lead story beside a "Most read" list, then a grid.
- Data: `FrontendController::blog_listing_data()` adds the topics that have
  posts (with counts), the header totals, the five most-read posts
  (`total_visitors` is a varchar, cast to sort) and eager-loads each post's
  `authorData`/`category`. Called from `blog_page`, `blog_search_page` and
  the category branch of `blog_single_page`.
- Admin sources: page name, description and SEO from Page Settings
  (`blog_page_{lang}_*`); posts per page, read-more text, FAQ category and
  the page's wording from Blog → Blog Page Settings (per language:
  `blog_page_{lang}_subtitle` — header text, else the meta description —
  `_search_hint`, `_most_read_title`, `_latest_title`, `_faq_title`,
  `_newsletter_title`, `_newsletter_text`; each empty one falls back to the
  default in `listing.blade.php`); posts, categories and authors from their
  CRUD screens.
- **Category URLs are served by `blog_single_page`**, not
  `category_wise_blog_page`: `blog/{slug}` is registered before
  `blog/{any}`, so it checks `BlogCategory::where('slug', …)` first. Live's
  `blog_categories` has a `slug` column that the local DB lacks, so locally
  every `/blog/{anything}` (posts too) errors on that query.
- The Widgets Manage "blog" sidebar (`pages/blog/sidebar.blade.php`) is no
  longer shown on the listings: categories became the topic chips, the
  newsletter widget became the band at the foot (same
  `frontend.subscribe.newsletter` route, so sign-ups still land in
  Newsletter Manage). The old breadcrumb banner is switched off by
  `@section('no-breadcrumb')`, checked in `frontend-page-master`.
- No numbered page links, by request: "Show more articles" is the only way
  on. It is a real `<a href="?page=N" rel="next">` (search keeps `search=`),
  which the script turns into fetching that page and appending its
  `.g4b-grid` in place, so the address stays `/blog`; crawlers and no-script
  visitors follow the link. `?page=N` pages are already `noindex, follow`
  with a clean canonical through `partials/header.blade.php`, which does
  that for every URL with a query string. The old AJAX
  `javascript:void(0)` pagination is gone.
- Search matches title **or** excerpt (title matches first, `%`/`_`
  escaped). Asked for JSON (`Accept: application/json`), the same
  `blog_search_page` route returns the search box's instant results (top 6,
  or the most-read posts when the box is empty).
- FAQ section under the index's articles: up to six questions from the FAQ
  category picked in Blog → Blog Page Settings → "FAQ Section"
  (`blog_page_faq_category`: a category id, `none` to hide, or empty for
  "Data & Accuracy"). `FrontendController::blog_page_faqs()`; the questions
  themselves are edited under Faq.

## Chat widget

The conversation system itself runs on a **separate server owned by the user**
(a `.in` domain), not in this codebase. This repo only contains the bubble and
a server-side bridge to that API.

- `resources/views/frontend/partials/chat-widget.blade.php` — launcher, panel,
  CSS and JS all in one file. CSS is inlined **on purpose**: production's
  `public_html/assets` is a real directory rather than a link to
  `@core/public/assets`, so a separate stylesheet would need copying by hand on
  every deploy. Don't split it out.
- `ChatWidgetController@send` (`POST /chat/send`, throttled) forwards the
  message to the external API server-to-server. The browser never calls the
  other domain, which is what avoids CORS, keeps the API key off the page, and
  allows the far end to be plain HTTP.
- `ChatWidgetSettingsController` — admin screen at General Settings → Chat
  Widget Settings. Endpoint, auth header/value, request field names, reply
  field path, timeout and all visitor-facing copy are `chat_widget_*` static
  options, so retargeting the API is an admin change, never a deploy. It also
  has a `test()` action that sends one throwaway message and returns the raw
  response, which is how you discover an unfamiliar API's reply field.
- Reply extraction tries `REPLY_CANDIDATES` in order when `reply_path` is
  blank; a plain-text (non-JSON) response body is used as-is.
- **Off unless `chat_widget_status` is `1`.** Included only by
  `home-01.blade.php`; move the `@include` into `frontend.partials.footer` to
  put it on every page.

## Navbar — one menu, one shared design

- **The menu items everywhere come from the Menu Builder**:
  `render_frontend_menu($primary_menu)`, where `$primary_menu` = the default
  `Menu` record (`app/MenuBuilder/`, admin at Appearance Settings → Menus
  Manage, `MenuController`, table `menus`; `$primary_menu` is injected by
  `GlobalVariableMiddleware`). B2B Leads is a `ServiceMegaMenu` item, which
  is where its mega menu comes from. Editing Menus Manage changes the
  homepage and every inner page alike.
- **`navbar-new.blade.php`** — the homepage's (`home-01`). Renders that menu,
  but keeps its own hardcoded logo (`assets/frontend/images/logo.png`,
  linking `#top`) and GA-tagged Log in / Sign up buttons. Its old hardcoded
  menu is still in the file as a fallback, shown only when the admin menu
  renders empty (e.g. a local DB with no default menu, which is the case
  locally today, so the local homepage shows the fallback).
- **`navbar-variant/navbar-04.blade.php`** — every inner page on live
  (`frontend-page-master` includes `navbar-{{navbar_variant}}`, and live's
  `navbar_variant` is `04`). Logo from `site_logo`. Locally `navbar_variant`
  is empty, so local inner pages render the old theme `navbar-.blade.php`
  instead and can't be used to preview the live inner-page navbar.
  `navbar.blade.php` is an unused twin of navbar-04 (home-02/03/04 only).
- **`navbar-style.blade.php`** — the CSS and JS all three share, inline so
  it deploys as a view. Everything is scoped to `#g4d-header`, which
  outranks home.css's old `!important` nav rules and the inline style the
  menu builder writes onto `.g4d-nav`; those were left in place, not
  removed. It also: glides a highlight between items and rests it on the
  current page; adds the icon tile and one-line description to each
  dropdown item from a path/label map in its script (the menu builder has no
  description field), drawn via `data-*` attributes and CSS so the link text
  stays a plain title for `mobile-nav.js`, which copies it into the phone
  drawer; switches to the drawer below 1100px (`mobile-nav.css` itself
  switches at 992px; the partial brings it forward); and lets a dropdown
  label that is a real page (B2B Leads → `/b2b`) navigate on click despite
  `home-01.js`, which makes every label click-to-toggle on the homepage.
- Which home page variant (01–21) is live: Appearance Settings → Home
  Variant (`admin.home.variant`, `AdminDashboardController@home_variant`).
  Which inner-page navbar style is used: Appearance Settings → Navbar
  Settings (`admin.navbar.settings`, sets `navbar_variant`).

## Logo / favicon — inconsistent source of truth

- **Favicon**: consistently DB-driven everywhere including the homepage —
  `header.blade.php` reads `site_favicon` (General Settings → Site
  Identity). One source of truth.
- **Logo**: DB-driven (`site_logo`/`site_white_logo`, same Site Identity
  screen) on every inner page and on `navbar.blade.php`, but **hardcoded**
  to `assets/frontend/images/logo.png` in `navbar-new.blade.php`. Changing
  the logo in admin currently updates every page except the live homepage.

## Footer

One footer for the whole site: `resources/views/frontend/partials/footer.blade.php`,
included by both `frontend-master.blade.php` and
`frontend-page-master.blade.php`. CSS is inline in the file, for the same
deploy reason as the chat widget.
- Four link columns ("Product Features", "Popular Datasets", "Healthcare
  Industry Lists", "Compliances") — **DB-driven**: each is a menu picked by
  the `footer_column_{one..four}_menu_id` / `_title` static options
  (Appearance Settings → Footer Settings); a column with no menu is left out.
- "Ask AI about Go4Database" (under the social icons) — **hardcoded**: five
  tiles opening ChatGPT, Perplexity, Grok, Claude and Google AI Mode with a
  prefilled question (`$askAiPrompt` in the file); clicks go to GA4 as
  `ask_ai` with the assistant name.
- Copyright line — **DB-driven**, `get_footer_copyright_text()` reads
  `site_{lang}_footer_copyright` (General Settings → Basic Settings).
- Social icons — **DB-driven**, `SocialIcons::all()` (Topbar Settings →
  social items; shared list used by both top bar and footer).
- Appearance Settings → Footer Color Settings writes static-option keys
  (`footer_widget_title_color` etc.) that **no longer appear anywhere** in
  the current `footer.blade.php` — dead admin control.
- A duplicate `footer.blade copy.php` exists (backup, not routed) — ignore.

## Top bar / contact info

- **`TopBarController`** — navbar CTA button text/toggle, `SocialIcons`
  CRUD, home-07-specific info items. Admin: Appearance Settings → Topbar
  Settings, `/admin-home/appearance-setting/topbar-settings`.
- **`ContactInfoController`** / model `ContactInfoItem` (table
  `contact_info_items`) — Admin: All Page Settings → Contact Page Manage →
  Contact Info. Data IS fetched by `FrontendController@index` and passed to
  home-01, but **home-01.blade.php never reads it** (dead on the homepage);
  it IS rendered on the real Contact Us page and on `home-10`/`home-14`.

## General Settings (`GeneralSettingsController`, prefix `/admin-home/general-settings/...`)

One controller, ~20 sub-pages, all under `resources/views/backend/general-settings/`:
Site Identity (logo/white-logo/favicon) · Basic Settings (title, tagline,
footer copyright, OG image, sticky nav, maintenance mode, language switcher,
SSL redirect) · Color Settings · Typography · SEO (meta tags/description/
schema) · Third-party Scripts · Email Template · Email Settings
(per-form success messages) · SMTP · Page Settings (per-page slugs/meta) ·
Payment Gateway (PayPal/Paytm/Razorpay/Paystack/Mollie/COD/Flutterwave/
Midtrans/Payfast/Cashfree/Instamojo/Mercado Pago) · Custom CSS/JS · Cache ·
GDPR · Preloader · Popup · Sitemap · RSS Feed · Module toggles · DB Upgrade ·
License (Envato key). All persist via `update_static_option()`/
`get_static_option()` into `static_options` — same mechanism as everything
above.

## Home page variants (`static_options.home_page_variant`, `'01'`–`'21'`)

Numeric string picks `home-XX.blade.php` directly — no name-based routing.
Each variant has a matching **admin section-editor** controller (returns
views under `backend.pages.home.<vertical>.*`) surfaced conditionally in
the sidebar's "Home Page Manage" submenu based on the active variant. There
is no separate "frontend renderer" controller per vertical — the blade file
itself is the renderer, fed by `FrontendController@index` + variant-specific
branches inside it.

| Variant | Vertical | Admin controller |
|---|---|---|
| 01–04 | Default multipurpose (01 = **live**, 02 adds team, 03 drops quality/adds CTA+brands, 04 adds team) | `HomePageController` |
| 05 | Portfolio | `PortfolioHomePageController` |
| 06 | Logistics | `LogisticsHomePageController` |
| 07 | Industry | `IndustryHomePageController` |
| 08 | Creative Agency | `CreativeAgencyHomePageController` |
| 09 | Construction | `ConstructionHomePageController` |
| 10 | Lawyer | `LawyerHomePageController` |
| 11 | Political | `PoliticalHomePageController` |
| 12 | Medical | `MedicalHomePageController` |
| 13 | Charity | `CharityHomePageController` |
| 14 | Creative Design Agency | `CreativeDesignAgencyHomePageController` |
| 15 | Fruit (sic "Frouit") | `FrouitHomePageController` |
| 16 | Cleaning | `CleaningHomePageController` |
| 17 | Course | `CourseHomePageController` |
| 18 | Grocery | `GroceryHomePageController` |
| 19 | Fashion Ecommerce | `Admin\FashionEcommerceHomePageController` (admin-only, no root frontend controller) |
| 20 | Newspaper | `Admin\NewspaperHomePageManageController` |
| 21 | Creative Agency ("Two") | `Admin\CreativeAgencyHomePageManageController` |

Dead/stray files in `home-pages/`: `home-new.php` (5 bytes, "hello",
referenced by a broken `@include` missing a closing quote in
`frontend-home-demo.blade.php`), `home-01.blade copy.php` (backup),
`page-builder.blade.php` (stub for the alternate drag-drop Page Builder
render path, toggled by `static_options.home_page_page_builder_status` —
currently off).

## Content vertical modules

Pattern: frontend is served by the monolithic `FrontendController.php`
(~2500 lines) for most verticals; admin CRUD is a same-named root-level
controller (not namespaced under `Admin/`) gated by
`adminPermissionCheck:*` middleware in `routes/admin.php`. Only Courses and
Appointments get dedicated `App\Http\Controllers\Frontend\*` classes.
Several "sub-entity" models have no dedicated admin controller — folded
into the parent (`EventAttendance`/`EventPaymentLogs`→`EventsController`,
`DonationLogs`→`DonationController`, `JobApplicant`→`JobsController`,
`ProductRatings`→`ProductsController`).

| Module | Model → table | Admin controller(s) | Admin views |
|---|---|---|---|
| Blog | `Blog`→`blogs`, `BlogCategory`, `Author`→`author_new` | `BlogController`, `AuthorController` | `backend/pages/blog/`, `backend/pages/author/` |
| Courses | `Course`, `CoursesCategory`, `CourseInstructor`, `CourseLession`, `CourseReview`, `CourseCoupon`, `CourseEnroll`, `CourseCurriculm`, `CourseCertificate` | `CoursesController` + category/coupon/instructor/lesson/review controllers, `Admin\CourseCertificateController`, `Admin\CourseEmailTemplateController` | `backend/courses/` |
| Jobs | `Jobs`, `JobsCategory`, `JobApplicant` | `JobsController` (jobs + applicants), `JobsCategoryController` | `backend/jobs/` |
| Events | `Events`, `EventsCategory`, `EventAttendance`, `EventPaymentLogs` | `EventsController` (events + attendance + payment logs), `EventsCategoryController` | `backend/events/` |
| Appointments | `Appointment`, `AppointmentCategory`, `AppointmentBooking`, `AppointmentBookingTime`, `AppointmentReview` | `AppointmentController` + category/booking-time/booking/review controllers | `backend/appointment/` |
| Donations | `Donation`, `DonationLogs` | `DonationController` (donations + payment logs) | `backend/donations/` |
| Products/Ecommerce | `Products`, `ProductCategory`, `ProductSubCategory`, `ProductVariant`, `ProductOrder`, `ProductCoupon`, `ProductShipping`, `ProductRatings` | `ProductsController` + category/subcategory/variant/coupon/shipping controllers, `ProductOrderController` | `backend/products/` |
| Misc blocks | `Testimonial`, `TeamMember`, `PricePlan`(+Category), `Brand`, `Works`/`WorksCategory`, `CaseStudy`/`Category`, `Faq`, `KeyFeatures`, `Counterup`, `HeaderSlider`, `ContactInfoItem`, `Services`/`ServiceCategory`/`ServiceSubcategory` | one controller each, same name as model | `backend/pages/{works,case-study,service,price-plan}/` + single-file views `faqs.blade.php`, `team-member.blade.php`, `brand.blade.php`, `counterup.blade.php`, `key-features.blade.php`, `testimonial.blade.php` |

Known dead/stale code (leave alone unless asked to clean up):
`app/Http/Controllers/ServiceController-old.php`,
`app/Http/Controllers/MediaUploadControllerOld.php`,
`ProductRatingsController.php` (orphaned, not wired into any route),
`service-old.blade.php` / `service-single-old.blade.php`.

## Admin panel navigation (ground truth of what's reachable in the UI)

`resources/views/backend/partials/sidebar.blade.php`. Top-level groups:
Dashboard, Admin Manage, Users Manage, Newsletter Manage, Pages, Blogs,
Author, Advertisements, B2C, List, Case Study, B2B, Image Gallery, Video
Gallery, Price Plan, Faq, Brand Logos, Team Members, Testimonial, Counterup,
All Modules, **All Page Settings** (Home Page Manage, About Page Manage,
Contact Page Manage, etc.), **Appearance Settings** (Topbar, Navbar, Home
Variant, Breadcrumb, Footer Color, Menus Manage, Widgets Manage, Popup
Builder, Form Builder, Email Templates, Media Images), **General Settings**
(see above), Languages. Each item gated by
`check_page_permission_by_string('<Permission Name>')`.

## Workflow rule for future edit requests

When asked to change a frontend section:
1. Find the section in `home-01.blade.php` (or the relevant page) and check
   whether it renders from a `static_options` key / Eloquent model
   (admin-editable) or is plain hardcoded HTML (blade-only edit) — see the
   table above for home-01 specifically.
2. If admin-editable: change it through the admin controller/route (so the
   admin panel stays truthful) rather than hardcoding the new value into
   the blade file.
3. If hardcoded: edit the blade file (and matching CSS/JS under
   `public/assets/frontend/`) directly — there is no backend counterpart,
   don't go looking for one.
4. If unsure which, grep the blade file for `static_options`/
   `filter_static_option_value`/`get_static_option`/an Eloquent variable
   name before editing either side.

## Full inventory (for grep-ability, not memorization)

- **Frontend views** (`resources/views/frontend/`, 212 files): `home-pages/`
  (24), `pages/` (16 direct + 16 subfolders: products, courses, blog,
  events, jobs, service, work, appointment, donations, knowledgebase,
  case-study, emails, list, author, package, support-ticket),
  `partials/` (34 direct + `navbar-variant/`, `preloader/`,
  `custom-js-for-page-builder-addon/`), `payment/` (11), `sitemap/` (9),
  `user/` (5 + `dashboard/` 14). Root-level: `frontend-home.blade.php`,
  `frontend-home-demo.blade.php`, `frontend-master.blade.php`,
  `frontend-master-new.blade.php`, `frontend-page-master.blade.php`,
  `home.blade.php`, `maintain.blade.php`, `thankyou.blade.php`.
- **Backend views** (`resources/views/backend/`, ~27 top-level folders):
  appointment, courses, donations, email-template, events, form-builder,
  frontend-user, general-settings, image-gallery, jobs, knowledgebase,
  languages, maintain-page, media-images, newsletter, package-order-manage,
  page-builder, pages (contains `home/`, `blog/`, `works/`, `case-study/`,
  `service/`, `price-plan/`, `menu/`, `contact-page/`), partials,
  payment-logs, popup-builder, products, quote-manage, support-ticket,
  user-role-manage, video-gallery, widgets.
- **Migrations**: 126 files, `2014_10_12` (Laravel stock) through
  `2026_09_08` (same-day). Core content-architecture migrations:
  `create_static_options_table`, `create_pages_table`, `create_menus_table`,
  `create_widgets_table`, `create_page_builders_table`.
- Models live flat under `app/` (no `app/Models/`), all rely on Laravel's
  default table-name convention (no explicit `$table` found in a spot check
  of 15 core models) except two appointment models
  (`AppointmentCategory`, `AppointmentBookingTime`) which also use the
  default convention.
