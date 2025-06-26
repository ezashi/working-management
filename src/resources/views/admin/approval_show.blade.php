@extends('layouts.admin')
@section('content')
<div>
  <h2>勤務詳細</h2>
  <div>
    <div>
      <p>名前   {{ $modificationRequest->attendance->user->name }}</p>
    </div>
    <div>
      <p>日付   {{ $modificationRequest->attendance->date->format('Y年   n月j日') }}</p>
    </div>

    <div>
      <p>出勤・退勤</p>
      <p>
        {{ $modificationRequest->modified_check_in ? substr($modificationRequest->modified_check_in, 0, 5) : '-' }}
        ~
        {{ $modificationRequest->modified_check_out ? substr($modificationRequest->modified_check_out, 0, 5) : '-' }}
      </p>
    </div>

    <div>
      @if($modificationRequest->modified_breaks)
        <p>休憩</p>
        @foreach($modificationRequest->modified_breaks as $index => $break)
          <p>休憩{{ $index + 1 }}: {{ $break['start_time'] ?? '-' }} ~ {{ $break['end_time'] ?? '-' }}</p>
        @endforeach
      @else
        <p>休憩</p>
        <p>-</p>
      @endif
    </div>

    <div>
      <p>備考</p>
      <p>{{ $modificationRequest->modified_note ?? '-' }}</p>
    </div>
  </div>
</div>
@endsection