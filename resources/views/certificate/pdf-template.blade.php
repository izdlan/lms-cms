<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate - {{ $exStudent->name }}</title>
    <style>
        @page {
            margin: 0.3in;
            size: A4 portrait;
        }
        
        body {
            margin: 0;
            padding: 0;
            font-family: 'Times New Roman', serif;
            background: white;
            color: #000;
            line-height: 1.3;
        }
        
        .certificate-container {
            width: 100%;
            min-height: 100vh;
            position: relative;
            background: white;
            padding: 15px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 25px;
            position: relative;
        }
        
        .logo-section {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
        }
        
        .logo {
            width: 120px;
            height: 120px;
            margin-right: 30px;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .crest {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
            border: 4px solid #fbbf24;
            border-radius: 50%;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
        }
        
        .crest::before {
            content: "👑";
            font-size: 20px;
            position: absolute;
            top: -8px;
            color: #fbbf24;
        }
        
        .crest::after {
            content: "⚔️📚🔥";
            font-size: 12px;
            position: absolute;
            color: white;
            text-align: center;
            line-height: 1.2;
        }
        
        .crest-banner {
            position: absolute;
            bottom: -15px;
            left: 50%;
            transform: translateX(-50%);
            background: #fbbf24;
            color: #1e3a8a;
            padding: 2px 8px;
            font-size: 8px;
            font-weight: bold;
            border-radius: 3px;
            white-space: nowrap;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
        
        .crest-banner::before {
            content: "Lucrat Lux Vestra";
        }
        
        .university-name {
            font-size: 36px;
            font-weight: bold;
            color: #dc3545;
            text-transform: uppercase;
            margin: 0;
            letter-spacing: 3px;
            font-family: 'Times New Roman', serif;
        }
        
        .university-subtitle {
            font-size: 18px;
            color: #007bff;
            text-transform: uppercase;
            text-decoration: underline;
            text-decoration-color: #007bff;
            text-decoration-thickness: 3px;
            margin: 8px 0 0 0;
            letter-spacing: 2px;
            font-weight: bold;
        }
        
        .main-title {
            font-size: 32px;
            font-weight: bold;
            color: #2c3e50;
            text-align: center;
            margin: 35px 0;
            font-family: 'Times New Roman', serif;
            font-style: italic;
            text-decoration: underline;
            text-decoration-color: #2c3e50;
            text-decoration-thickness: 2px;
            letter-spacing: 1px;
        }
        
        .actual-degree {
            font-size: 36px;
            font-weight: bold;
            color: #2c3e50;
            text-align: center;
            margin: 20px 0 35px 0;
            font-family: 'Times New Roman', serif;
            font-style: italic;
            text-decoration: underline;
            text-decoration-color: #2c3e50;
            text-decoration-thickness: 3px;
            letter-spacing: 2px;
        }
        
        .certification-text {
            font-size: 16px;
            text-align: center;
            margin: 25px 0;
            font-style: italic;
            font-family: 'Times New Roman', serif;
        }
        
        .student-name {
            font-size: 24px;
            font-weight: bold;
            color: #2c3e50;
            text-align: center;
            margin: 30px 0;
            font-style: italic;
            text-decoration: underline;
            text-decoration-color: #667eea;
            text-decoration-thickness: 3px;
            font-family: 'Times New Roman', serif;
        }
        
        .award-text {
            font-size: 18px;
            text-align: center;
            margin: 25px 0;
            font-style: italic;
            font-family: 'Times New Roman', serif;
        }
        
        .course-name {
            font-size: 28px;
            font-weight: bold;
            color: #2c3e50;
            text-align: center;
            margin: 25px 0;
            font-style: italic;
            text-decoration: underline;
            text-decoration-color: #667eea;
            text-decoration-thickness: 3px;
            font-family: 'Times New Roman', serif;
        }
        
        .description-text {
            font-size: 14px;
            text-align: center;
            margin: 25px 0;
            font-style: italic;
            line-height: 1.6;
            font-family: 'Times New Roman', serif;
        }
        
        .graduation-date {
            font-size: 18px;
            font-weight: bold;
            color: #2c3e50;
            text-align: center;
            margin: 25px 0;
            font-style: italic;
            font-family: 'Times New Roman', serif;
        }
        
        .signature-section {
            display: flex;
            justify-content: space-between;
            margin-top: 60px;
            width: 100%;
        }
        
        .signature-block {
            text-align: center;
            flex: 1;
            margin: 0 20px;
        }
        
        .signature-line {
            border-bottom: 2px solid #000;
            width: 180px;
            height: 50px;
            margin: 0 auto 15px auto;
            position: relative;
        }
        
        .signature-line::before {
            content: "✍️";
            position: absolute;
            top: -5px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 12px;
        }
        
        .signature-label {
            font-size: 14px;
            font-weight: bold;
            margin: 8px 0;
            font-family: 'Times New Roman', serif;
        }
        
        .signature-subtitle {
            font-size: 11px;
            color: #6c757d;
            margin-top: 5px;
            font-family: 'Times New Roman', serif;
        }
        
        .footer-section {
            position: absolute;
            bottom: 25px;
            right: 25px;
            text-align: right;
        }
        
        .certificate-number {
            font-size: 12px;
            font-weight: bold;
            color: #2c3e50;
            margin: 10px 0;
            font-family: 'Times New Roman', serif;
        }
        
        .qr-code {
            width: 80px;
            height: 80px;
            margin-top: 10px;
            border: 2px solid #dee2e6;
            border-radius: 8px;
        }
        
        .seal {
            position: absolute;
            bottom: 25px;
            left: 25px;
            width: 100px;
            height: 100px;
            border: 6px solid #dc3545;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: radial-gradient(circle, #dc3545 0%, #c82333 50%, #a71e34 100%);
            color: white;
            font-weight: bold;
            font-size: 10px;
            text-align: center;
            box-shadow: 0 6px 12px rgba(0,0,0,0.4);
            position: relative;
        }
        
        .seal::before {
            content: "👑";
            font-size: 20px;
            position: absolute;
            top: 8px;
            color: #fbbf24;
        }
        
        .seal::after {
            content: "OLYMPIA COLLEGE";
            position: absolute;
            bottom: 12px;
            font-size: 8px;
            font-weight: bold;
            color: #fbbf24;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.5);
        }
    </style>
</head>
<body>
    <div class="certificate-container">
        <!-- Header with Logo -->
        <div class="header">
            <div class="logo-section">
                <div class="logo">
                    <div class="crest"></div>
                    <div class="crest-banner"></div>
                </div>
                <div>
                    <h1 class="university-name">OLYMPIACOLLEGE</h1>
                    <p class="university-subtitle">MALAYSIA</p>
                </div>
            </div>
        </div>
        
        <!-- Main Title -->
        <div class="main-title">{{ $exStudent->program ?? 'Bachelor of Information Technology' }}</div>
        
        <!-- Certification Text -->
        <div class="certification-text">This is to certify that</div>
        
        <!-- Student Name -->
        <div class="student-name">{{ $exStudent->name }}</div>
        
        <!-- Award Text -->
        <div class="award-text">has been awarded the</div>
        
        <!-- Course Name -->
        <div class="course-name">{{ $exStudent->program ?? 'Bachelor of Information Technology' }}</div>
        
        <!-- Description -->
        <div class="description-text">
            having fulfilled the requirements prescribed by the Academic Board,<br>
            and with the assent of the Examination Board.<br>
            Witness our hand and seal this
        </div>
        
        <!-- Graduation Date -->
        <div class="graduation-date">{{ $exStudent->graduation_date ?? 'August 2023' }}</div>
        
        <!-- Signature Section -->
        <div class="signature-section">
            <div class="signature-block">
                <div class="signature-line"></div>
                <div class="signature-label">Director</div>
                <div class="signature-subtitle">Olympia College</div>
            </div>
            
            <div class="signature-block">
                <div class="signature-line"></div>
                <div class="signature-label">Chairman</div>
                <div class="signature-subtitle">Olympia Academic Board</div>
            </div>
            
            <div class="signature-block">
                <div class="signature-line"></div>
                <div class="signature-label">Registrar</div>
                <div class="signature-subtitle">Olympia Examination Board</div>
            </div>
        </div>
        
        <!-- Footer with Certificate Number and QR Code -->
        <div class="footer-section">
            <div class="certificate-number">
                {{ $exStudent->certificate_number ?? 'CERT-20251006-0003' }}
            </div>
            @if(isset($qrCodePath) && file_exists($qrCodePath))
                <img src="data:image/png;base64,{{ base64_encode(file_get_contents($qrCodePath)) }}" alt="QR Code" class="qr-code">
            @else
                <div class="qr-code" style="background: #f0f0f0; display: flex; align-items: center; justify-content: center; color: #666; font-size: 10px;">QR Code</div>
            @endif
        </div>
        
        <!-- Official Seal -->
        <div class="seal"></div>
    </div>
</body>
</html>
