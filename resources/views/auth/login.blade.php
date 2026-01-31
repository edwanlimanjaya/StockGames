<x-guest-layout>
    <style>
        .text-justify {
            text-align: justify;
        }
    </style>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div id="consent-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" style="display:none;">
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg max-w-2xl w-full max-h-[80vh] overflow-y-auto text-sm text-gray-900 dark:text-gray-100">
            <div class="flex justify-center">
                <h2 class="text-lg font-bold mb-4">
                    Persetujuan Partisipasi
                </h2>
            </div>
            
            <p class="mb-4 text-justify">
                Halo! Saya Agape Desfandi, mahasiswa program Doktor Ilmu Manajemen, Universitas Kristen Satya Wacana, Salatiga.
                Pertama-tama saya dan tim peneliti mengucapkan terima kasih atas kesediaannya meluangkan waktu membuka web eksperimen ini.
            </p>

            <p class="mb-4 text-justify">
                Tujuan eksperimen ini adalah mengetahui pemilihan saham yang dipengaruhi oleh tipe attachment to God. 
                Hasil yang diharapkan adalah pengembangan literasi keuangan khususnya pasar modal, sehingga dapat membantu investor mengambil keputusan dengan beberapa risiko yang dapat diterima. 
                Kami memohon izin  dari partisipan untuk menggunakan respon dan data sebagai bahan analisis penelitian disertasi ini. 
                Data yang anda berikan akan dijaga kerahasiannya dan tidak akan diberikan kepada pihak yang tidak berkepentingan.
            </p>

            <p class="mb-4 text-justify">
                Pada akhir games, setiap peserta yang menyelesaikan dan menjelaskan pilihannya dengan lengkap akan mendapatkan apresiasi sebesar Rp 50.000,-/orang.
                Terdapat apresiasi tambahan sebesar Rp 250.000,-/orang untuk 6 partisipan dengan penjelasan terbaik.
            </p>

            <p class="mb-4 text-justify">
                Dengan mengikuti eksperimen ini, saya menyatakan bahwa:
            </p>

            <label for="agree-checkbox" class="flex items-center gap-3 mb-6">
                <input type="checkbox" id="agree-checkbox" class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                <span>Saya bersedia mengikuti eksperimen dan mengizinkan peneliti menggunakan data saya.</span>
            </label>

            <div class="flex justify-center">
                <button onclick="confirmConsent()" 
                    class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-25 transition ease-in-out duration-150">
                    Setuju & Mulai
                </button>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between mt-4">
            <!-- Remember Me -->
            <div class="block mt-4">
                <label for="remember_me" class="inline-flex items-center">
                    <input id="remember_me" type="checkbox" class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800" name="remember">
                    <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Remember me') }}</span>
                </label>
            </div>

            <div class="mt-4">
                @if (Route::has('password.request'))
                    <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800" href="{{ route('password.request') }}">
                        {{ __('Forgot your password?') }}
                    </a>
                @endif
            </div>
        </div>

        <div class="flex items-center justify-end mt-4">
            @if (Route::has('register'))
                <a
                    href="{{ route('register') }}"
                    class="inline-block px-5 py-1.5 dark:text-[#EDEDEC] border-transparent hover:border-[#1915014a] border text-[#1b1b18] dark:hover:border-[#3E3E3A] dark:hover:border-[#62605b] rounded-sm text-sm leading-normal">
                    Register
                </a>
            @endif
            <x-primary-button class="ms-3" type="button" onclick="showConsentModal()">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>
    <script>
        function showConsentModal() { 
            document.getElementById('consent-modal').style.display = 'flex'; 
        }

        function confirmConsent() {
            const checkbox = document.getElementById('agree-checkbox');
            if (!checkbox.checked) {
                alert('Please check the consent box first.');
                return;
            }
            // document.getElementById('consent-modal').style.display = 'none';
            document.querySelector('form[action="{{ route('login') }}"]').submit();
        }
    </script>
</x-guest-layout>
