# Hestia Backend Walkthrough

The Hestia backend is now fully implemented with Laravel 12, Filament 4, and Sanctum.

## 1. Authentication & Users
- **Admin**: `admin@hestia.com` / `password` (Access to Filament Panel)
- **Guard**: `guard@hestia.com` / `password`
- **Residents**: 10 seeded users (e.g., `resident@hestia.com` / `password`)

## 2. API Endpoints (Prefix: `/api`)
All endpoints (except login/register) require `Authorization: Bearer <token>`.

### Auth
- `POST /register`: Register new user.
- `POST /login`: Login and get token.
- `POST /logout`: Revoke token.
- `GET /user`: Get current user profile.

### Modules
- **Invitations**: `GET /invitations`, `POST /invitations`, `POST /invitations/validate` (Check QR).
- **Payments**: `GET /payments`, `POST /payments` (Upload receipt).
- **Announcements**: `GET /announcements`, `GET /announcements/{id}`.
- **Amenities**: `GET /amenities`, `POST /reservations`, `GET /reservations`.
- **Incidents**: `GET /incidents`, `POST /incidents` (Report with photo).
- **Chat**: `GET /chat` (History), `POST /chat` (Send message).
- **Gate Access**: `GET /gate-access` (History), `POST /gate-access` (Request entry).
- **Marketplace**: `GET /marketplace`, `POST /marketplace` (Sell item), `GET /marketplace/{id}`.

## 3. Admin Panel (Filament)
Access at `/admin`.
- **Dashboard**: Overview.
- **Resources**:
    - **Users**: Manage Residents, Guards, Admins.
    - **Invitations**: View/Manage QR codes.
    - **Payments**: Approve/Reject payments, view receipts.
    - **Announcements**: Publish news/alerts (Push notification toggle).
    - **Amenities**: Manage facilities.
    - **Reservations**: View bookings.
    - **Incidents**: Track status (Open -> Resolved).
    - **Messages**: View chat logs.
    - **Gate Entries**: View entry logs/requests.
    - **Products**: Manage marketplace listings.

## 4. Database
- **Migrations**: All tables created (`users`, `invitations`, `payments`, `announcements`, `amenities`, `reservations`, `incidents`, `messages`, `gate_entries`, `products`).
- **Seeders**: Database seeded with initial users and data.
