<!DOCTYPE html>
<html>
<head>
    <title>Laporan Barang Keluar</title>
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
        <h2>Laporan Barang Keluar</h2>
        <p>Dicetak pada: {{ now()->format('d M Y H:i') }}</p>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Waktu Request</th>
                <th>SKU</th>
                <th>Nama Barang</th>
                <th>Jumlah</th>
                <th>Pemohon</th>
                <th>Status</th>
                <th>Disetujui Oleh</th>
            </tr>
        </thead>
        <tbody>
            @foreach($outgoings as $index => $outgoing)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $outgoing->created_at->format('d M Y H:i') }}</td>
                <td>{{ $outgoing->item->sku }}</td>
                <td>{{ $outgoing->item->name }}</td>
                <td>-{{ number_format($outgoing->quantity) }} {{ $outgoing->item->unit }}</td>
                <td>{{ $outgoing->requestedBy->name ?? '-' }}</td>
                <td>
                    @if($outgoing->status == 'approved') Disetujui 
                    @elseif($outgoing->status == 'rejected') Ditolak 
                    @else Pending @endif
                </td>
                <td>{{ $outgoing->approvedBy->name ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
