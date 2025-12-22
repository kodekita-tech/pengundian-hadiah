@extends('admin.layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="app-page-head d-flex flex-wrap gap-3 align-items-center justify-content-between mb-4">
    <div class="clearfix">
        <h1 class="app-page-title">Dashboard</h1>
        <span class="text-muted">{{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</span>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.event.create') }}" class="btn btn-primary waves-effect waves-light">
            <i class="fi fi-rr-plus me-1"></i> Buat Event Baru
        </a>
    </div>
</div>

{{-- Statistik Cards --}}
<div class="row mb-4">
    <div class="col-6 col-md-4 col-lg-3 mb-3">
        <div class="card bg-primary bg-opacity-05 shadow-none border-0">
            <div class="card-body">
                <div class="avatar bg-primary shadow-primary rounded-circle text-white mb-3">
                    <i class="fi fi-rr-calendar"></i>
                </div>
                <h3 class="mb-1">{{ number_format($statistics['total_events']) }}</h3>
                <h6 class="mb-0 text-muted">Total Event</h6>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-lg-3 mb-3">
        <div class="card bg-success bg-opacity-05 shadow-none border-0">
            <div class="card-body">
                <div class="avatar bg-success shadow-success rounded-circle text-white mb-3">
                    <i class="fi fi-rr-play"></i>
                </div>
                <h3 class="mb-1">{{ number_format($statistics['active_events']) }}</h3>
                <h6 class="mb-0 text-muted">Event Aktif</h6>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-lg-3 mb-3">
        <div class="card bg-info bg-opacity-05 shadow-none border-0">
            <div class="card-body">
                <div class="avatar bg-info shadow-info rounded-circle text-white mb-3">
                    <i class="fi fi-rr-users"></i>
                </div>
                <h3 class="mb-1">{{ number_format($statistics['total_participants']) }}</h3>
                <h6 class="mb-0 text-muted">Total Peserta</h6>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-lg-3 mb-3">
        <div class="card bg-warning bg-opacity-05 shadow-none border-0">
            <div class="card-body">
                <div class="avatar bg-warning shadow-warning rounded-circle text-white mb-3">
                    <i class="fi fi-rr-trophy"></i>
                </div>
                <h3 class="mb-1">{{ number_format($statistics['total_winners']) }}</h3>
                <h6 class="mb-0 text-muted">Total Pemenang</h6>
            </div>
        </div>
    </div>
    @if(in_array(auth()->user()->role, ['superadmin', 'developer']))
    <div class="col-6 col-md-4 col-lg-3 mb-3">
        <div class="card bg-secondary bg-opacity-05 shadow-none border-0">
            <div class="card-body">
                <div class="avatar bg-secondary shadow-secondary rounded-circle text-white mb-3">
                    <i class="fi fi-rr-building"></i>
                </div>
                <h3 class="mb-1">{{ number_format($statistics['total_opds'] ?? 0) }}</h3>
                <h6 class="mb-0 text-muted">Total OPD</h6>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-lg-3 mb-3">
        <div class="card bg-dark bg-opacity-05 shadow-none border-0">
            <div class="card-body">
                <div class="avatar bg-dark shadow-dark rounded-circle text-white mb-3">
                    <i class="fi fi-rr-user"></i>
                </div>
                <h3 class="mb-1">{{ number_format($statistics['total_users'] ?? 0) }}</h3>
                <h6 class="mb-0 text-muted">Total User</h6>
            </div>
        </div>
    </div>
    @endif
</div>

{{-- Quick Actions --}}
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header border-0 pb-0">
                <h6 class="card-title mb-0">Quick Actions</h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-6 col-md-3">
                        <a href="{{ route('admin.event.create') }}" class="btn btn-outline-primary w-100">
                            <i class="fi fi-rr-plus me-2"></i>Buat Event Baru
                        </a>
                    </div>
                    <div class="col-6 col-md-3">
                        <a href="{{ route('admin.event.index') }}" class="btn btn-outline-info w-100">
                            <i class="fi fi-rr-list me-2"></i>Lihat Semua Event
                        </a>
                    </div>
                    @if(in_array(auth()->user()->role, ['superadmin', 'developer']))
                    <div class="col-6 col-md-3">
                        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary w-100">
                            <i class="fi fi-rr-users me-2"></i>Manajemen User
                        </a>
                    </div>
                    <div class="col-6 col-md-3">
                        <a href="{{ route('admin.opd.index') }}" class="btn btn-outline-success w-100">
                            <i class="fi fi-rr-building me-2"></i>Manajemen OPD
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Charts Row --}}
<div class="row mb-4">
    {{-- Event per Bulan Chart --}}
    <div class="col-12 col-lg-8 mb-4">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between border-0 pb-0">
                <h6 class="card-title mb-0">Event per Bulan</h6>
            </div>
            <div class="card-body">
                <canvas id="eventChart" height="100"></canvas>
            </div>
        </div>
    </div>

    {{-- Status Distribution Chart --}}
    <div class="col-12 col-lg-4 mb-4">
        <div class="card">
            <div class="card-header border-0 pb-0">
                <h6 class="card-title mb-0">Distribusi Status Event</h6>
            </div>
            <div class="card-body">
                <canvas id="statusChart" height="250"></canvas>
            </div>
        </div>
    </div>
</div>

{{-- Participant Trend & Top Events --}}
<div class="row mb-4">
    <div class="col-12 col-lg-6 mb-4">
        <div class="card">
            <div class="card-header border-0 pb-0">
                <h6 class="card-title mb-0">Tren Pendaftaran Peserta (7 Hari Terakhir)</h6>
            </div>
            <div class="card-body">
                <canvas id="participantTrendChart" height="150"></canvas>
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-6 mb-4">
        <div class="card">
            <div class="card-header border-0 pb-0">
                <h6 class="card-title mb-0">Top Event (Peserta Terbanyak)</h6>
            </div>
            <div class="card-body">
                <canvas id="topEventsChart" height="150"></canvas>
            </div>
        </div>
    </div>
</div>

{{-- Event yang Memerlukan Perhatian --}}
@if($eventsEndingSoon->count() > 0 || $eventsWithoutParticipants->count() > 0 || $eventsNotDrawn->count() > 0)
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header border-0 pb-0">
                <h6 class="card-title mb-0">Event yang Memerlukan Perhatian</h6>
            </div>
            <div class="card-body">
                @if($eventsEndingSoon->count() > 0)
                <div class="alert alert-warning mb-3">
                    <h6 class="alert-heading">
                        <i class="fi fi-rr-clock me-2"></i>Event Akan Berakhir ({{ $eventsEndingSoon->count() }})
                    </h6>
                    <ul class="mb-0">
                        @foreach($eventsEndingSoon->take(5) as $event)
                        <li>
                            <a href="{{ route('admin.event.show', $event->id) }}" class="text-decoration-none">
                                <strong>{{ $event->nm_event }}</strong>
                            </a>
                            - Berakhir dalam {{ \Carbon\Carbon::parse($event->tgl_selesai)->diffForHumans() }}
                        </li>
                        @endforeach
                    </ul>
                </div>
                @endif

                @if($eventsWithoutParticipants->count() > 0)
                <div class="alert alert-info mb-3">
                    <h6 class="alert-heading">
                        <i class="fi fi-rr-info me-2"></i>Event Tanpa Peserta ({{ $eventsWithoutParticipants->count()
                        }})
                    </h6>
                    <ul class="mb-0">
                        @foreach($eventsWithoutParticipants->take(5) as $event)
                        <li>
                            <a href="{{ route('admin.event.participants.index', $event->id) }}"
                                class="text-decoration-none">
                                <strong>{{ $event->nm_event }}</strong>
                            </a>
                            - Belum ada peserta terdaftar
                        </li>
                        @endforeach
                    </ul>
                </div>
                @endif

                @if($eventsNotDrawn->count() > 0)
                <div class="alert alert-danger mb-0">
                    <h6 class="alert-heading">
                        <i class="fi fi-rr-exclamation me-2"></i>Event Belum Diundian ({{ $eventsNotDrawn->count() }})
                    </h6>
                    <ul class="mb-0">
                        @foreach($eventsNotDrawn->take(5) as $event)
                        <li>
                            <a href="{{ route('admin.event.show', $event->id) }}" class="text-decoration-none">
                                <strong>{{ $event->nm_event }}</strong>
                            </a>
                            - Event sudah selesai tapi belum diundian
                        </li>
                        @endforeach
                    </ul>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endif

{{-- Recent Events Table & Activity Feed --}}
<div class="row mb-4">
    {{-- Recent Events --}}
    <div class="col-12 col-lg-8 mb-4">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between border-0 pb-0">
                <h6 class="card-title mb-0">Event Terbaru</h6>
                <a href="{{ route('admin.event.index') }}" class="btn-link">Lihat Semua</a>
            </div>
            <div class="card-body">
                @if($recentEvents->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Nama Event</th>
                                <th>OPD</th>
                                <th>Status</th>
                                <th>Peserta</th>
                                <th>Pemenang</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentEvents as $event)
                            <tr>
                                <td>
                                    <strong>{{ $event->nm_event }}</strong>
                                    @if($event->deskripsi)
                                    <br><small class="text-muted">{{ \Illuminate\Support\Str::limit($event->deskripsi,
                                        40) }}</small>
                                    @endif
                                </td>
                                <td>{{ $event->opd->nama_penyelenggara ?? '-' }}</td>
                                <td>
                                    <span class="badge {{ $event->status_badge_class }} rounded-pill px-2 py-1">
                                        {{ $event->status_label }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $event->participants->count() }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-warning">{{ $event->winners->count() }}</span>
                                </td>
                                <td>
                                    <a href="{{ route('admin.event.show', $event->id) }}" class="btn btn-sm btn-primary"
                                        title="View">
                                        <i class="fi fi-rr-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-4">
                    <i class="fi fi-rr-calendar text-muted" style="font-size: 3rem;"></i>
                    <p class="text-muted mt-2">Belum ada event</p>
                    <a href="{{ route('admin.event.create') }}" class="btn btn-primary">
                        <i class="fi fi-rr-plus me-1"></i>Buat Event Pertama
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Activity Feed --}}
    <div class="col-12 col-lg-4 mb-4">
        <div class="card">
            <div class="card-header border-0 pb-0">
                <h6 class="card-title mb-0">Aktivitas Terkini</h6>
            </div>
            <div class="card-body">
                @if(count($activities) > 0)
                <div class="activity-feed">
                    @foreach($activities as $activity)
                    <div class="activity-item d-flex gap-3 mb-3 pb-3 border-bottom">
                        <div class="activity-icon">
                            <i class="{{ $activity['icon'] }} text-primary"></i>
                        </div>
                        <div class="activity-content flex-grow-1">
                            <p class="mb-1 small">
                                @if(isset($activity['link']))
                                <a href="{{ $activity['link'] }}" class="text-decoration-none">
                                    {{ $activity['message'] }}
                                </a>
                                @else
                                {{ $activity['message'] }}
                                @endif
                            </p>
                            <small class="text-muted">{{ \Carbon\Carbon::parse($activity['time'])->diffForHumans()
                                }}</small>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-4">
                    <i class="fi fi-rr-time-past text-muted" style="font-size: 2rem;"></i>
                    <p class="text-muted mt-2 small">Belum ada aktivitas</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Top Events --}}
