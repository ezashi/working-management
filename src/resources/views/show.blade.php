@extends('layouts.app')
@section('content')
<div>
  <h2>勤務詳細</h2>
  @if($hasPendingRequest)
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
        @else
          <p>休憩</p>
          @if($attendance->breaks && $attendance->breaks->count() > 0)
            @foreach($attendance->breaks as $index => $break)
              <p>休憩{{ $index + 1 }}: {{ $break->start_time ? substr($break->start_time, 0, 5) : '-' }} ~ {{ $break->end_time ? substr($break->end_time, 0, 5) : '-' }}</p>
            @endforeach
          @else
            <p>-</p>
          @endif
        @endif
      </div>

      <div>
        <p>備考</p>
        <p>{{ $pendingRequest->modified_note ?? ($attendance->note ?? '-') }}</p>
      </div>
    </div>

    <div>
      <p>*承認待ちのため修正はできません。</p>
    </div>
  @else
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
          <input type="time" name="check_out" id="check_out" value="{{ old('check_out', $attendance->check_out ? substr($attendance->check_out, 0, 5) : '') }}">
          @error('check_in')
            <div class="error-message">{{ $message }}</div>
          @enderror
          @error('check_out')
            <div class="error-message">{{ $message }}</div>
          @enderror
        </div>
        <div>
          <div>
            @foreach($attendance->breaks as $index => $break)
              <p>休憩{{ $index + 1 }}</p>
              <input type="time" name="breaks[{{ $index }}][start_time]" value="{{ old('breaks.'.$index.'.start_time', $break->start_time ? substr($break->start_time, 0, 5) : '') }}">
              ~
              <input type="time" name="breaks[{{ $index }}][end_time]" value="{{ old('breaks.'.$index.'.end_time', $break->end_time ? substr($break->end_time, 0, 5) : '') }}">
              @error('breaks')
                <div class="error-message">{{ $message }}</div>
              @enderror
            @endforeach
          </div>

          <div>
            @php
              $newIndex = count($attendance->breaks);
            @endphp
            <div>
              <p>休憩{{ $newIndex + 1 }}</p>
              <input type="time" name="breaks[{{ $newIndex }}][start_time]" value="{{ old('breaks.'.$newIndex.'.start_time', '') }}">
              ~
              <input type="time" name="breaks[{{ $newIndex }}][end_time]" value="{{ old('breaks.'.$newIndex.'.end_time', '') }}">
              @error('breaks')
                <div class="error-message">{{ $message }}</div>
              @enderror
            </div>
          </div>
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
        <button type="submit">修正</button>
      </div>
    </form>
  @endif
</div>
@endsection