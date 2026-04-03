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
            background-color: #f59e0b;
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
            background-color: #f59e0b;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 15px;
        }
        .button:hover {
            background-color: #d97706;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>New Payment Proof Submitted</h1>
            <p>Manual transfer proof requires review</p>
        </div>

        <div class="content">
            <p>Hi Admin,</p>

            <p>A new payment proof has been submitted by an owner and requires verification.</p>

            <h3>Owner Details</h3>
            <div class="details">
                <div class="detail-row">
                    <span class="detail-label">Owner Name:</span>
                    <span class="detail-value">{{ $user->nama }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Owner Email:</span>
                    <span class="detail-value">{{ $user->email }}</span>
                </div>
            </div>

            <h3>Payment Details</h3>
            <div class="details">
                <div class="detail-row">
                    <span class="detail-label">Package Name:</span>
                    <span class="detail-value">{{ $package->nama ?? 'N/A' }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Amount:</span>
                    <span class="detail-value">Rp {{ number_format($pembayaran->nominal, 0, ',', '.') }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Bank:</span>
                    <span class="detail-value">{{ $bank->nama_bank }} - {{ $bank->nomor_rekening }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Account Name:</span>
                    <span class="detail-value">{{ $bank->nama_rekening }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Submitted At:</span>
                    <span class="detail-value">{{ $pembayaran->created_at ? \Carbon\Carbon::parse($pembayaran->created_at)->format('d F Y H:i') : '-' }}</span>
                </div>
            </div>

            <p>Click the button below to review the proof of payment:</p>
            <a href="{{ $proofUrl }}" class="button">View Proof</a>

            <p style="margin-top: 20px; color: #666; font-size: 14px;">
                Please verify the payment in the admin panel and approve it once confirmed.
            </p>
        </div>

        <div class="footer">
            <p>&copy; {{ now()->year }} POS Phone. All rights reserved.</p>
            <p>This is an automated email, please do not reply to this address.</p>
        </div>
    </div>
</body>
</html>
