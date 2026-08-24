<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Rekap Tahunan {{ $recap['year'] }}</title>
    <style>@include('admin.rekap.partials.styles')</style>
</head>
<body>
    <div class="title-block">
        <div class="title">REKAP LAPORAN PAJAK TAHUNAN</div>
        <div class="subtitle">KP AHMAD SANDI — ahmadsandi.com</div>
    </div>

    <table class="identity">
        <tr>
            <td width="12%">TAHUN</td>
            <td width="2%">:</td>
            <td>{{ $recap['year'] }}</td>
        </tr>
        <tr>
            <td>DICETAK</td>
            <td>:</td>
            <td>{{ $recap['printedAt'] }}</td>
        </tr>
    </table>

    <strong>SPT MASA PER BULAN</strong>
    <table class="grid-table" style="margin-top: 4px;">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="25%">Nama Entitas</th>
                @foreach ($recap['months'] as $month)
                    <th>{{ $month }}</th>
                @endforeach
                <th width="7%">S+L</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($recap['rows'] as $row)
                <tr>
                    <td class="text-center">{{ $row['no'] }}</td>
                    <td>{{ $row['namaEntitas'] }}</td>
                    @foreach ($row['cells'] as $code)
                        <td class="text-center">{{ $code ?? '-' }}</td>
                    @endforeach
                    <td class="text-center">{{ $row['selesaiCount'] }}/12</td>
                </tr>
            @empty
                <tr>
                    <td colspan="15" class="text-center">Tidak ada klien.</td>
                </tr>
            @endforelse
            <tr class="total">
                <td colspan="2" class="text-center">JUMLAH DILAPORAN (L + S)</td>
                @foreach ($recap['monthlyReported'] as $count)
                    <td class="text-center">{{ $count }}</td>
                @endforeach
                <td class="text-center">{{ array_sum($recap['monthlyReported']) }}</td>
            </tr>
        </tbody>
    </table>

    <div class="legend">
        Keterangan: S = Selesai, L = Dilaporkan, P = Diproses, M = Menunggu Dokumen, D = Draft, - = Tidak ada laporan.
        Total SPT Masa tahun {{ $recap['year'] }}: {{ $recap['totalMasa'] }} laporan.
    </div>

    <strong style="display: block; margin-top: 12px;">SPT TAHUNAN</strong>
    <table class="grid-table" style="margin-top: 4px;">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="30%">Nama Entitas</th>
                <th width="25%">Jenis Laporan</th>
                <th width="20%">Status</th>
                <th width="20%">Deadline</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($recap['annualReports'] as $row)
                <tr>
                    <td class="text-center">{{ $row['no'] }}</td>
                    <td>{{ $row['namaEntitas'] }}</td>
                    <td>{{ $row['jenisLaporan'] }}</td>
                    <td>{{ $row['statusLabel'] }}</td>
                    <td class="text-center">{{ $row['deadline'] ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">Tidak ada SPT Tahunan pada tahun ini.</td>
                </tr>
            @endforelse
            <tr class="total">
                <td colspan="3" class="text-center">TOTAL SPT TAHUNAN</td>
                <td class="text-center" colspan="2">{{ count($recap['annualReports']) }}</td>
            </tr>
        </tbody>
    </table>
</body>
</html>