@if($topEvents->count() > 0)
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header border-0 pb-0">
                <h6 class="card-title mb-0">Top 5 Event (Peserta Terbanyak)</h6>
            </div>
            <div class="card-body">
                <div class="list-group">
                    @foreach($topEvents as $event)
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <a href="{{ route('admin.event.show', $event->id) }}" class="text-decoration-none">
                                <strong>{{ $event->nm_event }}</strong>
                            </a>
                            <br>
                            <small class="text-muted">{{ $event->opd->nama_penyelenggara ?? '-' }}</small>
                        </div>
                        <span class="badge bg-primary rounded-pill">{{ $event->participants_count }} peserta</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Event per Bulan Chart (Line)
        const eventCtx = document.getElementById('eventChart');
        if (eventCtx) {
            new Chart(eventCtx, {
                type: 'line',
                data: {
                    labels: @json($eventChartData['labels']),
                    datasets: [{
                        label: 'Jumlah Event',
                        data: @json($eventChartData['data']),
                        borderColor: '#007bff',
                        backgroundColor: 'rgba(0, 123, 255, 0.1)',
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    }
                }
            });
        }

        // Status Distribution Chart (Doughnut)
        const statusCtx = document.getElementById('statusChart');
        if (statusCtx) {
            const statusData = @json($statusDistribution);
            new Chart(statusCtx, {
                type: 'doughnut',
                data: {
                    labels: statusData.map(item => item.label),
                    datasets: [{
                        data: statusData.map(item => item.count),
                        backgroundColor: statusData.map(item => item.color)
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        }

        // Participant Trend Chart (Line Area)
        const trendCtx = document.getElementById('participantTrendChart');
        if (trendCtx) {
            new Chart(trendCtx, {
                type: 'line',
                data: {
                    labels: @json($participantTrend['labels']),
                    datasets: [{
                        label: 'Pendaftaran',
                        data: @json($participantTrend['data']),
                        borderColor: '#28a745',
                        backgroundColor: 'rgba(40, 167, 69, 0.1)',
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    }
                }
            });
        }

        // Top Events Chart (Bar)
        const topEventsCtx = document.getElementById('topEventsChart');
        if (topEventsCtx) {
            const topEventsData = @json($topEventsChart);
            new Chart(topEventsCtx, {
                type: 'bar',
                data: {
                    labels: topEventsData.labels.map(label => label.length > 20 ? label.substring(0, 20) + '...' : label),
                    datasets: [{
                        label: 'Jumlah Peserta',
                        data: topEventsData.data,
                        backgroundColor: '#17a2b8',
                        borderColor: '#17a2b8',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    indexAxis: 'y',
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    }
                }
            });
        }
    });
</script>
@endpush