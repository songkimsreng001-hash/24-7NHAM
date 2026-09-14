# 24/7 NHAM — Full-Stack Food Delivery Ecosystem & Admin Portal

A modern, full-stack food delivery and e-commerce platform built with **Laravel 11**, **MySQL**, **Bootstrap 5**, **Leaflet.js**, **Cropper.js**, and **Stripe**. Features multi-role capabilities for **Customers**, **Couriers / Drivers**, and **Administrators**, comprehensive live shipment tracking, instant real-time search, interactive profile photo cropping, seamless light/dark modes, and Google OAuth.

---

## 🌟 Highlights & Key Features

### 1. Multi-Role Ecosystem & Role Assignment
- **Customer / User**: Browse restaurants & menus, interactive cart, coupon code validation, Stripe checkout, multi-address management with Leaflet map pin-drop, 5-stage live delivery tracking, live support chat, dish reviews & ratings, and dispute filing.
- **Courier / Driver**: Dedicated Driver Portal (`/driver`), online/offline availability switch, order acceptance, live delivery milestones (`picked_up`, `on_the_way`, `delivered`), trip earnings analytics, and vehicle profile settings.
- **Store Administrator**: Admin Dashboard (`/dashboard`) with real-time KPI metrics, role assignment modals to promote/demote users (`admin`, `driver`, `customer`), active order reassignment & cancellation, promo code manager, dispute resolution center, store analytics, broadcast announcements, and audit logging.

### 2. Google OAuth Authentication
- **One-Click Sign-in & Registration**: Seamless "Sign in with Google" and "Register with Google" powered by Laravel Socialite.
- **Account Linking & Profile Sync**: Automatically links Google IDs to existing customer accounts by email, captures Google profile pictures, and verifies email addresses.
- **Role-Aware Redirection**: Redirects administrators to `/dashboard`, couriers to `/driver`, and customers to the homepage.
- **Windows cURL SSL Resilient**: Bundled trusted Mozilla CA certificates (`storage/cacert.pem`) with automatic fallback to prevent cURL Error 60.

### 3. Instant Automatic Search Across All Textboxes
- **Type-and-Search**: Search triggers immediately from character 1 (`length >= 1`) with debounced AJAX requests—no minimum 2-character lockouts.
- **Frontend Navbar Search**: Dropdown results displaying dish thumbnails, match highlights, prices, and direct links.
- **Search Products Page**: Dual live-suggest dropdown and traditional full-page grid filtering.
- **Admin Topbar Global Search**: Instant product suggestions with quick `Edit` and `View` actions (`Ctrl + K` shortcut).
- **Admin Sidebar Filter**: Real-time filtering of sidebar navigation links across Menu and General sections.
- **Order Tracking Search**: Auto-completes tracking codes, recipient names, order status, and timestamps.
- **Admin Management Tables**: Real-time client-side instant filtering across Products, Orders, Categories, and User tables.

### 4. Interactive Profile Photo Cropping
- **Interactive Avatar Circle**: Clicking on user profile picture opens file picker.
- **Format Verification**: Supports `.png`, `.jpg`, `.jpeg`, `.webp`, including uppercase extensions (`.PNG`, `.JPG`, `.JPEG`, `.WEBP`) up to 5MB.
- **1:1 Circular Cropping Modal**: Powered by Cropper.js with zoom, 90° rotation, drag repositioning, reset, and live circular previews (120px and 60px).
- **Dual Export**: Submits base64 data and standard binary file via `DataTransfer` API with safe backend hash storage.

### 5. Seamless Light & Dark Mode
- **Zero FOUC (Flash of Unstyled Content)**: Inline `<head>` script evaluates `localStorage.getItem('nham_theme')` or system `prefers-color-scheme` before HTML renders.
- **Frontend Navbar**: Desktop and mobile header theme toggle buttons with animated sun/moon icons.
- **Backend Admin Portal**: Theme toggle button in the sidebar and topbar with deep slate surfaces and high-contrast typography.

### 6. Responsive Sidebar Toggle
- **Open State**: Displays full brand name, icons, labels, badges, search box, and user card (248px width).
- **Closed / Collapsed State**: Compact 72px icon bar with centered icons and tooltip hover titles (`display: none` for labels without layout shifts).
- **Persistent State**: Saved in browser `localStorage` (`nham_sidebar_collapsed`).

