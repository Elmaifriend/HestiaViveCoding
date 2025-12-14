
**Notas Generales para el Frontend:**

1.  **Base URL:** `https://tu-dominio.com/api` (ajustar según entorno).
2.  **Headers Globales:** Todas las peticiones deben incluir `Accept: application/json`.
3.  **Autenticación:** Todas las rutas (excepto Login/Register) requieren el header `Authorization: Bearer <token>`.
4.  **Paginación:** Los endpoints de tipo "Listado" (Index) que usan `paginate()` devolverán una estructura con `data` (el array de objetos), `links` y `meta` (información de páginas).
5.  **Manejo de Errores (422):** Laravel devuelve los errores de validación con código 422 y un JSON con la estructura `{"message": "...", "errors": { "campo": ["error"] }}`.

-----

## 🔐 1. Autenticación (Auth)

### 1.1 Iniciar Sesión

Obtiene el token de acceso para el usuario.

  * **Endpoint:** `POST /login`
  * **Auth:** No requerida.
  * **Content-Type:** `application/json`

**Body (JSON):**

```json
{
  "email": "usuario@ejemplo.com", // Requerido, Email válido
  "password": "password123"       // Requerido
}
```

**Respuesta Exitosa (200 OK):**

```json
{
  "access_token": "1|msj...",
  "token_type": "Bearer",
  "user": {
    "id": 1,
    "name": "Juan Pérez",
    "email": "usuario@ejemplo.com",
    "role": "resident" // o 'admin', etc.
    // ...otros campos de usuario
  }
}
```

### 1.2 Registro de Usuario

  * **Endpoint:** `POST /register`
  * **Auth:** No requerida.
  * **Content-Type:** `application/json`

**Body (JSON):**

```json
{
  "name": "Nuevo Usuario",
  "email": "nuevo@ejemplo.com",
  "password": "password123",
  "password_confirmation": "password123", // Debe coincidir
  "role": "resident" // Requerido. Valores permitidos según Enum (ej: 'resident', 'admin')
}
```

**Respuesta Exitosa (201 Created):**
Devuelve la misma estructura que el Login (Token + User).

### 1.3 Obtener Usuario Actual

  * **Endpoint:** `GET /user`
  * **Auth:** Bearer Token.

**Respuesta Exitosa (200 OK):**

```json
{
  "id": 1,
  "name": "Juan Pérez",
  "email": "usuario@ejemplo.com",
  "role": "resident",
  "created_at": "2024-01-01T00:00:00.000000Z"
}
```

### 1.4 Cerrar Sesión

Revoca el token actual.

  * **Endpoint:** `POST /logout`
  * **Auth:** Bearer Token.

**Respuesta Exitosa (200 OK):**

```json
{ "message": "Logged out successfully" }
```

-----

## 🎟️ 2. Accesos e Invitaciones

### 2.1 Listar Invitaciones (Mis QR)

Muestra las invitaciones creadas por el usuario.

  * **Endpoint:** `GET /invitations`
  * **Auth:** Bearer Token.

**Respuesta Exitosa (200 OK):**

```json
[
  {
    "id": 1,
    "qr_code": "ABCD1234EFGH",
    "status": "active", // active, expired, used
    "expiration_date": "2024-12-14T10:00:00.000000Z",
    "created_at": "..."
  }
]
```

### 2.2 Crear Invitación (Generar QR)

  * **Endpoint:** `POST /invitations`
  * **Auth:** Bearer Token.
  * **Content-Type:** `application/json`

**Body (JSON):**

```json
{
  "expiration_hours": 24 // Opcional. Integer (1 a 168). Default: 24.
}
```

**Respuesta Exitosa (201 Created):**
Devuelve el objeto invitación creado con el código QR generado.

### 2.3 Validar QR (Para Guardias/Seguridad)

Verifica si un código QR es válido para ingresar.

  * **Endpoint:** `POST /invitations/validate`
  * **Auth:** Bearer Token.

**Body (JSON):**

```json
{
  "qr_code": "ABCD1234EFGH", // Requerido
  "mark_used": true          // Opcional (boolean). Si es true, la invitación pasa a estado 'used'.
}
```

**Respuesta Exitosa (200 OK):**

```json
{
  "message": "Valid QR.",
  "invitation": { ...objeto invitación... }
}
```

*Nota: Si está expirado o inválido devolverá error 400 o 404.*

### 2.4 Listar Accesos Frecuentes (Gate Access)

Historial de entradas registradas manualmente.

  * **Endpoint:** `GET /gate-access`
  * **Auth:** Bearer Token.

### 2.5 Registrar Acceso Manual

Para autorizar la entrada de alguien sin QR (ej. Uber, Visita rápida).

  * **Endpoint:** `POST /gate-access`
  * **Auth:** Bearer Token.

**Body (JSON):**

