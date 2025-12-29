# API Documentation

Base URL: `http://localhost:8000/api`

All endpoints return JSON responses.

**Authentication:** This API uses **token-only authentication** with Laravel Sanctum. After login, you'll receive a Bearer token that **must** be included in the `Authorization` header for all protected routes. No session fallback is supported.

---

## Authentication

### Tuteur Login
**POST** `/auth/tuteur/login`

**Request Body:**
```json
{
  "nin": "123456789012345678",
  "password": "password123"
}
```

**Success Response (200):**
```json
{
  "success": true,
  "message": "تم تسجيل الدخول بنجاح",
  "token": "1|xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx",
  "token_type": "Bearer",
  "expires_in": 2592000,
  "data": {
    "nin": "123456789012345678",
    "nom_ar": "اسم",
    "prenom_ar": "لقب"
  }
}
```

**Note:** Store the `token` value and include it in the `Authorization` header for subsequent requests:
```
Authorization: Bearer 1|xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
```

**Error Response (401):**
```json
{
  "success": false,
  "message": "رقم التعريف الوطني غير موجود",
  "errors": {
    "nin": ["رقم التعريف الوطني غير موجود"]
  }
}
```

### Tuteur Logout
**POST** `/auth/tuteur/logout`

**Headers:**
- `Authorization: Bearer {token}` (required)

**Success Response (200):**
```json
{
  "success": true,
  "message": "تم تسجيل الخروج بنجاح"
}
```

**Note:** This revokes the current token. Make sure to remove the token from client storage after logout.

### User Login
**POST** `/auth/user/login`

**Request Body:**
```json
{
  "code_user": "123456789012345678",
  "password": "password123"
}
```

**Success Response (200):**
```json
{
  "success": true,
  "message": "تم تسجيل الدخول بنجاح",
  "token": "1|xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx",
  "token_type": "Bearer",
  "expires_in": 2592000,
  "data": {
    "code_user": "123456789012345678",
    "nom_user": "اسم",
    "prenom_user": "لقب",
    "role": "ts_commune",
    "commune": "بلدية"
  }
}
```

**Note:** Store the `token` value and include it in the `Authorization` header for subsequent requests.

### User Logout
**POST** `/auth/user/logout`

**Success Response (200):**
```json
{
  "success": true,
  "message": "تم تسجيل الخروج بنجاح"
}
```

---

## Tuteur (Guardian) Management

### Create Tuteur Account
**POST** `/tuteurs`

**Request Body:** (FormData or JSON)
```json
{
  "nin": "123456789012345678",
  "num_cpt": "123456789012",
  "cle_cpt": "12",
  "nom_ar": "اسم",
  "prenom_ar": "لقب",
  "email": "email@example.com",
  "password": "password123",
  "date_naiss": "1990-01-01",
  "commune_naiss": "01001",
  "code_commune": "01001",
  "adresse": "العنوان",
  "tel": "0123456789"
}
```

**Success Response (201):**
```json
{
  "message": "تمت إضافة الولي/الوصي بنجاح",
  "data": { ... }
}
```

### Get All Tuteurs
**GET** `/tuteurs`

### Get Tuteur by ID
**GET** `/tuteurs/{nin}`

### Update Tuteur
**PUT** `/tuteurs/{nin}`

### Delete Tuteur
**DELETE** `/tuteurs/{nin}`

---

## Student (Eleve) Management

### Create Student (Protected - Requires Authentication)
**POST** `/eleves`

**Headers:**
- `Authorization: Bearer {token}` (required)
- `X-CSRF-TOKEN`: CSRF token
- `Accept: application/json`

**Request Body:** (FormData)
```
num_scolaire: "1234567890123456"
nom: "اسم"
prenom: "لقب"
nom_pere: "اسم الأب"
prenom_pere: "لقب الأب"
nom_mere: "اسم الأم"
prenom_mere: "لقب الأم"
date_naiss: "2010-01-01"
commune_naiss: "01001"
ecole: "code_etabliss"
niveau: "الابتدائي"
classe_scol: "السنة الأولى"
sexe: "ذكر"
commune_id: "01001"
...
```

**Success Response (201):**
```json
{
  "num_scolaire": "1234567890123456",
  "nom": "اسم",
  "prenom": "لقب",
  ...
}
```

### Get All Students
**GET** `/eleves`

### Get Student by ID
**GET** `/eleves/{num_scolaire}`

### Update Student (Protected)
**PUT** `/eleves/{num_scolaire}`

**Headers:**
- `Authorization: Bearer {token}` (required)
- `X-CSRF-TOKEN`: CSRF token
- `Accept: application/json`

### Delete Student (Protected)
**DELETE** `/eleves/{num_scolaire}`

**Headers:**
- `Authorization: Bearer {token}` (required)
- `X-CSRF-TOKEN`: CSRF token
- `Accept: application/json`

### Get Students by Tuteur
**GET** `/tuteur/{nin}/eleves`

### Check Matricule Availability
**GET** `/children/check-matricule/{matricule}`

**Response:**
```json
{
  "exists": false,
  "message": "Matricule disponible"
}
```

### Generate Istimara PDF (Protected)
**POST** `/eleves/{num_scolaire}/istimara/generate`

**Headers:**
- `Authorization: Bearer {token}` (required)
- `X-CSRF-TOKEN`: CSRF token
- `Accept: application/json`

**Success Response (200):**
```json
{
  "success": true,
  "message": "PDF generated successfully",
  "url": "/eleves/1234567890123456/istimara"
}
```

---

## Reference Data

### Get All Wilayas
**GET** `/wilayas`

### Get Communes by Wilaya
**GET** `/communes/by-wilaya/{wilayaId}`

**Example:** `/api/communes/by-wilaya/01`

### Get All Etablissements
**GET** `/etablissements`

**Query Parameters:**
- `code_wilaya` (optional)
- `code_commune` (optional)
- `niveau_enseignement` (optional)
- `nature_etablissement` (optional)

---

## Notes

- All routes are prefixed with `/api`
- **Token-only authentication** - All protected routes **require** Bearer token (no session fallback)
- Token expires after 30 days (2,592,000 seconds)
- CSRF token required for POST/PUT/DELETE requests
- All responses are in JSON format
- Error responses include `success: false` and error details
- Tokens are automatically revoked on logout
- **Important:** Always include `Authorization: Bearer {token}` header for protected endpoints

