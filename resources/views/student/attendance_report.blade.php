@extends('student.layout')

@section('title', 'سجل الحضور - كل الأيام')

@section('content')

{{-- Filter Bar --}}
<div class="card p-3 mb-4 border-0 shadow-sm">
  <form method="GET" action="{{ route('student.attendance.report') }}" class="row g-2 align-items-end">
    <div class="col-md-2">
      <label class="form-label">من تاريخ</label>
      <input type="date" name="from_date" class="form-control" value="{{ $fromDate }}">
    </div>
    <div class="col-md-2">
      <label class="form-label">إلى تاريخ</label>
      <input type="date" name="to_date" class="form-control" value="{{ $toDate }}">
    </div>
    <div class="col-md-2">
      <label class="form-label">المجموعة</label>
      <select name="group" class="form-select">
        <option value="">الكل</option>
        @foreach($groups as $g)
          <option value="{{ $g }}" {{ ($group ?? '') == $g ? 'selected' : '' }}>{{ $g }}</option>
        @endforeach
      </select>
    </div>
    <div class="col-md-2">
      <label class="form-label">المادة</label>
      <select name="subject" class="form-select">
        <option value="">كل المواد</option>
        @foreach($subjects as $s)
          <option value="{{ $s }}" {{ ($subject ?? '') == $s ? 'selected' : '' }}>{{ $s }}</option>
        @endforeach
      </select>
    </div>
    <div class="col-md-2">
      <button class="btn btn-warning w-100"><i class="bi bi-filter me-1"></i> عرض</button>
    </div>
    <div class="col-md-2">
      <a href="{{ route('student.attendance') }}" class="btn btn-outline-secondary w-100">
        <i class="bi bi-arrow-right me-1"></i> العودة
      </a>
    </div>
  </form>
</div>

{{-- Summary --}}
@php
  $totalPresent = collect($report)->sum('present');
  $totalAbsent = collect($report)->sum('absent');
  $totalLate = collect($report)->sum('late');
@endphp
<div class="row text-center mb-4">
  <div class="col-md-3 mb-2">
    <div class="card p-2 border-0 shadow-sm bg-primary bg-opacity-10">
      <strong class="text-primary">الأيام: {{ $dates->count() }}</strong>
    </div>
  </div>
  <div class="col-md-3 mb-2">
    <div class="card p-2 border-0 shadow-sm bg-success bg-opacity-10">
      <strong class="text-success">حاضر: {{ $totalPresent }}</strong>
    </div>
  </div>
  <div class="col-md-3 mb-2">
    <div class="card p-2 border-0 shadow-sm bg-danger bg-opacity-10">
      <strong class="text-danger">غائب: {{ $totalAbsent }}</strong>
    </div>
  </div>
  <div class="col-md-3 mb-2">
    <div class="card p-2 border-0 shadow-sm bg-warning bg-opacity-10">
      <strong class="text-warning">متأخر: {{ $totalLate }}</strong>
    </div>
  </div>
</div>

{{-- Report Table --}}
<div class="card border-0 shadow-sm">
  <div class="table-responsive">
    <table class="table table-bordered table-hover align-middle text-center mb-0" style="font-size: 0.85rem;">
      <thead>
        <tr>
          <th class="sticky-col">#</th>
          <th class="sticky-col">الطالب</th>
          <th>المجموعة</th>
          @foreach($dates as $d)
            <th style="white-space: nowrap; min-width: 60px;">{{ \Carbon\Carbon::parse($d)->format('m/d') }}</th>
          @endforeach
          <th class="table-success">حاضر</th>
          <th class="table-danger">غائب</th>
          <th class="table-warning">متأخر</th>
          <th class="table-info">النسبة</th>
        </tr>
      </thead>
      <tbody>
        @forelse($report as $index => $r)
          <tr>
            <td>{{ $index + 1 }}</td>
            <td class="fw-bold text-nowrap">{{ $r['student']->name }}</td>
            <td><span class="badge bg-secondary">{{ $r['student']->grade }}</span></td>
            @foreach($dates as $d)
              <td>
                @if($r['daily'][$d] == 'حاضر')
                  <span class="text-success">✓</span>
                @elseif($r['daily'][$d] == 'غائب')
                  <span class="text-danger">✗</span>
                @elseif($r['daily'][$d] == 'متأخر')
                  <span class="text-warning">●</span>
                @elseif($r['daily'][$d] == 'جزئي')
                  <span class="text-info">◐</span>
                @else
                  <span class="text-muted">-</span>
                @endif
              </td>
            @endforeach
            <td class="table-success fw-bold">{{ $r['present'] }}</td>
            <td class="table-danger fw-bold">{{ $r['absent'] }}</td>
            <td class="table-warning fw-bold">{{ $r['late'] }}</td>
            <td>
              <span class="badge {{ $r['rate'] >= 90 ? 'bg-success' : ($r['rate'] >= 70 ? 'bg-primary' : ($r['rate'] >= 50 ? 'bg-warning' : 'bg-danger')) }}">
                {{ $r['rate'] }}%
              </span>
            </td>
          </tr>
        @empty
          <tr><td colspan="{{ 5 + $dates->count() }}" class="text-muted py-4">لا توجد بيانات حضور في هذه الفترة</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

{{-- Legend --}}
<div class="mt-3 text-center text-muted">
  <span class="me-3"><span class="text-success">✓</span> حاضر</span>
  <span class="me-3"><span class="text-danger">✗</span> غائب</span>
  <span class="me-3"><span class="text-warning">●</span> متأخر</span>
  <span class="me-3"><span class="text-info">◐</span> حضور جزئي</span>
  <span><span class="text-muted">-</span> لم يسجل</span>
</div>

@endsection
