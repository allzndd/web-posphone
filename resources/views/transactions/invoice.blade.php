<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $transaction->invoice_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            padding: 40px;
            background: #f5f5f5;
        }

        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 40px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 40px;
            padding-bottom: 20px;
            border-bottom: 3px solid #0088cc;
        }

        .company-logo {
            flex: 1;
        }

        .logo-box {
            width: 120px;
            height: 120px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 10px;
        }

        .logo-box img {
            width: 120px;
            height: auto;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #333;
            margin-bottom: 5px;
        }

        .invoice-title {
            text-align: right;
            flex: 1;
        }

        .invoice-title h1 {
            font-size: 36px;
            color: #333;
            margin-bottom: 10px;
        }

        .company-info {
            font-size: 12px;
            color: #666;
            line-height: 1.6;
            max-width: 300px;
        }

        .invoice-details {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }

        .bill-to {
            flex: 1;
        }

        .bill-to h3 {
            font-size: 14px;
            color: #0088cc;
            margin-bottom: 10px;
            text-transform: uppercase;
        }

        .bill-to-info {
            font-size: 13px;
            color: #333;
            line-height: 1.6;
        }

        .bill-to-info strong {
            display: block;
            font-size: 15px;
            margin-bottom: 5px;
        }

        .invoice-meta {
            text-align: right;
            flex: 1;
        }

        .invoice-meta table {
            margin-left: auto;
            font-size: 13px;
        }

        .invoice-meta td {
            padding: 5px 10px;
        }

        .invoice-meta td:first-child {
            font-weight: bold;
            color: #666;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        .items-table thead {
            background: #f8f9fa;
        }

        .items-table th {
            text-align: left;
            padding: 12px;
            font-size: 12px;
            color: #666;
            text-transform: uppercase;
            border-bottom: 2px solid #dee2e6;
        }

        .items-table th:last-child,
        .items-table td:last-child {
            text-align: right;
        }

        .items-table th:nth-child(2),
        .items-table td:nth-child(2) {
            text-align: center;
        }

        .items-table th:nth-child(3),
        .items-table td:nth-child(3) {
            text-align: right;
        }

        .items-table tbody tr {
            border-bottom: 1px solid #dee2e6;
        }

        .items-table td {
            padding: 15px 12px;
            font-size: 14px;
            color: #333;
        }

        .item-name {
            font-weight: 500;
        }

        .totals {
            margin-left: auto;
            width: 300px;
        }

        .totals table {
            width: 100%;
            font-size: 14px;
        }

        .totals td {
            padding: 8px 0;
        }

        .totals td:first-child {
            text-align: left;
            color: #666;
        }

        .totals td:last-child {
            text-align: right;
            font-weight: 500;
        }

        .totals .subtotal-row {
            border-top: 1px solid #dee2e6;
            padding-top: 10px;
        }

        .totals .total-row {
            border-top: 2px solid #333;
            padding-top: 10px;
        }

        .totals .total-row td {
            font-size: 18px;
            font-weight: bold;
            color: #333;
            padding-top: 15px;
        }

        .payment-instructions {
            margin-top: 40px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 5px;
            font-size: 12px;
            color: #666;
            line-height: 1.6;
        }

        .payment-instructions h4 {
            font-size: 13px;
            color: #333;
            margin-bottom: 10px;
            text-transform: uppercase;
        }

        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
            text-align: center;
            font-size: 11px;
            color: #999;
        }

        @media print {
            body {
                padding: 0;
                background: white;
            }

            .invoice-container {
                box-shadow: none;
                padding: 20px;
            }

            .footer {
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <!-- Header -->
        <div class="header">
            <div class="company-logo">
                <div class="logo-box">
                    <img src="{{ asset('img/logo-miphone.jpg') }}" alt="MIPHONE GROUP">
                </div>
                <div class="company-name">MIPHONE GROUP</div>
                <div class="company-info">
                    Jl harapan mulya II<br>
                    jakarta pusat DKI JAKARTA<br>
                    ID<br>
                    +62 851-2107-6192<br>
                    miphonegroup@gmail.com
                </div>
            </div>
            <div class="invoice-title">
                <h1>Invoice</h1>
            </div>
        </div>

        <!-- Invoice Details -->
        <div class="invoice-details">
            <div class="bill-to">
                <h3>Bill To</h3>
                <div class="bill-to-info">
                    <strong>{{ $transaction->customer->name ?? 'Umum' }}</strong>
                    @if($transaction->customer && $transaction->customer->phone)
                        {{ $transaction->customer->phone }}
                    @endif
                </div>
            </div>
            <div class="invoice-meta">
                <table>
                    <tr>
                        <td>Invoice #</td>
                        <td>{{ $transaction->invoice_number }}</td>
                    </tr>
                    <tr>
                        <td>Date</td>
                        <td>{{ $transaction->date->format('d M Y') }}</td>
                    </tr>
                    @if($transaction->warranty_expires_at)
                    <tr>
                        <td>Warranty</td>
                        <td>
                            @php
                                $isExpired = now()->isAfter($transaction->warranty_expires_at);
                            @endphp
                            @if($isExpired)
                                <span style="color: #fc544b; font-weight: bold;">EXPIRED</span>
                            @else
                                <span style="color: #47c363; font-weight: bold;">Until {{ $transaction->warranty_expires_at->format('d M Y') }}</span>
                            @endif
                        </td>
                    </tr>
                    @endif
                </table>
            </div>
        </div>

        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transaction->items as $item)
                <tr>
                    <td class="item-name">
                        @if($item->type === 'product' && $item->product)
                            {{ $item->product->name }}
                            @if($item->product->storage)
                                {{ $item->product->storage }}
                            @endif
                        @else
                            {{ $item->service_name ?? 'Item' }}
                        @endif
                    </td>
                    <td>{{ $item->quantity }}</td>
                    <td>Rp{{ number_format($item->price_per_item, 0, ',', ',') }}</td>
                    <td>Rp{{ number_format($item->subtotal, 0, ',', ',') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Totals -->
        <div class="totals">
            <table>
                <tr class="subtotal-row">
                    <td>Subtotal</td>
                    <td>Rp{{ number_format($transaction->items->sum('subtotal'), 0, ',', ',') }}</td>
                </tr>
                @if($transaction->delivery_cost && $transaction->delivery_cost > 0)
                <tr>
                    <td>Delivery Cost</td>
                    <td>Rp{{ number_format($transaction->delivery_cost, 0, ',', ',') }}</td>
                </tr>
                @endif
                @if($transaction->tax_cost && $transaction->tax_cost > 0)
                <tr>
                    <td>Tax</td>
                    <td>Rp{{ number_format($transaction->tax_cost, 0, ',', ',') }}</td>
                </tr>
                @endif
                <tr class="total-row">
                    <td>Total</td>
                    <td>Rp{{ number_format($transaction->total_price, 0, ',', ',') }}</td>
                </tr>
            </table>
        </div>

        <!-- Payment Instructions -->
        @if($transaction->notes || $transaction->payment->method === 'transfer')
        <div class="payment-instructions">
            <h4>Payment Instructions</h4>
            @if($transaction->payment->method === 'transfer')
                Pembayaran wajib melalui nomor rekening<br>
                8705575009 BCA /Arimatil rab Muslaad
            @endif
            @if($transaction->notes)
                <br><br>{{ $transaction->notes }}
            @endif
        </div>
        @endif

        <!-- Footer -->
        <div class="footer">
            Generated by MIPHONE GROUP Invoice System
        </div>
    </div>

    <script>
        // Auto print when page loads
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>
