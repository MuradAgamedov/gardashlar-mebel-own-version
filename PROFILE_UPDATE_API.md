# Profile Update API Documentation

Bu API istifadəçilərə profil məlumatlarını (ad, soyad, telefon, email) yeniləməyə imkan verir. Email dəyişikliyi üçün təhlükəsiz iki mərhələli proses tətbiq edilib.

## 🔐 Authentication

Bütün endpoint-lər Bearer Token tələb edir:
```
Authorization: Bearer {your_access_token}
```

## 📋 Endpoints

### 1. Profile Update
**POST** `/api/profile/update`

İstifadəçi profil məlumatlarını yeniləyir.

#### Request Body
```json
{
    "name": "Vəli",
    "surname": "Əliyev", 
    "phone": "+994501234567",
    "email": "veli.aliyev@example.com"
}
```

#### Parameters
| Field | Type | Required | Description |
|-------|------|----------|-------------|
| name | string | No | İstifadəçinin adı (max: 255) |
| surname | string | No | İstifadəçinin soyadı (max: 255) |
| phone | string | No | Telefon nömrəsi (max: 20, unique) |
| email | string | No | Email ünvanı (email format, unique) |

#### Response - Success (200)
```json
{
    "success": true,
    "message": "Profil uğurla yeniləndi",
    "data": {
        "user": {
            "id": 1,
            "name": "Vəli",
            "surname": "Əliyev",
            "phone": "+994501234567",
            "email": "veli@example.com",
            "email_verified_at": "2024-01-01T12:00:00.000000Z"
        },
        "otp_sent": false
    }
}
```

#### Response - Email Change Required (200)
```json
{
    "success": true,
    "message": "Profil yeniləndi. Yeni email ünvanınıza təsdiq kodu göndərildi",
    "data": {
        "user": {
            "id": 1,
            "name": "Vəli",
            "surname": "Əliyev", 
            "phone": "+994501234567",
            "email": "veli@example.com",
            "email_verified_at": "2024-01-01T12:00:00.000000Z"
        },
        "otp_sent": true
    }
}
```

#### Response - Validation Error (422)
```json
{
    "success": false,
    "message": "Validation failed",
    "errors": {
        "email": ["Bu email ünvanı artıq istifadə olunub"],
        "phone": ["Bu telefon nömrəsi artıq istifadə olunub"]
    }
}
```

---

### 2. Confirm Email Change
**POST** `/api/profile/confirm-email`

Yeni email ünvanını OTP kodu ilə təsdiq edir.

#### Request Body
```json
{
    "otp_code": "123456"
}
```

#### Parameters
| Field | Type | Required | Description |
|-------|------|----------|-------------|
| otp_code | string | Yes | 6 rəqəmli OTP kodu |

#### Response - Success (200)
```json
{
    "success": true,
    "message": "Email uğurla təsdiqləndi və dəyişdirildi",
    "data": {
        "user": {
            "id": 1,
            "name": "Vəli",
            "surname": "Əliyev",
            "phone": "+994501234567",
            "email": "veli.aliyev@example.com",
            "email_verified_at": "2024-01-01T12:30:00.000000Z"
        }
    }
}
```

#### Response - Invalid OTP (422)
```json
{
    "success": false,
    "message": "OTP kod düzgün deyil",
    "errors": {
        "otp_code": ["OTP kod düzgün deyil"]
    }
}
```

#### Response - No Pending Email (422)
```json
{
    "success": false,
    "message": "Email dəyişmə sorğusu tapılmadı",
    "errors": {
        "pending_email": ["Aktiv email dəyişmə sorğusu yoxdur"]
    }
}
```

#### Response - Expired (422)
```json
{
    "success": false,
    "message": "Email dəyişmə sorğusunun vaxtı keçib",
    "errors": {
        "expired": ["Zəhmət olmasa yenidən cəhd edin"]
    }
}
```

---

## 🔄 Email Change Flow

### Prosess:
1. **Profile Update**: İstifadəçi yeni email daxil edir
2. **Pending State**: Email `pending_email` sahəsində saxlanılır
3. **OTP Send**: Yeni emailə OTP göndərilir
4. **User Stays Logged In**: İstifadəçi logout olmur
5. **OTP Verify**: İstifadəçi OTP kodunu daxil edir
6. **Email Updated**: Təsdiq olunduqdan sonra email yenilənir

### Üstünlüklər:
- ✅ İstifadəçi logout olmur
- ✅ Səhv email daxil etsə belə təhlükəsiz
- ✅ Köhnə email aktiv qalır təsdiq bitənə qədər
- ✅ 15 dəqiqə ərzində OTP etibarlıdır
- ✅ Yalnız yeni emailə OTP göndərilir

---

## 🚨 Error Codes

| HTTP Status | Description |
|-------------|-------------|
| 200 | Success |
| 401 | Unauthorized (invalid/missing token) |
| 422 | Validation Error |
| 500 | Internal Server Error |

---

## 📝 Validation Rules

### Name & Surname
- Type: string
- Max length: 255
- Required if provided

### Phone
- Type: string  
- Max length: 20
- Must be unique across users
- Nullable

### Email
- Must be valid email format
- Must be unique across users  
- Nullable

---

## 🔧 Testing Examples

### cURL Examples

#### Update Name and Phone
```bash
curl -X POST http://127.0.0.1:8000/api/profile/update \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Yeni Ad",
    "phone": "+994501234567"
  }'
```

#### Change Email
```bash
curl -X POST http://127.0.0.1:8000/api/profile/update \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "email": "yeni.email@example.com"
  }'
```

#### Confirm Email Change
```bash
curl -X POST http://127.0.0.1:8000/api/profile/confirm-email \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "otp_code": "123456"
  }'
```

---

## 📊 Database Changes

### User Model Fields
```php
// Existing fields
'name', 'surname', 'phone', 'email', 'password'

// New fields for email verification
'pending_email',           // Yeni email (təsdiq gözləyir)
'pending_email_token',     // Təhlükəsizlik tokeni
'pending_email_expires_at' // OTP-nin bitmə vaxtı
```

### Migration
```bash
php artisan migrate
```

Bu migration aşağıdakı sahələri əlavə edir:
- `pending_email` (nullable string)
- `pending_email_token` (nullable string)  
- `pending_email_expires_at` (nullable timestamp)
