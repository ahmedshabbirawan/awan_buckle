<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $voucherTitle }} - {{ $payment->voucher_no }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333;
            margin: 0;
            padding: 20px;
            background: #fff;
            font-size: 14px;
        }
        .voucher-container {
            max-width: 720px;
            margin: 0 auto;
            border: 2px solid #333;
            padding: 25px;
            border-radius: 6px;
        }
        .header {
            text-align: center;
            border-bottom: 2px dashed #444;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .company-name {
            font-size: 24px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .voucher-title {
            display: inline-block;
            margin-top: 10px;
            padding: 5px 20px;
            background-color: #333;
            color: #fff;
            font-size: 16px;
            font-weight: bold;
            border-radius: 4px;
            letter-spacing: 1px;
        }
        .meta-table, .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .meta-table td {
            padding: 6px;
            vertical-align: top;
        }
        .details-table th, .details-table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        .details-table th {
            background-color: #f7f7f7;
            font-weight: bold;
        }
        .amount-box {
            background-color: #f0fdf4;
            border: 1px solid #bbf7d0;
            padding: 12px;
            text-align: right;
            font-size: 18px;
            font-weight: bold;
            color: #166534;
            margin-bottom: 25px;
            border-radius: 4px;
        }
        .signatures {
            margin-top: 60px;
            display: flex;
            justify-content: space-between;
        }
        .signature-line {
            width: 28%;
            border-top: 1px solid #555;
            text-align: center;
            padding-top: 5px;
            font-size: 12px;
            color: #555;
        }
        .no-print-bar {
            text-align: center;
            margin-bottom: 20px;
        }
        .btn-print {
            padding: 8px 24px;
            font-size: 14px;
            background-color: #2563eb;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        @media print {
            .no-print-bar { display: none; }
            body { padding: 0; }
            .voucher-container { border: 1px solid #000; }
        }
    </style>
</head>
<body>
    <div class="no-print-bar">
        <button class="btn-print" onclick="window.print();">Print Voucher / Receipt</button>
    </div>

    <div class="voucher-container">
        <div class="header">
            <div class="company-name">AWAN BUCKLE</div>
            <div style="font-size: 13px; color: #666; margin-top: 3px;">Specialized Retail POS & Double-Entry Financial Engine</div>
            <div class="voucher-title">{{ $voucherTitle }}</div>
        </div>

        <table class="meta-table">
            <tr>
                <td style="width: 50%;">
                    <strong>Voucher No:</strong> <span style="font-family: monospace; font-size: 15px; font-weight: bold;">{{ $payment->voucher_no }}</span><br>
                    <strong>Date:</strong> {{ date('d-m-Y', strtotime($payment->payment_date)) }}<br>
                    <strong>Shop / Branch:</strong> {{ optional($payment->shop)->name ?? 'Main Branch' }}
                </td>
                <td style="width: 50%; text-align: right;">
                    <strong>{{ $partyType }}:</strong> {{ optional($party)->name ?? 'N/A' }}<br>
                    <strong>Contact:</strong> {{ optional($party)->mobile ?? (optional($party)->phone ?? 'N/A') }}<br>
                    <strong>Payment Method:</strong> {{ strtoupper($payment->payment_method) }}
                </td>
            </tr>
        </table>

        <table class="details-table">
            <thead>
                <tr>
                    <th style="width: 60%;">Description / Purpose</th>
                    <th style="width: 40%; text-align: right;">Amount (PKR)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        {{ $payment->payment_type === 'receipt' ? 'Settlement received from customer' : 'Payment disbursed to supplier' }}
                        @if($payment->reference_no)
                            <br><small style="color: #666;">Reference / Cheque: {{ $payment->reference_no }}</small>
                        @endif
                        @if($payment->notes)
                            <br><small style="color: #666;">Remarks: {{ $payment->notes }}</small>
                        @endif
                    </td>
                    <td style="text-align: right; font-weight: bold; font-size: 16px;">
                        PKR {{ number_format($payment->amount, 2) }}
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="amount-box">
            Total {{ $payment->payment_type === 'receipt' ? 'Received' : 'Paid' }}: PKR {{ number_format($payment->amount, 2) }}
        </div>

        <table style="width: 100%; margin-top: 20px; font-size: 12px; color: #777;">
            <tr>
                <td>Generated By: {{ optional($payment->creator)->name ?? 'System' }}</td>
                <td style="text-align: right;">Timestamp: {{ $payment->created_at->format('d-m-Y h:i A') }}</td>
            </tr>
        </table>

        <div class="signatures">
            <div class="signature-line">Prepared By</div>
            <div class="signature-line">Authorized Signature</div>
            <div class="signature-line">{{ $payment->payment_type === 'receipt' ? 'Customer Signature' : 'Receiver Signature' }}</div>
        </div>
    </div>

    <script>
        window.onload = function() {
            // Auto trigger print when requested
            if (window.location.search.indexOf('auto=1') !== -1) {
                window.print();
            }
        };
    </script>
</body>
</html>
