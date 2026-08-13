<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Welcome to Trendora</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: #f8f9fa;
            margin: 0;
            padding: 40px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: #fff;
            border-radius: 16px;
            padding: 40px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
            text-align: center;
        }
        .header {
            margin-bottom: 30px;
        }
        .header h1 {
            color: #667eea;
            font-size: 32px;
            margin: 0;
        }
        .btn {
            display: inline-block;
            padding: 12px 40px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: #fff;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            margin: 20px 0;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #f1f3f5;
            color: #b2bec3;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🛍️ Trendora</h1>
        </div>

        <h2>Welcome to Trendora, {{ $user->name }}! 🎉</h2>
        
        <p>We're excited to have you on board. You can now start shopping and discover amazing products.</p>

        <a href="{{ url('/') }}" class="btn">Start Shopping</a>

        <div class="footer">
            <p>Need help? Contact us at support@trendora.com</p>
            <p style="font-size: 12px;">© {{ date('Y') }} Trendora. All rights reserved.</p>
        </div>
    </div>
</body>
</html>