# ÉLITE · Maison Horlogère

> *"Time, held in trust."*

A cinematic luxury watch e-commerce platform built around the way real high-end maisons operate — with a **Private Acquisition** flow instead of standard checkout, a dark editorial admin panel, and settlement coordinated offline between the atelier and the client.

![ÉLITE Hero](https://img.shields.io/badge/Status-Portfolio_Demo-D9B08D?style=for-the-badge&labelColor=2C3531)
![Laravel](https://img.shields.io/badge/Laravel-11-FF2D20?style=for-the-badge&logo=laravel)
![PHP](https://img.shields.io/badge/PHP-8.4-777BB4?style=for-the-badge&logo=php&logoColor=white)
![Filament](https://img.shields.io/badge/Filament-v3-F59E0B?style=for-the-badge)
![License](https://img.shields.io/badge/License-MIT-116466?style=for-the-badge)

---

## The Concept

Unlike typical e-commerce platforms with *"add to cart → pay with Stripe"*, ÉLITE implements the acquisition model used by **Patek Philippe**, **Richard Mille**, and **Audemars Piguet AP House**:

> Clients submit a request for a piece. The atelier personally reviews it in a dedicated dossier. Settlement happens offline — via wire transfer, in-boutique visit, financing agreement, or crypto. The experience itself is the product.

Nobody buys a €35,000 Nautilus with a credit card at 2am. ÉLITE reflects that reality.

---

## Stack

| Layer | Technology |
|-------|------------|
| **Backend** | Laravel 11 · PHP 8.4 |
| **Frontend** | Blade · Alpine.js · Livewire 3 · Tailwind CSS 3 |
| **Admin** | Filament v3 (custom dark theme) |
| **Database** | MySQL 8 |
| **Cache & Queue** | Redis 7 |
| **Dev Environment** | Docker Compose · Laravel Herd |
| **Typography** | Playfair Display · Cormorant Garamond · Inter |

---

## Features

### Storefront

- **Cinematic hero sections** across Home, Shop, Brands, About, Contact
- **Editorial Brand pages** — each maison treated as its own narrative chapter with heritage timelines and signature pieces
- **Private Acquisition flow** — no instant checkout, requests reviewed by the atelier
- **Global search overlay** with live results, recent searches, suggestions, and keyboard shortcuts
- **Customer accounts** — My Acquisitions, Wishlist, Addresses, Profile
- **About page** as a 7-section cinematic journey (hero, founder letter, heritage timeline, craft, numbers, atelier principles, invitation)
- **Contact page** as a luxury concierge experience with 3 channels (email, phone, boutique)

### Private Acquisition System

- Four settlement methods: Wire Transfer · In-Boutique · Financing · Cryptocurrency
- Customer note with 1,000-character counter and required acknowledgement
- Confirmation page with hero band, 4-step timeline (pulsing active step), and "what happens next" cards
- Order reference format: `ELT-2026-00042`
- Status pills: Requested · Under Review · Approved · Awaiting Payment · Paid · Shipped · Delivered · Declined
- Settlement instructions dynamically rendered per preferred method after approval

### Admin Panel (Filament)

- **Custom dark cinematic theme** matching the brand palette and typography
- **Dashboard** with KPI cards (Revenue · Orders · AOV · Customers · Avg Approval Time)
- **Revenue chart** with 7D · 30D · 90D · 1Y tabs
- **Order status breakdown** stacked bar
- **Acquisitions sidebar** with 5 queues: Pending · Under Review · Approved · Fulfilled · Declined
- **Review dossier** per request: client details, address with copy, pieces with stock levels, totals, customer note, conversation thread, decision buttons, internal admin note, price adjustment, personal message, activity log
- **Multi-admin locking** — "Being reviewed by..." indicator to prevent double-handling
- **SLA tracking** — badges turn mint → gold → rose as 24h approaches
- **Desktop notifications** (opt-in, per queue page)
- **Branded email templates** for every status change (requested, approved, declined, info request, admin alert)

---

## Design System

**Color palette** (brand tokens used across storefront and admin):

| Token | Hex | Usage |
|-------|-----|-------|
| Primary Dark | `#2C3531` | Backgrounds, surfaces |
| Primary Teal | `#116466` | CTAs, highlights, admin accent |
| Accent Gold | `#D9B08D` | Prices, dividers, hairlines, brand marks |
| Accent Peach | `#FFCB9A` | Subtle warm highlights |
| Text Mint | `#D1E8E2` | Body text on dark backgrounds |

**Typography**:

- `Playfair Display` — headings, prices, numbers
- `Cormorant Garamond` italic — eyebrows, pull quotes, poetic subtitles
- `Inter` — body copy, UI, forms

**Principles**: no rounded corners above 4px, no emojis in UI, thin gold hairlines with 2px rotated diamond dividers, fade-in-on-scroll, Ken Burns slow zoom on cinematic images.

---

## Getting Started

### Prerequisites

- PHP 8.3 or 8.4
- Composer 2+
- Node 20+
- Docker Desktop
- Laravel Herd (recommended for Windows/Mac)

### Installation

```bash
# 1. Clone the repository
git clone https://github.com/BrikendGjyliqi/elite-watches.git
cd elite-watches

# 2. Install dependencies
composer install
npm install

# 3. Set up environment
cp .env.example .env
php artisan key:generate

# 4. Start Docker services (MySQL 8 + Redis 7 + phpMyAdmin)
docker compose up -d

# 5. Wait ~15 seconds for MySQL to be healthy, then migrate + seed
php artisan migrate --seed

# 6. Build frontend assets
npm run dev

# 7. Serve the app
#    Via Laravel Herd: http://elite-watches.test
#    Or via PHP server: php -S 127.0.0.1:8765 -t public
```

### Default Credentials

| Role | Email | Password |
|------|-------|----------|
| Admin | `admin@elite.com` | `password` |
| phpMyAdmin | `elite_user` | `elite_pass` → http://localhost:8081 |

### Important Configuration

Before going live, set real values in `.env` for:

- `CONCIERGE_WIRE_IBAN`, `CONCIERGE_WIRE_BIC`, `CONCIERGE_BANK_NAME`
- `CONCIERGE_BTC_ADDRESS`, `CONCIERGE_ETH_ADDRESS`, `CONCIERGE_USDT_ADDRESS`
- `CONCIERGE_NOTIFICATION_EMAIL`, `CONCIERGE_SLA_HOURS`

Clients see these in approval emails and on their acquisition page.

---

## Project Structure

app/
├── Filament/ Admin resources, pages, widgets
│ ├── Pages/ Dashboard, custom pages
│ ├── Resources/ Brand, Category, Watch, Order, User, Review
│ └── Widgets/ KPIs, charts, latest orders, stock alerts
├── Http/Controllers/ Storefront controllers (Home, Shop, Brand, Checkout)
├── Livewire/ GlobalSearch, Cart, Checkout, Dossier
├── Mail/ OrderRequested, OrderApproved, OrderDeclined, …
├── Models/ Eloquent models with relationships
└── Services/ AcquisitionService (core business logic)

resources/
├── css/filament/admin/ Custom Filament dark theme
├── js/ Alpine components
└── views/
├── components/ Reusable Blade components
├── filament/ Admin dossier, pages, custom login
├── layouts/ app.blade.php, admin, guest
├── livewire/ Component views
├── mail/ Branded email templates
└── pages/ home, shop, brands, about, contact, checkout, account

database/
├── migrations/ Schema (brands, watches, orders, acquisitions, messages)
└── seeders/ 10 brands · 18 watches · admin + 2 test customers


---

## Seeded Data

The project ships with real-world reference data:

- **10 maisons**: Rolex, Omega, Patek Philippe, Audemars Piguet, Cartier, TAG Heuer, Breitling, IWC Schaffhausen, Hublot, Panerai
- **18 watches** with realistic retail prices in EUR (€3,050 – €35,000), movement specifications, case materials, water resistance, power reserves, 3-sentence descriptions
- **20 realistic reviews** across various watches
- **1 admin account** + **2 test customer accounts**

---

## Testing

```bash
php artisan test
```

61 tests · 20 of which cover the acquisition flow end-to-end (request submission, admin approval, decline with reason, info-request thread, stock reservation, email dispatch).

---

## Roadmap

- [ ] Deploy production demo
- [ ] Mobile-width audit for storefront pages
- [ ] Automatic stock restoration on cancelled approved orders
- [ ] Multi-currency support (EUR / CHF / USD)
- [ ] Multi-language support (EN / SQ / DE)
- [ ] In-house editorial journal (/journal)
- [ ] Trade-in / consignment acquisition flow

---

## About the Author

Built by **Brikend Gjyliqi** — final-year Computer Science student at Universum International College, Prishtina, Kosovo. Developed as part of a portfolio ahead of graduation, exploring the intersection of software engineering and luxury product design.

- GitHub: [@BrikendGjyliqi](https://github.com/BrikendGjyliqi)
- Location: Prishtina, Kosovo

Open to full-stack and Laravel roles — remote or on-site in Europe.

---

## License

MIT License. See [LICENSE](LICENSE).

Watch brand names (Rolex, Omega, Patek Philippe, Audemars Piguet, Cartier, TAG Heuer, Breitling, IWC Schaffhausen, Hublot, Panerai) are trademarks of their respective owners and are used here as reference data for a fictional retail concept. No affiliation is implied.

---

<p align="center"><em>ÉLITE · Maison Horlogère · Est. MMXXVI · Prishtina</em></p>
