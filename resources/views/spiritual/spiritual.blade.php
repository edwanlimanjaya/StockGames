<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Attachment to God') }}
        </h2>
    </x-slot>

    @section('background-class', 'bg-spiritual')

    <div id="rekening-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg max-w-md w-full">
            <h2 class="mb-1 mt-1">Terima kasih!</h2>
            
            <p class="mb-1 mt-1">
                Kami tim peneliti mengucapkan terima kasih kepada pada investor yang telah menyelesaikan games pemilihan saham ini, oleh karena itu sebagai bentuk apreasiasi dari kami, para peserta games yang menyelesaikan berhak mendapatkan uang apresiasi sebesar Rp 50.000, silakan mengisi nomor rekening di bawah ini:
            </p>
            
            <x-input-label for="bank_account_number" :value="__('Bank Account Number')" />
            <input type="text" id="bank_account_number" class="w-full border px-2 py-1 mb-4 mt-1" pattern="\d{10,16}" inputmode="numeric" minlength="10" maxlength="16" name="bank_account_number" :value="old('bank_account_number')" required autofocus autocomplete="bank_account_number">
            
            <x-input-label for="bank_account_name" :value="__('Bank Account Name')" />
            <input type="text" id="bank_account_name" class="w-full border px-2 py-1 mb-4 mt-1" name="bank_account_name" :value="old('bank_account_name')" required autofocus autocomplete="bank_account_name">
            
            <div class="flex justify-end gap-2">
                <button onclick="closeRekeningModal()" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-25 transition ease-in-out duration-150 mr-1">Batal</button>
                <button onclick="submitFinal()" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-25 transition ease-in-out duration-150 ml-1">Submit</button>
            </div>
        </div>
    </div>


    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg"> -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg" style="background-color: rgba(255,255,255,0.8);">    
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div>
                        <x-input-label id="question-title">
                            {{ $questions[0]->title }}
                        </x-input-label>
                        <div id="answer-container" class="flex gap-4 mt-2">
                        </div>
                    </div>
                    <div class="mt-6 space-y-6">
                        <div class="flex items-center gap-4">
                            <x-primary-button id="next-btn" onclick="nextQuestion()">
                                {{ __('Next') }}
                            </x-primary-button>
                        </div>
                    </div>
                    <form method="post" id="submit-form" action="{{ route('spiritual.submit') }}" class="mt-6 space-y-6" style="display: none;">
                        @csrf

                        <div>
                            <input type="hidden" name="scores" id="scores-json">
                        </div>
                        <div class="flex items-center gap-4">
                            <x-primary-button>{{ __('Submit') }}</x-primary-button>

                            @if (session('status') === 'answer-updated')
                                <p
                                    x-data="{ show: true }"
                                    x-show="show"
                                    x-transition
                                    x-init="setTimeout(() => show = false, 2000)"
                                    class="text-sm text-gray-600 dark:text-gray-400"
                                >{{ __('Submit.') }}</p>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        const questions = @json($questions);
        let currentIndex = 0;
        let scores = {};

        function updateNumber(value) {
            document.getElementById('number-display').innerText = value;
        }


        function renderAnswerInput(index) {
            const question = questions[index];
            const container = document.getElementById('answer-container');
            container.innerHTML = '';

            const baseInputClass = 'w-full h-2 mt-2 bg-gray-300 rounded-lg appearance-none cursor-pointer focus:outline-none focus:ring-0';
            const sliderDirection = question.direction === 'reverse' ? 'direction-rtl' : '';

            // label selalu 1–4
            const labels = ['1','2','3','4'];

            const labelHTML = labels.map(label => `
                <div class="flex flex-col items-center w-1/4">
                    <div style="height: 0.8em; border-left: 0.15em solid black;"></div>
                    <span>${label}</span>
                </div>
            `).join('');

            container.innerHTML = `
                <div class="flex flex-col max-w-md">
                    <!-- Slider -->
                    <input type="range" id="score-input" name="score-input" value="2" min="1" max="4" step="1"
                        class="${baseInputClass} ${sliderDirection}" required />

                    <!-- Angka + garis vertikal -->
                    <div class="flex justify-between text-sm mt-2 w-full">
                        ${labelHTML}
                    </div>
                </div>
            `;
        }


        function nextQuestion() {
            const rawValue = parseInt(document.getElementById('score-input').value);
            const question = questions[currentIndex];

            if (isNaN(rawValue)) {
                alert('Answer cannot be empty.');
                return;
            }

            const finalValue = question.direction === 'reverse' ? 5 - rawValue : rawValue;
            scores[question.id] = finalValue;
            console.log(scores);
            currentIndex++;

            if (currentIndex < questions.length) {
                document.getElementById('question-title').innerText = questions[currentIndex].title;
                renderAnswerInput(currentIndex);
            } else {
                document.getElementById('next-btn').style.display = 'none';
                document.getElementById('submit-form').style.display = 'block';
            }
        }

        renderAnswerInput(currentIndex);

        document.getElementById('submit-form').addEventListener('submit', function(e) {
            e.preventDefault(); // cegah submit langsung
            document.getElementById('scores-json').value = JSON.stringify(scores);
            document.getElementById('rekening-modal').classList.remove('hidden');
        });

        function submitFinal() {
            const rekening = document.getElementById('bank_account_number').value.trim();
            if (!rekening) {
                alert('Nomor rekening wajib diisi.');
                return;
            }

            const bank = document.getElementById('bank_account_name').value.trim();
            if (!bank) {
                alert('Nama bank wajib diisi.');
                return;
            }

            // tambahkan ke form hidden
            const form = document.getElementById('submit-form');
            const rekeningHidden = document.createElement('input');
            rekeningHidden.type = 'hidden';
            rekeningHidden.name = 'bank_account_number';
            rekeningHidden.value = rekening;

            const bankHidden = document.createElement('input');
            bankHidden.type = 'hidden';
            bankHidden.name = 'bank_account_name';
            bankHidden.value = bank;

            form.appendChild(rekeningHidden);
            form.appendChild(bankHidden);

            form.submit(); // kirim beneran ke server
        }

        function closeRekeningModal() {
            document.getElementById('rekening-modal').classList.add('hidden');
        }

    </script>
</x-app-layout>
