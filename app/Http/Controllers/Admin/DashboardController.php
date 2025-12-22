<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected $dashboardService;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(DashboardService $dashboardService)
    {
        $this->middleware('auth');
        $this->dashboardService = $dashboardService;
    }

    /**
     * Show the admin dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $statistics = $this->dashboardService->getStatistics();
        $recentEvents = $this->dashboardService->getRecentEvents(10);
        $eventsEndingSoon = $this->dashboardService->getEventsEndingSoon(3);
        $eventsWithoutParticipants = $this->dashboardService->getEventsWithoutParticipants();
        $topEvents = $this->dashboardService->getTopEventsByParticipants(5);
        $eventsNotDrawn = $this->dashboardService->getEventsNotDrawn();
        $eventChartData = $this->dashboardService->getEventChartData(6);
        $statusDistribution = $this->dashboardService->getEventStatusDistribution();
        $participantTrend = $this->dashboardService->getParticipantRegistrationTrend(7);
        $topEventsChart = $this->dashboardService->getTopEventsByParticipantsForChart(10);
        $activities = $this->dashboardService->getRecentActivities(10);

        return view('admin.dashboard', compact(
            'statistics',
            'recentEvents',
            'eventsEndingSoon',
            'eventsWithoutParticipants',
            'topEvents',
            'eventsNotDrawn',
            'eventChartData',
            'statusDistribution',
            'participantTrend',
            'topEventsChart',
            'activities'
        ));
    }
}
