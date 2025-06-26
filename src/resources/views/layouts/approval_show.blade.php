@extends('layouts.app')
@section('content')
<div>
  <h2>勤務詳細</h2>
  <div>
    <div>
      <p>名前   {{ $attendance->user->name }}</p>
    </div>
    <div>
      <p>日付   {{ $attendance->date->format('Y年   n月j日') }}</p>
    </div>

    <div>
      <p>出勤・退勤</p>
      <p>
        {{ $pendingRequest->modified_check_in ? substr($pendingRequest->modified_check_in, 0, 5) : ($attendance->check_in ? substr($attendance->check_in, 0, 5) : '-') }}
        ~
        {{ $pendingRequest->modified_check_out ? substr($pendingRequest->modified_check_out, 0, 5) : ($attendance->check_out ? substr($attendance->check_out, 0, 5) : '-') }}
      </p>
    </div>

    <div>
      @if($pendingRequest->modified_breaks)
        <p>休憩</p>
        @foreach($pendingRequest->modified_breaks as $index => $break)
          <p>休憩{{ $index + 1 }}: {{ $break['start_time'] ?? '-' }} ~ {{ $break['end_time'] ?? '-' }}</p>
        @endforeach
      @endif
    </div>

    <div>
      <p>備考</p>
      <p>{{ $pendingRequest->modified_note ?? '-' }}</p>
    </div>
  </div>
</div>
@endsection