<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GTS Response - {{ $response->user->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            body { -webkit-print-color-adjust: exact; }
            @page { margin: 0.5cm; size: A4 portrait; }
            .no-print { display: none !important; }
            .page-break { page-break-inside: avoid; }
        }
        .question-label { font-size: 0.75rem; font-weight: 700; color: #374151; margin-bottom: 0.1rem; }
        .answer-text { font-size: 0.8rem; color: #111827; }
        .section-title { font-size: 0.9rem; font-weight: 800; color: #047857; border-bottom: 2px solid #047857; margin-top: 1rem; margin-bottom: 0.5rem; padding-bottom: 0.2rem; text-transform: uppercase; }
        td, th { border: 1px solid #e5e7eb; padding: 2px 4px; font-size: 0.7rem; }
    </style>
</head>
<body class="bg-white text-gray-800 p-4 max-w-[210mm] mx-auto" onload="window.print()">

    <!-- Header -->
    <div class="flex justify-between items-start border-b-2 border-green-600 pb-4 mb-4">
        <div class="flex items-center gap-4">
            {{-- <img src="{{ asset('logo.png') }}" class="h-12 w-auto" alt="Logo"> --}}
            <div>
                <h1 class="text-xl font-bold text-green-700 leading-tight">CHED Graduate Tracer Survey</h1>
                <p class="text-xs text-gray-500">Generated on {{ date('F d, Y h:i A') }}</p>
            </div>
        </div>
        <div class="text-right">
            <h2 class="text-lg font-bold">{{ $response->user->name }}</h2>
            <p class="text-xs text-gray-600">{{ $response->department_name }}</p>
            <p class="text-xs text-gray-500">{{ $response->user->email }}</p>
        </div>
    </div>

    <!-- Content -->
    <div class="space-y-2">
        @php 
            $currentSection = ''; 
            // Group questions by section manually if needed, or just iterate.
            // Using grid for compact layout.
        @endphp

        <div class="grid grid-cols-2 gap-x-6 gap-y-2">
            @foreach($response->form->questions->sortBy('order') as $question)
                @php
                    $ans = $response->answers->where('question_id', $question->id)->first();
                    $val = $ans ? $ans->answer_text : 'N/A';
                    
                    // Decode if JSON
                    $startJson = json_decode($val, true);
                    $isJson = (json_last_error() === JSON_ERROR_NONE && is_array($startJson));
                    
                    // Force full width for tables or long text
                    $isFullWidth = ($question->type === 'dynamic_table' || strlen($question->question_text) > 80);
                    $colSpan = $isFullWidth ? 'col-span-2' : 'col-span-1';
                @endphp

                {{-- Section Header Check --}}
                @if($question->section && $question->section !== $currentSection)
                    @php $currentSection = $question->section; @endphp
                    <div class="col-span-2 mt-2">
                        <h3 class="section-title">{{ $currentSection }}</h3>
                    </div>
                @endif

                <div class="{{ $colSpan }} page-break mb-2">
                    <div class="question-label">
                        {{ $question->options['question_number'] ?? '' }}. {{ $question->question_text }}
                    </div>
                    <div class="answer-text bg-gray-50 p-1 rounded border border-gray-100 min-h-[1.5rem]">
                        @if($isJson && $question->type === 'dynamic_table')
                            <table class="w-full mt-1">
                                <tbody>
                                    @foreach($startJson as $row)
                                        <tr class="even:bg-gray-100">
                                            @foreach($row as $k => $v)
                                                <td><span class="font-semibold text-gray-500 text-[0.6rem]">{{ $k }}:</span> {{ $v }}</td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @elseif($isJson && array_is_list($startJson))
                            {{ implode(', ', $startJson) }}
                        @elseif($question->type === 'date_group' && $isJson)
                            {{ $startJson['month'] ?? '' }} {{ $startJson['day'] ?? '' }}, {{ $startJson['year'] ?? '' }}
                        @elseif($isJson)
                            <!-- Fallback for other JSON -->
                            <pre class="whitespace-pre-wrap text-[0.65rem]">{{ json_encode($startJson, JSON_PRETTY_PRINT) }}</pre>
                        @else
                            {{ $val }}
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Footer -->
    <div class="mt-8 pt-4 border-t border-gray-200 text-center text-xs text-gray-400 no-print">
        <button onclick="window.print()" class="bg-green-600 text-white px-4 py-2 rounded font-bold shadow hover:bg-green-700">PRINT / SAVE AS PDF</button>
        <button onclick="window.close()" class="ml-2 text-gray-600 underline">Close</button>
    </div>

</body>
</html>
