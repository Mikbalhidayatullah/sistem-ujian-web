<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Rekap Nilai {{ $exam->title }}</title>
</head>
<body>
    <table border="1">
        <tr>
            <th colspan="10">Rekap Nilai Ujian</th>
        </tr>
        <tr>
            <td>Judul Ujian</td>
            <td colspan="9">{{ $exam->title }}</td>
        </tr>
        <tr>
            <td>Mata Pelajaran</td>
            <td colspan="9">{{ $exam->subject->display_name }}</td>
        </tr>
        <tr>
            <td>Token</td>
            <td>{{ $exam->access_token }}</td>
            <td colspan="2">PIN</td>
            <td>{{ $exam->access_pin }}</td>
            <td>Durasi</td>
            <td>{{ $exam->duration_minutes }} menit</td>
            <td colspan="2">Jumlah Soal</td>
            <td>{{ $exam->questions->count() }}</td>
        </tr>
    </table>

    <table border="1" style="margin-top: 16px;">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Siswa</th>
                <th>Status</th>
                <th>Nilai</th>
                <th>Jawaban Benar</th>
                <th>Jawaban Salah</th>
                <th>Soal Dijawab</th>
                <th>Waktu Dipakai</th>
                <th>Pelanggaran</th>
                <th>Waktu Submit</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($exam->attempts as $attempt)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $attempt->participantName() }}</td>
                    <td>{{ $attempt->status }}</td>
                    <td>{{ number_format((float) $attempt->score, 2) }}</td>
                    <td>{{ $attempt->correctCount() }}</td>
                    <td>{{ $attempt->wrongCount() }}</td>
                    <td>{{ $attempt->answeredCount() }}</td>
                    <td>{{ $attempt->timeSpentForHumans() }}</td>
                    <td>{{ $attempt->violation_count }}</td>
                    <td>{{ $attempt->submitted_at?->format('d-m-Y H:i:s') ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="10">Belum ada siswa yang mengerjakan ujian ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
