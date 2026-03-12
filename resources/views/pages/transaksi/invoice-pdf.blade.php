<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Invoice {{ $transaksi->invoice }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.4;
            width: 100%;
        }

        .invoice-container {
            width: 515px;
            margin: 20px auto;
        }

        /* Header Title */
        .invoice-title {
            text-align: center;
            font-size: 22px;
            font-weight: bold;
            letter-spacing: 3px;
            padding: 10px 0;
            border-top: 3px solid #000;
            border-bottom: 3px solid #000;
            margin-bottom: 20px;
            text-transform: uppercase;
        }

        /* Info Table */
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        .info-table td {
            padding: 5px 8px;
            vertical-align: top;
            font-size: 11px;
        }

        .info-table .label-col {
            width: 60px;
            font-weight: bold;
        }

        .info-table .company-col {
            font-weight: bold;
        }

        .info-table .header-row td {
            font-weight: bold;
            border-bottom: 1px solid #000;
            padding-bottom: 4px;
            font-size: 11px;
        }

        .info-table .data-row td {
            border-bottom: 1px solid #ccc;
        }

        /* Invoice Meta */
        .invoice-meta {
            margin-bottom: 5px;
            font-size: 11px;
        }

        .invoice-meta table {
            width: 100%;
        }

        .invoice-meta td {
            padding: 2px 0;
        }

        .invoice-meta .label {
            width: 100px;
            font-weight: normal;
            text-transform: uppercase;
        }

        .page-info {
            text-align: right;
            font-size: 11px;
            margin-bottom: 5px;
        }

        /* Items Table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        .items-table thead th {
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
            padding: 6px 8px;
            text-align: left;
            font-size: 11px;
            font-weight: bold;
        }

        .items-table thead th.text-center {
            text-align: center;
        }

        .items-table thead th.text-right {
            text-align: right;
        }

        .items-table tbody td {
            padding: 6px 8px;
            font-size: 11px;
            vertical-align: top;
        }

        .items-table tbody td.text-center {
            text-align: center;
        }

        .items-table tbody td.text-right {
            text-align: right;
        }

        .items-table tbody tr:last-child td {
            border-bottom: 1px solid #000;
        }

        .item-imei {
            font-size: 10px;
            color: #666;
            margin-top: 2px;
        }

        /* Totals */
        .totals-outer {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .totals-table {
            width: 300px;
            border-collapse: collapse;
        }

        .totals-table td {
            padding: 4px 8px;
            font-size: 11px;
        }

        .totals-table .label {
            text-align: right;
            font-weight: bold;
            width: 140px;
        }

        .totals-table .value {
            text-align: right;
            width: 160px;
        }

        .totals-table .grand-total td {
            border-top: 1px solid #000;
            font-weight: bold;
            font-size: 12px;
            padding-top: 6px;
        }

        /* Payment Method */
        .payment-method {
            margin-top: 20px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
        }

        /* Footer */
        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 12px;
        }

        .signature-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 50px;
        }

        .signature-cell {
            text-align: right;
            font-size: 11px;
            padding-top: 5px;
            border-top: 1px solid #000;
            width: 200px;
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <!-- Title -->
        <div class="invoice-title">{{ strtoupper($transaksi->toko ? $transaksi->toko->nama . ' INVOICE' : 'PROFORMA INVOICE') }}</div>

        <!-- Company Info -->
        <table class="info-table">
            <thead>
                <tr class="header-row">
                    <td style="width: 60px;"></td>
                    <td>Company Name</td>
                    <td>Address</td>
                </tr>
            </thead>
            <tbody>
                <tr class="data-row">
                    <td class="label-col">FROM:</td>
                    <td class="company-col">{{ $transaksi->toko ? $transaksi->toko->nama : '-' }}</td>
                    <td>{{ $transaksi->toko && $transaksi->toko->alamat ? $transaksi->toko->alamat : '-' }}</td>
                </tr>
                <tr class="data-row">
                    <td class="label-col">TO:</td>
                    <td colspan="2">
                        @if($transaksi->is_transaksi_masuk)
                            {{ $transaksi->pelanggan ? $transaksi->pelanggan->nama : '-' }}
                        @else
                            {{ $transaksi->supplier ? $transaksi->supplier->nama : '-' }}
                        @endif
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- Invoice Meta -->
        <div class="invoice-meta">
            <table>
                <tr>
                    <td class="label">INVOICE NO:</td>
                    <td>{{ $transaksi->invoice }}</td>
                </tr>
                <tr>
                    <td class="label">DATE:</td>
                    <td>{{ $transaksi->created_at->format('d/m/Y') }}</td>
                </tr>
                <tr>
                    <td class="label">TERM:</td>
                    <td>{{ $transaksi->payment_terms ?? '-' }}</td>
                </tr>
            </table>
        </div>

        <!-- Page Info -->
        <div class="page-info">Page: 1 Of 1</div>

        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th>Item Description</th>
                    <th class="text-center" style="width: 50px;">Qty</th>
                    <th class="text-right" style="width: 120px;">Unit Price</th>
                    <th class="text-right" style="width: 140px;">Total Price ({{ get_currency_symbol() }})</th>
                </tr>
            </thead>
            <tbody>
                @php $subtotal = 0; $totalDiskon = 0; @endphp
                @foreach($transaksi->items as $item)
                @php
                    $itemName = '';
                    $itemImei = '';

                    // Build item description
                    if ($item->product_name || $item->produk) {
                        $parts = [];
                        
                        // Product type label
                        $productType = $item->product_type ?? ($item->produk ? $item->produk->product_type : '');
                        if ($productType === 'electronic') {
                            $parts[] = 'USED';
                        }
                        
                        // Merk/Brand name
                        $merkName = $item->merk_name ?? ($item->produk && $item->produk->merk ? $item->produk->merk->nama : '');
                        if ($merkName) $parts[] = strtoupper($merkName);
                        
                        // Product name
                        $prodName = $item->product_name ?? ($item->produk ? $item->produk->nama : '');
                        if ($prodName) $parts[] = strtoupper($prodName);
                        
                        // Storage
                        $storage = $item->penyimpanan ?? ($item->produk && $item->produk->penyimpanan ? $item->produk->penyimpanan->penyimpanan : '');
                        if ($storage) $parts[] = $storage;
                        
                        // Color
                        $warna = $item->warna ?? ($item->produk && $item->produk->warna ? $item->produk->warna->warna : '');
                        if ($warna) $parts[] = strtoupper($warna);
                        
                        // RAM
                        $ram = $item->ram ?? ($item->produk && $item->produk->ram ? $item->produk->ram->ram : '');
                        
                        if ($productType === 'electronic') {
                            $itemName = 'MODEL : ' . implode(' ', $parts);
                        } elseif ($productType === 'accessory' || $productType === 'accessories') {
                            $itemName = implode(' ', $parts);
                        } else {
                            $itemName = implode(' ', $parts);
                        }
                        
                        // IMEI
                        $itemImei = $item->imei ?? ($item->produk ? $item->produk->imei : '');
                    } elseif ($item->service) {
                        $itemName = $item->service->nama . ' (Service)';
                    } else {
                        $itemName = 'Item Tidak Diketahui';
                    }

                    $lineTotal = $item->subtotal;
                    $subtotal += $lineTotal;
                    $totalDiskon += ($item->diskon ?? 0);
                @endphp
                <tr>
                    <td>
                        {{ $itemName }}
                        @if($itemImei)
                            <div class="item-imei">IMEI : {{ $itemImei }}</div>
                        @endif
                    </td>
                    <td class="text-center">{{ $item->quantity }}</td>
                    <td class="text-right">{{ number_format($item->harga_satuan, 2, '.', ',') }}</td>
                    <td class="text-right">{{ number_format($lineTotal, 2, '.', ',') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Totals -->
        <table class="totals-outer">
            <tr>
                <td style="width: 215px;"></td>
                <td style="text-align: right;">
                    <table class="totals-table">
                        <tr>
                            <td class="label">Sum Total :</td>
                            <td class="value">{{ number_format($subtotal, 2, '.', ',') }}</td>
                        </tr>
                        <tr>
                            <td class="label">Paid:</td>
                            <td class="value">{{ $transaksi->paid_amount ? number_format($transaksi->paid_amount, 2, '.', ',') : '' }}</td>
                        </tr>
                        <tr>
                            <td class="label">Discount:</td>
                            <td class="value">{{ $totalDiskon > 0 ? number_format($totalDiskon, 2, '.', ',') : '' }}</td>
                        </tr>
                        <tr class="grand-total">
                            <td class="label">Balance Sum:</td>
                            <td class="value">{{ number_format($transaksi->total_harga, 2, '.', ',') }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <!-- Payment Method -->
        <div class="payment-method">
            PAYMENT METHOD : {{ strtoupper(str_replace('-', ' ', $transaksi->metode_pembayaran ?? '-')) }}
        </div>

        @if($transaksi->keterangan)
        <div style="margin-top: 10px; font-size: 11px;">
            <strong>Note:</strong> {{ $transaksi->keterangan }}
        </div>
        @endif

        <!-- Thank You -->
        <div class="footer">Thank you !</div>

        <!-- Signature -->
        <table class="signature-table">
            <tr>
                <td></td>
                <td class="signature-cell">Authorized Signature</td>
            </tr>
        </table>
    </div>
</body>
</html>
