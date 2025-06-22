@extends('layouts.admin')
@section('content')
<div>
  <h2>勤怠詳細</h2>
  <form action="{{ route('modification.request.approval', ['attendance_correct_request' => $modificationRequest->id]) }}" method="POST">
    @csrf
    <div>
      <h3>申請情報</h3>
      <div>
        <h3>名前</h3>
        <p>{{ $modificationRequest->user->name }}</p>
      </div>
      <div>
        <h3>日付</h3>
        <p>{{ $modificationRequest->attendance->date->format('Y年   m月d日') }}</p>
      </div>
      <div>
        <h3>出勤・退勤</h3>
        <p>{{ $modificationRequest->modified_check_in ?? '-' }} ~ {{ $modificationRequest->attendance->check_out ?? '-' }}</p>
      </div>
      <div>
        <h3>休憩</h3>
        <p>
        @if($modificationRequest->modified_breaks)
            @foreach($modificationRequest->modified_breaks as $index => $break)
              休憩{{ $index + 1 }}: {{ $break['start_time'] }} ~ {{ $break['end_time'] ?? '未終了' }}<br>
            @endforeach
          @else
            -
          @endif
        </p>
      </div>
      <div>
        <h3>備考</h3>
        <p>{{ $modificationRequest->modified_note ?? '-' }}</p>
      </div>
    </div>
    @if($hasPendingRequest)
      <p>承認済み</p>
    @else
      <button type="submit">承認</button>
    @endif
  </form>
</div>
@endsection