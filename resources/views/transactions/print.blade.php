<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction Receipt #{{ $transaction->invoice_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html, body {
            height: auto;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Courier New', monospace;
            font-size: 11px;
            line-height: 1.3;
            background: #f5f5f5;
        }

        .receipt {
            width: 72mm; /* printable width */
            max-width: 72mm;
            background: white;
            padding: 10px;
            margin: 20px auto;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        .header {
            text-align: center;
            margin-bottom: 8px;
            padding-bottom: 5px;
            border-bottom: 2px dashed #000;
        }

        .header h1 {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 3px;
        }

        .header p {
            font-size: 10px;
            margin: 1px 0;
        }

        .info-section {
            margin-bottom: 8px;
            padding-bottom: 5px;
            border-bottom: 1px dashed #000;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            margin: 2px 0;
        }

        .info-label {
            font-weight: bold;
        }

        .items-table {
            width: 100%;
            margin-bottom: 8px;
            border-bottom: 1px dashed #000;
            padding-bottom: 5px;
        }

        .item-row {
            margin: 5px 0;
        }

        .item-name {
            font-weight: bold;
        }

        .item-details {
            display: flex;
            justify-content: space-between;
            margin-top: 1px;
            font-size: 10px;
        }

        .totals {
            margin-bottom: 8px;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            margin: 3px 0;
        }

        .total-row.grand {
            font-weight: bold;
            font-size: 13px;
            margin-top: 5px;
            padding-top: 5px;
            border-top: 2px solid #000;
        }

        .footer {
            text-align: center;
            margin-top: 8px;
            padding-top: 5px;
            border-top: 2px dashed #000;
        }

        .footer p {
            margin: 2px 0;
            font-size: 10px;
        }

        @media print {
            body {
                background: white;
                margin: 0;
                padding: 0;
            }

            .receipt {
                width: 72mm;
                max-width: 72mm;
                margin: 0;
                padding: 5px;
                box-shadow: none;
            }

            @page {
                margin: 0;
                size: 72mm 210mm; /* 72mm width x 210mm height */
            }

            /* avoid breaking key blocks across pages */
            .item-row,
            .info-section,
            .totals,
            .footer {
                page-break-inside: avoid;
                break-inside: avoid;
            }
        }

        @media screen {
            .receipt {
                margin: 20px auto;
            }
        }
    </style>
</head>
<body>
    <div class="receipt">
        <!-- Header -->
        <div class="header">
            <h1>TOKO HANDPHONE</h1>
            <p>Jl. Contoh No. 123</p>
            <p>Telp: (021) 12345678</p>
        </div>

        <!-- Transaction Info -->
        <div class="info-section">
            <div class="info-row">
                <span class="info-label">No Invoice:</span>
                <span>{{ $transaction->invoice_number }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Tanggal:</span>
                <span>{{ $transaction->date->format('d/m/Y H:i') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Kasir:</span>
                <span>{{ $transaction->cashier ? $transaction->cashier->name : '-' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Customer:</span>
                <span>{{ $transaction->customer->name }}</span>
            </div>
        </div>

        <!-- Items -->
        <div class="items-table">
            @php($__srIndex = 0)
            @foreach($transaction->items as $item)
                <div class="item-row">
                    <div class="item-name">
                        @if($item->type === 'product' && $item->product)
                            {{ $item->product->name }}
                        @else
                            Unknown Item
                        @endif
                    </div>
                    <div class="item-details">
                        <span>{{ $item->quantity }} x Rp {{ number_format($item->price_per_item, 0, ',', '.') }}</span>
                        <span>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                    </div>
                    @if($item->discount > 0)
                        <div class="item-details">
                            <span>Diskon Item</span>
                            <span>- Rp {{ number_format($item->discount, 0, ',', '.') }}</span>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        <!-- Totals -->
        <div class="totals">
            <div class="total-row">
                <span>Subtotal:</span>
                <span>Rp {{ number_format($transaction->items->sum('subtotal'), 0, ',', '.') }}</span>
            </div>

            @if($transaction->delivery_cost > 0)
                <div class="total-row">
                    <span>Ongkir:</span>
                    <span>Rp {{ number_format($transaction->delivery_cost, 0, ',', '.') }}</span>
                </div>
            @endif

            @if($transaction->tax_cost > 0)
                <div class="total-row">
                    <span>Pajak:</span>
                    <span>Rp {{ number_format($transaction->tax_cost, 0, ',', '.') }}</span>
                </div>
            @endif

            <div class="total-row grand">
                <span>TOTAL:</span>
                <span>Rp {{ number_format($transaction->total_price, 0, ',', '.') }}</span>
            </div>

            @if($transaction->payment)
            <div class="total-row">
                <span>Metode Bayar:</span>
                <span>{{ ucfirst(str_replace('_', ' ', $transaction->payment->method)) }}</span>
            </div>
            <div class="total-row">
                <span>Status:</span>
                <span>{{ $transaction->payment->status === 'paid' ? 'LUNAS' : 'BELUM LUNAS' }}</span>
            </div>
            @endif
        </div>

        <!-- Notes -->
        @if($transaction->notes)
            <div class="info-section">
                <div class="info-row">
                    <span class="info-label">Catatan:</span>
                </div>
                <div style="margin-top: 5px;">
                    {{ $transaction->notes }}
                </div>
            </div>
        @endif

        <!-- Footer -->
        <div class="footer">
            <p>*** TERIMA KASIH ***</p>
            <p>Barang yang sudah dibeli tidak dapat dikembalikan</p>
            <p>{{ now()->format('d/m/Y H:i:s') }}</p>
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
