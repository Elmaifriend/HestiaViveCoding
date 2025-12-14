# Hestia API Documentation

**Base URL**: `http://localhost:8000/api`
**Authentication**: Bearer Token (Available via `/login`)

---

## 1. Authentication

### Register
`POST /register`
- **Body**: `{ "name": "John Doe", "email": "john@example.com", "password": "password", "password_confirmation": "password" }`
- **Response**: `{ "token": "..." }`

### Login
`POST /login`
- **Body**: `{ "email": "resident@hestia.com", "password": "password" }`
- **Response**: `{ "token": "..." }`

### Logout
`POST /logout`
- **Headers**: `Authorization: Bearer <token>`
- **Response**: `{ "message": "Logged out" }`

### User Profile
`GET /user`
- **Headers**: `Authorization: Bearer <token>`
- **Response**: User object with roles.

---

## 2. Modules

### Invitations
- `GET /invitations`: List my invitations.
- `POST /invitations`: Create invitation.
    - Body: `{ "visitor_name": "Jane", "notes": "Optional" }`
- `POST /invitations/validate`: Validate QR (Guard/Admin).
    - Body: `{ "qr_code": "uuid-string" }`

### Payments
- `GET /payments`: List my payments.
- `POST /payments`: Submit payment receipt.
    - Body (Multipart): `{ "amount": 100.00, "description": "Rent", "receipt": [File] }`

### Announcements
- `GET /announcements`: List published announcements.
- `GET /announcements/{id}`: Get details.

### Amenities & Reservations
- `GET /amenities`: List available amenities.
- `GET /reservations`: List my reservations.
- `POST /reservations`: Book an amenity.
    - Body: `{ "amenity_id": 1, "date": "2025-12-25", "start_time": "14:00", "hours": 2 }`

### Incidents
- `GET /incidents`: List my reports.
- `POST /incidents`: Report incident.
    - Body (Multipart): `{ "title": "Leak", "description": "Water leak in hall", "photo": [File] }`

### Chat
- `GET /chat`: Get message history.
- `POST /chat`: Send message.
    - Body: `{ "receiver_id": 1, "content": "Hello guard" }`

### Gate Access
- `GET /gate-access`: List entry history.
- `POST /gate-access`: Request remote opening.
    - Body: `{ "guest_name": "Uber", "plate": "ABC-123" }`

### Marketplace
- `GET /marketplace`: List items for sale.
- `POST /marketplace`: Sell item.
    - Body (Multipart): `{ "title": "Bike", "price": 50.00, "description": "Used bike", "photo": [File] }`
- `GET /marketplace/{id}`: Get item details.
