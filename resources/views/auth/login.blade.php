<x-layouts.guest :title="'Masuk ke Akun'">

    <main class="wf-login-shell">

        {{-- =====================================================
             BRAND PANEL
             ===================================================== --}}
        <section class="wf-login-brand-panel">

            <div class="wf-login-brand-inner">

                <div class="wf-login-brand-header">

                    <div class="wf-login-logo" aria-hidden="true">
                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.9"
                            stroke-linecap="round"
                        >
                            <path d="M2.5 9.8a14 14 0 0 1 19 0" />
                            <path d="M5.8 13.1a9.2 9.2 0 0 1 12.4 0" />
                            <path d="M9.2 16.4a4.4 4.4 0 0 1 5.6 0" />
                            <path d="M12 20h.01" />
                        </svg>
                    </div>

                    <div>
                        <div class="wf-login-brand-name">
                            WiFi<span>Care</span>
                        </div>

                        <div class="wf-login-brand-caption">
                            Operational platform
                        </div>
                    </div>

                </div>


                <div class="wf-login-eyebrow">
                    Manajemen Layanan Teknis
                </div>


                <h1 class="wf-login-brand-title">
                    Kendalikan operasional.
                    <span>Layani lebih cepat.</span>
                </h1>


                <p class="wf-login-brand-copy">
                    WiFiCare menyatukan pekerjaan teknisi, jadwal kunjungan,
                    dan informasi layanan dalam satu ruang kerja yang rapi
                    dan terukur.
                </p>


                <div
                    class="wf-login-service-list"
                    aria-label="Fokus operasional WiFiCare"
                >

                    <div class="wf-login-service-item">

                        <span
                            class="wf-login-service-icon"
                            aria-hidden="true"
                        >
                            <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.7"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            >
                                <rect
                                    x="4"
                                    y="4"
                                    width="16"
                                    height="16"
                                    rx="2"
                                />
                                <path d="M8 9h8M8 13h5M8 17h3" />
                            </svg>
                        </span>

                        <span class="wf-login-service-label">
                            Work orders
                        </span>

                    </div>


                    <div class="wf-login-service-item">

                        <span
                            class="wf-login-service-icon"
                            aria-hidden="true"
                        >
                            <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.7"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            >
                                <rect
                                    x="3.5"
                                    y="5"
                                    width="17"
                                    height="15"
                                    rx="2"
                                />
                                <path d="M7.5 3.5v3M16.5 3.5v3M3.5 9.5h17" />
                            </svg>
                        </span>

                        <span class="wf-login-service-label">
                            Scheduling
                        </span>

                    </div>


                    <div class="wf-login-service-item">

                        <span
                            class="wf-login-service-icon"
                            aria-hidden="true"
                        >
                            <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.7"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            >
                                <path d="M4 18V9m5 9V5m5 13v-7m5 7V3" />
                            </svg>
                        </span>

                        <span class="wf-login-service-label">
                            Service reports
                        </span>

                    </div>

                </div>


                <div class="wf-login-internal-status">
                    <span></span>
                    Ruang kerja internal WiFiCare
                </div>

            </div>

        </section>


        {{-- =====================================================
             LOGIN PANEL
             ===================================================== --}}
        <section class="wf-login-form-panel">

            <div class="wf-login-form-wrap">

                <div class="wf-login-card">

                    {{-- Header --}}
                    <div class="wf-login-card-header">

                        <div class="wf-login-card-brand">

                            <div
                                class="wf-login-logo wf-login-logo-small"
                                aria-hidden="true"
                            >
                                <svg
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.9"
                                    stroke-linecap="round"
                                >
                                    <path d="M2.5 9.8a14 14 0 0 1 19 0" />
                                    <path d="M5.8 13.1a9.2 9.2 0 0 1 12.4 0" />
                                    <path d="M9.2 16.4a4.4 4.4 0 0 1 5.6 0" />
                                    <path d="M12 20h.01" />
                                </svg>
                            </div>

                            <div>

                                <div class="wf-login-logo-wordmark">
                                    WiFi<span>Care</span>
                                </div>

                                <div class="wf-login-logo-caption">
                                    Internal workspace
                                </div>

                            </div>

                        </div>

                    </div>


                    {{-- Content --}}
                    <div class="wf-login-card-content">

                        <div class="wf-login-heading">

                            <h2>
                                Masuk ke ruang kerja
                            </h2>

                            <p>
                                Gunakan akun terdaftar untuk melanjutkan
                                ke sistem operasional WiFiCare.
                            </p>

                        </div>


                        {{-- Error Summary --}}
                        @if ($errors->any())

                            <div
                                class="wf-login-alert"
                                role="alert"
                                aria-live="polite"
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
                                    <circle
                                        cx="12"
                                        cy="12"
                                        r="9"
                                    />

                                    <path d="M12 8v4M12 16h.01" />
                                </svg>

                                <div>

                                    <strong>
                                        Login belum dapat diproses.
                                    </strong>

                                    <p>
                                        Periksa kembali data yang Anda masukkan.
                                    </p>

                                </div>

                            </div>

                        @endif


                        {{-- Login Form --}}
                        <form
                            method="POST"
                            action="{{ route('login') }}"
                            id="login-form"
                        >

                            @csrf


                            {{-- EMAIL --}}
                            <div class="wf-login-field">

                                <label
                                    for="email"
                                    class="wf-login-label"
                                >
                                    Email kerja
                                </label>

                                <div class="wf-login-input-shell">

                                    <svg
                                        class="wf-login-input-icon"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="1.7"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        aria-hidden="true"
                                    >
                                        <rect
                                            x="3.5"
                                            y="5"
                                            width="17"
                                            height="14"
                                            rx="2"
                                        />

                                        <path d="m4.5 7 7.5 5 7.5-5" />
                                    </svg>

                                    <input
                                        type="email"
                                        id="email"
                                        name="email"
                                        value="{{ old('email') }}"
                                        autocomplete="username"
                                        inputmode="email"
                                        autocapitalize="none"
                                        spellcheck="false"
                                        required
                                        autofocus
                                        placeholder="nama@perusahaan.com"
                                        class="wf-login-input @error('email') wf-login-input-error @enderror"
                                        aria-describedby="email-error"
                                    >

                                </div>

                                @error('email')

                                    <p
                                        id="email-error"
                                        class="wf-login-error"
                                        role="alert"
                                    >
                                        {{ $message }}
                                    </p>

                                @enderror

                            </div>


                            {{-- PASSWORD --}}
                            <div class="wf-login-field">

                                <label
                                    for="password"
                                    class="wf-login-label"
                                >
                                    Password
                                </label>

                                <div class="wf-login-input-shell">

                                    <svg
                                        class="wf-login-input-icon"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="1.7"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        aria-hidden="true"
                                    >
                                        <rect
                                            x="4.5"
                                            y="10"
                                            width="15"
                                            height="10"
                                            rx="2"
                                        />

                                        <path d="M8 10V7.8a4 4 0 0 1 8 0V10" />
                                    </svg>


                                    <input
                                        type="password"
                                        id="password"
                                        name="password"
                                        autocomplete="current-password"
                                        required
                                        placeholder="Masukkan password"
                                        class="wf-login-input @error('password') wf-login-input-error @enderror"
                                        aria-describedby="password-error"
                                    >


                                    <button
                                        type="button"
                                        class="wf-login-toggle"
                                        id="toggle-password"
                                        aria-label="Tampilkan password"
                                        aria-controls="password"
                                        aria-pressed="false"
                                    >

                                        <svg
                                            id="eye-closed"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="1.7"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            aria-hidden="true"
                                        >
                                            <path d="M2.5 12s3.5-5 9.5-5 9.5 5 9.5 5-3.5 5-9.5 5-9.5-5-9.5-5Z" />
                                            <circle
                                                cx="12"
                                                cy="12"
                                                r="2.2"
                                            />
                                        </svg>


                                        <svg
                                            id="eye-open"
                                            class="wf-hidden"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="1.7"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            aria-hidden="true"
                                        >
                                            <path d="M3 3l18 18" />
                                            <path d="M10.6 10.6a2 2 0 0 0 2.8 2.8" />
                                            <path d="M9.9 5.3A10.5 10.5 0 0 1 12 5c6 0 9.5 7 9.5 7a17.6 17.6 0 0 1-3.1 3.7M6.2 6.2C3.8 8 2.5 12 2.5 12S6 19 12 19a10.3 10.3 0 0 0 4-.8" />
                                        </svg>

                                    </button>

                                </div>

                                @error('password')

                                    <p
                                        id="password-error"
                                        class="wf-login-error"
                                        role="alert"
                                    >
                                        {{ $message }}
                                    </p>

                                @enderror

                            </div>


                            {{-- OPTIONS --}}
                            <div class="wf-login-options">

                                <label
                                    for="remember"
                                    class="wf-login-remember"
                                >

                                    <input
                                        type="checkbox"
                                        id="remember"
                                        name="remember"
                                        value="1"
                                        {{ old('remember') ? 'checked' : '' }}
                                    >

                                    <span>
                                        Ingat perangkat ini
                                    </span>

                                </label>


                                <span class="wf-login-authorized">
                                    Akses terotorisasi
                                </span>

                            </div>


                            {{-- SUBMIT --}}
                            <button
                                type="submit"
                                class="wf-login-submit"
                                id="login-submit"
                            >

                                <span id="login-submit-label">
                                    Masuk ke WiFiCare
                                </span>

                                <svg
                                    id="login-submit-spinner"
                                    class="wf-hidden wf-spinner"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    aria-hidden="true"
                                >
                                    <circle
                                        cx="12"
                                        cy="12"
                                        r="9"
                                        stroke="currentColor"
                                        stroke-opacity=".3"
                                        stroke-width="2"
                                    />

                                    <path
                                        d="M21 12a9 9 0 0 0-9-9"
                                        stroke="currentColor"
                                        stroke-width="2"
                                        stroke-linecap="round"
                                    />
                                </svg>

                            </button>

                        </form>


                        <div class="wf-login-security-note">

                            <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.7"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                aria-hidden="true"
                            >
                                <rect
                                    x="4.5"
                                    y="10"
                                    width="15"
                                    height="10"
                                    rx="2"
                                />

                                <path d="M8 10V7.8a4 4 0 0 1 8 0V10" />
                            </svg>

                            <span>
                                Sesi login dilindungi oleh autentikasi dan CSRF Laravel.
                            </span>

                        </div>

                    </div>

                </div>


                <p class="wf-login-footer">
                    &copy; {{ date('Y') }} WiFiCare · Sistem Manajemen Teknisi & Layanan Internet
                </p>

            </div>

        </section>

    </main>


    @push('scripts')

        <script>
            (() => {

                const form = document.getElementById('login-form');
                const password = document.getElementById('password');
                const toggle = document.getElementById('toggle-password');

                const eyeClosed = document.getElementById('eye-closed');
                const eyeOpen = document.getElementById('eye-open');

                const submit = document.getElementById('login-submit');
                const submitLabel = document.getElementById('login-submit-label');
                const spinner = document.getElementById('login-submit-spinner');


                if (toggle && password) {

                    toggle.addEventListener('click', () => {

                        const showing = password.type === 'text';

                        password.type = showing
                            ? 'password'
                            : 'text';

                        toggle.setAttribute(
                            'aria-pressed',
                            String(!showing)
                        );

                        toggle.setAttribute(
                            'aria-label',
                            showing
                                ? 'Tampilkan password'
                                : 'Sembunyikan password'
                        );

                        eyeClosed.classList.toggle(
                            'wf-hidden',
                            !showing
                        );

                        eyeOpen.classList.toggle(
                            'wf-hidden',
                            showing
                        );

                    });

                }


                if (form) {

                    form.addEventListener('submit', () => {

                        if (!form.checkValidity()) {
                            return;
                        }

                        if (submit) {
                            submit.disabled = true;
                        }

                        if (submitLabel) {
                            submitLabel.textContent = 'Memverifikasi…';
                        }

                        if (spinner) {
                            spinner.classList.remove('wf-hidden');
                        }

                    });

                }

            })();
        </script>

    @endpush

</x-layouts.guest>