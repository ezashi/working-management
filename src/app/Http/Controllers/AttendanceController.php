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
    $currentTime = Carbon::now()->isoFormat('YYYY年M月D日(ddd) HH:mm');

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
    if ($month === now()->format('Y-m')) {
      $date = Carbon::now()->startOfMonth();
    } else {
      $date = Carbon::parse($month)->startOfMonth();
    }

    $attendances = $user->attendances()
    ->whereYear('date', $date->year)
    ->whereMonth('date', $date->month)
    ->orderBy('date', 'desc')
    ->with('breaks', 'user')
    ->get();

    $prevMonth = $date->copy()->subMonth()->format('Y-m');
    $nextMonth = $date->copy()->addMonth()->format('Y-m');

    return view('list', compact('attendances', 'month', 'prevMonth', 'nextMonth'));
  }



  public function show($id)
  {
    if (auth()->user()->isAdmin()) {
      return redirect()->route('attendance.show', $id);
    }

    $attendance = Attendance::with('breaks')->findOrFail($id);

    $hasPendingRequest = $attendance->modificationRequests()
      ->where('status', 'pending')
      ->exists();

    return view('show', compact('attendance', 'hasPendingRequest'));
  }


  public function update(AttendanceModificationRequest $request, $id)
  {
    $attendance = Attendance::findOrFail($id);

    $hasPendingRequest = $attendance->modificationRequests()
      ->where('status', 'pending')
      ->exists();

    if ($hasPendingRequest) {
      return redirect()->route('attendance.show', $attendance->id);
    }

    ModificationRequest::create([
      'attendance_id' => $attendance->id,
      'user_id' => auth()->id(),
      'modified_check_in' => $request->check_in,
      'modified_check_out' => $request->check_out,
      'modified_breaks' => $request->breaks ? array_values($request->breaks) : null,
      'modified_note' => $request->note,
      'status' => 'pending',
    ]);

    return redirect()->route('attendance.show', $attendance->id);
  }
}