# Suggest A Place Form API

## Endpoint
```
POST /api/suggest-a-place-form
```

## Description
Submit a new suggest a place form with optional file upload.

## Headers
```
Content-Type: multipart/form-data
```

## Request Body (Form Data)

### Required Fields:
- `name_surname` (string, max 255 chars) - Full name and surname
- `email` (email, max 255 chars) - Email address
- `area_sqm` (string, max 50 chars) - Area in square meters
- `mobile` (string, max 20 chars) - Mobile number
- `address` (string, max 500 chars) - Address
- `floor_count` (string, max 50 chars) - Number of floors
- `is_repaired` (boolean) - Repair status (true=repaired, false=unrepaired)
- `has_warehouse` (boolean) - Has warehouse area (true=yes, false=no)
- `has_parking` (boolean) - Has parking (true=yes, false=no)
- `details` (string, max 2000 chars) - Detailed information

### Optional Fields:
- `file` (file) - Uploaded file (PDF, DOC, DOCX, XLS, XLSX, TXT, JPG, JPEG, PNG, max 10MB)

## Postman Body Examples

### Example 1: Basic Form (No File)
```
POST /api/suggest-a-place-form
Content-Type: multipart/form-data

Form Data:
- name_surname: "Ahmed Aliyev"
- email: "ahmed@example.com"
- area_sqm: "150"
- mobile: "+994501234567"
- address: "Bakı şəhəri, Nərimanov rayonu, Atatürk prospekti 123"
- floor_count: "3"
- is_repaired: true
- has_warehouse: true
- has_parking: false
- details: "Bu yer mebel istehsalı üçün çox uyğundur. Mərkəzi yerləşir və əlçatandır."
```

### Example 2: Form with File Upload
```
POST /api/suggest-a-place-form
Content-Type: multipart/form-data

Form Data:
- name_surname: "Fatima Məmmədova"
- email: "fatima@example.com"
- area_sqm: "200"
- mobile: "+994551234567"
- address: "Sumqayıt şəhəri, 28 May küçəsi 45"
- floor_count: "2"
- is_repaired: false
- has_warehouse: false
- has_parking: true
- details: "Bu yerin təmiri lazımdır amma potensialı yüksəkdir. Avtopark mövcuddur."
- file: [Select PDF file from your computer]
```

### Example 3: Form with Image File
```
POST /api/suggest-a-place-form
Content-Type: multipart/form-data

Form Data:
- name_surname: "Rəşad Hüseynov"
- email: "rashad@example.com"
- area_sqm: "300"
- mobile: "+994701234567"
- address: "Gəncə şəhəri, Heydər Əliyev prospekti 78"
- floor_count: "4"
- is_repaired: true
- has_warehouse: true
- has_parking: true
- details: "Bu yer tam təmirli və bütün lazımi şəraitlərə malikdir. Böyük sahəsi var."
- file: [Select JPG image from your computer]
```

## Response Examples

### Success Response (201 Created)
```json
{
    "success": true,
    "message": "Form submitted successfully",
    "data": {
        "id": 1
    }
}
```

### Validation Error Response (422 Unprocessable Entity)
```json
{
    "success": false,
    "message": "Validation failed",
    "errors": {
        "name_surname": ["Name and surname is required."],
        "email": ["Email must be a valid email address."],
        "area_sqm": ["Area is required."],
        "mobile": ["Mobile number is required."],
        "address": ["Address is required."],
        "floor_count": ["Floor count is required."],
        "is_repaired": ["Repair status is required."],
        "has_warehouse": ["Warehouse status is required."],
        "has_parking": ["Parking status is required."],
        "details": ["Details are required."]
    }
}
```

### File Upload Error Response (500 Internal Server Error)
```json
{
    "success": false,
    "message": "Error submitting form: File upload failed: File type not allowed. Allowed types: pdf, doc, docx, xls, xlsx, txt, jpg, jpeg, png. Got: gif",
    "data": null
}
```

## Allowed File Types
- PDF (.pdf)
- Word Document (.doc, .docx)
- Excel Spreadsheet (.xls, .xlsx)
- Text File (.txt)
- Images (.jpg, .jpeg, .png)

## File Size Limit
- Maximum: 10MB

## Boolean Field Values
- `is_repaired`: true = "Təmirli", false = "Təmirsiz"
- `has_warehouse`: true = "Bəli", false = "Xeyr"
- `has_parking`: true = "Bəli", false = "Xeyr"

## Notes
- Files are stored in `storage/suggest-a-place-forms/` directory
- Original filenames are preserved (with automatic numbering if duplicate exists)
- Form submissions are marked as unread by default
- Admin can view, mark as read, and delete submissions through admin panel
- Boolean fields are automatically converted to Azerbaijani text in admin panel 