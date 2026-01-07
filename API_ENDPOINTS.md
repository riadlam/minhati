# API Endpoints Documentation

All API endpoints are prefixed with `/api` automatically.

Base URL: `https://your-domain.com/api`

## Authentication

All protected endpoints require a Bearer token in the Authorization header:
```
Authorization: Bearer {token}
```

### Authentication Endpoints

| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|---------------|
| POST | `/auth/tuteur/login` | Login as Tuteur | No |
| POST | `/auth/user/login` | Login as User | No |
| POST | `/auth/tuteur/logout` | Logout Tuteur | Yes (api.tuteur) |
| POST | `/auth/user/logout` | Logout User | Yes (api.user) |

---

## Tuteur Endpoints

| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|---------------|
| GET | `/tuteurs` | List all tuteurs | No |
| GET | `/tuteurs/{id}` | Get tuteur by ID | No |
| POST | `/tuteurs` | Create new tuteur | No |
| PUT | `/tuteurs/{id}` | Update tuteur | No |
| DELETE | `/tuteurs/{id}` | Delete tuteur | No |
| GET | `/tuteurs/mothers` | Get mothers for authenticated tuteur | Yes (api.tuteur) |
| POST | `/check/mother/nin` | Check if mother NIN exists | No |
| POST | `/check/mother/nss` | Check if mother NSS exists | No |

---

## Mother Endpoints

All mother endpoints require `api.tuteur` authentication.

| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|---------------|
| GET | `/mothers` | List all mothers for authenticated tuteur | Yes (api.tuteur) |
| POST | `/mothers` | Create new mother | Yes (api.tuteur) |
| GET | `/mothers/{id}` | Get mother by ID | Yes (api.tuteur) |
| PUT | `/mothers/{id}` | Update mother | Yes (api.tuteur) |
| DELETE | `/mothers/{id}` | Delete mother | Yes (api.tuteur) |

### Request/Response Examples

#### GET `/api/mothers`
**Response:**
```json
[
  {
    "id": 1,
    "nin": "123456789012345678",
    "nss": "123456789012",
    "nom_ar": "لقب",
    "prenom_ar": "اسم",
    "nom_fr": "Nom",
    "prenom_fr": "Prenom",
    "categorie_sociale": "عديم الدخل",
    "montant_s": "50000.00",
    "tuteur_nin": "987654321098765432",
    "date_insertion": "2024-01-01",
    "created_at": "2024-01-01T00:00:00.000000Z",
    "updated_at": "2024-01-01T00:00:00.000000Z"
  }
]
```

#### POST `/api/mothers`
**Request Body:**
```json
{
  "nin": "123456789012345678",
  "nss": "123456789012",
  "nom_ar": "لقب",
  "prenom_ar": "اسم",
  "nom_fr": "Nom",
  "prenom_fr": "Prenom",
  "categorie_sociale": "عديم الدخل",
  "montant_s": 50000.00
}
```

**Response (201):**
```json
{
  "message": "تم إنشاء الأم بنجاح",
  "data": {
    "id": 1,
    "nin": "123456789012345678",
    ...
  }
}
```

**Error Response (422):**
```json
{
  "message": "فشل في التحقق من البيانات",
  "errors": {
    "nin": ["الرقم الوطني للأم يجب أن يحتوي على 18 رقمًا بالضبط"]
  }
}
```

---

## Father Endpoints

All father endpoints require `api.tuteur` authentication.

| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|---------------|
| GET | `/fathers` | List all fathers for authenticated tuteur | Yes (api.tuteur) |
| POST | `/fathers` | Create new father | Yes (api.tuteur) |
| GET | `/fathers/{id}` | Get father by ID | Yes (api.tuteur) |
| PUT | `/fathers/{id}` | Update father | Yes (api.tuteur) |
| DELETE | `/fathers/{id}` | Delete father | Yes (api.tuteur) |

### Request/Response Examples

