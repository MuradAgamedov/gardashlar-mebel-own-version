# Reset Password API Documentation

## Complete Password Reset Flow

### 1. Forgot Password - Send OTP
**Endpoint:** `POST /api/forgot-password`

**Request:**
```json
{
    "email": "user@example.com"
}
```

**Success Response (200):**
```json
{
    "success": true,
    "message": "Şifrə yeniləmə üçün OTP kodu göndərildi",
    "data": {
        "email": "user@example.com",
        "otp_sent": true
    }
}
```

**Error Responses:**
- **404** - User not found
- **429** - Rate limit (1 minute cooldown)
- **422** - Validation error

---

### 2. Verify Reset OTP
**Endpoint:** `POST /api/verify-reset-otp`

**Request:**
```json
{
    "email": "user@example.com",
    "otp_code": "123456"
}
```

**Success Response (200):**
```json
{
    "success": true,
    "message": "OTP təsdiqləndi. İndi yeni şifrə təyin edə bilərsiniz",
    "data": {
        "email": "user@example.com",
        "verified": true,
        "reset_token": "abc123xyz789..."
    }
}
```

**Error Response (422):**
```json
{
    "success": false,
    "message": "OTP kodu düzgün deyil və ya vaxtı keçib",
    "errors": {
        "otp_code": ["OTP kodu düzgün deyil və ya vaxtı keçib"]
    }
}
```

---

### 3. Reset Password
**Endpoint:** `POST /api/reset-password`

**Request:**
```json
{
    "reset_token": "abc123xyz789...",
    "password": "NewPassword123!",
    "password_confirmation": "NewPassword123!"
}
```

**Success Response (200):**
```json
{
    "success": true,
    "message": "Şifrə uğurla yeniləndi və email təsdiqləndi",
    "data": {
        "email": "user@example.com",
        "password_reset": true,
        "email_verified": true,
        "email_verified_now": true
    }
}
```

**Error Response (422):**
```json
{
    "success": false,
    "message": "Reset token düzgün deyil və ya vaxtı keçib",
    "errors": {
        "reset_token": ["Reset token düzgün deyil və ya vaxtı keçib"]
    }
}
```

---

### 4. Resend OTP (Bonus)
**Endpoint:** `POST /api/resend-otp`

**Request:**
```json
{
    "email": "user@example.com",
    "purpose": "password_reset"
}
```

**Success Response (200):**
```json
{
    "success": true,
    "message": "OTP kodu yenidən göndərildi",
    "data": {
        "contact_type": "email",
        "contact_info": "user@example.com",
        "otp_sent": true
    }
}
```

> **Note:** `purpose` can be `registration` (default) or `password_reset`

---

## Features

✅ **Rate Limiting** - 1 minute cooldown between OTP requests  
✅ **OTP Expiration** - 10 minutes validity  
✅ **Reset Token Security** - Temporary tokens with 10-minute expiry  
✅ **Email Verification Bonus** - Automatically verifies email if not verified  
✅ **Strong Password Validation** - Minimum 8 characters, uppercase, number, special character  
✅ **Comprehensive Logging** - All actions logged for debugging  
✅ **Azerbaijani Messages** - User-friendly error messages  

---

## Complete Flow Example

```bash
# Step 1: Send OTP
curl -X POST http://localhost:8000/api/forgot-password \
  -H "Content-Type: application/json" \
  -d '{"email": "user@example.com"}'

# Step 2: Verify OTP (get reset_token)
curl -X POST http://localhost:8000/api/verify-reset-otp \
  -H "Content-Type: application/json" \
  -d '{"email": "user@example.com", "otp_code": "123456"}'

# Step 3: Reset Password
curl -X POST http://localhost:8000/api/reset-password \
  -H "Content-Type: application/json" \
  -d '{
    "reset_token": "abc123xyz789...",
    "password": "NewPassword123!",
    "password_confirmation": "NewPassword123!"
  }'
```

---

## Database Changes

New migration adds `purpose` column to `otp_codes` table:
```sql
ALTER TABLE otp_codes ADD COLUMN purpose VARCHAR(255) NULL COMMENT 'Purpose of OTP: registration, password_reset, etc.';
```

---

## Security Notes

- Reset tokens are stored in cache (Redis/File) with 10-minute expiry
- OTP codes are marked as used after verification
- Rate limiting prevents brute force attacks
- Email verification is automatically completed during password reset
- All sensitive operations are logged for audit trail
