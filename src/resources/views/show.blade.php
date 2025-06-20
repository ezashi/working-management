@extends('layouts.app')
@section('content')
<div>
  <h2>勤務詳細</h2>
  <form action="{{ route('attendance.update', $attendance->id) }}" method="post">
    @csrf
    @method('put')
    <div>
      <div>
        <p>名前   {{ $attendance->user->name }}</p>
      </div>
      <div>
        <p>日付   {{ $attendance->date->format('Y年   n月j日') }}</p>
      </div>
    </div>

    <div>
      <div>
        <p>出勤・退勤</p>
<input type="time" name="check_in" id="check_in" value="{{ old('check_in', $attendance->check_in ? substr($attendance->check_in, 0, 5) : '') }}">
        ~
        <input type="time" name="check_out" id="check_out" value="{{ old('check_out', $attendance->check_out ? substr($attendance->check_in, 0, 5) : '') }}">
        @error('check_in')
          <div class="error-message">{{ $message }}</div>
        @enderror
        @error('check_out')
          <div class="error-message">{{ $message }}</div>
        @enderror
      </div>
      <div>
        @foreach($attendance->breaks as $index => $break)
          <p>休憩{{ $index + 1 }}</p>
          <input type="time" name="breaks[{{ $index }}][start_time]" value="{{ old('breaks.'.$index.'.start_time', $break->start_time ? substr($attendance->check_in, 0, 5) : '') }}">
          ~
          <input type="time" name="breaks[{{ $index }}][end_time]" value="{{ old('breaks.'.$index.'.end_time', $break->end_time ? substr($attendance->check_in, 0, 5) : '') }}">
          @error('breaks.'.$index.'.start_time')
            <div class="error-message">{{ $message }}</div>
          @enderror
          @error('breaks.'.$index.'.end_time')
            <div class="error-message">{{ $message }}</div>
          @enderror
        @endforeach
      </div>
      <div>
        <p>備考</p>
        <textarea name="note" id="note">{{ old('note', $attendance->note) }}</textarea>
        @error('note')
          <div class="error-message">{{ $message }}</div>
        @enderror
      </div>
    </div>
    <div>
      @if($hasPendingRequest)
        <p>*承認待ちのため修正はできません。</p>
      @else
        <button type="submit">修正</button>
      @endif
    </div>
  </form>
</div>
@endsection