---

## 🏛️ Architecture & System Modules

```
d:\lavavel_blog\
├── app/
│   ├── Http/Controllers/
│   │   ├── AdminPortalController.php     ← User roles, broadcasts, promos, analytics, disputes
│   │   ├── AuthController.php            ← Auth, Google OAuth, dashboard, registration
│   │   ├── CustomerOrderController.php   ← Customer order history, live tracking, cancellation
│   │   ├── DriverController.php          ← Courier portal, order acceptance, earnings
│   │   ├── ChatController.php            ← Customer-driver & support live chat channels
│   │   ├── NotificationController.php    ← In-app notifications & read badges
│   │   ├── AddressController.php         ← Multi-address pin-drop & distance calculation
│   │   ├── PromoController.php           ← Coupon code redemption & discounts
│   │   ├── ReviewController.php          ← Order & driver reviews with star ratings
│   │   ├── DisputeController.php         ← Customer dispute filing & admin resolution
│   │   ├── StoreController.php           ← Cart, checkout, Stripe payment intents
│   │   ├── ProductController.php         ← Product CRUD & inventory
│   │   └── CategoryController.php        ← Category management
│   ├── Models/
│   │   ├── User.php                      ← Customer, Driver, and Admin roles & relationships
│   │   ├── Order.php                     ← Full lifecycle status & delivery tracking
│   │   ├── OrderItem.php                 ← Individual cart items & pricing
│   │   ├── UserAddress.php               ← GPS coordinates (lat/lng), default address
│   │   ├── ChatMessage.php               ← Live chat channels & read receipts
│   │   ├── InAppNotification.php         ← Push alerts across customer, driver, and admin
│   │   ├── PromoCode.php                 ← Discount percentages and fixed reductions
│   │   ├── OrderReview.php               ← Ratings and feedback
│   │   ├── Dispute.php                   ← Dispute tickets & resolution notes
│   │   └── AuditLog.php                  ← System admin activity logs
│   └── Providers/
│       └── AppServiceProvider.php        ← Global view composers & topbar alerts
├── resources/views/
│   ├── layout/
│   │   ├── frontend.blade.php            ← Customer layout (Mega menu, navbar search, dark mode)
│   │   └── backend.blade.php             ← Admin layout (Collapsible sidebar, topbar, dark mode)
│   ├── frontend/                         ← Customer views (Cart, checkout, tracking, addresses)
│   ├── backend/                          ← Admin views (Users, active orders, disputes, analytics)
│   ├── driver/                           ← Courier views (Dashboard, active trip, earnings)
│   └── auth/                             ← Login, register, profile cropper, password reset
├── routes/
│   ├── web.php                           ← Web routes (Public, Customer, Driver, Admin)
│   └── api.php                           ← REST API routes (Sanctum protected)
└── tests/Feature/                        ← Automated test suites (55 tests, 322 assertions)
```

---

## 🛠️ Installation & Setup

### 1. Prerequisites
- **PHP**: 8.2 or higher (OpenSSL, cURL, PDO MySQL, Mbstring extensions enabled)
- **Composer**: Latest version
- **Node.js & NPM**: Node 18+
- **Database**: MySQL 8.0+

### 2. Clone & Install Dependencies
```powershell
# Clone the repository
git clone https://github.com/your-username/lavavel_blog.git
cd lavavel_blog

# Install PHP dependencies
composer install

# Install Frontend dependencies
npm install
```

### 3. Environment Configuration
Copy the `.env.example` file to `.env`:
```powershell
cp .env.example .env
php artisan key:generate
```

Configure your `.env` settings:
```ini
APP_NAME="24/7 NHAM"
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ecommerce_1
DB_USERNAME=root
DB_PASSWORD=

# Stripe Payment Keys
STRIPE_KEY=pk_test_YOUR_PUBLISHABLE_KEY
STRIPE_SECRET=sk_test_YOUR_SECRET_KEY

# Google OAuth Credentials
GOOGLE_CLIENT_ID=your-google-client-id.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=your-google-client-secret
GOOGLE_REDIRECT_URI=http://127.0.0.1:8000/api/auth/google/callback
```

