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
        ->orderBy('modified_approval_at', 'desc')
        ->get();

      return view('admin.index', compact('pendingRequests', 'approvalRequests'));
    } else{
      $pendingRequests = $user->modificationRequests()
        ->with('attendance')
        ->where('status', 'pending')
        ->orderBy('created_at', 'desc')
        ->get();

      $approvalRequests = $user->modificationRequests()
        ->with('attendance')
        ->where('status', 'approval')
        ->orderBy('modified_approval_at', 'desc')
        ->get();

      return view('index', compact('pendingRequests', 'approvalRequests'));
    }
  }


  public function showApproval($id)
  {
    if ($user->isAdmin()) {
      $modificationRequest = ModificationRequest::with(['attendance.user', 'attendance.breaks', 'approval'])
        ->where('id', $id)
        ->where('status', 'approval')
        ->firstOrFail();

      return view('admin.approval_show', compact('modificationRequest'));
    }else {
      $modificationRequest = ModificationRequest::with(['attendance.user', 'attendance.breaks'])
        ->where('id', $id)
        ->where('user_id', auth()->id())
        ->where('status', 'approval')
        ->firstOrFail();

      return view('approval_show', compact('modificationRequest'));
    }
  }



  public function show($attendance_correct_request)
  {
    $modificationRequest = ModificationRequest::with(['attendance.user', 'attendance.breaks', 'user'])->findOrFail($attendance_correct_request);

    return view('admin.modification_request_approval', compact('modificationRequest'));
  }


  public function approval(Request $request, $attendance_correct_request)
  {
    $modificationRequest = ModificationRequest::findOrFail($attendance_correct_request);
    $attendance = $modificationRequest->attendance;

    $attendance->update([
      'check_in' => $modificationRequest->modified_check_in,
      'check_out' => $modificationRequest->modified_check_out,
      'note' => $modificationRequest->modified_note,
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
      'modified_approval_by' => auth()->id(),
      'modified_approval_at' => now(),
    ]);

    return redirect()->route('modification.request.show', $attendance_correct_request);
  }
}