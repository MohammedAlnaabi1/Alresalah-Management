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
  <h2>تقرير بيانات الطلاب</h2>
  <table>
    <thead>
      <tr>
        <th>#</th>
        <th>الاسم</th>
        <th>الصف</th>
        <th>الفصل</th>
        <th>الجنس</th>
        <th>تاريخ الميلاد</th>
        <th>ولي الأمر</th>
        <th>الهاتف</th>
        <th>الحالة</th>
      </tr>
    </thead>
    <tbody>
      @forelse($students as $index => $s)
        <tr>
          <td>{{ $index + 1 }}</td>
          <td>{{ $s->name }}</td>
          <td>{{ $s->grade }}</td>
          <td>{{ $s->class_name }}</td>
          <td>{{ $s->gender }}</td>
          <td>{{ $s->date_of_birth->format('Y-m-d') }}</td>
          <td>{{ $s->parent_name }}</td>
          <td>{{ $s->phone }}</td>
          <td>{{ $s->status }}</td>
        </tr>
      @empty
        <tr><td colspan="9">لا توجد بيانات</td></tr>
      @endforelse
    </tbody>
  </table>
  <div class="footer">تم التصدير بتاريخ {{ now()->format('Y-m-d H:i') }} — مدرسة الرسالة</div>
</body>
</html>
