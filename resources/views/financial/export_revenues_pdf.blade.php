<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
  <title>تقرير الإيرادات - مدرسة الرسالة</title>
  <style>
    body {
      font-family: DejaVu Sans, sans-serif;
      direction: rtl;
      text-align: right;
      color: #333;
      margin: 30px;
    }
    header {
      text-align: center;
      border-bottom: 2px solid #198754;
      padding-bottom: 10px;
      margin-bottom: 20px;
    }
    header h2 {
      font-size: 22px;
      color: #198754;
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
      text-align: center;
    }
    th {
      background-color: #d4edda;
      color: #000;
    }
    tr:nth-child(even) {
      background-color: #fafafa;
    }
    h3 {
      color: #198754;
      margin-top: 25px;
      text-align: center;
    }
    .summary {
      margin-top: 25px;
      text-align: right;
    }
    .summary p {
      margin: 5px 0;
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

  <header>
    <h2>مدرسة الرسالة</h2>
  </header>

  <h3>تقرير الإيرادات</h3>

  <table>
    <thead>
      <tr>
        <th>#</th>
        <th>المصدر</th>
        <th>النوع</th>
        <th>المبلغ (ر.ع)</th>
        <th>التاريخ</th>
        <th>ملاحظات</th>
      </tr>
    </thead>
    <tbody>
      @forelse($revenues as $index => $rev)
        <tr>
          <td>{{ $index + 1 }}</td>
          <td>{{ $rev->source }}</td>
          <td>{{ $rev->type }}</td>
          <td>{{ number_format($rev->amount, 3) }}</td>
          <td>{{ $rev->date }}</td>
          <td>{{ $rev->notes ?? '-' }}</td>
        </tr>
      @empty
        <tr>
          <td colspan="6">لا توجد بيانات</td>
        </tr>
      @endforelse
    </tbody>
  </table>

  <div class="summary">
    <p><strong>إجمالي الإيرادات:</strong> {{ number_format($revenues->sum('amount'), 3) }} ر.ع</p>
    <p><strong>عدد السجلات:</strong> {{ $revenues->count() }}</p>
  </div>

  <footer>
    <p>نظام إدارة مدرسة الرسالة | {{ now()->format('Y-m-d') }}</p>
  </footer>

</body>
</html>
