# Crinkle Nest — Artisanal Bakery (Laravel 10)

A Laravel build of the Stitch design in `stitch_crinkle_nest_bakery_website/`.
Every text, image, video, link, colour and policy on the site comes from the database and is editable in the admin panel.

## Setup

Requirements: PHP 8.1+, Composer, MySQL/MariaDB (XAMPP works).

```bash
composer install
cp .env.example .env        # then set DB_DATABASE=crinkle_nest, DB_USERNAME, DB_PASSWORD
php artisan key:generate
php artisan migrate --seed  # creates tables and loads all content from the design
php artisan storage:link    # serves uploaded images/videos
php artisan serve
```

Open `http://127.0.0.1:8000` for the site and `http://127.0.0.1:8000/admin` for the admin panel.

**Seeded accounts (change these passwords after first login):**

| Role | Email | Password |
|---|---|---|
| Admin | admin@crinklenest.com | admin12345 |
| Demo customer | ananya@example.com | customer123 |

To reset everything back to the design content: `php artisan migrate:fresh --seed` (this wipes all data).

## Pages

| URL | Page |
|---|---|
| `/` | Home: hero, video shorts, trending, category carousels, collections, promo, about, CTA |
| `/about` | About Us |
| `/contact` | Contact, with an inquiry form that is saved to the database |
| `/menu`, `/menu/{category}` | Full menu and category pages |
| `/cart` | Basket and checkout (coupons, delivery slots, gift note) |
| `/orders` | My Orders: live tracker, history, reorder, rating, printable bake slip |
| `/profile` | My Profile: details, photo, dietary prefs, addresses, rewards, WhatsApp notifications |
| `/page/{slug}` | Policy pages (Terms & Conditions, Privacy Policy, plus any you add) |
| `/login`, `/register` | Customer accounts (guest checkout also works) |

## Admin panel (`/admin`)

- **Orders**: change status (Confirmed → Baking → Packing → Dispatched → Delivered), payment status, target arrival, live-tracking link, and status note. Marking an order delivered awards loyalty points.
- **Messages**: contact-form inquiries, with one-click WhatsApp or email replies.
- **Products / Categories**: images, prices, badges, and flags for Trending, cart upsell, and Frequently Paired. A category can appear as a carousel on the home page or as a collection card.
- **Videos (Shorts)**: upload an MP4/WebM, or paste a YouTube, Shorts, Vimeo or .mp4 link, plus a thumbnail. Videos play in a pop-up player.
- **Page Content**: every section of every page (titles, paragraphs, images, buttons, badges, and repeatable items such as chips, pillars, steps, cards and options). Sections can be hidden.
- **Policy Pages**: create any number of pages with clauses. Clause content is HTML with a live preview and supports the site's Tailwind classes.
- **Testimonials, FAQs, Navigation Menus** (header, both footer columns, footer bottom bar), **Coupons**, **Delivery Slots**, **Customers & Admins**.
- **Settings**: branding (name, tagline, logo, favicon), contact details and map, WhatsApp number and message templates, social links, footer, checkout fees and free-delivery threshold, rewards, theme colours, SEO, and analytics scripts.

### Link shortcuts (any link field)
- `/about`, `#menu-catalog`, `https://…`: normal links
- `{whatsapp}`: opens a WhatsApp chat with the number from Settings
- `{whatsapp:Your pre-filled message}`: the same, with a custom message
- `{phone}` inside button text is replaced with the WhatsApp number from Settings

## Notes

- Design images are seeded as their original remote URLs. Upload your own in the admin to replace them; uploads are stored in `storage/app/public`.
- Video upload size is limited by PHP's `upload_max_filesize` and `post_max_size` (in XAMPP, edit `php.ini`).
- "Pay Online" records the order as payment pending and tells the customer that a payment link will follow on WhatsApp. No payment gateway is connected yet.

## Tests

```bash
php artisan test
```

The feature tests use an in-memory SQLite database (your MySQL data is not touched). They cover the pages, CMS-driven content, the contact form, cart → coupon → checkout, accounts, admin access control, every admin screen, uploads, section and item editing, settings, and order status/points.
