<x-layouts.app
    :title="'Pekerjaan'"
    :header="'Pekerjaan'"
>

    @php

        $statusLabels = [
            'pending' => ['Pending', 'pending'],
            'confirmed' => ['Dikonfirmasi', 'confirmed'],
            'assigned' => ['Ditugaskan', 'assigned'],
            'accepted' => ['Diterima', 'accepted'],
            'on_the_way' => ['Perjalanan', 'on_the_way'],
            'in_progress' => ['Dikerjakan', 'in_progress'],
            'diagnosis' => ['Diagnosa', 'diagnosis'],
            'repairing' => ['Perbaikan', 'repairing'],
            'completed' => ['Selesai', 'completed'],
            'cancelled' => ['Dibatalkan', 'cancelled'],
        ];

    @endphp


    <section class="wc-page-intro">

        <div>

            <span class="wc-section-kicker">
                WORK ORDER MANAGEMENT
            </span>

            <h2>
                Pekerjaan
            </h2>

            <p>
                Kelola seluruh siklus pekerjaan mulai dari
                permintaan pelanggan hingga penyelesaian teknisi.
            </p>

        </div>


        <a
            href="{{ route('admin.orders.create') }}"
            class="wc-primary-action"
        >

            <svg
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.8"
            >
                <path d="M12 5v14"/>
                <path d="M5 12h14"/>
            </svg>

            Buat pekerjaan

        </a>

    </section>


    <section class="wc-filter-panel">

        <form
            method="GET"
            class="wc-order-filter"
        >

            <div class="wc-search-box wc-order-search">

                <svg
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.7"
                >
                    <circle cx="11" cy="11" r="7"/>
                    <path d="m20 20-4-4"/>
                </svg>

                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Cari nomor order atau pelanggan..."
                >

            </div>


            <select
                name="status"
                class="wc-filter-select"
                onchange="this.form.submit()"
            >

                <option value="">
                    Semua status
                </option>

                @foreach($statuses as $status)

                    <option
                        value="{{ $status }}"
                        {{ request('status') === $status ? 'selected' : '' }}
                    >
                        {{ $statusLabels[$status][0] ?? $status }}
                    </option>

                @endforeach

            </select>


            <select
                name="technician_id"
                class="wc-filter-select"
                onchange="this.form.submit()"
            >

                <option value="">
                    Semua teknisi
                </option>

                @foreach($technicians as $technician)

                    <option
                        value="{{ $technician->id }}"
                        {{ request('technician_id') == $technician->id ? 'selected' : '' }}
                    >
                        {{ $technician->user?->name }}
                    </option>

                @endforeach

            </select>


            <input
                type="date"
                name="date_from"
                value="{{ request('date_from') }}"
                class="wc-filter-date"
                title="Tanggal mulai"
            >


            <input
                type="date"
                name="date_to"
                value="{{ request('date_to') }}"
                class="wc-filter-date"
                title="Tanggal akhir"
            >


            <button
                type="submit"
                class="wc-filter-button"
            >
                Filter
            </button>

        </form>


        <div class="wc-result-meta">

            <strong>
                {{ number_format($orders->total()) }}
            </strong>

            pekerjaan ditemukan

        </div>

    </section>


    <section class="wc-table-panel">

        <div class="wc-table-wrap">

            <table class="wc-data-table wc-orders-table">

                <thead>

                    <tr>

                        <th>
                            Work order
                        </th>

                        <th>
                            Pelanggan
                        </th>

                        <th>
                            Teknisi
                        </th>

                        <th>
                            Jadwal
                        </th>

                        <th class="wc-align-right">
                            Nilai
                        </th>

                        <th>
                            Status
                        </th>

                        <th></th>

                    </tr>

                </thead>


                <tbody>

                    @forelse($orders as $order)

                        <tr>

                            <td>

                                <div class="wc-order-cell">

                                    <strong>
                                        {{ $order->order_number }}
                                    </strong>

                                    <span>
                                        Dibuat {{ $order->created_at?->translatedFormat('d M Y') }}
                                    </span>

                                </div>

                            </td>


                            <td>

                                <div class="wc-contact-cell">

                                    <strong>
                                        {{ $order->customer?->name ?? '—' }}
                                    </strong>

                                    <span>
                                        {{ $order->items->first()?->service?->name ?? 'Layanan' }}
                                    </span>

                                </div>

                            </td>


                            <td>

                                @if($order->technician?->user)

                                    <div class="wc-mini-person">

                                        <div class="wc-mini-avatar">
                                            {{ strtoupper(substr($order->technician->user->name, 0, 2)) }}
                                        </div>

                                        <span>
                                            {{ $order->technician->user->name }}
                                        </span>

                                    </div>

                                @else

                                    <span class="wc-unassigned">
                                        Belum ditugaskan
                                    </span>

                                @endif

                            </td>


                            <td>

                                <div class="wc-schedule-cell">

                                    <strong>
                                        {{ $order->scheduled_at?->format('d M Y') ?? '—' }}
                                    </strong>

                                    <span>
                                        {{ $order->scheduled_at?->format('H:i') ?? '—' }}
                                    </span>

                                </div>

                            </td>


                            <td class="wc-align-right">

                                <strong class="wc-money">

                                    Rp
                                    {{ number_format($order->total_amount, 0, ',', '.') }}

                                </strong>

                            </td>


                            <td>

                                <span class="wc-status-badge wc-status-badge-{{ $order->status }}">

                                    <span></span>

                                    {{ $statusLabels[$order->status][0] ?? $order->status }}

                                </span>

                            </td>


                            <td class="wc-table-action">

                                <a
                                    href="{{ route('admin.orders.show', $order->id) }}"
                                >

                                    Buka

                                    <svg
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="1.7"
                                    >
                                        <path d="m9 18 6-6-6-6"/>
                                    </svg>

                                </a>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="7"
                                class="wc-table-empty"
                            >

                                <strong>
                                    Tidak ada pekerjaan ditemukan
                                </strong>

                                <span>
                                    Coba ubah filter atau kata pencarian.
                                </span>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        <div class="wc-pagination">
            {{ $orders->links() }}
        </div>

    </section>

</x-layouts.app>