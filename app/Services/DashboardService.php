<?php

namespace App\Services;

use App\Models\Event;
use App\Models\Participant;
use App\Models\Winner;
use App\Models\Opd;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardService
{
    /**
     * Get all statistics for dashboard
     */
    public function getStatistics(): array
    {
        $user = Auth::user();

        return [
            'total_events' => $this->getTotalEvents($user),
            'active_events' => $this->getActiveEvents($user),
            'total_participants' => $this->getTotalParticipants($user),
            'total_winners' => $this->getTotalWinners($user),
            'total_opds' => $this->getTotalOpds($user),
            'total_users' => $this->getTotalUsers($user),
        ];
    }

    /**
     * Get total events count
     */
    public function getTotalEvents($user): int
    {
        $query = Event::query();

        if ($user->role === 'admin_penyelenggara') {
            $query->where('opd_id', $user->opd_id);
        }

        return $query->count();
    }

    /**
     * Get active events count
     */
    public function getActiveEvents($user): int
    {
        $query = Event::where('status', 'aktif');

        if ($user->role === 'admin_penyelenggara') {
            $query->where('opd_id', $user->opd_id);
        }

        return $query->count();
    }

    /**
     * Get total participants count
     */
    public function getTotalParticipants($user): int
    {
        if ($user->role === 'admin_penyelenggara') {
            return Participant::whereHas('event', function ($q) use ($user) {
                $q->where('opd_id', $user->opd_id);
            })->count();
        }

        return Participant::count();
    }

    /**
     * Get total winners count
     */
    public function getTotalWinners($user): int
    {
        if ($user->role === 'admin_penyelenggara') {
            return Winner::whereHas('event', function ($q) use ($user) {
                $q->where('opd_id', $user->opd_id);
            })->count();
        }

        return Winner::count();
    }

    /**
     * Get total OPDs (only for super admin/developer)
     */
    public function getTotalOpds($user): ?int
    {
        if (!in_array($user->role, ['superadmin', 'developer'])) {
            return null;
        }

        return Opd::count();
    }

    /**
     * Get total users (only for super admin/developer)
     */
    public function getTotalUsers($user): ?int
    {
        if (!in_array($user->role, ['superadmin', 'developer'])) {
            return null;
        }

        return User::count();
    }

    /**
     * Get recent events
     */
    public function getRecentEvents(int $limit = 10): \Illuminate\Database\Eloquent\Collection
    {
        $user = Auth::user();

        $query = Event::with(['opd', 'participants', 'winners'])
            ->orderBy('created_at', 'desc');

        if ($user->role === 'admin_penyelenggara') {
            $query->where('opd_id', $user->opd_id);
        }

        return $query->take($limit)->get();
    }

    /**
     * Get events ending soon
     */
    public function getEventsEndingSoon(int $days = 3): \Illuminate\Database\Eloquent\Collection
    {
        $user = Auth::user();
        $endDate = Carbon::now('Asia/Jakarta')->addDays($days);
        $now = Carbon::now('Asia/Jakarta');

        $query = Event::where('tgl_selesai', '<=', $endDate)
            ->where('tgl_selesai', '>=', $now)
            ->orderBy('tgl_selesai', 'asc');

        if ($user->role === 'admin_penyelenggara') {
            $query->where('opd_id', $user->opd_id);
        }

        return $query->get();
    }

    /**
     * Get events without participants
     */
    public function getEventsWithoutParticipants(): \Illuminate\Database\Eloquent\Collection
    {
        $user = Auth::user();

        $query = Event::doesntHave('participants')
            ->orderBy('created_at', 'desc');

        if ($user->role === 'admin_penyelenggara') {
            $query->where('opd_id', $user->opd_id);
        }

        return $query->get();
    }

    /**
     * Get top events by participants
     */
    public function getTopEventsByParticipants(int $limit = 5): \Illuminate\Database\Eloquent\Collection
    {
        $user = Auth::user();

        $query = Event::withCount('participants')
            ->orderBy('participants_count', 'desc')
            ->take($limit);

        if ($user->role === 'admin_penyelenggara') {
            $query->where('opd_id', $user->opd_id);
        }

        return $query->get();
    }

    /**
     * Get events not drawn yet
     */
    public function getEventsNotDrawn(): \Illuminate\Database\Eloquent\Collection
    {
        $user = Auth::user();
        $now = Carbon::now('Asia/Jakarta');

        $query = Event::where('tgl_selesai', '<', $now)
            ->doesntHave('winners')
            ->orderBy('tgl_selesai', 'desc');

        if ($user->role === 'admin_penyelenggara') {
            $query->where('opd_id', $user->opd_id);
        }

        return $query->get();
    }

    /**
     * Get event chart data (events per month)
     */
    public function getEventChartData(int $months = 6): array
    {
        $user = Auth::user();
        $startDate = Carbon::now('Asia/Jakarta')->subMonths($months);

        $query = Event::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as count')
            ->where('created_at', '>=', $startDate)
            ->groupBy('month')
            ->orderBy('month', 'asc');

        if ($user->role === 'admin_penyelenggara') {
            $query->where('opd_id', $user->opd_id);
        }

        $data = $query->get();

        $labels = [];
        $counts = [];

        // Fill missing months with 0
        for ($i = $months - 1; $i >= 0; $i--) {
            $month = Carbon::now('Asia/Jakarta')->subMonths($i)->format('Y-m');
            $monthLabel = Carbon::now('Asia/Jakarta')->subMonths($i)->format('M Y');

            $labels[] = $monthLabel;
            $counts[] = $data->firstWhere('month', $month)->count ?? 0;
        }

        return [
            'labels' => $labels,
            'data' => $counts,
            'total' => array_sum($counts),
        ];
    }

    /**
     * Get event status distribution
     */
    public function getEventStatusDistribution(): array
    {
        $user = Auth::user();

        $query = Event::selectRaw('status, COUNT(*) as count')
            ->groupBy('status');

        if ($user->role === 'admin_penyelenggara') {
            $query->where('opd_id', $user->opd_id);
        }

        $data = $query->get();

        $colors = [
            'aktif' => '#28a745',
            'tidak_aktif' => '#6c757d',
        ];

        return $data->map(function ($item) use ($colors) {
            return [
                'status' => $item->status,
                'label' => ucfirst(str_replace('_', ' ', $item->status)),
                'count' => $item->count,
                'color' => $colors[$item->status] ?? '#6c757d',
            ];
        })->toArray();
    }

    /**
     * Get participant registration trend
     */
    public function getParticipantRegistrationTrend(int $days = 7): array
    {
        $user = Auth::user();
        $startDate = Carbon::now('Asia/Jakarta')->subDays($days);

        $query = Participant::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', $startDate)
            ->groupBy('date')
            ->orderBy('date', 'asc');

        if ($user->role === 'admin_penyelenggara') {
            $query->whereHas('event', function ($q) use ($user) {
                $q->where('opd_id', $user->opd_id);
            });
        }

        $data = $query->get();

        $labels = [];
        $counts = [];

        // Fill missing days with 0
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::now('Asia/Jakarta')->subDays($i)->format('Y-m-d');
            $dateLabel = Carbon::now('Asia/Jakarta')->subDays($i)->format('d M');

            $labels[] = $dateLabel;
            $counts[] = $data->firstWhere('date', $date)->count ?? 0;
        }

        return [
            'labels' => $labels,
            'data' => $counts,
            'total' => array_sum($counts),
        ];
    }

    /**
     * Get top events by participants for chart
     */
    public function getTopEventsByParticipantsForChart(int $limit = 10): array
    {
        $user = Auth::user();

        $query = Event::withCount('participants')
            ->orderBy('participants_count', 'desc')
            ->take($limit);

        if ($user->role === 'admin_penyelenggara') {
            $query->where('opd_id', $user->opd_id);
        }

        $events = $query->get();

        return [
            'labels' => $events->pluck('nm_event')->toArray(),
            'data' => $events->pluck('participants_count')->toArray(),
        ];
    }

    /**
     * Get recent activities
     */
    public function getRecentActivities(int $limit = 10): array
    {
        $user = Auth::user();
        $activities = [];

        // Recent events
        $eventsQuery = Event::with('opd')
            ->orderBy('created_at', 'desc')
            ->take(5);

        if ($user->role === 'admin_penyelenggara') {
            $eventsQuery->where('opd_id', $user->opd_id);
        }

        foreach ($eventsQuery->get() as $event) {
            $activities[] = [
                'type' => 'event_created',
                'icon' => 'fi fi-rr-calendar',
                'message' => "Event '{$event->nm_event}' dibuat",
                'time' => $event->created_at,
                'link' => route('admin.event.show', $event->id),
            ];
        }

        // Recent participants (grouped by event)
        $participantsQuery = Participant::with('event')
            ->selectRaw('event_id, COUNT(*) as count, MAX(created_at) as latest_created')
            ->groupBy('event_id')
            ->orderBy('latest_created', 'desc')
            ->take(5);

        if ($user->role === 'admin_penyelenggara') {
            $participantsQuery->whereHas('event', function ($q) use ($user) {
                $q->where('opd_id', $user->opd_id);
            });
        }

        foreach ($participantsQuery->get() as $participant) {
            $activities[] = [
                'type' => 'participants_registered',
                'icon' => 'fi fi-rr-users',
                'message' => "{$participant->count} peserta baru terdaftar di Event '{$participant->event->nm_event}'",
                'time' => $participant->latest_created,
                'link' => route('admin.event.participants.index', $participant->event_id),
            ];
        }

        // Recent winners
        $winnersQuery = Winner::with(['event', 'participant'])
            ->orderBy('drawn_at', 'desc')
            ->take(5);

        if ($user->role === 'admin_penyelenggara') {
            $winnersQuery->whereHas('event', function ($q) use ($user) {
                $q->where('opd_id', $user->opd_id);
            });
        }

        foreach ($winnersQuery->get() as $winner) {
            $activities[] = [
                'type' => 'draw_completed',
                'icon' => 'fi fi-rr-trophy',
                'message' => "Pengundian dilakukan untuk Event '{$winner->event->nm_event}'",
                'time' => $winner->drawn_at,
                'link' => route('admin.event.show', $winner->event_id),
            ];
        }

        // Sort by time and limit
        usort($activities, function ($a, $b) {
            return $b['time'] <=> $a['time'];
        });

        return array_slice($activities, 0, $limit);
    }
}
