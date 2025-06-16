<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\BreakTime;
use App\Models\Attendance;
use Illuminate\Http\Request;
use App\Models\ModificationRequest;
use App\Http\Requests\CheckInRequest;
use App\Http\Requests\CheckOutRequest;
use App\Http\Requests\BreakEndRequest;
use App\Http\Requests\BreakStartRequest;
use App\Http\Requests\AttendanceModificationRequest;

class AttendanceController extends Controller
{
  public function attendance()
  {
    $user = auth()->user();
    $currentStatus = $user->currentStatus();
    $todayAttendance = $user->todayAttendance();
    $currentTime = Carbon::now()->format('Y年m月d日 H:i:s');

    return view('attendance', compact('currentStatus', 'todayAttendance', 'currentTime'));
  }


  public function checkIn(CheckInRequest $inRequest)
  {
    $attendance = Attendance::create([
      'user_id' => auth()->id(),
      'date' => today(),
      'check_in' => now()->format('H:i:s'),
      'status' => 'working',
    ]);

    return redirect()->route('attendance');
  }


  public function checkOut(CheckOutRequest $outRequest)
  {
    $attendance = auth()->user()->todayAttendance();
    $attendance->update([
      'check_out' => now()->format('H:i:s'),
      'status' => 'finished',
    ]);

    return redirect()->route('attendance');
  }


  public function breakStart(BreakStartRequest $startRequest)
  {
    $attendance = auth()->user()->todayAttendance();

    BreakTime::create([
      'attendance_id' => $attendance->id,
      'start_time' => now()->format('H:i:s'),
    ]);

    $attendance->update(['status' => 'break']);

    return redirect()->route('attendance');
    }


  public function breakEnd(BreakEndRequest $endRequest)
  {
    $attendance = auth()->user()->todayAttendance();
    $activeBreak = $attendance->activeBreak();

    if ($activeBreak) {
      $activeBreak->update(['end_time' => now()->format('H:i:s')]);
    }

    $attendance->update(['status' => 'working']);

    return redirect()->route('attendance');
  }



  public function list(Request $request)
  {
    $user = auth()->user();
    $month = $request->get('month', now()->format('Y-m'));
    $date = Carbon::parse($month . '-01');

    $attendances = $user->attendances()
    ->whereYear('date', $date->year)
    ->whereMonth('date', $date->month)
    ->orderBy('date', 'desc')
    ->with('breaks')
    ->get();

    $prevMonth = $date->copy()->subMonth()->format('Y-m');
    $nextMonth = $date->copy()->addMonth()->format('Y-m');

    return view('list', compact('attendances', 'month', 'prevMonth', 'nextMonth'));
  }



  public function show(Attendance $attendance)
  {
    if ($attendance->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
      abort(403);
    }

    $attendance->load('breaks');

    $hasPendingRequest = $attendance->modificationRequests()
      ->where('status', 'pending')
      ->exists();

    return view('show', compact('attendance', 'hasPendingRequest'));
  }


  public function update(AttendanceUpdateRequest $request, Attendance $attendance)
  {
    if ($attendance->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
      abort(403);
    }

    if (!auth()->user()->isAdmin()) {
      if ($attendance->modificationRequests()->where('status', 'pending')->exists()) {
        return redirect()->route('attendances.show', $attendance);
      }

      $modifiedBreaks = [];
      if ($request->has('breaks')) {
        foreach ($request->breaks as $break) {
          if (!empty($break['start_time'])) {
            $modifiedBreaks[] = [
              'start_time' => $break['start_time'],
              'end_time' => $break['end_time'] ?? null
            ];
          }
        }
      }

      $modificationRequest = ModificationRequest::create([
        'attendance_id' => $attendance->id,
        'user_id' => auth()->id(),
        'check_in' => $request->check_in,
        'check_out' => $request->check_out,
        'breaks' => $this->processBreaks($request->breaks),
        'note' => $request->note,
        'status' => 'pending',
      ]);

      return redirect()->route('attendances.show', $attendance);
    }

    $attendance->update([
      'check_in' => $request->check_in,
      'check_out' => $request->check_out,
      'note' => $request->note,
    ]);

    if ($request->has('breaks')) {
      $attendance->breaks()->delete();

      foreach ($request->breaks as $break) {
        if (!empty($break['start_time'])) {
          $attendance->breaks()->create([
            'start_time' => $break['start_time'],
            'end_time' => $break['end_time'] ?? null,
          ]);
        }
      }
    }

    return redirect()->route('attendances.show', $attendance);
  }
}