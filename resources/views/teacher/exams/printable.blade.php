<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $exam->title }} | Cetak Soal</title>
    <style>
        @page {
            size: A4;
            margin: 2cm 3cm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: DejaVu Sans, Arial, sans-serif;
            color: #0f172a;
            font-size: 11px;
            line-height: 1.45;
        }

        .sheet {
            width: 100%;
        }

        .header {
            margin-bottom: 16px;
            padding-bottom: 10px;
        }

        .kop {
            display: table;
            width: 100%;
            margin-bottom: 16px;
            table-layout: fixed;
        }

        .kop-badge,
        .kop-body,
        .kop-spacer {
            display: table-cell;
            vertical-align: top;
        }

        .kop-badge,
        .kop-spacer {
            width: 72px;
        }

        .kop-mark {
            width: 56px;
            height: 56px;
            color: #0f172a;
            font-size: 14px;
            font-weight: 800;
            text-align: center;
            line-height: 56px;
        }

        .kop-mark img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            display: block;
        }

        .kop-body {
            text-align: center;
            padding-top: 0;
        }

        .kop-title {
            margin: 0;
            font-size: 15px;
            font-weight: 800;
            font-family: DejaVu Sans, Arial, sans-serif;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.02em;
            line-height: 1.2;
        }

        .kop-subtitle {
            margin: 6px 0 0;
            font-size: 9px;
            font-family: DejaVu Sans, Arial, sans-serif;
            color: #0f172a;
            font-weight: 700;
            line-height: 1.2;
        }

        .kop-address {
            margin: 4px 0 0;
            font-size: 8.5px;
            font-family: DejaVu Sans, Arial, sans-serif;
            color: #0f172a;
            line-height: 1.2;
        }

        .kop-divider {
            height: 4px;
            margin-top: 10px;
            border-top: 2px solid #000000;
            border-bottom: 1px solid #000000;
        }

        .kicker {
            margin-top: 8px;
            font-size: 9px;
            font-family: DejaVu Sans, Arial, sans-serif;
            letter-spacing: 0.18em;
            text-transform: uppercase;
            color: #64748b;
            text-align: center;
            line-height: 1.2;
        }

        .title {
            margin: 10px 0 0;
            font-size: 16px;
            font-weight: 700;
            font-family: DejaVu Sans, Arial, sans-serif;
            color: #0f172a;
            text-align: center;
            line-height: 1.15;
        }

        .meta {
            margin-top: 14px;
            border-collapse: collapse;
            width: 100%;
            border: 1px solid #0f172a;
        }

        .meta td {
            padding: 7px 10px;
            vertical-align: top;
            border: 1px solid #0f172a;
        }

        .meta-label {
            width: 148px;
            color: #0f172a;
            font-weight: 700;
            background: #f8fafc;
        }

        .meta-separator {
            width: 16px;
            text-align: center;
            font-weight: 700;
            background: #f8fafc;
        }

        .meta-value {
            color: #0f172a;
        }

        .meta-heading {
            margin: 16px 0 8px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.18em;
            text-transform: uppercase;
            color: #334155;
        }

        .questions {
            margin-top: 18px;
        }

        .question {
            margin-bottom: 22px;
            page-break-inside: avoid;
        }

        .question-head {
            display: table;
            width: 100%;
        }

        .question-head-row {
            display: table-row;
        }

        .question-number,
        .question-prompt-wrap {
            display: table-cell;
            vertical-align: top;
        }

        .question-number {
            width: 26px;
            font-weight: 700;
        }

        .question-prompt {
            display: inline-block;
            font-size: 14px;
            font-weight: 600;
            color: #111827;
            line-height: 1.6;
        }

        .question-media {
            margin: 12px 0 10px 26px;
        }

        .question-media img {
            max-width: 220px;
            max-height: 150px;
            width: auto;
            height: auto;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            object-fit: contain;
        }

        .media-note {
            margin-left: 26px;
            color: #64748b;
            font-size: 11px;
            font-style: italic;
        }

        .options {
            list-style: none;
            margin: 12px 0 0 26px;
            padding: 0;
        }

        .options li {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            margin: 6px 0;
        }

        .option-label {
            flex: 0 0 26px;
            font-weight: 700;
        }

        .option-text {
            flex: 1 1 auto;
        }

        .answer-key-block {
            margin-top: 28px;
            padding-top: 14px;
            border-top: 1px solid #cbd5e1;
            page-break-inside: avoid;
        }

        .answer-key-title {
            margin: 0 0 10px;
            font-size: 16px;
            font-weight: 700;
        }

        .answer-key-list {
            margin: 0;
            padding-left: 18px;
            columns: 5;
            column-gap: 18px;
        }

        .answer-key-list li {
            margin: 4px 0;
        }

        @media print {
            .print-tools {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="sheet">
        @if ($autoPrint)
            <div class="print-tools" style="margin-bottom: 16px;">
                <button type="button" onclick="window.print()" style="padding: 10px 14px; border-radius: 10px; border: 1px solid #cbd5e1; background: white; font-weight: 600; cursor: pointer;">
                    Print
                </button>
            </div>
        @endif

        <div class="header">
            <div class="kop">
                <div class="kop-badge">
                    <div class="kop-mark">
                        @if ($schoolLogoDataUri)
                            <img src="{{ $schoolLogoDataUri }}" alt="Logo sekolah">
                        @else
                            SMK
                        @endif
                    </div>
                </div>
                <div class="kop-body">
                    <h1 class="kop-title">{{ $schoolName }}</h1>
                    <p class="kop-subtitle">Jurusan : {{ $schoolDepartment }}</p>
                    <p class="kop-address">{{ $schoolAddress }}</p>
                </div>
                <div class="kop-spacer"></div>
            </div>

            <div class="kop-divider"></div>

            <div class="kicker">{{ $exam->subject->display_name }}</div>
            <h1 class="title">{{ $exam->title }}</h1>

            <div class="meta-heading">Identitas Dokumen</div>
            <table class="meta">
                <tr>
                    <td class="meta-label">Guru</td>
                    <td class="meta-separator">:</td>
                    <td class="meta-value">{{ $exam->teacher->name }}</td>
                </tr>
                <tr>
                    <td class="meta-label">Durasi</td>
                    <td class="meta-separator">:</td>
                    <td class="meta-value">{{ $exam->duration_minutes }} menit</td>
                </tr>
                <tr>
                    <td class="meta-label">Token / PIN</td>
                    <td class="meta-separator">:</td>
                    <td class="meta-value">{{ $exam->access_token }} / {{ $exam->access_pin }}</td>
                </tr>
                <tr>
                    <td class="meta-label">Deskripsi</td>
                    <td class="meta-separator">:</td>
                    <td class="meta-value">{{ $exam->description ?: 'Tidak ada deskripsi tambahan.' }}</td>
                </tr>
            </table>
        </div>

        <div class="questions">
            @foreach ($printQuestions as $question)
                <div class="question">
                    <div class="question-head">
                        <div class="question-head-row">
                            <span class="question-number">{{ $question['number'] }}.</span>
                            <div class="question-prompt-wrap">
                                <div class="question-prompt">{!! nl2br(e($question['prompt'])) !!}</div>
                            </div>
                        </div>
                    </div>

                    @if ($question['media_data_uri'])
                        <div class="question-media">
                            <img src="{{ $question['media_data_uri'] }}" alt="Media soal {{ $question['number'] }}">
                        </div>
                    @elseif ($question['media_note'])
                        <div class="media-note">{{ $question['media_note'] }}</div>
                    @endif

                    <ul class="options">
                        @foreach ($question['options'] as $option)
                            <li>
                                <span class="option-label">{{ $option['label'] }}.</span>
                                <span class="option-text">{{ $option['text'] }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        </div>

        <div class="answer-key-block">
            <h2 class="answer-key-title">Kunci jawaban :</h2>
            <ol class="answer-key-list">
                @foreach ($answerKey as $item)
                    <li>{{ $item['label'] }}</li>
                @endforeach
            </ol>
        </div>
    </div>

    @if ($autoPrint)
        <script>
            window.addEventListener('load', () => {
                setTimeout(() => window.print(), 250);
            });
        </script>
    @endif
</body>
</html>
