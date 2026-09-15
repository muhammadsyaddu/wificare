<x-layouts.app
    :title="'Dashboard Teknisi'"
    :header="'Dashboard Operasional'"
>

    {{-- =========================================================
         PAGE INTRO
         ========================================================= --}}

    <section class="wc-page-intro">

        <div>

            <span class="wc-section-kicker">
                TEKNISI OPERATIONS
            </span>

            <h2>
                Selamat datang, {{ $profile->user?->name ?? auth()->user()->name }}
            </h2>

            <p>
                Pantau pekerjaan lapangan, jadwal kunjungan,
                progres layanan, dan aktivitas pekerjaan Anda.
            </p>

        </div>

        <a
            href="{{ route('teknisi.orders.index') }}"
            class="wc-primary-action"
        >

            <svg
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.8"
                stroke-linecap="round"
                stroke-linejoin="round"
                aria-hidden="true"
            >
                <path d="M4 5.5h16v13H4z"/>
                <path d="M8 9h8"/>
                <path d="M8 13h5"/>
            </svg>

            Lihat pekerjaan

        </a>

    </section>


    {{-- =========================================================
         KPI
         ========================================================= --}}

    <section class="wc-kpi-grid">

        {{-- Pelanggan --}}
        <article class="wc-kpi-card">

            <div class="wc-kpi-top">

                <span class="wc-kpi-label">
                    Pelanggan
                </span>

                <span class="wc-kpi-icon">

                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.7"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        aria-hidden="true"
                    >
                        <circle cx="9" cy="7" r="3"/>
                        <path d="M3.5 20a5.5 5.5 0 0 1 11 0"/>
                        <path d="M16 5a3 3 0 0 1 0 5.8"/>
                        <path d="M18 14a4 4 0 0 1 3 4"/>
                    </svg>

                </span>

            </div>

            <strong>
                {{ number_format($totalCustomers) }}
            </strong>

            <span class="wc-kpi-meta">
                pelanggan yang pernah ditangani
            </span>

        </article>


        {{-- Pekerjaan Aktif --}}
        <article class="wc-kpi-card wc-kpi-attention">

            <div class="wc-kpi-top">

                <span class="wc-kpi-label">
                    Pekerjaan aktif
                </span>

                <span class="wc-kpi-icon">

                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.7"
                        stroke-linecap="round"
                        aria-hidden="true"
                    >
                        <circle cx="12" cy="12" r="9"/>
                        <path d="M12 7v5l3 2"/>
                    </svg>

                </span>

            </div>

            <strong>
                {{ number_format($totalActive) }}
            </strong>

            <span class="wc-kpi-meta">
                pekerjaan belum selesai
            </span>

        </article>


        {{-- Selesai --}}
        <article class="wc-kpi-card wc-kpi-success">

            <div class="wc-kpi-top">

                <span class="wc-kpi-label">
                    Pekerjaan selesai
                </span>

                <span class="wc-kpi-icon">

                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.7"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        aria-hidden="true"
                    >
                        <circle cx="12" cy="12" r="9"/>
                        <path d="m8 12 2.5 2.5L16 9"/>
                    </svg>

                </span>

            </div>

            <strong>
                {{ number_format($totalCompleted) }}
            </strong>

            <span class="wc-kpi-meta">
                total pekerjaan berhasil diselesaikan
            </span>

        </article>


        {{-- Rating --}}
        <article class="wc-kpi-card">

            <div class="wc-kpi-top">

                <span class="wc-kpi-label">
                    Rating pelanggan
                </span>

                <span class="wc-kpi-icon">

                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.7"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        aria-hidden="true"
                    >
                        <path d="m12 3 2.8 5.7 6.2.9-4.5 4.4 1.1 6.2-5.6-3-5.6 3 1.1-6.2L3 9.6l6.2-.9L12 3z"/>
                    </svg>

                </span>

            </div>

            <strong>
                {{ number_format($averageRating, 1) }}
            </strong>

            <span class="wc-kpi-meta">
                rata-rata penilaian pekerjaan
            </span>

        </article>

    </section>


    {{-- =========================================================
         MAIN OPERATIONS
         ========================================================= --}}

    <section class="wc-dashboard-grid wc-dashboard-grid-primary">


        {{-- =====================================================
             STATUS PEKERJAAN
             ===================================================== --}}

        <article class="wc-panel">

            <header class="wc-panel-header">

                <div>

                    <span class="wc-panel-eyebrow">
                        WORKLOAD
                    </span>

                    <h3>
                        Kondisi pekerjaan
                    </h3>

                </div>

                <span class="wc-panel-count">
                    {{ number_format($totalActive) }} aktif
                </span>

            </header>


            <div class="wc-status-list">

                @foreach($statusLabels as $status => $label)

                    @php
                        $count = $statusDistribution[$status] ?? 0;
                    @endphp

                    <div class="wc-status-row">

                        <div class="wc-status-name">

                            <span
                                class="wc-status-dot wc-status-{{ $status }}"
                                aria-hidden="true"
                            ></span>

                            <span>
                                {{ $label }}
                            </span>

                        </div>

                        <strong>
                            {{ number_format($count) }}
                        </strong>

                    </div>

                @endforeach

            </div>

        </article>


        {{-- =====================================================
             TREND 6 BULAN
             ===================================================== --}}

        <article class="wc-panel wc-panel-trend">

            <header class="wc-panel-header">

                <div>

                    <span class="wc-panel-eyebrow">
                        PERFORMANCE
                    </span>

                    <h3>
                        Aktivitas pekerjaan
                    </h3>

                </div>

                <span class="wc-panel-note">
                    6 bulan terakhir
                </span>

            </header>


            @php
                $maxMonthly = max(
                    $monthlyOrders->pluck('total')->all()
                ) ?: 1;
            @endphp


            <div class="wc-month-chart">

                @forelse($monthlyOrders as $month)

                    @php
                        $percentage =
                            ($month['total'] / $maxMonthly) * 100;
                    @endphp

                    <div class="wc-month-row">

                        <span class="wc-month-label">
                            {{ $month['label'] }}
                        </span>

                        <div class="wc-month-track">

                            <div
                                class="wc-month-bar"
                                style="width: {{ max($percentage, $month['total'] > 0 ? 3 : 0) }}%"
                            ></div>

                        </div>

                        <strong>
                            {{ number_format($month['total']) }}
                        </strong>

                    </div>

                @empty

                    <div class="wc-empty">
                        Belum ada data aktivitas pekerjaan.
                    </div>

                @endforelse

            </div>

        </article>

    </section>


    {{-- =========================================================
         TODAY + UPCOMING
         ========================================================= --}}

    <section class="wc-dashboard-grid wc-dashboard-grid-secondary">


        {{-- =====================================================
             JADWAL HARI INI
             ===================================================== --}}

        <article class="wc-panel">

            <header class="wc-panel-header">

                <div>

                    <span class="wc-panel-eyebrow">
                        TODAY
                    </span>

                    <h3>
                        Jadwal hari ini
                    </h3>

                </div>

                <a
                    href="{{ route('teknisi.orders.index') }}"
                    class="wc-panel-link"
                >
                    Lihat semua
                </a>

            </header>


            <div class="wc-order-feed">

                @forelse($todayOrders as $order)

                    <a
                        href="{{ route('teknisi.orders.show', $order->id) }}"
                        class="wc-order-feed-row"
                    >

                        <div class="wc-order-time">

                            {{ $order->scheduled_at?->format('H:i') ?? '--:--' }}

                        </div>


                        <div class="wc-order-feed-main">

                            <strong>
                                {{ $order->order_number }}
                            </strong>

                            <span>
                                {{ $order->customer?->name ?? 'Pelanggan' }}
                            </span>

                        </div>


                        <span
                            class="wc-status-badge wc-status-badge-{{ $order->status }}"
                        >

                            {{ $statusLabels[$order->status] ?? $order->status }}

                        </span>

                    </a>

                @empty

                    <div class="wc-empty wc-empty-large">

                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.5"
                            aria-hidden="true"
                        >
                            <rect
                                x="3.5"
                                y="5"
                                width="17"
                                height="15"
                                rx="2"
                            />
                            <path d="M7.5 3.5v3M16.5 3.5v3M3.5 9.5h17"/>
                        </svg>

                        <strong>
                            Tidak ada jadwal hari ini
                        </strong>

                        <span>
                            Belum ada pekerjaan yang dijadwalkan.
                        </span>

                    </div>

                @endforelse

            </div>

        </article>


        {{-- =====================================================
             PEKERJAAN MENDATANG
             ===================================================== --}}

        <article class="wc-panel">

            <header class="wc-panel-header">

                <div>

                    <span class="wc-panel-eyebrow">
                        UPCOMING
                    </span>

                    <h3>
                        Pekerjaan mendatang
                    </h3>

                </div>

                <span class="wc-panel-note">
                    Maks. 5 pekerjaan
                </span>

            </header>


            <div class="wc-order-feed">

                @forelse($upcomingOrders as $order)

                    <a
                        href="{{ route('teknisi.orders.show', $order->id) }}"
                        class="wc-order-feed-row"
                    >

                        <div class="wc-order-time">

                            {{ $order->scheduled_at?->format('d M') ?? '--' }}

                        </div>


                        <div class="wc-order-feed-main">

                            <strong>
                                {{ $order->order_number }}
                            </strong>

                            <span>
                                {{ $order->customer?->name ?? 'Pelanggan' }}
                                ·
                                {{ $order->scheduled_at?->format('H:i') ?? '--:--' }}
                            </span>

                        </div>


                        <span
                            class="wc-status-badge wc-status-badge-{{ $order->status }}"
                        >

                            {{ $statusLabels[$order->status] ?? $order->status }}

                        </span>

                    </a>

                @empty

                    <div class="wc-empty wc-empty-large">

                        <strong>
                            Belum ada pekerjaan mendatang
                        </strong>

                        <span>
                            Jadwal pekerjaan berikutnya akan muncul di sini.
                        </span>

                    </div>

                @endforelse

            </div>

        </article>

    </section>


    {{-- =========================================================
         RECENT ACTIVITY
         ========================================================= --}}

    <section class="wc-dashboard-grid wc-dashboard-grid-secondary">


        {{-- =====================================================
             AKTIVITAS TERBARU
             ===================================================== --}}

        <article class="wc-panel">

            <header class="wc-panel-header">

                <div>

                    <span class="wc-panel-eyebrow">
                        ACTIVITY
                    </span>

                    <h3>
                        Aktivitas terbaru
                    </h3>

                </div>

            </header>


            <div class="wc-activity-list">

                @forelse($recentActivities as $activity)

                    <div class="wc-activity-row">

                        <span class="wc-activity-marker">

                            @if($activity->status === 'completed')
                                ✓
                            @elseif($activity->status === 'cancelled')
                                ×
                            @else
                                →
                            @endif

                        </span>


                        <div>

                            <strong>
                                {{ $activity->order?->order_number ?? 'Pekerjaan' }}
                            </strong>

                            <span>

                                {{ $statusLabels[$activity->status] ?? $activity->status }}

                                @if($activity->changer?->name)
                                    · {{ $activity->changer->name }}
                                @endif

                            </span>

                        </div>


                        <time>
                            {{ $activity->created_at?->diffForHumans() }}
                        </time>

                    </div>

                @empty

                    <div class="wc-empty">
                        Belum ada aktivitas pekerjaan.
                    </div>

                @endforelse

            </div>

        </article>


        {{-- =====================================================
             GANGGUAN TERKAIT
             ===================================================== --}}

        <article class="wc-panel">

            <header class="wc-panel-header">

                <div>

                    <span class="wc-panel-eyebrow">
                        SERVICE DESK
                    </span>

                    <h3>
                        Gangguan terkait pekerjaan
                    </h3>

                </div>

            </header>


            <div class="wc-activity-list">

                @forelse($recentIssues as $issue)

                    @if($issue->order)

                        <a
                            href="{{ route('teknisi.orders.show', $issue->order->id) }}"
                            class="wc-activity-row"
                            style="text-decoration: none; color: inherit;"
                        >

                            <span class="wc-activity-marker">
                                !
                            </span>


                            <div>

                                <strong>
                                    {{ $issue->title }}
                                </strong>

                                <span>

                                    {{ $issue->user?->name ?? 'Pelanggan' }}

                                    @if($issue->category?->name)
                                        · {{ $issue->category->name }}
                                    @endif

                                </span>

                            </div>


                            <span>
                                →
                            </span>

                        </a>

                    @endif

                @empty

                    <div class="wc-empty">

                        <strong>
                            Tidak ada gangguan terkait
                        </strong>

                        <span>
                            Belum ada laporan gangguan pada pekerjaan Anda.
                        </span>

                    </div>

                @endforelse

            </div>

        </article>

    </section>


    {{-- =========================================================
         QUICK INFORMATION
         ========================================================= --}}

    <section class="wc-panel">

        <header class="wc-panel-header">

            <div>

                <span class="wc-panel-eyebrow">
                    PERFORMANCE
                </span>

                <h3>
                    Ringkasan bulan ini
                </h3>

            </div>

            <span class="wc-panel-note">
                {{ now()->format('F Y') }}
            </span>

        </header>


        <div class="wc-status-list">

            <div class="wc-status-row">

                <div class="wc-status-name">
                    <span class="wc-status-dot wc-status-completed"></span>

                    <span>
                        Pekerjaan selesai bulan ini
                    </span>
                </div>

                <strong>
                    {{ number_format($monthCompleted) }}
                </strong>

            </div>


            <div class="wc-status-row">

                <div class="wc-status-name">
                    <span class="wc-status-dot wc-status-in_progress"></span>

                    <span>
                        Pekerjaan aktif
                    </span>
                </div>

                <strong>
                    {{ number_format($totalActive) }}
                </strong>

            </div>


            <div class="wc-status-row">

                <div class="wc-status-name">
                    <span class="wc-status-dot wc-status-accepted"></span>

                    <span>
                        Status teknisi
                    </span>
                </div>

                <strong>
                    {{ $profile->is_available ? 'Tersedia' : 'Tidak tersedia' }}
                </strong>

            </div>

        </div>

    </section>

</x-layouts.app>