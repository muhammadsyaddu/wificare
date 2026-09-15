<x-layouts.app
    :title="'Teknisi'"
    :header="'Teknisi'"
>

    <section class="wc-page-intro">

        <div>

            <span class="wc-section-kicker">
                FIELD OPERATIONS
            </span>

            <h2>
                Teknisi
            </h2>

            <p>
                Pantau status verifikasi, kompetensi,
                pengalaman, dan kesiapan teknisi lapangan.
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
                    placeholder="Cari nama, kode, atau spesialisasi..."
                >

            </div>


            <select
                name="status"
                class="wc-filter-select"
                onchange="this.form.submit()"
            >

                <option value="">
                    Semua verifikasi
                </option>

                @foreach([
                    'pending' => 'Pending',
                    'verified' => 'Terverifikasi',
                    'rejected' => 'Ditolak',
                    'suspended' => 'Ditangguhkan'
                ] as $value => $label)

                    <option
                        value="{{ $value }}"
                        {{ request('status') === $value ? 'selected' : '' }}
                    >
                        {{ $label }}
                    </option>

                @endforeach

            </select>


            <select
                name="availability"
                class="wc-filter-select"
                onchange="this.form.submit()"
            >

                <option value="">
                    Semua ketersediaan
                </option>

                <option
                    value="available"
                    {{ request('availability') === 'available' ? 'selected' : '' }}
                >
                    Tersedia
                </option>

                <option
                    value="unavailable"
                    {{ request('availability') === 'unavailable' ? 'selected' : '' }}
                >
                    Tidak tersedia
                </option>

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
                {{ number_format($technicians->total()) }}
            </strong>

            teknisi ditemukan

        </div>

    </section>


    <section class="wc-technician-grid">

        @forelse($technicians as $tech)

            @php

                $profile = $tech->technicianProfile;

                $verificationLabels = [
                    'pending' => 'Pending',
                    'verified' => 'Terverifikasi',
                    'rejected' => 'Ditolak',
                    'suspended' => 'Ditangguhkan',
                ];

            @endphp


            <article class="wc-technician-card">

                <div class="wc-tech-card-top">

                    <div class="wc-tech-avatar">
                        {{ strtoupper(substr($tech->name, 0, 2)) }}
                    </div>


                    <div class="wc-tech-identity">

                        <strong>
                            {{ $tech->name }}
                        </strong>

                        <span>
                            {{ $profile?->technician_code ?? 'Kode belum tersedia' }}
                        </span>

                    </div>


                    @if($profile)

                        <span class="wc-verification-pill wc-verification-{{ $profile->verification_status }}">

                            {{ $verificationLabels[$profile->verification_status] ?? $profile->verification_status }}

                        </span>

                    @endif

                </div>


                <div class="wc-tech-specialization">

                    <span>
                        Spesialisasi
                    </span>

                    <strong>
                        {{ $profile?->specialization ?? 'Belum ditentukan' }}
                    </strong>

                </div>


                <div class="wc-tech-meta">

                    <div>

                        <span>
                            Pengalaman
                        </span>

                        <strong>
                            {{ $profile?->experience_years ?? 0 }}
                            tahun
                        </strong>

                    </div>


                    <div>

                        <span>
                            Kesiapan
                        </span>

                        @if($profile?->is_available)

                            <strong class="wc-availability is-available">

                                <span></span>

                                Tersedia

                            </strong>

                        @else

                            <strong class="wc-availability is-unavailable">

                                <span></span>

                                Tidak tersedia

                            </strong>

                        @endif

                    </div>

                </div>


                <a
                    href="{{ route('admin.technicians.show', $tech->id) }}"
                    class="wc-tech-detail"
                >

                    Buka profil teknisi

                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.7"
                    >
                        <path d="m9 18 6-6-6-6"/>
                    </svg>

                </a>

            </article>

        @empty

            <div class="wc-empty wc-empty-large">

                <strong>
                    Tidak ada teknisi ditemukan
                </strong>

                <span>
                    Coba ubah pencarian atau filter.
                </span>

            </div>

        @endforelse

    </section>


    <div class="wc-pagination">
        {{ $technicians->links() }}
    </div>

</x-layouts.app>