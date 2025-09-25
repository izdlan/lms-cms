<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Under Maintenance | Olympia Education</title>
    <link rel="shortcut icon" type="image/x-icon" href="/store/1/favicon.png">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #333;
        }

        .maintenance-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            padding: 60px 40px;
            text-align: center;
            max-width: 500px;
            width: 90%;
            position: relative;
            overflow: hidden;
        }

        .maintenance-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #20c997, #17a2b8, #6f42c1);
        }

        .icon-container {
            width: 120px;
            height: 120px;
            background: linear-gradient(135deg, #e3f2fd, #bbdefb);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
            position: relative;
            animation: pulse 2s infinite;
        }

        .icon-container::before {
            content: '';
            position: absolute;
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, #20c997, #17a2b8);
            border-radius: 50%;
            opacity: 0.1;
            animation: ripple 2s infinite;
        }

        .maintenance-icon {
            font-size: 48px;
            color: #20c997;
            z-index: 1;
            position: relative;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        @keyframes ripple {
            0% { transform: scale(0.8); opacity: 0.1; }
            50% { transform: scale(1.2); opacity: 0.05; }
            100% { transform: scale(1.4); opacity: 0; }
        }

        h1 {
            font-size: 2.5rem;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 20px;
            line-height: 1.2;
        }

        .subtitle {
            font-size: 1.1rem;
            color: #7f8c8d;
            margin-bottom: 30px;
            line-height: 1.6;
        }

        .description {
            font-size: 1rem;
            color: #95a5a6;
            margin-bottom: 40px;
            line-height: 1.6;
        }

        .progress-container {
            background: #ecf0f1;
            border-radius: 10px;
            height: 8px;
            margin: 30px 0;
            overflow: hidden;
        }

        .progress-bar {
            background: linear-gradient(90deg, #20c997, #17a2b8);
            height: 100%;
            width: 65%;
            border-radius: 10px;
            animation: progress 3s ease-in-out infinite;
        }

        @keyframes progress {
            0% { width: 0%; }
            50% { width: 65%; }
            100% { width: 0%; }
        }

        .contact-info {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 20px;
            margin-top: 30px;
            border-left: 4px solid #20c997;
        }

        .contact-info h3 {
            color: #2c3e50;
            font-size: 1.1rem;
            margin-bottom: 10px;
        }

        .contact-info p {
            color: #7f8c8d;
            font-size: 0.9rem;
            margin-bottom: 5px;
        }

        .contact-info a {
            color: #20c997;
            text-decoration: none;
            font-weight: 600;
        }

        .contact-info a:hover {
            text-decoration: underline;
        }

        .logo {
            position: absolute;
            top: 20px;
            left: 20px;
            font-size: 1.2rem;
            font-weight: 700;
            color: #20c997;
        }

        @media (max-width: 768px) {
            .maintenance-container {
                padding: 40px 20px;
                margin: 20px;
            }

            h1 {
                font-size: 2rem;
            }

            .icon-container {
                width: 100px;
                height: 100px;
            }

            .maintenance-icon {
                font-size: 40px;
            }
        }

        .floating-elements {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            pointer-events: none;
        }

        .floating-elements::before,
        .floating-elements::after {
            content: '';
            position: absolute;
            width: 20px;
            height: 20px;
            background: rgba(32, 201, 151, 0.1);
            border-radius: 50%;
            animation: float 6s ease-in-out infinite;
        }

        .floating-elements::before {
            top: 20%;
            left: 10%;
            animation-delay: 0s;
        }

        .floating-elements::after {
            top: 60%;
            right: 10%;
            animation-delay: 3s;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
        }
    </style>
</head>
<body>
    <div class="maintenance-container">
        <div class="floating-elements"></div>
        
        <div class="icon-container">
            <i class="maintenance-icon">🔧</i>
        </div>

        <h1>We'll Be Back Soon</h1>
        <p class="subtitle">Olympia Education</p>
        <p class="description">
            We're performing some scheduled maintenance to improve your experience. 
            Our team is working hard to get everything back online as quickly as possible.
        </p>

        <div class="progress-container">
            <div class="progress-bar"></div>
        </div>

        <div class="contact-info">
            <h3>Need Immediate Assistance?</h3>
            <p>Email: <a href="mailto:support@olympia-education.com">support@olympia-education.com</a></p>
            <p>Phone: <a href="tel:+60123456789">+60 12-345 6789</a></p>
            <p>Expected completion: Within 2-4 hours</p>
        </div>
    </div>

    <script>
        // Auto-refresh every 30 seconds to check if maintenance is complete
        setTimeout(function() {
            window.location.reload();
        }, 30000);

        // Add some interactive elements
        document.addEventListener('DOMContentLoaded', function() {
            const icon = document.querySelector('.maintenance-icon');
            
            icon.addEventListener('click', function() {
                this.style.transform = 'rotate(360deg)';
                this.style.transition = 'transform 0.5s ease';
                
                setTimeout(() => {
                    this.style.transform = 'rotate(0deg)';
                }, 500);
            });
        });
    </script>
</body>
</html>
