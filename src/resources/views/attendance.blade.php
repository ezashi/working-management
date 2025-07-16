@extends('layouts.app')
@section('content')
<div>
  <span class="status">
    @if($currentStatus === 'not_working')
      勤務外
    @elseif($currentStatus === 'working')
      出勤中
    @elseif($currentStatus === 'break')
      休憩中
    @elseif($currentStatus === 'finished')
      退勤済
    @endif
  </span>
  <p>
    <span id="current-day" class="day">{{ $currentDay }}</span>
    <br>
    <span id="current-time" class="time">{{ $currentTime }}</span>
  </p>
  @if($currentStatus === 'not_working')
    <form method="POST" action="{{ route('attendance.check-in') }}">
      @csrf
      <button type="submit">
        出勤
      </button>
    </form>
  @endif
  @if($currentStatus === 'working')
    <form method="POST" action="{{ route('attendance.check-out') }}">
      @csrf
      <button type="submit">
        退勤
      </button>
    </form>
    <form method="POST" action="{{ route('attendance.break-start') }}">
      @csrf
      <button type="submit">
        休憩入
      </button>
    </form>
  @endif
  @if($currentStatus === 'break')
    <form method="POST" action="{{ route('attendance.break-end') }}">
      @csrf
      <button type="submit">
        休憩戻
      </button>
    </form>
  @endif
  @if($currentStatus === 'finished')
    お疲れ様でした。
  @endif
</div>
@endsection