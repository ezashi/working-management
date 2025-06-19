@extends('layouts.admin')
@section('content')
<div>
  <h2>{{ $targetDate->format('Y年m月d日') }}の勤怠</h2>
  <div>
    <a href="{{ route('admin.attendance', ['date' => $prevDate]) }}">←前日</a>
    <span>{{ $targetDate->format('Y/m/d') }}</span>
    <a href="{{ route('admin.attendance', ['date' => $nextDate]) }}">翌日→</a>
  </div>
  <div>
    <table>
      <thead>
        <tr>
          <th>名前</th>
          <th>出勤</th>
          <th>退勤</th>
          <th>休憩</th>
          <th>合計</th>
          <th>詳細</th>
        </tr>
      </thead>
      <tbody>
        @foreach($attendances as $attendance)
          <tr>
          <td>{{ $attendance->user->name }}</td>
            <td>{{ $attendance->check_in ? substr($attendance->check_in, 0, 5) : '-' }}</td>
            <td>{{ $attendance->check_out ? substr($attendance->check_out, 0, 5) : '-' }}</td>
            <td>{{ $attendance->totalBreakTime() ? gmdate('H:i', $attendance->totalBreakTime() * 60) : '-' }}</td>
            <td>{{ $attendance->workingHours() ? gmdate('H:i', floor($attendance->workingHours()) * 60) : '-' }}</td>
            <td>
              <a href="{{ route('admin.show', $attendance) }}">詳細</a>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endsection