<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Rekap Bulanan {{ $recap['periode'] }}</title>
    <style>@include('admin.rekap.partials.styles')</style>
</head>
<body>
    <div class="title-block">
        <div class="title">REKAP LAPORAN PAJAK BULANAN</div>
        <div class="subtitle">KP AHMAD SANDI — ahmadsandi.com</div>
    </div>

    <table class="identity">
        <tr>
            <td width="12%">PERIODE</td>
            <td width="2%">:</td>
            <td>{{ strtoupper($recap['periodeLabel']) }}</td>
        </tr>
        <tr>
            <td>DICETAK</td>
            <td>:</td>
            <td>{{ $recap['printedAt'] }}</td>
        </tr>
    </table>

    <table class="grid-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="28%">Nama Entitas</th>
                <th width="15%">Jenis Klien</th>
                <th width="20%">Jenis Laporan</th>
                <th width="14%">Deadline</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($recap['sections'] as $section)
                <tr class="section">
                    <td colspan="5">{{ $section['label'] }} ({{ $section['count'] }} laporan)</td>
                </tr>
                @foreach ($section['rows'] as $row)
                    <tr>
                        <td class="text-center">{{ $row['no'] }}</td>
                        <td>{{ $row['namaEntitas'] }}</td>
                        <td>{{ $row['jenisKlien'] }}</td>
                        <td>{{ $row['jenisLaporan'] }}</td>
                        <td class="text-center">{{ $row['deadline'] ?? '-' }}</td>
                    </tr>
                @endforeach
            @empty
                <tr>
                    <td colspan="5" class="text-center">Tidak ada laporan pada periode ini.</td>
                </tr>
            @endforelse
            <tr class="total">
                <td colspan="4" class="text-center">TOTAL LAPORAN</td>
                <td class="text-center">{{ $recap['total'] }}</td>
            </tr>
        </tbody>
    </table>
</body>
</html>
