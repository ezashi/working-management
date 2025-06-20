@extends('layouts.admin')
@section('content')
<div>
  <h2>{{ $user->name }}さんの勤怠一覧</h2>
  <div>
    <a href="{{ route('admin.list', ['id' => $user->id, 'month' => $prevMonth]) }}">←前月</a>
    <span>{{ $date->format('Y/m') }}</span>
    <a href="{{ route('admin.list', ['id' => $user->id, 'month' => $nextMonth]) }}">翌月→</a>
  </div>
  <div>
    <table>
      <thead>
        <tr>
          <th>日付</th>
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
            <td>{{ $attendance->date->format('m/d') }}</td>
            <td>{{ $attendance->check_in ? substr($attendance->check_in, 0, 5) : '' }}</td>
            <td>{{ $attendance->check_out ? substr($attendance->check_out, 0, 5) : '' }}</td>
            <td>{{ $attendance->totalBreakTime() ? gmdate('H:i', $attendance->totalBreakTime() * 60) : '' }}</td>
            <td>{{ $attendance->workingHours() ? gmdate('H:i', floor($attendance->workingHours()) * 60) : '' }}</td>
            <td>
              <a href="{{ route('admin.show', $attendance->id) }}">詳細</a>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endsection