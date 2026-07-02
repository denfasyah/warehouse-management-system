<!DOCTYPE html>
<html>
<head>
    <title>Laporan Kapasitas Storage</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .header { text-align: center; margin-bottom: 30px; }
        .full { color: red; font-weight: bold; }
        .warn { color: #d97706; font-weight: bold; }
        .safe { color: green; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Laporan Kapasitas Storage Gudang</h2>
        <p>Dicetak pada: {{ now()->format('d M Y H:i') }}</p>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kode Lokasi</th>
                <th>Zona</th>
                <th>Level</th>
                <th>Kapasitas Max</th>
                <th>Terisi</th>
                <th>Persentase</th>
            </tr>
        </thead>
        <tbody>
            @foreach($locations as $index => $loc)
            @php
                $filled = $loc->items->sum('stock');
                $max = $loc->capacity > 0 ? $loc->capacity : 100;
                $pct = min(100, round(($filled / $max) * 100));
            @endphp
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $loc->code }}</td>
                <td>Zona {{ $loc->zone }}</td>
                <td>{{ $loc->level }}</td>
                <td>{{ number_format($max) }} Unit</td>
                <td>{{ number_format($filled) }} Unit</td>
                <td class="{{ $pct > 80 ? 'full' : ($pct > 50 ? 'warn' : 'safe') }}">
                    {{ $pct }}%
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
