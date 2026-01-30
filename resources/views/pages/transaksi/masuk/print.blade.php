<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nota Penjualan #{{ $transaksi->invoice }}</title>
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
            width: 72mm;
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

        .type-badge {
            display: inline-block;
            background: #16a34a;
            color: white;
            padding: 2px 8px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
            margin-top: 5px;
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
                size: 72mm 210mm;
            }

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
            <h1>{{ $transaksi->toko ? $transaksi->toko->nama : 'TOKO' }}</h1>
            <p>{{ $transaksi->toko && $transaksi->toko->alamat ? $transaksi->toko->alamat : 'Alamat Toko' }}</p>
            <p>Telp: {{ $transaksi->toko && $transaksi->toko->no_telp ? $transaksi->toko->no_telp : '-' }}</p>
            <div class="type-badge">PENJUALAN</div>
        </div>

        <!-- Transaction Info -->
        <div class="info-section">
            <div class="info-row">
                <span class="info-label">No Invoice:</span>
                <span>{{ $transaksi->invoice }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Tanggal:</span>
                <span>{{ $transaksi->created_at->format('d/m/Y H:i') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Toko:</span>
                <span>{{ $transaksi->toko ? $transaksi->toko->nama : '-' }}</span>
            </div>
            @if($transaksi->pelanggan)
            <div class="info-row">
                <span class="info-label">Pelanggan:</span>
                <span>{{ $transaksi->pelanggan->nama }}</span>
            </div>
            @endif
            <div class="info-row">
                <span class="info-label">Status:</span>
                <span>{{ strtoupper($transaksi->status) }}</span>
            </div>
        </div>

        <!-- Items -->
        <div class="items-table">
            @foreach($transaksi->items as $item)
                <div class="item-row">
                    <div class="item-name">
                        @if($item->produk)
                            {{ $item->produk->nama }}
                            @if($item->produk->merk)
                                - {{ $item->produk->merk->nama }}
                            @endif
                        @elseif($item->service)
                            {{ $item->service->nama }} (Service)
                        @else
                            Item Tidak Diketahui
                        @endif
                    </div>
                    <div class="item-details">
                        <span>{{ $item->quantity }} x {{ get_currency_symbol() }} {{ number_format($item->harga_satuan, get_decimal_places()) }}</span>
                        <span>{{ get_currency_symbol() }} {{ number_format($item->subtotal, get_decimal_places()) }}</span>
                    </div>
                    @if($item->diskon > 0)
                        <div class="item-details">
                            <span>Diskon</span>
                            <span>- {{ get_currency_symbol() }} {{ number_format($item->diskon, get_decimal_places()) }}</span>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        <!-- Totals -->
        <div class="totals">
            <div class="total-row">
                <span>Subtotal:</span>
                <span>{{ get_currency_symbol() }} {{ number_format($transaksi->items->sum('subtotal'), get_decimal_places()) }}</span>
            </div>

            <div class="total-row grand">
                <span>TOTAL:</span>
                <span>{{ get_currency_symbol() }} {{ number_format($transaksi->total_harga, get_decimal_places()) }}</span>
            </div>

            <div class="total-row">
                <span>Metode Bayar:</span>
                <span>{{ strtoupper(str_replace('-', ' ', $transaksi->metode_pembayaran)) }}</span>
            </div>
        </div>

        <!-- Notes -->
        @if($transaksi->keterangan)
            <div class="info-section">
                <div class="info-row">
                    <span class="info-label">Catatan:</span>
                </div>
                <div style="margin-top: 5px;">
                    {{ $transaksi->keterangan }}
                </div>
            </div>
        @endif

        <!-- Footer -->
        <div class="footer">
            <p>*** TERIMA KASIH ***</p>
            <p>Nota Penjualan / Sales Receipt</p>
            <p>Dicetak: {{ now()->format('d/m/Y H:i:s') }}</p>
        </div>
    </div>

    <script>
        // Set success message ke sessionStorage sebelum print
        sessionStorage.setItem('transaksi_success_message', 'Incoming transaction has been successfully created');
        
        let printDialogClosed = false;
        
        // Auto print saat halaman dibuka
        window.onload = function() {
            // Print langsung
            window.print();
        };

        // Event setelah print dialog ditutup (baik print maupun cancel)
        window.onafterprint = function() {
            if (!printDialogClosed) {
                printDialogClosed = true;
                // Redirect ke index
                window.location.replace('{{ route("transaksi.masuk.index") }}');
            }
        };

        // Backup: jika onafterprint tidak trigger, gunakan detection lain
        // Beberapa browser tidak support onafterprint dengan baik
        if (!window.matchMedia) {
            // Fallback untuk browser lama
            setTimeout(function() {
                if (!printDialogClosed) {
                    printDialogClosed = true;
                    window.location.replace('{{ route("transaksi.masuk.index") }}');
                }
            }, 1000);
        } else {
            // Detection untuk browser modern
            const mediaQueryList = window.matchMedia('print');
            
            mediaQueryList.addListener(function(mql) {
                if (!mql.matches && !printDialogClosed) {
                    // User keluar dari print mode (print selesai atau cancel)
                    printDialogClosed = true;
                    setTimeout(function() {
                        window.location.replace('{{ route("transaksi.masuk.index") }}');
                    }, 100);
                }
            });
        }

        // Handle jika user tekan back button atau close tab
        window.addEventListener('beforeunload', function(e) {
            sessionStorage.setItem('transaksi_success_message', 'Incoming transaction has been successfully created');
        });

        // Failsafe: redirect otomatis setelah 5 detik jika tidak ada aksi
        setTimeout(function() {
            if (!printDialogClosed) {
                printDialogClosed = true;
                window.location.replace('{{ route("transaksi.masuk.index") }}');
            }
        }, 5000);
    </script>
</body>
</html>
