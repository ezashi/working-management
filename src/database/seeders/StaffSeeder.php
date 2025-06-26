<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Attendance;
use App\Models\BreakTime;
use App\Models\ModificationRequest;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class StaffSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $staffUsers = User::factory(15)->user()->create();

        foreach ($staffUsers as $user) {
            echo "- {$user->name} ({$user->email})\n";
        }

        foreach ($staffUsers as $index => $user) {

            $attendanceCount = rand(15, 25);
            $attendances = collect();

            for ($i = 0; $i < $attendanceCount; $i++) {
                $date = Carbon::now()->subDays(rand(1, 30));

                if ($attendances->where('date', $date->format('Y-m-d'))->isNotEmpty()) {
                    continue;
                }

                $checkInHour = rand(8, 10);
                $checkInMinute = rand(0, 59);
                $checkIn = Carbon::parse($date)->setTime($checkInHour, $checkInMinute);

                $workHours = rand(7, 9);
                $workMinutes = rand(0, 59);
                $checkOut = $checkIn->copy()->addHours($workHours)->addMinutes($workMinutes);

                $attendance = Attendance::create([
                    'user_id' => $user->id,
                    'date' => $date->format('Y-m-d'),
                    'check_in' => $checkIn->format('H:i:s'),
                    'check_out' => $checkOut->format('H:i:s'),
                    'status' => 'finished',
                    'note' => rand(1, 10) <= 3 ? fake()->sentence() : null,
                ]);

                $attendances->push($attendance);

                if (rand(1, 10) <= 8) {
                $this->createBreakTimes($attendance, $checkIn, $checkOut);
            }
            }

            $this->createTodayAttendance($user, $index);

            $this->createModificationRequests($user, $attendances);
        }
    }

    /**
     * 休憩時間を作成
     */
    private function createBreakTimes($attendance, $checkIn, $checkOut)
    {
        $breaks = [];

        if (rand(1, 10) <= 9) {
            $lunchStart = Carbon::createFromTime(12, rand(0, 30));
            $lunchEnd = $lunchStart->copy()->addMinutes(rand(45, 75));

        $breaks[] = [
            'start_time' => $lunchStart->format('H:i:s'),
            'end_time' => $lunchEnd->format('H:i:s'),
        ];
        }

        if (rand(1, 10) <= 4) {
            $breakStart = Carbon::createFromTime(rand(14, 16), rand(0, 59));
            $breakEnd = $breakStart->copy()->addMinutes(rand(10, 20));

            $breaks[] = [
                'start_time' => $breakStart->format('H:i:s'),
                'end_time' => $breakEnd->format('H:i:s'),
            ];
        }

        if (rand(1, 10) <= 3) {
            $morningBreakStart = Carbon::createFromTime(rand(10, 11), rand(0, 59));
            $morningBreakEnd = $morningBreakStart->copy()->addMinutes(rand(10, 15));

            array_unshift($breaks, [
                'start_time' => $morningBreakStart->format('H:i:s'),
                'end_time' => $morningBreakEnd->format('H:i:s'),
            ]);
        }

        foreach ($breaks as $break) {
            BreakTime::create([
                'attendance_id' => $attendance->id,
                'start_time' => $break['start_time'],
                'end_time' => $break['end_time'],
            ]);
        }
    }

    /**
     * 今日の勤怠データを作成
     */
    private function createTodayAttendance($user, $index)
    {
        $today = Carbon::today();

        $statusType = $index % 5;

        switch ($statusType) {
            case 0:
                $checkIn = $today->copy()->setTime(rand(8, 10), rand(0, 59));
                $attendance = Attendance::create([
                    'user_id' => $user->id,
                    'date' => $today->format('Y-m-d'),
                    'check_in' => $checkIn->format('H:i:s'),
                    'check_out' => null,
                    'status' => 'working',
                ]);
                break;

            case 1:
                $checkIn = $today->copy()->setTime(rand(8, 10), rand(0, 59));
                $attendance = Attendance::create([
                    'user_id' => $user->id,
                    'date' => $today->format('Y-m-d'),
                    'check_in' => $checkIn->format('H:i:s'),
                    'check_out' => null,
                    'status' => 'break',
                ]);

                BreakTime::create([
                    'attendance_id' => $attendance->id,
                    'start_time' => Carbon::now()->subMinutes(rand(15, 45))->format('H:i:s'),
                    'end_time' => null,
                ]);
                break;

            case 2:
                $checkIn = $today->copy()->setTime(rand(8, 10), rand(0, 59));
                $checkOut = $checkIn->copy()->addHours(rand(7, 9))->addMinutes(rand(0, 59));
                $attendance = Attendance::create([
                    'user_id' => $user->id,
                    'date' => $today->format('Y-m-d'),
                    'check_in' => $checkIn->format('H:i:s'),
                    'check_out' => $checkOut->format('H:i:s'),
                    'status' => 'finished',
                ]);

                BreakTime::create([
                    'attendance_id' => $attendance->id,
                    'start_time' => '12:00:00',
                    'end_time' => '13:00:00',
                ]);

                if (rand(1, 10) <= 5) {
                    BreakTime::create([
                        'attendance_id' => $attendance->id,
                        'start_time' => '15:00:00',
                        'end_time' => '15:15:00',
                    ]);
                }
                break;

            default:
                break;
        }
    }

    /**
     * 修正申請を作成
     */
    private function createModificationRequests($user, $attendances)
    {
        if ($attendances->isEmpty()) {
            return;
        }

        $requestCount = rand(2, 4);
        $selectedAttendances = $attendances->random(min($requestCount, $attendances->count()));

        foreach ($selectedAttendances as $attendance) {
            $status = fake()->randomElement(['pending', 'approval', 'rejected']);

            $modifiedCheckIn = Carbon::createFromTime(rand(8, 10), rand(0, 59));
            $modifiedCheckOut = $modifiedCheckIn->copy()->addHours(rand(7, 9))->addMinutes(rand(0, 59));

            $reason = fake()->randomElement([
                '電車遅延のため出勤時間を修正お願いします。',
                '病院での検査のため早退させていただきました。',
                '打刻し忘れのため修正をお願いします。',
                '残業をした分の退勤時間修正をお願いします。',
                'システムエラーで正しく打刻されませんでした。',
                '会議が長引いたため退勤時間の修正をお願いします。',
                '交通事故渋滞で遅刻したため出勤時間を修正してください。',
                '体調不良で早退したため退勤時間を修正お願いします。'
            ]);

            $modificationRequest = ModificationRequest::create([
                'attendance_id' => $attendance->id,
                'user_id' => $user->id,
                'modified_check_in' => $modifiedCheckIn->format('H:i:s'),
                'modified_check_out' => $modifiedCheckOut->format('H:i:s'),
                'modified_breaks' => [
                    [
                        'start_time' => '12:00',
                        'end_time' => '13:00',
                    ]
                ],
                'modified_note' => $reason,
                'status' => $status,
                'modified_approval_by' => $status !== 'pending' ? 1 : null,
                'modified_approval_at' => $status !== 'pending' ? fake()->dateTimeBetween('-7 days', 'now') : null,
            ]);

            $attendance->update([
                'note' => $reason
            ]);
        }
    }
}