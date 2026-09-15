<x-layouts.app
    :title="'Gangguan'"
    :header="'Gangguan'"
>

    <section class="wc-page-intro">

        <div>

            <span class="wc-section-kicker">
                SERVICE DESK
            </span>

            <h2>
                Laporan gangguan
            </h2>

            <p>
                Pantau laporan kendala pelanggan dan
                tindak lanjuti gangguan yang belum memiliki pekerjaan.
            </p>

        </div>

    </section>


    <section class="wc-filter-panel">

        <form
            method="GET"
            class="wc-filter-form"
        >

            <div class="wc-search-box">

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
                    placeholder="Cari judul atau pelanggan..."
                >

            </div>


            <select
                name="category"
                class="wc-filter-select"
                onchange="this.form.submit()"
            >

                <option value="">
                    Semua kategori
                </option>

                @foreach($categories as $category)

                    <option
                        value="{{ $category->id }}"
                        {{ request('category') == $category->id ? 'selected' : '' }}
                    >
                        {{ $category->name }}
                    </option>

                @endforeach

            </select>


            <button
                type="submit"
                class="wc-filter-button"
            >
                Terapkan
            </button>

        </form>


        <div class="wc-result-meta">

            <strong>
                {{ number_format($issues->total()) }}
            </strong>

            laporan ditemukan

        </div>

    </section>


    <section class="wc-incident-list">

        @forelse($issues as $issue)

            @php

                $hasOrder = (bool) $issue->order;

            @endphp


            <article class="wc-incident-card">

                <div class="wc-incident-indicator {{ $hasOrder ? 'is-assigned' : 'is-open' }}">
                </div>


                <div class="wc-incident-main">

                    <div class="wc-incident-heading">

                        <div>

                            <span class="wc-incident-category">
                                {{ $issue->category?->name ?? 'Gangguan umum' }}
                            </span>

                            <h3>
                                {{ $issue->title }}
                            </h3>

                        </div>


                        @if($hasOrder)

                            <span class="wc-state-pill is-info">

                                <span></span>

                                Order dibuat

                            </span>

                        @else

                            <span class="wc-state-pill is-warning">

                                <span></span>

                                Perlu ditindaklanjuti

                            </span>

                        @endif

                    </div>


                    <p class="wc-incident-description">

                        {{ $issue->description }}

                    </p>


                    <div class="wc-incident-meta">

                        <span>

                            <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.6"
                            >
                                <circle cx="9" cy="7" r="3"/>
                                <path d="M3.5 20a5.5 5.5 0 0 1 11 0"/>
                            </svg>

                            {{ $issue->user?->name ?? 'Pelanggan' }}

                        </span>


                        <span>

                            <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.6"
                            >
                                <rect x="4" y="5" width="16" height="15" rx="2"/>
                                <path d="M8 3.5v3M16 3.5v3M4 9h16"/>
                            </svg>

                            {{ $issue->created_at->translatedFormat('d M Y, H:i') }}

                        </span>


                        @if($hasOrder)

                            <span>

                                <svg
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.6"
                                >
                                    <rect x="4" y="3.5" width="16" height="17" rx="2"/>
                                    <path d="M8 8h8M8 12h5"/>
                                </svg>

                                {{ $issue->order->order_number }}

                            </span>

                        @endif

                    </div>

                </div>


                <div class="wc-incident-action">

                    @if(!$hasOrder)

                        <a
                            href="{{ route('admin.orders.create', ['issue_id' => $issue->id]) }}"
                            class="wc-primary-action wc-primary-action-small"
                        >

                            Buat pekerjaan

                            <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.7"
                            >
                                <path d="m9 18 6-6-6-6"/>
                            </svg>

                        </a>

                    @else

                        <a
                            href="{{ route('admin.orders.show', $issue->order->id) }}"
                            class="wc-secondary-action"
                        >
                            Buka order
                        </a>

                    @endif

                </div>

            </article>

        @empty

            <div class="wc-empty wc-empty-large">

                <strong>
                    Tidak ada laporan gangguan
                </strong>

                <span>
                    Semua laporan yang sesuai filter akan muncul di sini.
                </span>

            </div>

        @endforelse

    </section>


    <div class="wc-pagination">
        {{ $issues->links() }}
    </div>

</x-layouts.app>