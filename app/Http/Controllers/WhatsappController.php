<?php

namespace App\Http\Controllers;

use App\Models\WaGroup;
use App\Models\WaMessageLog;
use App\Models\WaSchedule;
use App\Services\WhatsappService;
use Illuminate\Http\Request;

class WhatsappController extends Controller
{
    public function __construct(private WhatsappService $wa) {}

    // ── Dashboard ────────────────────────────────────────────────────────────

    public function index()
    {
        $stats = [
            'total_groups'    => WaGroup::where('is_active', true)->count(),
            'total_schedules' => WaSchedule::where('is_active', true)->count(),
            'sent_today'      => WaMessageLog::where('status', 'sent')
                ->whereDate('sent_at', today())->count(),
            'failed_today'    => WaMessageLog::where('status', 'failed')
                ->whereDate('sent_at', today())->count(),
        ];
        return view('modules.whatsapp.index', compact('stats'));
    }

    // Ajax: device status
    public function status()
    {
        return response()->json($this->wa->getStatus());
    }

    // Ajax: fetch WA groups from device
    public function fetchGroups()
    {
        $groups = $this->wa->fetchGroups();
        return response()->json(['success' => true, 'data' => $groups]);
    }

    // Ajax: logout WA device
    public function logoutDevice()
    {
        $ok = $this->wa->logout();
        return response()->json(['success' => $ok]);
    }

    // ── Groups ───────────────────────────────────────────────────────────────

    public function groups()
    {
        if (request()->ajax()) {
            $groups = WaGroup::orderByDesc('created_at')->get();
            return response()->json(['success' => true, 'data' => $groups]);
        }
        return view('modules.whatsapp.groups');
    }

