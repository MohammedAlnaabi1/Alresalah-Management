<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <title>التقرير المالي - أكاديمية الرسالة</title>
  <style>

    @font-face {
    font-family: 'Cairo';
    src: url('{{ public_path('fonts/Cairo-Regular.ttf') }}') format('truetype');
  }

  body {
    font-family: 'Cairo', DejaVu Sans, sans-serif;
    direction: rtl;
    text-align: right;
  }

  table {
    width: 100%;
    border-collapse: collapse;
  }

  th, td {
    border: 1px solid #ccc;
    padding: 8px;
  }

  th {
    background-color: #f2f2f2;
  }
    body {
      font-family: DejaVu Sans, sans-serif;
      direction: rtl;
      text-align: center;
      color: #333;
      margin: 30px;
    }

    header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      border-bottom: 2px solid #0d6efd;
      padding-bottom: 10px;
      margin-bottom: 20px;
    }

    header img {
      width: 90px;
    }

    header h2 {
      flex-grow: 1;
      font-size: 22px;
      color: #0d6efd;
      margin: 0;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 15px;
    }

    th, td {
      border: 1px solid #ddd;
      padding: 8px;
      font-size: 13px;
    }

    th {
      background-color: #f1f1f1;
      color: #000;
    }

    tr:nth-child(even) {
      background-color: #fafafa;
    }

    h3 {
      color: #0d6efd;
      margin-top: 25px;
    }

    footer {
      position: fixed;
      bottom: 10px;
      left: 0;
      right: 0;
      text-align: center;
      font-size: 12px;
      color: #888;
    }
  </style>
</head>

<body>

  {{-- ✅ الترويسة --}}
  <header>
    <img src="{{ public_path('images/logo.png') }}" alt="شعار الأكاديمية">
    <h2>أكاديمية الرسالة</h2>
    <div style="width:90px;"></div> {{-- لتوازن التصميم --}}
  </header>

  {{-- ✅ عنوان التقرير --}}
  <h3>📊 التقرير المالي للفترة من {{ $from }} إلى {{ $to }}</h3>
  <p style="font-size: 14px; color: #666;">
    نوع التقرير: 
    @if($type == 'all') شامل (إيرادات ومصروفات)
    @elseif($type == 'revenues') إيرادات فقط
    @else مصروفات فقط
    @endif
  </p>

  {{-- ✅ الجدول --}}
  <table>
    <thead>
      <tr>
        <th>#</th>
        <th>النوع</th>
        <th>الوصف</th>
        <th>المبلغ (ر.ع)</th>
        <th>التاريخ</th>
      </tr>
    </thead>
    <tbody>
      @foreach($transactions as $index => $t)
        <tr>
          <td>{{ $index + 1 }}</td>
          <td>{{ $t['type'] }}</td>
          <td>{{ $t['name'] }}</td>
          <td>{{ number_format($t['amount'], 2) }}</td>
          <td>{{ \Carbon\Carbon::parse($t['date'])->format('Y-m-d') }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>

  {{-- ✅ الإجماليات --}}
  <div style="margin-top: 25px; text-align: right;">
    <p><strong>💰 إجمالي الإيرادات:</strong> {{ number_format($totalRevenues, 2) }} ر.ع</p>
    <p><strong>💸 إجمالي المصروفات:</strong> {{ number_format($totalExpenses, 2) }} ر.ع</p>
    <p><strong>📈 صافي الربح:</strong> {{ number_format($netBalance, 2) }} ر.ع</p>
  </div>

  {{-- ✅ التذييل --}}
  <footer>
    <p>نظام إدارة الأكاديمية | {{ now()->format('Y-m-d') }}</p>
  </footer>

</body>
</html>
