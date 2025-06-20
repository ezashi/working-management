<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Illuminate\Http\Request;
use App\Models\ModificationRequest;


class ModificationRequestController extends Controller
{
  public function index()
  {
    $user = auth()->user();

    if ($user->isAdmin()) {
      $pendingRequests = ModificationRequest::with(['attendance.user', 'user'])
        ->where('status', 'pending')
        ->orderBy('created_at', 'desc')
        ->get();

      $approvalRequests = ModificationRequest::with(['attendance.user', 'user', 'approval'])
        ->where('status', 'approval')
        ->orderBy('approval_at', 'desc')
        ->get();

      return view('correction_request_list', compact('pendingRequests', 'approvalRequests'));
    } else{
      $pendingRequests = $user->modificationRequests()
        ->with('attendance')
        ->where('status', 'pending')
        ->orderBy('created_at', 'desc')
        ->get();

      $approvalRequests = $user->modificationRequests()
        ->with('attendance')
        ->where('status', 'approval')
        ->orderBy('approval_at', 'desc')
        ->get();

      return view('correction_request_list', compact('pendingRequests', 'approvalRequests'));
    }
  }


  public function show(ModificationRequest $modificationRequest)
  {
    $modificationRequest->load(['attendance.user', 'attendance.breaks', 'user']);

    return view('admin.modification_request_approval', compact('modificationRequest'));
  }


  public function approval(Request $request, ModificationRequest $modificationRequest)
  {
    $attendance = $modificationRequest->attendance;

    $attendance->update([
      'check_in' => $modificationRequest->check_in,
      'check_out' => $modificationRequest->check_out,
      'note' => $modificationRequest->note,
    ]);

    if ($modificationRequest->modified_breaks) {
      $attendance->breaks()->delete();

      foreach ($modificationRequest->modified_breaks as $break) {
        if (!empty($break['start_time'])) {
          $attendance->breaks()->create([
            'start_time' => $break['start_time'],
            'end_time' => $break['end_time'] ?? null,
          ]);
        }
      }
    }

    $modificationRequest->update([
      'status' => 'approval',
      'approval_by' => auth()->id(),
      'approval_at' => now(),
    ]);

    return redirect()->route('correction.requests.index');
  }
}