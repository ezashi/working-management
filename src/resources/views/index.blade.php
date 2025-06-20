@extends('layouts.app')
@section('content')
<div>
  <h2>申請一覧</h2>
</div>
<div>
  <div>
    <h3>申請待ち</h3>
    @if($pendingRequests->isEmpty())
      <p>承認待ちの申請はありません。</p>
    @else
      <table>
        <thead>
          <tr>
            <th>状態</th>
            <th>名前</th>
            <th>対象日時</th>
            <th>申請理由</th>
            <th>申請日時</th>
            <th>詳細</th>
          </tr>
        </thead>
        <tbody>
          @foreach($correctionRequests as $request)
          <tr>
            <td>
              <span>承認待ち</span>
            </td>
            <td>{{ $request->user->name }}</td>
            <td>{{ $request->attendance->date->format('Y/m/d') }}</td>
            <td>{{ $request->note }}</td>
            <td>{{ $request->created_at->format('Y/m/d') }}</td>
            <td>
              <a href="{{ route('attendance.show', $request->attendance) }}">詳細</a>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    @endif
  </div>
  <div>
    <h3>承認済み</h3>
    @if($approvalRequests->isEmpty())
      <p>承認済みの申請はありません。</p>
    @else
      <table>
        <thead>
          <tr>
            <th>状態</th>
            <th>名前</th>
            <th>対象日時</th>
            <th>申請理由</th>
            <th>申請日時</th>
            <th>詳細</th>
          </tr>
        </thead>
        <tbody>
          @foreach($approvalRequests as $request)
          <tr>
            <td>
              <span>承認済み</span>
            </td>
            <td>{{ $request->user->name }}</td>
            <td>{{ $request->attendance->date->format('Y/m/d') }}</td>
            <td>{{ $request->note }}</td>
            <td>{{ $request->approval_at->format('Y/m/d') }}</td>
            <td>
              <a href="{{ route('attendance.show', $request->attendance) }}">詳細</a>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    @endif
  </div>
</div>
@endsection