```json
{
  "guest_name": "Chofer Uber", // Requerido
  "entry_date": "2024-12-13 18:30:00" // Requerido (Formato Y-m-d H:i:s)
}
```

-----

## 🛒 3. Marketplace (Compra/Venta)

### 3.1 Listar Productos

Muestra productos activos de todos los residentes (Paginado).

  * **Endpoint:** `GET /marketplace`
  * **Auth:** Bearer Token.

**Respuesta Exitosa (200 OK):**

```json
{
  "data": [
    {
      "id": 10,
      "title": "Bicicleta",
      "price": 150.00,
      "photo_url": "https://dominio.com/storage/products/foto.jpg",
      "resident": { "name": "Vecino Juan" },
      "created_at": "..."
    }
  ],
  "links": { ... },
  "meta": { ... }
}
```

### 3.2 Publicar Producto

⚠️ **Importante:** Este endpoint requiere `multipart/form-data` para subir la imagen.

  * **Endpoint:** `POST /marketplace`
  * **Auth:** Bearer Token.
  * **Content-Type:** `multipart/form-data`

**Body (Form-Data):**

  * `title`: (Text) "Bicicleta de montaña" (Requerido)
  * `description`: (Text) "Usada en buen estado" (Requerido)
  * `price`: (Number) 500 (Requerido)
  * `photo`: (File) archivo.jpg (Opcional, máx 10MB)

### 3.3 Ver Detalle de Producto

  * **Endpoint:** `GET /marketplace/{id}`
  * **Auth:** Bearer Token.

-----

## 📅 4. Reservas y Amenidades

### 4.1 Listar Amenidades

Obtiene la lista de áreas comunes disponibles (Alberca, Gym, Salón).

  * **Endpoint:** `GET /amenities`
  * **Auth:** Bearer Token.

**Respuesta Exitosa (200 OK):**

```json
[
  { "id": 1, "name": "Salón de Eventos", "capacity": 50 },
  { "id": 2, "name": "Asador Norte", "capacity": 10 }
]
```

### 4.2 Mis Reservaciones

  * **Endpoint:** `GET /reservations`
  * **Auth:** Bearer Token.

### 4.3 Crear Reservación

  * **Endpoint:** `POST /reservations`
  * **Auth:** Bearer Token.
  * **Content-Type:** `application/json`

**Body (JSON):**

```json
{
  "amenity_id": 1,         // Requerido (ID válido de amenity)
  "date": "2024-12-20 16:00:00", // Requerido. Fecha futura.
  "duration_hours": 3      // Requerido. Min: 1, Max: 8.
}
```

**Errores Comunes:**

  * `422 Unprocessable Entity`: Si el horario ya está ocupado ("Time slot not available").

-----

## 🚨 5. Incidentes (Reportes)

### 5.1 Reportar Incidente

⚠️ **Importante:** Requiere `multipart/form-data`.

  * **Endpoint:** `POST /incidents`
  * **Auth:** Bearer Token.
  * **Content-Type:** `multipart/form-data`

**Body (Form-Data):**

  * `title`: (Text) "Fuga de agua" (Requerido)
  * `description`: (Text) "En la entrada principal..." (Requerido)
  * `photo`: (File) evidencia.jpg (Opcional, máx 10MB)

### 5.2 Listar Mis Incidentes

  * **Endpoint:** `GET /incidents`
  * **Auth:** Bearer Token.

-----

## 💳 6. Pagos (Comprobantes)

### 6.1 Subir Comprobante de Pago

⚠️ **Importante:** Requiere `multipart/form-data`.

  * **Endpoint:** `POST /payments`
  * **Auth:** Bearer Token.
  * **Content-Type:** `multipart/form-data`

**Body (Form-Data):**

  * `amount`: (Number) 1200.50 (Requerido)
  * `date_paid`: (Date) "2024-12-01" (Requerido)
  * `receipt`: (File) comprobante.pdf o imagen (Opcional, máx 10MB)

### 6.2 Historial de Pagos

  * **Endpoint:** `GET /payments`
  * **Auth:** Bearer Token.

-----

## 💬 7. Chat (Mensajería Interna)

### 7.1 Listar Mensajes

Obtiene historial de chat (paginado).

  * **Endpoint:** `GET /chat`
  * **Auth:** Bearer Token.

### 7.2 Enviar Mensaje

  * **Endpoint:** `POST /chat`
  * **Auth:** Bearer Token.
  * **Content-Type:** `application/json`

**Body (JSON):**

```json
{
  "receiver_id": 5,        // Requerido (ID del usuario destino)
  "content": "Hola vecino" // Requerido
}
```

-----

## 📢 8. Anuncios (Noticias)

### 8.1 Listar Anuncios

Noticias publicadas por la administración (Paginado).

  * **Endpoint:** `GET /announcements`
  * **Auth:** Bearer Token.

### 8.2 Ver Anuncio Individual

  * **Endpoint:** `GET /announcements/{id}`
  * **Auth:** Bearer Token.