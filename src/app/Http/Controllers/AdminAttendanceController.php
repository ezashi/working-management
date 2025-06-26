<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Attendance;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\AttendanceModificationRequest;


class AdminAttendanceController extends Controller
{
  public function attendance(Request $request)
  {
    $date = $request->get('date', now()->format('Y-m-d'));
    $targetDate = Carbon::parse($date);

    $attendances = Attendance::with(['user', 'breaks'])
      ->whereDate('date', $targetDate)
      ->orderBy('user_id')
      ->get();

    $prevDate = $targetDate->copy()->subDay()->format('Y-m-d');
    $nextDate = $targetDate->copy()->addDay()->format('Y-m-d');

    return view('admin.attendance', compact('attendances', 'date', 'prevDate', 'nextDate', 'targetDate'));
  }

  public function staff()
  {
    $staffs = User::where('role', 'user')->get();

    return view('admin.staff', compact('staffs'));
  }

  public function list(Request $request, $id)
  {
    $user = User::findOrFail($id);

    $month = $request->get('month', now()->format('Y-m'));
    if ($month === now()->format('Y-m')) {
      $date = Carbon::now()->startOfMonth();
    } else {
      $date = Carbon::parse($month)->startOfMonth();
    }

    $attendances = $user->attendances()
      ->whereYear('date', $date->year)
      ->whereMonth('date', $date->month)
      ->orderBy('date')
      ->with('breaks')
      ->get();

    $prevMonth = $date->copy()->subMonth()->format('Y-m');
    $nextMonth = $date->copy()->addMonth()->format('Y-m');

    $totalWorkingDays = $attendances->count();
    $totalWorkingMinutes = 0;
    $totalBreakMinutes = 0;

    foreach ($attendances as $attendance) {
      $totalWorkingMinutes += $attendance->workingHours();
      $totalBreakMinutes += $attendance->totalBreakTime();
    }

    return view('admin.list', compact(
      'user',
      'attendances',
      'month',
      'prevMonth',
      'nextMonth',
      'date',
      'totalWorkingDays',
      'totalWorkingMinutes',
      'totalBreakMinutes'
    ));
  }

  public function show($id)
  {
    $attendance = Attendance::with(['user', 'breaks', 'modificationRequests'])->findOrFail($id);

    $pendingRequest = $attendance->modificationRequests()
      ->where('status', 'pending')
      ->first();

    $hasPendingRequest = $pendingRequest !== null;

    return view('admin.show', compact('attendance', 'hasPendingRequest', 'pendingRequest'));
  }

  public function update(AttendanceModificationRequest $request, $id)
  {
    $modificationRequest = new AttendanceModificationRequest();
    $validator = \Validator::make($request->all(), $modificationRequest->rules(), $modificationRequest->messages());

    if ($validator->fails()) {
        return redirect()->back()
            ->withErrors($validator)
            ->withInput();
    }

    $attendance = Attendance::findOrFail($id);

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

    return redirect()->route('attendance.show', $attendance->id);
  }
}