    public function storeGroup(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'wa_id'       => 'required|string|max:255',
            'type'        => 'required|in:group,number',
            'description' => 'nullable|string|max:500',
            'is_active'   => 'boolean',
        ]);
        $group = WaGroup::create($data);
        return response()->json(['success' => true, 'data' => $group]);
    }

    public function updateGroup(Request $request, WaGroup $group)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'wa_id'       => 'required|string|max:255',
            'type'        => 'required|in:group,number',
            'description' => 'nullable|string|max:500',
            'is_active'   => 'boolean',
        ]);
        $group->update($data);
        return response()->json(['success' => true, 'data' => $group]);
    }

    public function destroyGroup(WaGroup $group)
    {
        $group->delete();
        return response()->json(['success' => true]);
    }

    // ── Schedules ────────────────────────────────────────────────────────────

    public function schedules()
    {
        if (request()->ajax()) {
            $schedules = WaSchedule::orderByDesc('created_at')->get()->map(function ($s) {
                $s->groups_data = WaGroup::whereIn('id', $s->group_ids)->get(['id', 'name']);
                return $s;
            });
            return response()->json(['success' => true, 'data' => $schedules]);
        }
        $groups = WaGroup::where('is_active', true)->orderBy('name')->get();
        return view('modules.whatsapp.schedules', compact('groups'));
    }

    public function createSchedule()
    {
        $groups = WaGroup::where('is_active', true)->orderBy('name')->get();

        return view('modules.whatsapp.schedule-form', [
            'groups' => $groups,
            'scheduleForm' => [
                'id' => null,
                'name' => '',
                'group_ids' => [],
                'message_template' => '{name} GAVE MONEY TO {amount} TRUST',
                'data_rows' => [['name' => '', 'amount' => '']],
                'schedule_type' => 'once',
                'cron_expression' => '',
                'scheduled_at' => '',
                'schedule_time' => '08:00',
                'schedule_day' => 1,
                'is_active' => true,
            ],
            'pageMode' => 'create',
        ]);
    }

    public function editSchedulePage(WaSchedule $schedule)
    {
        $groups = WaGroup::where('is_active', true)->orderBy('name')->get();

        return view('modules.whatsapp.schedule-form', [
            'groups' => $groups,
            'scheduleForm' => $this->mapScheduleForForm($schedule),
            'pageMode' => 'edit',
        ]);
    }

    public function storeSchedule(Request $request)
    {
        $data = $request->validate([
            'name'              => 'required|string|max:255',
            'group_ids'         => 'required|array|min:1',
            'group_ids.*'       => 'integer|exists:wa_groups,id',
            'message_template'  => 'required|string',
            'data_rows'         => 'required|array|min:1',
            'schedule_type'     => 'required|in:once,daily,weekly,cron',
            'cron_expression'   => 'nullable|string|max:100',
            'scheduled_at'      => 'nullable|date',
            'schedule_time'     => 'nullable|string|regex:/^\d{2}:\d{2}$/',
            'schedule_day'      => 'nullable|integer|between:0,6',
            'is_active'         => 'boolean',
        ]);
        $schedule = WaSchedule::create($data);
        return response()->json(['success' => true, 'data' => $schedule]);
    }

    public function updateSchedule(Request $request, WaSchedule $schedule)
    {
        $data = $request->validate([
            'name'              => 'required|string|max:255',
            'group_ids'         => 'required|array|min:1',
            'group_ids.*'       => 'integer|exists:wa_groups,id',
            'message_template'  => 'required|string',
            'data_rows'         => 'required|array|min:1',
            'schedule_type'     => 'required|in:once,daily,weekly,cron',
            'cron_expression'   => 'nullable|string|max:100',
            'scheduled_at'      => 'nullable|date',
            'schedule_time'     => 'nullable|string|regex:/^\d{2}:\d{2}$/',
            'schedule_day'      => 'nullable|integer|between:0,6',
            'is_active'         => 'boolean',
        ]);
        $schedule->update($data);
        return response()->json(['success' => true, 'data' => $schedule->fresh()]);
    }

    public function destroySchedule(WaSchedule $schedule)
    {
        $schedule->delete();
        return response()->json(['success' => true]);
    }

    public function toggleSchedule(WaSchedule $schedule)
    {
        $schedule->update(['is_active' => ! $schedule->is_active]);
        return response()->json(['success' => true, 'data' => $schedule->fresh()]);
    }

    /** Manually trigger sending a schedule right now */
    public function sendNow(WaSchedule $schedule)
    {
        $result = $this->wa->processSchedule($schedule);
        return response()->json([
            'success' => true,
            'message' => "Sent: {$result['sent']}, Failed: {$result['failed']}",
            'data'    => $result,
        ]);
    }

    // ── History ──────────────────────────────────────────────────────────────

    public function history(Request $request)
    {
        if ($request->ajax()) {
            $query = WaMessageLog::with('schedule')
                ->orderByDesc('sent_at');

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('group_name', 'like', "%{$search}%")
                      ->orWhere('schedule_name', 'like', "%{$search}%")
                      ->orWhere('message', 'like', "%{$search}%");
                });
            }
            if ($request->filled('date')) {
                $query->whereDate('sent_at', $request->date);
            }

            $logs = $query->paginate(50);
            return response()->json($logs);
        }

        return view('modules.whatsapp.history');
    }

    public function destroyLog(WaMessageLog $log)
    {
        $log->delete();
        return response()->json(['success' => true]);
    }

    public function clearHistory(Request $request)
    {
        $days = max(1, (int) $request->input('days', 30));
        $deleted = WaMessageLog::where('sent_at', '<', now()->subDays($days))->delete();
        return response()->json(['success' => true, 'deleted' => $deleted]);
    }

    private function mapScheduleForForm(WaSchedule $schedule): array
    {
        return [
            'id' => $schedule->id,
            'name' => $schedule->name,
            'group_ids' => array_map('intval', $schedule->group_ids ?? []),
            'message_template' => $schedule->message_template,
            'data_rows' => $schedule->data_rows ?: [['name' => '', 'amount' => '']],
            'schedule_type' => $schedule->schedule_type,
            'cron_expression' => $schedule->cron_expression ?? '',
            'scheduled_at' => optional($schedule->scheduled_at)->format('Y-m-d\TH:i') ?? '',
            'schedule_time' => $schedule->schedule_time ?? '08:00',
            'schedule_day' => $schedule->schedule_day ?? 1,
            'is_active' => (bool) $schedule->is_active,
        ];
    }
}
