@extends('layouts.admin')
@section('content')
<div>
  <h2>勤怠詳細</h2>
  <form action="{{ route('modification.request.approval', ['attendance_correct_request' => $modificationRequest->id]) }}" method="POST">
    @csrf
    <div>
      <div>
        <p>
          名前
          {{ $modificationRequest->user->name }}
        </p>
      </div>
      <div>
        <p>
          日付
          {{ $modificationRequest->attendance->date->format('Y年   m月d日') }}
        </p>
      </div>
      <div>
        <p>
          出勤・退勤
          {{ $modificationRequest->modified_check_in ? substr($modificationRequest->modified_check_in, 0, 5) : '-' }}
          ~
          {{ $modificationRequest->modified_check_out ? substr($modificationRequest->modified_check_out, 0, 5) : '-' }}
        </p>
      </div>
      <div>
        <p>
          @if($modificationRequest->modified_breaks)
            @foreach($modificationRequest->modified_breaks as $index => $break)
              休憩{{ $index + 1 }}  {{ $break['start_time'] }} ~ {{ $break['end_time'] ?? '未終了' }}<br>
            @endforeach
          @endif
        </p>
      </div>
      <div>
        <p>
          備考
          {{ $modificationRequest->modified_note ?? '-' }}
        </p>
      </div>
    </div>
    @if($modificationRequest->status === 'approval' || session('approved'))
      <p>承認済み</p>
    @else
      <button type="submit">承認</button>
    @endif
  </form>
</div>
@endsection