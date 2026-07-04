<html dir="rtl">
<head>
  <style>
    body { font-family: 'XBRiyaz', sans-serif; direction: rtl; }
    h2 { text-align: center; color: #f57c00; margin-bottom: 20px; }
    table { width: 100%; border-collapse: collapse; margin-top: 10px; }
    th { background-color: #f57c00; color: #fff; padding: 8px; text-align: center; }
    td { padding: 6px 8px; text-align: center; border: 1px solid #ddd; }
    tr:nth-child(even) { background-color: #f9f9f9; }
    .footer { text-align: center; margin-top: 20px; font-size: 12px; color: #888; }
  </style>
</head>
<body>
  <h2>تقرير الدرجات</h2>
  <table>
    <thead>
      <tr>
        <th>#</th>
        <th>اسم الطالب</th>
        <th>المادة</th>
        <th>الدرجة</th>
        <th>من</th>
        <th>النسبة %</th>
        <th>الفصل الدراسي</th>
        <th>العام الدراسي</th>
      </tr>
    </thead>
    <tbody>
      @forelse($grades as $index => $g)
        @php $pct = $g->max_grade > 0 ? round(($g->grade_value / $g->max_grade) * 100, 1) : 0; @endphp
        <tr>
          <td>{{ $index + 1 }}</td>
          <td>{{ $g->student->name ?? '-' }}</td>
          <td>{{ $g->subject }}</td>
          <td>{{ $g->grade_value }}</td>
          <td>{{ $g->max_grade }}</td>
          <td>{{ $pct }}%</td>
          <td>{{ $g->semester }}</td>
          <td>{{ $g->academic_year }}</td>
        </tr>
      @empty
        <tr><td colspan="8">لا توجد بيانات</td></tr>
      @endforelse
    </tbody>
  </table>
  <div class="footer">تم التصدير بتاريخ {{ now()->format('Y-m-d H:i') }} — مدرسة الرسالة</div>
</body>
</html>
