# Become A Investor Form API

## Endpoint
```
POST /api/become-a-investor-form
```

## Description
Submit a new become a investor form with optional file upload.

## Headers
```
Content-Type: multipart/form-data
```

## Request Body (Form Data)

### Required Fields:
- `name` (string, max 255 chars) - Full name
- `mobile` (string, max 20 chars) - Mobile number
- `email` (email, max 255 chars) - Email address
- `message` (string, max 2000 chars) - Application message

### Optional Fields:
- `file` (file) - Uploaded file (PDF, DOC, DOCX, XLS, XLSX, TXT, max 10MB)

## Postman Body Examples

### Example 1: Basic Form (No File)
```
POST /api/become-a-investor-form
Content-Type: multipart/form-data

Form Data:
- name: "John Doe"
- mobile: "+994501234567"
- email: "john@example.com"
- message: "I want to become an investor in your company. I have experience in furniture industry and would like to discuss investment opportunities."
```

### Example 2: Form with File Upload
```
POST /api/become-a-investor-form
Content-Type: multipart/form-data

Form Data:
- name: "Jane Smith"
- mobile: "+994551234567"
- email: "jane@example.com"
- message: "I am interested in investing in your furniture business. Please find my business proposal attached."
- file: [Select PDF file from your computer]
```

### Example 3: Form with Excel File
```
POST /api/become-a-investor-form
Content-Type: multipart/form-data

Form Data:
- name: "Ahmed Aliyev"
- mobile: "+994701234567"
- email: "ahmed@example.com"
- message: "I would like to invest in your company. I have prepared a detailed financial analysis in the attached Excel file."
- file: [Select XLSX file from your computer]
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
        "name": ["Name is required."],
        "email": ["Email must be a valid email address."],
        "mobile": ["Mobile number is required."],
        "message": ["Message is required."]
    }
}
```

### File Upload Error Response (500 Internal Server Error)
```json
{
    "success": false,
    "message": "Error submitting form: File upload failed: File type not allowed. Allowed types: pdf, doc, docx, xls, xlsx, txt. Got: jpg",
    "data": null
}
```

## Allowed File Types
- PDF (.pdf)
- Word Document (.doc, .docx)
- Excel Spreadsheet (.xls, .xlsx)
- Text File (.txt)

## File Size Limit
- Maximum: 10MB

## Notes
- Files are stored in `storage/become-a-investor-forms/` directory
- Original filenames are preserved (with automatic numbering if duplicate exists)
- Form submissions are marked as unread by default
- Admin can view, mark as read, and delete submissions through admin panel 