#### GET `/api/fathers`
**Response:**
```json
[
  {
    "id": 1,
    "nin": "123456789012345678",
    "nss": "123456789012",
    "nom_ar": "لقب",
    "prenom_ar": "اسم",
    "nom_fr": "Nom",
    "prenom_fr": "Prenom",
    "categorie_sociale": "عديم الدخل",
    "montant_s": "50000.00",
    "tuteur_nin": "987654321098765432",
    "date_insertion": "2024-01-01",
    "created_at": "2024-01-01T00:00:00.000000Z",
    "updated_at": "2024-01-01T00:00:00.000000Z"
  }
]
```

#### POST `/api/fathers`
**Request Body:**
```json
{
  "nin": "123456789012345678",
  "nss": "123456789012",
  "nom_ar": "لقب",
  "prenom_ar": "اسم",
  "nom_fr": "Nom",
  "prenom_fr": "Prenom",
  "categorie_sociale": "عديم الدخل",
  "montant_s": 50000.00
}
```

**Response (201):**
```json
{
  "message": "تم إنشاء الأب بنجاح",
  "data": {
    "id": 1,
    "nin": "123456789012345678",
    ...
  }
}
```

---

## Élève (Student) Endpoints

| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|---------------|
| GET | `/eleves` | List all students | No |
| GET | `/eleves/{id}` | Get student by ID | No |
| GET | `/tuteur/{nin}/eleves` | Get students by tuteur NIN | No |
| GET | `/children/check-matricule/{matricule}` | Check if matricule exists | No |
| POST | `/eleves` | Create new student | Yes (api.tuteur) |
| PUT | `/eleves/{num_scolaire}` | Update student | Yes (api.tuteur) |
| DELETE | `/eleves/{num_scolaire}` | Delete student | Yes (api.tuteur) |
| POST | `/eleves/{num_scolaire}/istimara/generate` | Generate istimara | Yes (api.tuteur) |

---

## Other Endpoints

### Wilaya Routes
- GET `/wilayas` - List all wilayas
- GET `/wilayas/{id}` - Get wilaya by ID
- POST `/wilayas` - Create wilaya
- PUT `/wilayas/{id}` - Update wilaya
- DELETE `/wilayas/{id}` - Delete wilaya

### Commune Routes
- GET `/communes` - List all communes
- GET `/communes/{id}` - Get commune by ID
- GET `/communes/by-wilaya/{wilayaId}` - Get communes by wilaya
- POST `/communes` - Create commune
- PUT `/communes/{id}` - Update commune
- DELETE `/communes/{id}` - Delete commune

### Etablissement Routes
- GET `/etablissements` - List all establishments (with filters)
- GET `/etablissements/{id}` - Get establishment by ID
- POST `/etablissements` - Create establishment
- PUT `/etablissements/{id}` - Update establishment
- DELETE `/etablissements/{id}` - Delete establishment

### User Routes
- GET `/user/current` - Get current authenticated user
- GET `/users` - List all users
- GET `/users/{id}` - Get user by ID
- POST `/users` - Create user
- PUT `/users/{id}` - Update user
- DELETE `/users/{id}` - Delete user

---

## Error Responses

All endpoints return standard HTTP status codes:

- `200` - Success
- `201` - Created
- `401` - Unauthorized (missing or invalid token)
- `404` - Not Found
- `422` - Validation Error
- `500` - Server Error

### Error Response Format:
```json
{
  "message": "Error message in Arabic",
  "errors": {
    "field_name": ["Error message"]
  }
}
```

---

## Authentication Flow

1. **Login** - POST to `/api/auth/tuteur/login` or `/api/auth/user/login`
   - Returns: `{ "token": "...", "user": {...} }`

2. **Use Token** - Include in Authorization header:
   ```
   Authorization: Bearer {token}
   ```

3. **Logout** - POST to `/api/auth/tuteur/logout` or `/api/auth/user/logout`
   - Token is revoked

---

## Notes

- All dates are in ISO 8601 format
- All monetary values are decimals
- NIN (National ID) must be exactly 18 digits
- NSS (Social Security Number) must be exactly 12 digits
- All protected endpoints automatically filter data by authenticated user's `tuteur_nin`
- Mothers and Fathers are scoped to the authenticated tuteur

