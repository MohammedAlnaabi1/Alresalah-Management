<html dir="rtl">
<head>
  <style>
    body { font-family: 'XBRiyaz', sans-serif; direction: rtl; }
    h2 { text-align: center; color: #f57c00; margin-bottom: 5px; }
    h4 { text-align: center; color: #555; margin-bottom: 20px; }
    table { width: 100%; border-collapse: collapse; margin-top: 10px; }
    th { background-color: #f57c00; color: #fff; padding: 8px; text-align: center; }
    td { padding: 6px 8px; text-align: center; border: 1px solid #ddd; }
    tr:nth-child(even) { background-color: #f9f9f9; }
    .present { color: green; font-weight: bold; }
    .absent { color: red; font-weight: bold; }
    .late { color: orange; font-weight: bold; }
    .footer { text-align: center; margin-top: 20px; font-size: 12px; color: #888; }
  </style>
</head>
<body>
  <h2>تقرير الحضور والغياب</h2>
  <h4>التاريخ: {{ $date }}</h4>
  <table>
    <thead>
      <tr>
        <th>#</th>
        <th>اسم الطالب</th>
        <th>الصف</th>
        <th>الفصل</th>
        <th>الحالة</th>
        <th>ملاحظات</th>
      </tr>
    </thead>
    <tbody>
      @forelse($attendances as $index => $a)
        <tr>
          <td>{{ $index + 1 }}</td>
          <td>{{ $a->student->name ?? '-' }}</td>
          <td>{{ $a->student->grade ?? '-' }}</td>
          <td>{{ $a->student->class_name ?? '-' }}</td>
          <td class="{{ $a->status == 'حاضر' ? 'present' : ($a->status == 'غائب' ? 'absent' : 'late') }}">
            {{ $a->status }}
          </td>
          <td>{{ $a->notes ?? '-' }}</td>
        </tr>
      @empty
        <tr><td colspan="6">لا توجد بيانات حضور لهذا اليوم</td></tr>
      @endforelse
    </tbody>
  </table>
  <div class="footer">تم التصدير بتاريخ {{ now()->format('Y-m-d H:i') }} — مدرسة الرسالة</div>
</body>
</html>