### 4. Database Migrations
```powershell
php artisan migrate
```
*(Note: Never run `migrate:fresh` or `db:wipe` on production environments).*

### 5. Build Assets & Start Server
```powershell
# Build frontend assets
npm run build

# Start Laravel development server
php artisan serve
# App running at http://127.0.0.1:8000
```

---

## 🧪 Testing & Code Quality

The project includes an extensive automated test suite covering all authentication, delivery, role assignment, and search flows:

```powershell
# Run the complete test suite
php artisan test
```

### Test Coverage (55 Tests, 322 Assertions)
- **`GoogleAuthTest`** (7 tests): Google redirect, customer creation, account linking, admin dashboard redirect, API callback, error handling, suspended user guard.
- **`AdminAssignRoleTest`** (6 tests): User role assignment (`admin`, `driver`, `customer`), courier onboarding, self-demotion protection.
- **`SidebarToggleTest`** (3 tests): Icon and name toggling, collapsed icon bar, localStorage persistence.
- **`InstantSearchTest`** (5 tests): Real-time navbar search, 1-character trigger, category filtering.
- **`ThemeModeTest`** (3 tests): Light/dark mode switching, FOUC prevention.
- **`ProfilePhotoUploadTest`** (9 tests): Image validation, crop data handling, uppercase extension support (`.PNG`, `.WEBP`).
- **`ComprehensiveDeliveryPlatformTest`** (11 tests): Full delivery lifecycle from order placement to driver delivery.
- **`CustomerOrderTrackingTest`** (6 tests): Live tracking lookup, milestone progressions.
- **`AuthRoleTest`** (3 tests): Role-based middleware and access boundaries.

### Code Style (Laravel Pint)
All PHP files adhere to clean code and PSR-12 / Laravel standards:
```powershell
vendor\bin\pint --test
```

---

## 🧭 Core Route Map

| Role / Area | Path | Method | Description |
| :--- | :--- | :--- | :--- |
| **Public** | `/` | `GET` | Homepage with hero, category sliders & dish grid |
| **Public** | `/list` | `GET` | Browse full food & beverage catalog |
| **Public** | `/search` | `GET` | Instant search results page |
| **Public** | `/track/{code?}` | `GET` | Public live order tracking timeline |
| **Auth** | `/login` | `GET/POST` | Sign in with email or Google |
| **Auth** | `/registration` | `GET/POST` | Register account or register with Google |
| **Auth** | `/auth/redirect` | `GET` | Google OAuth redirect |
| **Auth** | `/api/auth/google/callback` | `GET` | Google OAuth callback handler |
| **Customer** | `/my-orders` | `GET` | Order history, receipts & reorder |
| **Customer** | `/addresses` | `GET/POST` | Multi-address book with map pin-drop |
| **Customer** | `/favorites` | `GET` | Saved meals and favorite items |
| **Customer** | `/support/chat` | `GET/POST` | 24/7 Live Support customer chat |
| **Driver** | `/driver` | `GET` | Courier dashboard & incoming orders |
| **Driver** | `/driver/toggle-status` | `POST` | Switch driver availability (online/offline) |
| **Driver** | `/driver/active` | `GET` | Active delivery route & status milestones |
| **Driver** | `/driver/earnings` | `GET` | Trip earnings and payout summary |
| **Admin** | `/dashboard` | `GET` | Store manager dashboard & KPIs |
| **Admin** | `/admin/users` | `GET` | User & Courier directory & role assignment |
| **Admin** | `/admin/users/{id}/assign-role` | `POST` | Assign role (`admin`, `driver`, `customer`) |
| **Admin** | `/admin/active-orders` | `GET` | Live delivery dispatch & reassignment |
| **Admin** | `/admin/promos` | `GET/POST` | Promo discount code management |
| **Admin** | `/admin/disputes` | `GET/POST` | Dispute review & resolution center |
| **Admin** | `/admin/broadcast` | `GET/POST` | Push broadcast notification sender |
| **Admin** | `/admin/analytics` | `GET` | Revenue charts, driver ratings, sales KPIs |
| **Admin** | `/admin/audit-logs` | `GET` | Administrative security audit trail |

---

## 📄 License
This project is open-sourced software licensed under the [MIT license](LICENSE).
