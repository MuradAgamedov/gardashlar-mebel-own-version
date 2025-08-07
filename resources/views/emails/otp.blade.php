<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>OTP Kodu</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            border-radius: 5px;
        }
        .content {
            padding: 20px;
            background-color: #ffffff;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-top: 20px;
        }
        .otp-code {
            font-size: 32px;
            font-weight: bold;
            color: #007bff;
            text-align: center;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 5px;
            margin: 20px 0;
            letter-spacing: 5px;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            color: #666;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Gardashlar</h1>
        <p>Qeydiyyat Təsdiqləmə Kodu</p>
    </div>
    
    <div class="content">
        <h2>Salam!</h2>
        <p>Qeydiyyatınızı tamamlamaq üçün aşağıdakı OTP kodunu istifadə edin:</p>
        
        <div class="otp-code">
            {{ $otp }}
        </div>
        
        <p><strong>Diqqət:</strong></p>
        <ul>
            <li>Bu kod 10 dəqiqə ərzində etibarlıdır</li>
            <li>Kodu heç kimə verməyin</li>
            <li>Əgər siz bu email-i göndərməmisinizsə, onu nəzərə almayın</li>
        </ul>
        
        <p>Təşəkkür edirik!</p>
        <p>Gardashlar Komandası</p>
    </div>
    
    <div class="footer">
        <p>Bu email avtomatik olaraq göndərilmişdir. Cavablamayın.</p>
    </div>
</body>
</html> 