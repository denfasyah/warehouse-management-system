<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Barcode - {{ $item->sku }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Arial', sans-serif;
            background-color: #f3f4f6;
            min-height: 100vh;
            padding: 20px;
        }

        /* Top action bar (hidden on print) */
        .action-bar {
            max-width: 400px;
            margin: 0 auto 20px auto;
            display: flex;
            gap: 10px;
        }

        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background-color: white;
            color: #374151;
            border: 1px solid #e5e7eb;
            padding: 10px 16px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            transition: background 0.15s;
        }
        .btn-back:hover { background-color: #f9fafb; }

        .btn-print {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            flex: 1;
            justify-content: center;
            background-color: #181c61;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 2px 6px rgba(24,28,97,0.3);
            transition: opacity 0.15s;
        }
        .btn-print:hover { opacity: 0.9; }

        /* Label stiker */
        .label-container {
            background-color: white;
            width: 80mm;
            margin: 0 auto;
            padding: 8mm 10mm;
            text-align: center;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.12);
            border: 1px solid #e5e7eb;
        }

        .company-name {
            font-size: 9pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-bottom: 6px;
            padding-bottom: 6px;
            border-bottom: 1px dashed #ccc;
            color: #181c61;
        }

        .item-name {
            font-size: 11pt;
            font-weight: bold;
            margin: 8px 0 3px 0;
            line-height: 1.3;
            color: #111;
        }

        .item-category {
            font-size: 8pt;
            color: #6b7280;
            margin-bottom: 12px;
        }

        .barcode-wrapper {
            margin: 12px 0;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
        }

        .sku-text {
            font-size: 8pt;
            font-family: 'Courier New', monospace;
            color: #374151;
            letter-spacing: 1px;
            margin-top: 4px;
        }

        .location {
            font-size: 9pt;
            font-weight: bold;
            color: #111;
            margin-top: 10px;
            padding-top: 6px;
            border-top: 1px dashed #ccc;
        }

        /* Print-specific styles */
        @media print {
            body {
                background-color: white;
                padding: 0;
            }
            .action-bar {
                display: none !important;
            }
            .label-container {
                box-shadow: none;
                border-radius: 0;
                border: none;
                margin: 0;
                width: 80mm;
                padding: 5mm 8mm;
            }
        }
    </style>
</head>
<body>

    <!-- Action Bar (back + print) - tersembunyi saat print -->
    <div class="action-bar">
        <a href="{{ route('admin.items.index') }}" class="btn-back">
            ← Kembali ke Daftar Barang
        </a>
        <button class="btn-print" onclick="window.print()">
            🖨️ Cetak Barcode
        </button>
    </div>

    <!-- Label Barcode -->
    <div class="label-container">
        <div class="company-name">WMS · IndoOne Sentosa</div>
        <div class="item-name">{{ $item->name }}</div>
        <div class="item-category">{{ $item->category->name }}</div>

        <div class="barcode-wrapper">
            {!! $barcodeHtml !!}
        </div>

        <div class="sku-text">{{ $item->sku }}</div>

        <div class="location">📍 Lokasi Rak: {{ $item->locations->pluck('code')->join(', ') ?: 'Belum diatur' }}</div>
    </div>

    <script>
        // Auto-open print dialog setelah halaman load
        window.addEventListener('load', function () {
            setTimeout(() => window.print(), 300);
        });
    </script>
</body>
</html>
