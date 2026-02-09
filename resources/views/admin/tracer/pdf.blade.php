<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CHED GTS Response - {{ $response->user->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            body {
                -webkit-print-color-adjust: exact;
            }

            @page {
                margin: 1cm;
                size: A4 portrait;
            }
        }

        .section-header {
            border-bottom: 2px solid #166534;
            padding-bottom: 4px;
            margin-top: 20px;
            margin-bottom: 10px;
            font-weight: 900;
            color: #166534;
            text-transform: uppercase;
            font-size: 14px;
        }

        .data-label {
            font-size: 9px;
            text-transform: uppercase;
            font-weight: 900;
            color: #6b7280;
            margin-bottom: 2px;
        }

        .data-value {
            font-size: 11px;
            font-weight: 600;
            color: #111827;
            padding: 4px;
            border: 1px solid #f3f4f6;
            background: #f9fafb;
            margin-bottom: 10px;
            border-radius: 4px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }

        th {
            background: #f3f4f6;
            color: #4b5563;
            font-size: 8px;
            font-weight: 900;
            text-transform: uppercase;
            padding: 4px;
            text-align: left;
            border: 1px solid #e5e7eb;
        }

        td {
            padding: 4px;
            border: 1px solid #e5e7eb;
            font-size: 9px;
            color: #374151;
        }
    </style>
</head>

<body class="p-8 text-gray-900" onload="window.print()">

    <!-- Header -->
    <div class="flex justify-between items-end border-b-4 border-green-800 pb-6 mb-8">
        <div>
            <h1 class="text-2xl font-black text-green-900 uppercase">CHED Graduate Tracer Survey</h1>
            <p class="text-xs text-gray-500 italic mt-1 font-medium">Official Individual Response Record</p>
        </div>
        <div class="text-right">
            <p class="text-xs font-black text-gray-400 uppercase tracking-widest">Submitted On</p>
            <p class="text-lg font-bold">{{ $response->created_at->format('M d, Y') }}</p>
        </div>
    </div>

    <!-- Alumni Info Area -->
    <div class="grid grid-cols-2 gap-x-8 gap-y-2 mb-8 bg-gray-50 p-6 rounded-2xl border border-gray-100">
        <div>
            <p class="data-label">Full Name</p>
            <p class="text-lg font-black">{{ $response->user->name }}</p>
        </div>
        <div>
            <p class="data-label">Department / Course</p>
            <p class="text-lg font-black">{{ $response->department_name }}</p>
        </div>
        <div>
            <p class="data-label">Email Address</p>
            <p class="font-bold text-gray-600">{{ $response->user->email }}</p>
        </div>
        <div>
            <p class="data-label">Response ID</p>
            <p class="font-mono text-xs">{{ $response->id }}</p>
        </div>
    </div>

    <!-- Content -->
    <div class="space-y-4">
        {{-- Section A --}}
        <div class="section-header">Section A: General Information</div>
        <div class="grid grid-cols-2 gap-x-6">
            <div>
                <p class="data-label">Q1. Name</p>
                <p class="data-value">{{ $data['q1_name'] ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="data-label">Q2. Permanent Address</p>
                <p class="data-value">{{ $data['q2_address'] ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="data-label">Q6. Civil Status</p>
                <p class="data-value">{{ $data['q6_civil_status'] ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="data-label">Q7. Sex</p>
                <p class="data-value">{{ $data['q7_sex'] ?? 'N/A' }}</p>
            </div>
        </div>

        {{-- Section B --}}
        <div class="section-header">Section B: Educational Background</div>
        <p class="data-label">Q12. Educational Attainment</p>
        <table>
            <thead>
                <tr>
                    <th>Degree</th>
                    <th>Institution</th>
                    <th>Year</th>
                    <th>Honors</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data['q12'] ?? [] as $row)
                    @if(!empty($row['degree']))
                        <tr>
                            <td class="font-bold">{{ $row['degree'] }}</td>
                            <td>{{ $row['college'] }}</td>
                            <td>{{ $row['year'] }}</td>
                            <td>{{ $row['honors'] ?: 'N/A' }}</td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>

        {{-- Section D --}}
        <div class="section-header">Section D: Employment Data</div>
        <div class="grid grid-cols-2 gap-x-6">
            <div>
                <p class="data-label">Q16. Presently Employed?</p>
                <p class="data-value text-lg font-black text-green-700">{{ $data['q16_employed'] ?? 'N/A' }}</p>
            </div>
            @if(($data['q16_employed'] ?? '') === 'Yes')
                <div>
                    <p class="data-label">Q18. Employment Status</p>
                    <p class="data-value">{{ $data['q18_status'] ?? 'N/A' }}</p>
                </div>
                <div class="col-span-2">
                    <p class="data-label">Q19. Present Occupation</p>
                    <p class="data-value font-bold">{{ $data['q19_occupation'] ?? 'N/A' }}</p>
                </div>
            @else
                <div class="col-span-2">
                    <p class="data-label">Q17. Reasons for not being employed</p>
                    <div class="flex flex-wrap gap-2 mb-2">
                        @foreach($data['q17_reasons'] ?? [] as $r => $val)
                            <span class="text-[9px] bg-gray-100 px-2 py-0.5 rounded-full border border-gray-200">{{ $r }}</span>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        {{-- Section Suggestions --}}
        @if(!empty($data['q34_suggestions']))
            <div class="section-header">Suggestions</div>
            <p class="data-label">Q34. Suggestions to improve curriculum</p>
            <div class="data-value italic leading-relaxed">{{ $data['q34_suggestions'] }}</div>
        @endif
    </div>

    <!-- Final Footer -->
    <div
        class="mt-12 pt-4 border-t border-gray-100 text-[8px] text-gray-400 text-center uppercase tracking-widest no-print">
        End of Document &bull; Generated for Administrative Review Only
    </div>

</body>

</html>