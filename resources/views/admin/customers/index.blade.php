<x-layouts.app
    :title="'Pelanggan'"
    :header="'Pelanggan'"
>

    <section class="wc-page-intro">

        <div>

            <span class="wc-section-kicker">
                CUSTOMER DIRECTORY
            </span>

            <h2>
                Pelanggan
            </h2>

            <p>
                Kelola akun pelanggan dan riwayat layanan
                dari satu direktori terpusat.
            </p>

        </div>


        <a
            href="{{ route('admin.customers.create') }}"
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

            Tambah pelanggan

        </a>

    </section>


    {{-- Search / filter --}}
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
                    placeholder="Cari nama, email, atau telepon..."
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

                <option
                    value="active"
                    {{ request('status') === 'active' ? 'selected' : '' }}
                >
                    Aktif
                </option>

                <option
                    value="inactive"
                    {{ request('status') === 'inactive' ? 'selected' : '' }}
                >
                    Nonaktif
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
                {{ number_format($customers->total()) }}
            </strong>

            pelanggan ditemukan

        </div>

    </section>


    {{-- Customer table --}}
    <section class="wc-table-panel">

        <div class="wc-table-wrap">

            <table class="wc-data-table">

                <thead>

                    <tr>

                        <th>
                            Pelanggan
                        </th>

                        <th>
                            Kontak
                        </th>

                        <th>
                            Lokasi
                        </th>

                        <th class="wc-align-center">
                            Order
                        </th>

                        <th>
                            Status
                        </th>

                        <th></th>

                    </tr>

                </thead>


                <tbody>

                    @forelse($customers as $customer)

                        <tr>

                            <td>

                                <div class="wc-person-cell">

                                    <div class="wc-person-avatar">
                                        {{ strtoupper(substr($customer->name, 0, 2)) }}
                                    </div>

                                    <div>

                                        <strong>
                                            {{ $customer->name }}
                                        </strong>

                                        <span>
                                            ID #{{ str_pad($customer->id, 5, '0', STR_PAD_LEFT) }}
                                        </span>

                                    </div>

                                </div>

                            </td>


                            <td>

                                <div class="wc-contact-cell">

                                    <strong>
                                        {{ $customer->email }}
                                    </strong>

                                    <span>
                                        {{ $customer->phone ?? 'Telepon belum tersedia' }}
                                    </span>

                                </div>

                            </td>


                            <td>

                                <span class="wc-location-cell">

                                    <svg
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="1.6"
                                    >
                                        <path d="M12 21s7-6.1 7-12a7 7 0 1 0-14 0c0 5.9 7 12 7 12Z"/>
                                        <circle cx="12" cy="9" r="2.2"/>
                                    </svg>

                                    {{ $customer->addresses->first()?->city ?? 'Belum ada alamat' }}

                                </span>

                            </td>


                            <td class="wc-align-center">

                                <strong class="wc-number">
                                    {{ $customer->orders_as_customer_count }}
                                </strong>

                            </td>


                            <td>

                                <span class="wc-state-pill {{ $customer->is_active ? 'is-success' : 'is-muted' }}">

                                    <span></span>

                                    {{ $customer->is_active ? 'Aktif' : 'Nonaktif' }}

                                </span>

                            </td>


                            <td class="wc-table-action">

                                <a
                                    href="{{ route('admin.customers.show', $customer->id) }}"
                                >

                                    Detail

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
                                colspan="6"
                                class="wc-table-empty"
                            >

                                <strong>
                                    Tidak ada pelanggan ditemukan
                                </strong>

                                <span>
                                    Coba ubah kata pencarian atau filter.
                                </span>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        <div class="wc-pagination">
            {{ $customers->links() }}
        </div>

    </section>

</x-layouts.app>