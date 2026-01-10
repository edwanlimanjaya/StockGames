<x-app-layout>
    <style>
        .text-justify {
            text-align: justify;
        }
    </style>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Financial Literacy') }}
        </h2>
    </x-slot>
    
    @section('background-class', 'bg-financial')

    <div id="consent-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg max-w-2xl w-full max-h-[80vh] overflow-y-auto text-sm text-gray-900 dark:text-gray-100">
            <div class="flex justify-center">
                <h2 class="text-lg font-bold mb-4">
                    Selamat Datang, Games Investasi
                </h2>
            </div>
            
            <p class="mb-4 text-justify">
                Sebelum masuk pada sesi games, para investor diminta untuk melakukan self assesment dengan menjawab pertanyaan pendahuluan. Para investor diharuskan menjawab dengan sejujur-jujurnya sesuai dengan kondisi yang dialami para investor. 
            </p>

            <label for="agree-checkbox" class="flex items-center gap-3 mb-6">
                <input type="checkbox" id="agree-checkbox" class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                <span>Dengan ini saya setuju melakukan <i>self assessment</i> dan bersedia menjawab setiap pertanyaan pendahuluan dengan sejujur-jujurnya.</span>
            </label>

            <div class="flex justify-center">
                <button onclick="confirmConsent()" 
                    class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-25 transition ease-in-out duration-150">
                    Setuju & Mulai
                </button>
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
                        <!-- {{-- Debug output --}} -->
                        <!-- <p class="text-sm text-gray-500" id="question-type">Type: {{ $questions[0]->type ?? 'NULL' }}</p> -->
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
                    <form method="post" id="submit-form" action="{{ route('financial-literacy.submit') }}" class="mt-6 space-y-6" style="display: none;">
                        @csrf

                        <div>
                            <input type="hidden" name="answers" id="answers-json">
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
        let answers = {};

        function renderAnswerInput(index) {
            const question = questions[index];
            const container = document.getElementById('answer-container');
            container.innerHTML = '';

            const baseInputClass = 'mt-2 border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500';
            const baseLabelClass = 'block font-medium text-sm text-gray-700';

            let options = question.options;

            if (typeof options === 'string') {
                try {
                    options = JSON.parse(options);
                } catch (e) {
                    console.log('Gagal parse options:', options);
                    options = [];
                }
            }

            if (question.type === 'binary') {
                container.innerHTML = `
                    <label class="inline-flex items-center gap-3">
                        <input type="radio" id="answer-input-ya" name="answer-input" value="ya" class="${baseInputClass}" required />
                        <label for="answer-input-ya" class="${baseLabelClass}">Ya</label>
                    </label>
                    <label class="inline-flex items-center gap-3">
                        <input type="radio" id="answer-input-tidak" name="answer-input" value="tidak" class="${baseInputClass}" required />
                        <label for="answer-input-tidak" class="${baseLabelClass}">Tidak</label>
                    </label>
                `;
            } else if (question.type === 'multiple' && Array.isArray(options)) {
                options.forEach((opt, i) => {
                container.innerHTML += `
                    <label class="inline-flex items-center gap-3">
                        <input type="radio" id="answer-input-${i}" name="answer-input" value="${opt}" class="${baseInputClass}" required />
                        <label for="answer-input-${i}" class="${baseLabelClass}">${opt}</label>
                    </label>
                `;
                });
            }
        }

        function nextQuestion() {
            const input = document.querySelector('input[name="answer-input"]:checked')?.value;

            if (!input) {
                alert('Answer cannot be empty.');
                return;
            }

            answers[questions[currentIndex].id] = input;
            console.log(answers[questions[currentIndex].id]);
            currentIndex++;

            if (currentIndex < questions.length) {
                document.getElementById('question-title').innerText = questions[currentIndex].title;
                // document.getElementById('question-type').innerText = questions[currentIndex].type;
                renderAnswerInput(currentIndex);
                const currentAnswer = answers[questions[currentIndex].id];
                if (currentAnswer) {
                    const radio = document.querySelector(`input[name="answer-input"][value="${currentAnswer}"]`);
                    if (radio) radio.checked = true;
                } else {
                    document.querySelectorAll('input[name="answer-input"]').forEach(r => r.checked = false);
                }
            } else {
                document.getElementById('next-btn').style.display = 'none';
                document.getElementById('submit-form').style.display = 'block';
            }
        }        

        function confirmConsent() {
            const checkbox = document.getElementById('agree-checkbox');
            if (!checkbox.checked) {
                alert('Please check the consent box first.');
                return;
            }
            document.getElementById('consent-modal').style.display = 'none';
            renderAnswerInput(currentIndex); // mulai soal setelah setuju
        }

        // renderAnswerInput(currentIndex);
        
        document.getElementById('submit-form').addEventListener('submit', function() {
            document.getElementById('answers-json').value = JSON.stringify(answers);
        });
    </script>
</x-app-layout>
