<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f9f9f9;
        }
        .header {
            background-color: #4318FF;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            background-color: white;
            padding: 20px;
            margin-top: -1px;
        }
        .footer {
            background-color: #f0f0f0;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #666;
            border-radius: 0 0 5px 5px;
            margin-top: -1px;
        }
        .success-badge {
            display: inline-block;
            background-color: #10b981;
            color: white;
            padding: 8px 16px;
            border-radius: 5px;
            margin: 10px 0;
            font-weight: bold;
        }
        .details {
            background-color: #f5f5f5;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #ddd;
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .detail-label {
            font-weight: bold;
            color: #555;
        }
        .detail-value {
            color: #333;
        }
        .button {
            display: inline-block;
            background-color: #4318FF;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 15px;
        }
        .button:hover {
            background-color: #3811d9;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Payment Approved!</h1>
            <p>Your subscription payment has been verified</p>
        </div>

        <div class="content">
            <p>Hi {{ $owner->nama }},</p>

            <p>Great news! Your payment has been reviewed and approved by our admin team. Your subscription is now active and you can start using all features.</p>

            <div class="success-badge">✓ Subscription Activated</div>

            <h3>Payment Details</h3>
            <div class="details">
                <div class="detail-row">
                    <span class="detail-label">Package Name:</span>
                    <span class="detail-value">{{ $package->nama ?? 'N/A' }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Amount Paid:</span>
                    <span class="detail-value">Rp {{ number_format($pembayaran->nominal, 0, ',', '.') }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Payment Method:</span>
                    <span class="detail-value">{{ ucfirst($pembayaran->metode_pembayaran) }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Payment Status:</span>
                    <span class="detail-value" style="color: #10b981; font-weight: bold;">Paid</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Approved Date:</span>
                    <span class="detail-value">{{ now()->format('d F Y H:i') }}</span>
                </div>
            </div>

            <h3>What's Next?</h3>
            <p>You can now log in to your dashboard and start using all features. If you have any questions or need support, don't hesitate to contact us.</p>

            <a href="{{ route('dashboard') }}" class="button">Go to Dashboard</a>

            <p style="margin-top: 30px; color: #666; font-size: 14px;">
                <strong>Thank you for your business!</strong><br>
                We appreciate your subscription and look forward to serving you.
            </p>
        </div>

        <div class="footer">
            <p>&copy; {{ now()->year }} POS Phone. All rights reserved.</p>
            <p>This is an automated email, please do not reply to this address.</p>
        </div>
    </div>
</body>
</html>
