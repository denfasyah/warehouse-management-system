<!DOCTYPE html>
<html>
<head>
    <title>Laporan Barang Masuk</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .header { text-align: center; margin-bottom: 30px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Laporan Barang Masuk</h2>
        <p>Dicetak pada: {{ now()->format('d M Y H:i') }}</p>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Waktu Masuk</th>
                <th>SKU</th>
                <th>Nama Barang</th>
                <th>Jumlah</th>
                <th>Penerima</th>
                <th>Lokasi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($incomings as $index => $incoming)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $incoming->created_at->format('d M Y H:i') }}</td>
                <td>{{ $incoming->item->sku }}</td>
                <td>{{ $incoming->item->name }}</td>
                <td>+{{ number_format($incoming->quantity) }} {{ $incoming->item->unit }}</td>
                <td>{{ $incoming->user->name ?? '-' }}</td>
                <td>{{ $incoming->location->code ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
