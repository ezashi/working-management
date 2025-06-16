@extends('layouts.app')
@section('content')
<div>
  <h2>勤務詳細</h2>
  @if($hasPendingRequest)
    <div>*承認待ちのため修正はできません。</div>
  @endif
  <form action="{{ route('attendances.update', $attendance }}" method="post">
    @csrf
    @method('put')
    <div>
      <div>
        <h3>名前</h3>
        <p>{{ $attendance->user->name }}</p>
      </div>
      <div>
        <h3>日付</h3>
        <p>{{ $attendance->date->format('Y年   m月d日') }}</p>
      </div>
    </div>

    <div>
      <div>
        <h3>出勤・退勤</h3>
        <input type="time" name="check_in" id="check_in" value="{{ old('check_in', $attendance->check_in) }}">
        @error('check_in')
          <div class="error-message">{{ $message }}</div>
        @enderror
        ~
        <input type="time" name="check_out" id="check_out" value="{{ old('check_out', $attendance->check_out) }}">
        @error('check_out')
          <div class="error-message">{{ $message }}</div>
        @enderror
      </div>
      <div>
        @foreach($attendance->breaks as $index => $break)
          <h3>休憩{{ $index + 1 }}</h3>
          <input type="time" name="breaks[{{ $index }}][start_time]" value="{{ old('breaks.'.$index.'.start_time', $break->start_time) }}">
          @error('breaks.'.$index.'.start_time')
            <div class="error-message">{{ $message }}</div>
          @enderror
          ~
          <input type="time" name="breaks[{{ $index }}][end_time]" value="{{ old('breaks.'.$index.'.end_time', $break->end_time) }}">
          @error('breaks.'.$index.'.end_time')
            <div class="error-message">{{ $message }}</div>
          @enderror
        @endforeach
      </div>
      <div>
        <h3>備考</h3>
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