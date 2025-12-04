@extends('layouts.app')

@section('title', 'Point of Sale')

@push('style')
<!-- CSS Libraries -->
<link rel="stylesheet" href="{{ asset('library/select2/dist/css/select2.min.css') }}">
<style>
    .transaction-form {
        background: #fff;
        padding: 20px;
        border-radius: 3px;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    .total-section {
        background: #f4f6f9;
        padding: 15px;
        border-radius: 3px;
        margin-top: 20px;
    }
    .action-buttons {
        margin-top: 20px;
    }
    .table th {
        background: #f4f6f9;
    }

    /* Search results styling */
    .list-group-item {
        padding: 12px 15px !important;
        font-size: 14px !important;
        cursor: pointer;
        border: 1px solid rgba(0,0,0,0.125);
    }
    .list-group-item:hover {
        background-color: #f8f9fa;
    }
    .list-group-item strong {
        font-size: 15px;
        color: #333;
    }
    .list-group-item small {
        font-size: 13px;
        color: #666;
    }
</style>
@endpush

@section('main')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Sales</h1>
        </div>

        <div class="section-body">
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible show fade">
                    <div class="alert-body">
                        <button class="close" data-dismiss="alert">
                            <span>×</span>
                        </button>
                        <strong>Error!</strong> {{ session('error') }}
                    </div>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible show fade">
                    <div class="alert-body">
                        <button class="close" data-dismiss="alert">
                            <span>×</span>
                        </button>
                        <strong>Validation Errors:</strong>
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <form action="{{ route('transaction.store') }}" method="POST" id="transactionForm">
                @csrf
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Date</label>
                                            <input type="date"
                                                   name="date"
                                                   class="form-control"
                                                   value="{{ date('Y-m-d') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Kasir</label>
                                            <input type="text"
                                                   class="form-control"
                                                   value="{{ Auth::user()->name }}"
                                                   readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Customer</label>
                                            <select name="customer_id" class="form-control select2">
                                                <option value="">Umum</option>
                                                @foreach($customers as $customer)
                                                    <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                          <label>Invoice</label>
                          <input type="text"
                              class="form-control"
                              name="invoice_number"
                              value="{{ $invoiceNumber ?? ('MP' . date('ymd') . '0001') }}"
                              readonly>
                                        </div>
                                    </div>
                                </div>

                                <ul class="nav nav-tabs" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" data-toggle="tab" href="#products" role="tab">
                                            <i class="fas fa-box"></i> Products
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-toggle="tab" href="#tradein" role="tab">
                                            <i class="fas fa-exchange-alt"></i> Trade-In
                                        </a>
                                    </li>
                                </ul>

                                <div class="tab-content mt-3">
                                    <div class="tab-pane fade show active" id="products" role="tabpanel">
                                        <div class="row">
                                            <div class="col-md-6 mb-2">
                                                <label class="mb-1">Cari Produk (Nama/IMEI)</label>
                                                <input type="text" id="productSearchInput" class="form-control" placeholder="Ketik nama atau IMEI produk...">
                                                <div id="productSearchResults" class="list-group mt-1" style="max-height: 300px; overflow-y: auto; display: none; position: absolute; z-index: 1000; width: 50%; background: white; box-shadow: 0 4px 8px rgba(0,0,0,0.15); border-radius: 4px;"></div>
                                            </div>
                                            <div class="col-md-2 mb-2">
                                                <label class="mb-1">Qty</label>
                                                <input type="number" id="qtyInput" class="form-control" value="1" min="1">
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <label class="mb-1">&nbsp;</label>
                                                <div class="input-group">
                                                    <input type="text" id="barcodeInput" class="form-control" placeholder="Atau scan barcode">
                                                    <div class="input-group-append">
                                                        <button type="button" class="btn btn-primary" onclick="addItem()">
                                                            <i class="fas fa-barcode"></i> Scan
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="tradein" role="tabpanel">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>Old Phone</label>
                                                    <input type="text" id="ti_old_phone" class="form-control" placeholder="Contoh: iPhone 11 64GB">
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label>Old IMEI</label>
                                                    <input type="text" id="ti_old_imei" class="form-control" placeholder="IMEI">
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label>Old Value (Credit)</label>
                                                    <input type="number" id="ti_old_value" class="form-control" min="0" value="0">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>New Product</label>
                                                    <input type="text" id="tradeInProductSearchInput" class="form-control" placeholder="Cari produk...">
                                                    <div id="tradeInProductSearchResults" style="position:absolute; z-index:1050; background:#fff; border:1px solid #ddd; max-height:200px; overflow-y:auto; width:calc(100% - 30px); display:none;"></div>
                                                    <input type="hidden" id="ti_new_product" name="ti_new_product">
                                                    <input type="hidden" id="ti_new_product_name" name="ti_new_product_name">
                                                    <input type="hidden" id="ti_new_product_price" name="ti_new_product_price">
                                                    <input type="hidden" id="ti_new_product_imei" name="ti_new_product_imei">
                                                </div>
                                            </div>
                                            <div class="col-md-1 d-flex align-items-end">
                                                <button type="button" class="btn btn-primary btn-block" onclick="applyTradeIn()">Terapkan</button>
                                            </div>
                                        </div>
                                        <input type="hidden" name="tradein_old_phone" id="tradein_old_phone">
                                        <input type="hidden" name="tradein_old_imei" id="tradein_old_imei">
                                        <input type="hidden" name="tradein_old_value" id="tradein_old_value">
                                        <input type="hidden" name="tradein_new_product_id" id="tradein_new_product_id">
                                    </div>
                                </div>

                                <div class="table-responsive mt-4">
                                    <table class="table table-striped" id="itemsTable">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Barcode</th>
                                                <th>Product Item</th>
                                                <th>Price</th>
                                                <th>Qty</th>
                                                <th>Discount Item</th>
                                                <th>Total</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr id="noItemsRow">
                                                <td colspan="8" class="text-center">Tidak ada item</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <div class="row mt-4">
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label>Note</label>
                                            <textarea name="note" class="form-control" rows="3"></textarea>
                                        </div>
                                        <div class="form-group">
                                            <label>Garansi</label>
                                            <select name="warranty_period" class="form-control">
                                                <option value="">Tanpa Garansi</option>
                                                <option value="7">1 Minggu</option>
                                                <option value="60">2 Bulan</option>
                                                <option value="30">1 Bulan</option>
                                                <option value="90">3 Bulan</option>
                                                <option value="180">6 Bulan</option>
                                                <option value="365">1 Tahun</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="form-group">
                                                    <label>Sub Total</label>
                                                    <input type="text" class="form-control" id="subtotal" readonly>
                                                </div>
                                                <div class="form-group">
                                                    <label>Discount</label>
                                                    <input type="number" name="discount" class="form-control" id="discount" value="0">
                                                </div>
                                                <div class="form-group">
                                                    <label>Grand Total</label>
                                                    <input type="text" class="form-control" id="grandTotal" readonly>
                                                </div>
                                                <div class="form-group">
                                                    <label>Cash</label>
                                                    <input type="number" name="cash" class="form-control" id="cash" required>
                                                </div>
                                                <div class="form-group">
                                                    <label>Change</label>
                                                    <input type="text" class="form-control" id="change" readonly>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-4">
                                    <div class="col-12 text-right">
                                        <button type="button" class="btn btn-danger" onclick="cancelTransaction()">
                                            <i class="fas fa-times"></i> Cancel
                                        </button>
                                        <button type="submit" class="btn btn-success" id="processPayment">
                                            <i class="fas fa-check"></i> Process Payment
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<!-- JS Libraries -->
<script src="{{ asset('library/select2/dist/js/select2.full.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    // Initialize Select2 for customer and service dropdowns only
    $('#customerSelect, #serviceSelect').select2();

    // Product search with autocomplete (simple input + AJAX)
    let searchTimeout;
    $('#productSearchInput').on('input', function() {
        clearTimeout(searchTimeout);
        const query = $(this).val().trim();

        if (query.length < 2) {
            $('#productSearchResults').hide().empty();
            return;
        }

        searchTimeout = setTimeout(function() {
            $.ajax({
                url: '/api/products/search',
                method: 'GET',
                data: { search: query },
                success: function(products) {
                    console.log('Found products:', products);
                    const resultsDiv = $('#productSearchResults');
                    resultsDiv.empty();

                    if (products && products.length > 0) {
                        products.forEach(function(p) {
                            const item = $('<a href="#" class="list-group-item list-group-item-action"></a>')
                                .text(p.name + (p.imei ? ' - IMEI: ' + p.imei : '') + ' (Stock: ' + p.stock + ')')
                                .data('product', p)
                                .click(function(e) {
                                    e.preventDefault();
                                    addProductToTable($(this).data('product'));
                                    $('#productSearchInput').val('');
                                    resultsDiv.hide();
                                });
                            resultsDiv.append(item);
                        });
                        resultsDiv.show();
                    } else {
                        resultsDiv.html('<div class="list-group-item">Tidak ada produk ditemukan</div>').show();
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Search error:', error);
                    console.error('Response:', xhr.responseText);
                    $('#productSearchResults').html('<div class="list-group-item text-danger">Error: ' + error + '</div>').show();
                }
            });
        }, 300);
    });

    // Click outside to close results
    $(document).on('click', function(e) {
        if (!$(e.target).closest('#productSearchInput, #productSearchResults').length) {
            $('#productSearchResults').hide();
        }
    });

    // Service product search with autocomplete
    let serviceProductSearchTimeout;
    $('#serviceProductSearchInput').on('input', function() {
        clearTimeout(serviceProductSearchTimeout);
        const query = $(this).val().trim();

        if (query.length < 2) {
            $('#serviceProductSearchResults').hide().empty();
            return;
        }

        serviceProductSearchTimeout = setTimeout(function() {
            $.ajax({
                url: '/api/products/search',
                method: 'GET',
                data: { search: query },
                success: function(products) {
                    const resultsDiv = $('#serviceProductSearchResults');
                    resultsDiv.empty();

                    if (products && products.length > 0) {
                        products.forEach(function(p) {
                            const imei = p.imei ? ' - IMEI: ' + p.imei : '';
                            const stock = ' (Stok: ' + (p.stock || 0) + ')';
                            const item = $('<a href="#" class="list-group-item list-group-item-action"></a>')
                                .html(`<strong>${p.name}</strong>${imei}${stock}<br><small>Harga: ${formatCurrency(p.sell_price || p.selling_price || p.price)}</small>`)
                                .data('product', p)
                                .click(function(e) {
                                    e.preventDefault();
                                    const prod = $(this).data('product');
                                    $('#selectedServiceProductId').val(prod.id);
                                    $('#selectedServiceProductName').val(prod.name);
                                    $('#selectedServiceProductImei').val(prod.imei || '');
                                    $('#serviceProductSearchInput').val(prod.name + (prod.imei ? ' (' + prod.imei + ')' : ''));
                                    resultsDiv.hide();
                                });
                            resultsDiv.append(item);
                        });
                        resultsDiv.show();
                    } else {
                        resultsDiv.html('<div class="list-group-item">Tidak ada produk ditemukan</div>').show();
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Product search error:', error);
                    $('#serviceProductSearchResults').html('<div class="list-group-item text-danger">Error: ' + error + '</div>').show();
                }
            });
        }, 300);
    });

    // Trade-In product search with autocomplete
    let tradeInProductSearchTimeout;
    $('#tradeInProductSearchInput').on('input', function() {
        clearTimeout(tradeInProductSearchTimeout);
        const query = $(this).val().trim();

        if (query.length < 2) {
            $('#tradeInProductSearchResults').hide().empty();
            return;
        }

        tradeInProductSearchTimeout = setTimeout(function() {
            $.ajax({
                url: '/api/products/search',
                method: 'GET',
                data: { search: query },
                success: function(products) {
                    const resultsDiv = $('#tradeInProductSearchResults');
                    resultsDiv.empty();

                    if (products && products.length > 0) {
                        products.forEach(function(p) {
                            const imei = p.imei ? ' - IMEI: ' + p.imei : '';
                            const stock = ' (Stok: ' + (p.stock || 0) + ')';
                            const item = $('<a href="#" class="list-group-item list-group-item-action"></a>')
                                .html(`<strong>${p.name}</strong>${imei}${stock}<br><small>Harga: ${formatCurrency(p.sell_price || p.selling_price || p.price)}</small>`)
                                .data('product', p)
                                .click(function(e) {
                                    e.preventDefault();
                                    const prod = $(this).data('product');
                                    $('#ti_new_product').val(prod.id);
                                    $('#ti_new_product_name').val(prod.name);
                                    $('#ti_new_product_price').val(prod.sell_price || prod.selling_price || prod.price);
                                    $('#ti_new_product_imei').val(prod.imei || '');
                                    $('#tradeInProductSearchInput').val(prod.name + (prod.imei ? ' (' + prod.imei + ')' : ''));
                                    resultsDiv.hide();
                                });
                            resultsDiv.append(item);
                        });
                        resultsDiv.show();
                    } else {
                        resultsDiv.html('<div class="list-group-item">Tidak ada produk ditemukan</div>').show();
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Trade-In product search error:', error);
                    $('#tradeInProductSearchResults').html('<div class="list-group-item text-danger">Error: ' + error + '</div>').show();
                }
            });
        }, 300);
    });

    // Hide trade-in product search results when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('#tradeInProductSearchInput, #tradeInProductSearchResults').length) {
            $('#tradeInProductSearchResults').hide();
        }
    });

    // Service search with autocomplete
    let serviceSearchTimeout;
    $('#serviceSearchInput').on('input', function() {
        clearTimeout(serviceSearchTimeout);
        const query = $(this).val().trim();

        if (query.length < 2) {
            $('#serviceSearchResults').hide().empty();
            return;
        }

        serviceSearchTimeout = setTimeout(function() {
            $.ajax({
                url: '/api/services/search',
                method: 'GET',
                data: { search: query },
                success: function(services) {
                    console.log('Found services:', services);
                    const resultsDiv = $('#serviceSearchResults');
                    resultsDiv.empty();

                    if (services && services.length > 0) {
                        services.forEach(function(s) {
                            const description = s.description ? ' - ' + s.description : '';
                            const duration = s.duration ? ' (' + s.duration + ' menit)' : '';
                            const item = $('<a href="#" class="list-group-item list-group-item-action"></a>')
                                .html(`<strong>${s.name}</strong>${description}${duration}<br><small>Harga: ${formatCurrency(s.price)}</small>`)
                                .data('service', s)
                                .click(function(e) {
                                    e.preventDefault();
                                    addServiceToTable($(this).data('service'));
                                    $('#serviceSearchInput').val('');
                                    resultsDiv.hide();
                                });
                            resultsDiv.append(item);
                        });
                        resultsDiv.show();
                    } else {
                        resultsDiv.html('<div class="list-group-item">Tidak ada service ditemukan</div>').show();
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Service search error:', error);
                    $('#serviceSearchResults').html('<div class="list-group-item text-danger">Error: ' + error + '</div>').show();
                }
            });
        }, 300);
    });

    // Add manual service to table
    window.addManualService = function() {
        const productId = $('#selectedServiceProductId').val();
        const productName = $('#selectedServiceProductName').val();
        const imei = $('#selectedServiceProductImei').val();
        const serviceType = $('#serviceTypeInput').val();
        const price = parseFloat($('#servicePriceInput').val()) || 0;
        const duration = parseInt($('#serviceDurationInput').val()) || 0;
        const status = $('#serviceStatusInput').val() || 'progress';

        // Validation
        if (!productId) {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Produk harus dipilih!'
            });
            return;
        }

        if (price <= 0) {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Harga servis harus lebih dari 0!'
            });
            return;
        }

        const serviceName = `Servis ${productName}`;
        const qty = 1; // Services always qty = 1
        const rowTotal = price * qty;
        const statusBadge = status === 'done' ? '<span class="badge badge-success">Selesai</span>' : '<span class="badge badge-warning">Progress</span>';

        $('#noItemsRow').remove();

        const rowCount = $('#itemsTable tbody tr').length + 1;
        const row = $('<tr>');
        row.append(`<td>${rowCount}</td>`);
        row.append(`<td>${imei || '-'}</td>`);
        row.append(`<td><span class="badge badge-info">Service</span> ${serviceName}${serviceType ? ' - <span class="badge badge-primary">' + serviceType + '</span>' : ''} ${statusBadge}${duration ? ' (' + duration + ' hari)' : ''}</td>`);
        row.append(`<td>${formatCurrency(price)}</td>`);
        row.append(`
            <td>
                <input type="number" name="quantities[]" value="${qty}" min="1" class="form-control item-qty" style="width:80px" onchange="updateRowTotal(this)">
                <input type="hidden" name="types[]" value="service_manual">
                <input type="hidden" name="service_names[]" value="${serviceName}">
                <input type="hidden" name="service_prices[]" value="${price}">
                <input type="hidden" name="service_durations[]" value="${duration}">
                <input type="hidden" name="service_types[]" value="${serviceType}">
                <input type="hidden" name="service_imeis[]" value="${imei}">
                <input type="hidden" name="service_statuses[]" value="${status}">
                <input type="hidden" name="service_product_ids[]" value="${productId}">
            </td>
        `);
        row.append(`<td><input type="number" name="discounts[]" value="0" min="0" class="form-control item-discount" style="width:100px" onchange="updateRowTotal(this)"></td>`);
        row.append(`<td class="row-total">${formatCurrency(rowTotal)}</td>`);
        row.append(`<td><button type="button" class="btn btn-danger btn-sm" onclick="removeRow(this)"><i class="fas fa-trash"></i></button></td>`);

        // Store raw numbers for calculation
        row.data('price', price);
        row.data('rowtotal', rowTotal);

        $('#itemsTable tbody').append(row);
        clearServiceInputs();
        updateTotals();
    };

    // Clear service inputs
    window.clearServiceInputs = function() {
        $('#serviceProductSearchInput').val('');
        $('#selectedServiceProductId').val('');
        $('#selectedServiceProductName').val('');
        $('#selectedServiceProductImei').val('');
        $('#serviceProductSearchResults').hide().empty();
        $('#serviceTypeInput').val('');
        $('#servicePriceInput').val('');
        $('#serviceDurationInput').val('');
        $('#serviceStatusInput').val('progress');
    };

    // Function to add service to table
    window.addServiceToTable = function(service) {
        const qty = 1; // Services always qty = 1
        const price = parseFloat(service.price) || 0;
        const rowTotal = price * qty;
        const serviceType = $('#serviceTypeInput').val();
        const imei = $('#serviceImeiInput').val();
        const status = $('#serviceStatusInput').val() || 'progress';
        const statusBadge = status === 'done' ? '<span class="badge badge-success">Selesai</span>' : '<span class="badge badge-warning">Progress</span>';

        $('#noItemsRow').remove();

        const rowCount = $('#itemsTable tbody tr').length + 1;
        const row = $('<tr>');
        row.append(`<td>${rowCount}</td>`);
        row.append(`<td>${imei || '-'}</td>`);
        row.append(`<td><span class="badge badge-info">Service</span> ${service.name}${serviceType ? ' - <span class="badge badge-primary">' + serviceType + '</span>' : ''} ${statusBadge}${service.duration ? ' (' + service.duration + ' menit)' : ''}</td>`);
        row.append(`<td>${formatCurrency(price)}</td>`);
        row.append(`
            <td>
                <input type="number" name="quantities[]" value="${qty}" min="1" class="form-control item-qty" style="width:80px" onchange="updateRowTotal(this)">
                <input type="hidden" name="types[]" value="service">
                <input type="hidden" name="items[]" value="${service.id}">
                <input type="hidden" name="service_types[]" value="${serviceType}">
                <input type="hidden" name="service_imeis[]" value="${imei}">
                <input type="hidden" name="service_statuses[]" value="${status}">
            </td>
        `);
        row.append(`<td><input type="number" name="discounts[]" value="0" min="0" class="form-control item-discount" style="width:100px" onchange="updateRowTotal(this)"></td>`);
        row.append(`<td class="row-total">${formatCurrency(rowTotal)}</td>`);
        row.append(`<td><button type="button" class="btn btn-danger btn-sm" onclick="removeRow(this)"><i class="fas fa-trash"></i></button></td>`);

        // Store raw numbers for calculation
        row.data('price', price);
        row.data('rowtotal', rowTotal);

        $('#itemsTable tbody').append(row);
        clearServiceSearch();
        updateTotals();
    };

    // Clear service search
    window.clearServiceSearch = function() {
        $('#serviceSearchInput').val('');
        $('#serviceTypeInput').val('');
        $('#serviceImeiInput').val('');
        $('#serviceStatusInput').val('progress');
        $('#serviceSearchResults').hide().empty();
    };

    // Function to add product to table
    window.addProductToTable = function(product) {
        const qty = parseInt($('#qtyInput').val()) || 1;
        const price = parseFloat(product.sell_price || product.selling_price) || 0;
        const rowTotal = price * qty;

        $('#noItemsRow').remove();

        const rowCount = $('#itemsTable tbody tr').length + 1;
        const row = $('<tr>');
        row.append(`<td>${rowCount}</td>`);
        row.append(`<td>${product.imei || '-'}</td>`);
        row.append(`<td>${product.name}${product.imei ? ' - IMEI: ' + product.imei : ''}</td>`);
        row.append(`<td>${formatCurrency(price)}</td>`);
        row.append(`
            <td>
                <input type="number" name="quantities[]" value="${qty}" min="1" class="form-control item-qty" style="width:80px" onchange="updateRowTotal(this)">
                <input type="hidden" name="types[]" value="product">
                <input type="hidden" name="items[]" value="${product.id}">
            </td>
        `);
        row.append(`<td><input type="number" name="discounts[]" value="0" min="0" class="form-control item-discount" style="width:100px" onchange="updateRowTotal(this)"></td>`);
        row.append(`<td class="row-total">${formatCurrency(rowTotal)}</td>`);
        row.append(`<td><button type="button" class="btn btn-danger btn-sm" onclick="removeRow(this)"><i class="fas fa-trash"></i></button></td>`);

        // Store raw numbers for calculation
        row.data('price', price);
        row.data('rowtotal', rowTotal);

        $('#itemsTable tbody').append(row);
        $('#qtyInput').val(1);
        updateTotals();
    };

    // Listen for barcode input
    $('#barcodeInput').on('keypress', function(e) {
        if (e.which == 13) { // Enter key
            e.preventDefault();
            addItem();
        }
    });

    // Handle adding items
    window.addItem = function() {
        const barcode = $('#barcodeInput').val();
        const qty = parseInt($('#qtyInput').val()) || 1;

        if (!barcode) return;

        // Fetch product data from API
        $.get(`/api/products/search?search=${encodeURIComponent(barcode)}`, function(products) {
            if (!products || products.length === 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Product Not Found',
                    text: 'No product found matching this query.'
                });
                return;
            }
            const product = products[0];
            const price = parseFloat(product.sell_price || product.selling_price || product.price) || 0;
            const rowTotal = price * qty;

            $('#noItemsRow').remove();

            // Add product to table
            const rowCount = $('#itemsTable tbody tr').length + 1;
            const row = $('<tr>');
            row.append(`<td>${rowCount}</td>`);
            row.append(`<td>${product.imei || product.barcode || '-'}</td>`);
            row.append(`<td>${product.name}</td>`);
            row.append(`<td>${formatCurrency(price)}</td>`);
            row.append(`
                <td>
                    <input type="number"
                           name="quantities[]"
                           value="${qty}"
                           min="1"
                           class="form-control item-qty"
                           style="width: 80px"
                           onchange="updateRowTotal(this)">
                    <input type="hidden" name="types[]" value="product">
                    <input type="hidden" name="items[]" value="${product.id}">
                </td>
            `);
            row.append(`
                <td>
                    <input type="number"
                           name="discounts[]"
                           value="0"
                           min="0"
                           class="form-control item-discount"
                           style="width: 100px"
                           onchange="updateRowTotal(this)">
                </td>
            `);
            row.append(`
                <td class="row-total">
                    ${formatCurrency(rowTotal)}
                </td>
            `);
            row.append(`
                <td>
                    <button type="button" class="btn btn-danger btn-sm" onclick="removeRow(this)">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            `);

            // Store raw numbers for calculation
            row.data('price', price);
            row.data('rowtotal', rowTotal);
            row.append(`<input type="hidden" name="types[]" value="product">`);
            row.append(`<input type="hidden" name="items[]" value="${product.id}">`);

            $('#itemsTable tbody').append(row);

            // Clear inputs
            $('#barcodeInput').val('').focus();
            $('#qtyInput').val(1);

            updateTotals();
        });
    };

    // Removed addSelectedProduct (no longer needed)

    // Handle removing rows
    window.removeRow = function(button) {
        $(button).closest('tr').remove();
        if ($('#itemsTable tbody tr').length === 0) {
            $('#itemsTable tbody').append(`
                <tr id="noItemsRow">
                    <td colspan="8" class="text-center">Tidak ada item</td>
                </tr>
            `);
        }
        updateTotals();
    };

    // Update row total when quantity or discount changes
    window.updateRowTotal = function(input) {
        const row = $(input).closest('tr');
        const price = parseFloat(row.data('price')) || 0;
        const qty = parseInt(row.find('.item-qty').val()) || 0;
        const discount = parseFloat(row.find('.item-discount').val()) || 0;
        const total = (price * qty) - discount;

        // Store raw number in data attribute for calculation
        row.data('rowtotal', total);
        row.find('.row-total').text(formatCurrency(total));
        updateTotals();
    };

    // Update all totals
    window.updateTotals = function() {
        let subtotal = 0;
        $('#itemsTable tbody tr:not(#noItemsRow)').each(function() {
            // Get raw number from data attribute instead of parsing formatted text
            const rowTotal = parseFloat($(this).data('rowtotal')) || 0;
            subtotal += rowTotal;
        });

        const discount = parseFloat($('#discount').val()) || 0;
        const grandTotal = Math.max(0, subtotal - discount);

        // Store raw numbers for calculation
        $('#subtotal').data('raw', subtotal);
        $('#grandTotal').data('raw', grandTotal);

        // Display formatted currency
        $('#subtotal').val(formatCurrency(subtotal));
        $('#grandTotal').val(formatCurrency(grandTotal));

        updateChange();
    };

    // Apply trade-in: set global discount as old value and add selected product as 1 qty item
    window.applyTradeIn = function() {
        const oldPhone = $('#ti_old_phone').val().trim();
        const oldImei = $('#ti_old_imei').val().trim();
        const oldValue = parseFloat($('#ti_old_value').val()) || 0;
        const productId = $('#ti_new_product').val();
        const productName = $('#ti_new_product_name').val();
        const productPrice = parseFloat($('#ti_new_product_price').val()) || 0;
        const rowTotal = productPrice * 1;

        if (!productId) {
            Swal.fire({ icon: 'warning', title: 'Pilih produk baru', timer: 1500, showConfirmButton: false });
            return;
        }

        // set hidden payload
        $('#tradein_old_phone').val(oldPhone);
        $('#tradein_old_imei').val(oldImei);
        $('#tradein_old_value').val(oldValue);
        $('#tradein_new_product_id').val(productId);

        // ensure the product row exists once, quantity 1
        $('#noItemsRow').remove();
        const row = $('<tr>');
        const rowCount = $('#itemsTable tbody tr').length + 1;
        row.append(`<td>${rowCount}</td>`);
        row.append(`<td>PRD${productId}</td>`);
        row.append(`<td>${productName}</td>`);
        row.append(`<td>${formatCurrency(productPrice)}</td>`);
        row.append(`<td>
            <input type="number" name="quantities[]" value="1" min="1" class="form-control item-qty" style="width:80px" onchange="updateRowTotal(this)">
            <input type="hidden" name="types[]" value="product">
            <input type="hidden" name="items[]" value="${productId}">
        </td>`);
        row.append(`<td><input type="number" name="discounts[]" value="0" min="0" class="form-control item-discount" style="width:100px" onchange="updateRowTotal(this)"></td>`);
        row.append(`<td class="row-total">${formatCurrency(rowTotal)}</td>`);
        row.append(`<td><button type="button" class="btn btn-danger btn-sm" onclick="removeRow(this)"><i class="fas fa-trash"></i></button></td>`);

        // Store raw numbers for calculation
        row.data('price', productPrice);
        row.data('rowtotal', rowTotal);

        $('#itemsTable tbody').append(row);

        // apply global discount equal to old value (credit)
        $('#discount').val(oldValue);
        updateTotals();

        Swal.fire({ icon: 'success', title: 'Trade-In diterapkan', timer: 1200, showConfirmButton: false });
    };

    // Update change amount
    window.updateChange = function() {
        // Get raw grand total from data attribute
        const grandTotal = parseFloat($('#grandTotal').data('raw')) || 0;
        const cash = parseFloat($('#cash').val()) || 0;
        const change = cash - grandTotal;

        // Store raw change for potential use
        $('#change').data('raw', Math.max(0, change));

        // Display formatted currency
        $('#change').val(formatCurrency(Math.max(0, change)));

        // Enable/disable payment button
        $('#processPayment').prop('disabled', cash < grandTotal);
    };

    // Format currency
    window.formatCurrency = function(amount) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        }).format(amount);
    };

    // Handle transaction cancellation
    window.cancelTransaction = function() {
        Swal.fire({
            title: 'Cancel Transaction?',
            text: "All items will be removed. This cannot be undone!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, cancel it!'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.reload();
            }
        });
    };

    // Handle adding services
    window.addService = function() {
        const selectedService = $('#serviceSelect').select2('data')[0];
        if (!selectedService) return;

        const qty = parseInt($('#serviceQtyInput').val()) || 1;
        const price = parseFloat(selectedService.element.dataset.price) || 0;
        const rowTotal = price * qty;

        $('#noItemsRow').remove();

        // Add service to table
        const rowCount = $('#itemsTable tbody tr').length + 1;
        const row = $('<tr>');
        row.append(`<td>${rowCount}</td>`);
        row.append(`<td>SRV${selectedService.id}</td>`);
        row.append(`<td>${selectedService.text}</td>`);
        row.append(`<td>${formatCurrency(price)}</td>`);
        row.append(`
            <td>
                <input type="number"
                       name="quantities[]"
                       value="${qty}"
                       min="1"
                       class="form-control item-qty"
                       style="width: 80px"
                       onchange="updateRowTotal(this)">
                <input type="hidden" name="types[]" value="service">
                <input type="hidden" name="items[]" value="${selectedService.id}">
            </td>
        `);
        row.append(`
            <td>
                <input type="number"
                       name="discounts[]"
                       value="0"
                       min="0"
                       class="form-control item-discount"
                       style="width: 100px"
                       onchange="updateRowTotal(this)">
            </td>
        `);
        row.append(`
            <td class="row-total">
                ${formatCurrency(rowTotal)}
            </td>
        `);
        row.append(`
            <td>
                <button type="button" class="btn btn-danger btn-sm" onclick="removeRow(this)">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `);

        // Store raw numbers for calculation
        row.data('price', price);
        row.data('rowtotal', rowTotal);

        $('#itemsTable tbody').append(row);

        // Clear inputs
        $('#serviceSelect').val('').trigger('change');
        $('#serviceQtyInput').val(1);

        updateTotals();
    };

    // Listen for changes
    $('#discount').on('input', updateTotals);
    $('#cash').on('input', updateChange);

    // Form validation and submit
    $('#transactionForm').on('submit', function(e) {
        // Check if there are items
        const itemCount = $('#itemsTable tbody tr:not(#noItemsRow)').length;
        if (itemCount === 0) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'No Items',
                text: 'Please add at least one product or service to the transaction.'
            });
            return false;
        }

        // Check if cash is sufficient
        const grandTotal = parseFloat($('#grandTotal').data('raw')) || 0;
        const cash = parseFloat($('#cash').val()) || 0;
        if (cash < grandTotal) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Insufficient Cash',
                text: `Cash amount (${formatCurrency(cash)}) is less than Grand Total (${formatCurrency(grandTotal)})`
            });
            return false;
        }

        // Log form data for debugging
        console.log('Submitting transaction:', {
            items: $('input[name="items[]"]').length,
            types: $('input[name="types[]"]').length,
            quantities: $('input[name="quantities[]"]').length,
            discounts: $('input[name="discounts[]"]').length,
            grandTotal: grandTotal,
            cash: cash
        });

        return true;
    });
});
</script>
@endpush
