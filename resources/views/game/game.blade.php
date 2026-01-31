<x-app-layout>
    <style>
        .text-justify {
            text-align: justify;
        }
        .custom-list {
            list-style-type: decimal;
            padding-left: 1rem; 
            text-align: justify;
        }

        .custom-list li {
            margin-bottom: 0.5rem;
        }

        @keyframes blink {
            0%, 50%, 100% { opacity: 1; }
            25%, 75% { opacity: 0; }
        }

        .blink {
            animation: blink 3s infinite;
        }

    </style>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Session') . ' ' . $session }}
        </h2>
    </x-slot>

    @section('background-class', 'custom-bg-' . $questions[0]->id)
    @push('custom-style')
        <style>
            @foreach($backgroundImages as $id => $image)
                .custom-bg-{{ $id }} {
                    @if($image)
                        background-image: url('/images/background/{{ $image }}');
                    @else
                        background-image: none;
                        background-color: white;
                    @endif
                    background-size: cover;
                    background-repeat: no-repeat;
                    background-position: center;
                }
            @endforeach
        </style>
    @endpush

    <div id="consent-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg max-w-2xl w-full max-h-[80vh] overflow-y-auto text-sm text-gray-900 dark:text-gray-100">
            <div class="flex justify-center">
                <h2 class="text-lg font-bold mb-4">
                    Persetujuan Partisipasi
                </h2>
            </div>
            
            <p class="mb-4 text-center">
                Berikut adalah ketentuan yang wajib diikuti oleh investor:
            </p>

            <ol class="custom-list">
                <li>
                    Dalam {{ 'Sesi ' . $session }} terdapat 5 soal pilihan saham. 
                    Pada tiap soal tersedia 4 pilihan saham, dan investor wajib memilih 2 saham dengan batas waktu yang ditentukan.
                </li>
                <li>
                    Investor diberikan modal virtual sebesar Rp 10.000.000,-. 
                    Setiap pembelian saham dibatasi flat sebesar Rp 1.000.000,-, sehingga pada tiap soal Anda harus membeli 2 saham dengan nilai yang sama.
                </li>
                <li>
                    Setiap pilihan saham akan disertai informasi berupa jenis perusahaan, aktivitas perusahaan, risiko, dan profit.
                </li>
                <li>
                    Peserta diminta memberikan alasan yang jelas untuk setiap saham yang dipilih.
                </li>
                <li>
                    Terdapat apresiasi tambahan sebesar Rp 250.000,- per orang untuk 6 peserta yang memberikan alasan pemilihan saham dengan jelas dan baik.
                </li>
            </ol>

            <p class="mt-6 text-center font-semibold">
                Selamat bermain!
            </p>

            <div class="flex justify-center">
                <button onclick="confirmConsent()" 
                    class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-25 transition ease-in-out duration-150">
                    Mulai
                </button>
            </div>
        </div>
    </div>

    <div id="all-session-summary"
        class="py-12 max-w-7xl mx-auto sm:px-6 lg:px-8 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
        @if(!$hasAnswers)
            <p class="text-gray-500">Belum menjawab</p>
        @endif
    </div>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div id="session-card" class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg" style="background-color: rgba(255,255,255,0.8);">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex justify-end mt-2" id="balance">
                        <x-input-label id="current-balance">{{ __('10000000') }}</x-input-label>
                    </div>
                    <div class="mt-6 space-y-6 flex justify-center" id="company-list">
                    </div>
                    <div class="mt-6 space-y-6">
                        <div class="flex items-center gap-4">
                            <x-primary-button id="next-btn" onclick="nextQuestion()" disabled class="opacity-50 cursor-not-allowed">
                                {{ __('Next') }}
                            </x-primary-button>
                        </div>
                    </div>
                    <form method="post" id="submit-form" action="{{ route('game-session.submit') }}" class="mt-6 space-y-6" style="display: none;">
                        @csrf

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
        const companies = @json($companies->keyBy('id'));
        const allCompanies = @json($allCompanies);
        let currentIndex = 0;
        let selectedCompanies = [];
        let finalAnswers = [];
        const session = {{ $session }};
        const previousAnswers = @json($previousAnswers);
        const hasAnswers = @json($hasAnswers);

        console.log("Previous Answers:", previousAnswers);
        console.log("Companies: ", companies);

        function formatRupiah(value) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(value);
        }

        function parseRupiah(rupiahString) {
            return parseInt(rupiahString.replace(/[^0-9]/g, ''));
        }

        document.addEventListener('DOMContentLoaded', () => {
            const balanceElement = document.getElementById('current-balance');
            const rawValue = parseInt(balanceElement.innerText);
            balanceElement.innerText = formatRupiah(rawValue);
        });

        function convertToArray(question) {
            let options = question.options;

            if (typeof options === 'string') {
                try {
                    options = JSON.parse(options);
                } catch (e) {
                    console.error('Gagal parse options:', options);
                    options = [];
                }
            }

            if (!Array.isArray(options)) {
                console.warn('Options bukan array:', options);
                options = [];
            }

            return options;
        }

        function showContent(index) {
            const question = questions[index];
            const options = convertToArray(question);
            const container = document.getElementById('company-list');
            container.innerHTML = '';

            const baseStyle = 'inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-25 transition ease-in-out duration-150';

            const wrapper = document.createElement('div');
            wrapper.className = 'w-full';

            const tableContainer = document.createElement('div');
            tableContainer.className = 'w-[80%] max-w-4xl mx-auto';

            const table = document.createElement('table');
            table.className = 'w-full text-sm table-fixed border border-gray-300 rounded-md shadow-sm';

            const tbody = document.createElement('tbody');

            const badList = [];
            const goodList = [];

            options.forEach(option => {
                const company = companies[option];
                if (!company) return;

                const html = `
                    <div class="flex flex-col">
                        <div class="flex items-center gap-2">
                            <p class="font-semibold">${company.name} (${company.code})</p>
                            <p class="text-green-600 font-semibold ml-1 blink bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 ">${company.return.toFixed(2)} %</p>
                        </div>

                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">${company.description}</p>


                        <div class="flex justify-end mt-2">
                            <button id="buy-btn" class="${baseStyle}" data-id="${company.id}" onclick="buy(this)">Buy</button>
                        </div>
                    </div>
                `;

                if (company.type === 'bad') {
                    badList.push(html);
                } else {
                    goodList.push(html);
                }
            });

            const maxRows = Math.max(badList.length, goodList.length);
            for (let i = 0; i < maxRows; i++) {
                const row = document.createElement('tr');
                row.className = 'border border-gray-300';

                const badCell = document.createElement('td');
                badCell.className = 'px-4 py-2 align-top border border-gray-300 break-words';
                badCell.style.width = '50%'; 

                badCell.innerHTML = badList[i]
                ? `<div class="flex flex-col h-full justify-between">${badList[i]}</div>`
                : '';

                const goodCell = document.createElement('td');
                goodCell.className = 'px-4 py-2 align-top border border-gray-300 break-words';
                goodCell.style.width = '50%'; 

                goodCell.innerHTML = goodList[i]
                ? `<div class="flex flex-col h-full justify-between">${goodList[i]}</div>`
                : '';

                if (session % 2 === 1) {
                    row.appendChild(badCell);
                    row.appendChild(goodCell);
                } else {
                    row.appendChild(goodCell);
                    row.appendChild(badCell);
                }

                tbody.appendChild(row);
            }

            table.appendChild(tbody);
            tableContainer.appendChild(table);
            wrapper.appendChild(tableContainer);
            container.appendChild(wrapper);
        }


        function nextQuestion() {
            finalAnswers.push({
                question_id: questions[currentIndex].id,
                selected: selectedCompanies.slice() // berisi array {id, reason}
            });

            selectedCompanies = [];
            updateNextButtonState();
            currentIndex++;

            if (currentIndex < questions.length) {
                showContent(currentIndex);
                setBackground(currentIndex);
            } else {
                /* document.getElementById('next-btn').style.display = 'none';
                document.getElementById('submit-form').style.display = 'none';
                document.getElementById('balance').style.display = 'none';
                document.querySelector('.mt-6.space-y-6').style.display = 'none'; */

                /* document.getElementById('session-card').classList.add('hidden');
                document.getElementById('submit_hidden').classList.remove('hidden');
                document.getElementById(`submit_${session}`).classList.add('hidden'); */

                /* addSessionSummary(session, finalAnswers);

                const form = document.getElementById('submit-form');

                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'answers';
                input.value = JSON.stringify(finalAnswers);
                form.appendChild(input);

                const sessionInput = document.createElement('input');
                sessionInput.type = 'hidden';
                sessionInput.name = 'session_id';
                sessionInput.value = {{ $session }};
                form.appendChild(sessionInput); */

                document.getElementById('session-card').classList.add('hidden'); 

                renderSessionSummary(session, finalAnswers); 

                const answersInput = document.getElementById(`answers_${session}`); 
                
                if (answersInput) { 
                    answersInput.value = JSON.stringify(finalAnswers); 
                }

                const submitBtn = document.getElementById(`submit_${session}`); 
                
                if (submitBtn) { 
                    submitBtn.classList.remove('hidden'); 
                }
            }
        }


        function buy(button) {
            const companyId = button.getAttribute('data-id');
            let currentBalance = parseRupiah(document.getElementById('current-balance').innerText);

            if (selectedCompanies.length >= 2) {
                alert('Maximum 2 choices per question!');
                return;
            }

            if (currentBalance < 1000000) {
                console.log('Not enough balance!');
                return;
            }

            const reason = prompt("Reason:");
            if (!reason) {
                return;
            }

            currentBalance -= 1000000;
            document.getElementById('current-balance').innerText = formatRupiah(currentBalance);

            button.disabled = true;
            button.classList.remove('bg-white', 'dark:bg-gray-800', 'hover:bg-gray-50', 'dark:hover:bg-gray-700');
            button.classList.add('bg-red-600', 'cursor-not-allowed', 'text-white');

            console.log('Bought company ID:', companyId, 'Reason:', reason);

            selectedCompanies.push({ id: companyId, reason: reason });

            renderSessionSummary(session, [{ 
                question_id: questions[currentIndex].id, 
                selected: selectedCompanies 
            }]);

            updateNextButtonState();
        }

        function updateNextButtonState() {
            const nextBtn = document.getElementById('next-btn');
            if (selectedCompanies.length == 2) {
                nextBtn.disabled = false;
                nextBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            } else {
                nextBtn.disabled = true;
                nextBtn.classList.add('opacity-50', 'cursor-not-allowed');
            }
        }

        function setBackground(questionIndex) {
            const qId = questions[questionIndex].id;

            document.body.classList.forEach(cls => {
                if (cls.startsWith('custom-bg-')) {
                    document.body.classList.remove(cls);
                }
            });

            document.body.classList.add('custom-bg-' + qId);
        }

        function renderSessionSummary(sessionNumber, answers) {
            const container = document.getElementById('all-session-summary');
            container.classList.remove('hidden');

            let wrapper = document.getElementById(`summary-session-${sessionNumber}`);
            
            if (!wrapper) {
                // wrapper baru → render semua jawaban
                wrapper = document.createElement('div');
                wrapper.id = `summary-session-${sessionNumber}`;
                wrapper.className = 'mt-6 border rounded shadow-sm bg-white dark:bg-gray-800';
                container.appendChild(wrapper);

                const header = document.createElement('button');
                header.className = 'w-full text-left px-4 py-2 font-semibold bg-gray-200 dark:bg-gray-600';
                header.innerText = `Portofolio Sesi ${sessionNumber}`;
                wrapper.appendChild(header);

                const content = document.createElement('div');
                content.className = 'px-4 py-2 hidden';
                wrapper.appendChild(content);

                header.addEventListener('click', () => {
                    content.classList.toggle('hidden');
                });

                const table = document.createElement('table');
                table.className = 'w-full text-sm border border-gray-300 rounded-md shadow-sm';

                const thead = document.createElement('thead');
                thead.innerHTML = `
                    <tr class="bg-gray-100 dark:bg-gray-700">
                        <th class="px-2 py-1 border">Nama Perusahaan</th>
                        <th class="px-2 py-1 border">Return (%)</th>
                        <th class="px-2 py-1 border">Alasan</th>
                    </tr>
                `;
                table.appendChild(thead);

                const tbody = document.createElement('tbody');
                // render semua jawaban sesi ini sekali
                answers.forEach(answer => {
                    answer.selected.forEach(sel => {
                        const company = allCompanies[sel.id];
                        if (!company) return;
                        const row = document.createElement('tr');
                        row.dataset.qid = answer.question_id;
                        row.dataset.cid = sel.id;
                        row.innerHTML = `
                            <td class="px-2 py-1 border">${company.name} (${company.code})</td>
                            <td class="px-2 py-1 border">${company.return.toFixed(2)}</td>
                            <td class="px-2 py-1 border">${sel.reason}</td>
                        `;
                        tbody.appendChild(row);
                    });
                });

                table.appendChild(tbody);
                content.appendChild(table);

                // form submit khusus sesi ini
                const form = document.createElement('form'); 
                form.method = 'post'; 
                form.action = "{{ route('game-session.submit') }}"; 
                form.className = 'mt-4'; 
                form.innerHTML = ` @csrf 
                    <input type="hidden" name="answers" id="answers_${sessionNumber}">
                    <input type="hidden" name="session_id" value='${sessionNumber}'>
                    <button type="submit" id="submit_${sessionNumber}" 
                        class="hidden inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                        Submit
                    </button>`;
                content.appendChild(form);
            } else {
                // wrapper sudah ada → cukup tambahkan jawaban terakhir
                const tbody = wrapper.querySelector('tbody');
                const latestAnswer = answers[answers.length - 1];

                latestAnswer.selected.forEach(sel => {
                    const company = allCompanies[sel.id];
                    if (!company) return;

                    const exists = tbody.querySelector(
                        `tr[data-qid="${latestAnswer.question_id}"][data-cid="${sel.id}"]`
                    );
                    if (exists) return;

                    const row = document.createElement('tr');
                    row.dataset.qid = latestAnswer.question_id;
                    row.dataset.cid = sel.id;
                    row.innerHTML = `
                        <td class="px-2 py-1 border">${company.name} (${company.code})</td>
                        <td class="px-2 py-1 border">${company.return.toFixed(2)}</td>
                        <td class="px-2 py-1 border">${sel.reason}</td>
                    `;
                    tbody.appendChild(row);
                });
            }

            // update hidden input answers dengan jawaban sesi ini (semua)
            const answersInput = wrapper.querySelector(`#answers_${sessionNumber}`);
            if (answersInput) {
                answersInput.value = JSON.stringify(answers);
            }
        }


        document.addEventListener('DOMContentLoaded', () => {
            if (previousAnswers && previousAnswers.length > 0) {
                const grouped = {};
                previousAnswers.forEach(ans => {
                    const sessionId = ans.session; // semua sesi
                    if (!grouped[sessionId]) grouped[sessionId] = [];
                    grouped[sessionId].push({
                        question_id: ans.game_question_id,
                        selected: JSON.parse(ans.choices).map((id, idx) => ({
                            id: id,
                            reason: JSON.parse(ans.reasons)[idx] ?? ''
                        }))
                    });
                });

                Object.keys(grouped).forEach(sessionId => {
                    // panggil renderSessionSummary, bukan addSessionSummary
                    renderSessionSummary(sessionId, grouped[sessionId]);
                });
            }
        });


        function confirmConsent() {
            /* const checkbox = document.getElementById('agree-checkbox');
            if (!checkbox.checked) {
                alert('Please check the consent box first.');
                return;
            } */
            document.getElementById('consent-modal').style.display = 'none';
            showContent(currentIndex);
        }

        /* showContent(currentIndex); */

    </script>
</x-app-layout>
