<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เกมจัดหมวดพยัญชนะ (ไตรยางศ์) - Thai Categorizer Game</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Sarabun:wght@400;700&display=swap');
        
        :root {
            --color-primary: #3b82f6;
            --color-secondary: #10b981;
            --color-background: #f3f4f6;
            --color-surface: #ffffff;
            --color-text: #1f2937;
        }

        body {
            font-family: 'Sarabun', sans-serif;
            background-color: var(--color-background);
            color: var(--color-text);
            margin: 0;
            padding: 0;
        }

        .periphery {
            display: flex;
            width: 100%;
            min-height: 100vh;
        }

        #content {
            flex-grow: 1;
            padding: 20px;
            background-color: #f3f4f6;
        }

        .game-container {
            max-width: 900px;
            margin: 0 auto;
            background-color: var(--color-surface);
            border-radius: 16px;
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
            padding: 24px;
        }

        .btn-action {
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 700;
            transition: background-color 0.2s;
            box-shadow: 0 4px rgba(0, 0, 0, 0.1);
        }
        
        .score-item:nth-child(odd) { background-color: #f9fafb; }
        .score-item:nth-child(even) { background-color: #ffffff; }

        /* Categorizer Specific Styles */
        #categorizer-consonant {
            font-size: 8rem;
            font-weight: 900;
            line-height: 1;
            color: #f97316;
            text-shadow: 4px 4px 0 #ea580c;
        }
        .categorizer-btn {
            font-size: 1.5rem;
            padding: 15px 30px;
            width: 100%;
            transition: background-color 0.2s, transform 0.1s;
            border-radius: 12px;
            font-weight: bold;
            box-shadow: 0 4px rgba(0,0,0,0.1);
        }
        .categorizer-btn:active {
            transform: translateY(2px);
            box-shadow: 0 2px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="periphery">
        <?php include 'sidebar.php'; ?>
        
        <div id="content">
            <div class="game-container">
                <h1 class="text-3xl font-extrabold text-center mb-6 text-gray-800">เกมจัดหมวดพยัญชนะ (ไตรยางศ์)</h1>
                
                <div id="categorizer-game-content" class="p-4">
                    <div class="flex justify-center space-x-4 mb-6">
                        <button class="btn-action bg-emerald-500 text-white hover:bg-emerald-600 w-32" onclick="handleCategorizerStartReset()">เริ่มใหม่</button>
                        <button id="categorizer-pause-button" class="btn-action bg-red-500 text-white hover:bg-red-600 w-32" onclick="pauseResumeCategorizer()" disabled>หยุดเวลา</button>
                    </div>

                    <div class="bg-blue-50 p-6 rounded-lg shadow-inner mb-6 flex justify-between items-center flex-wrap">
                        <div class="text-left">
                            <p class="text-lg font-bold text-blue-700">ข้อที่:</p>
                            <p id="categorizer-counter" class="text-3xl font-extrabold text-blue-800">0 / 20</p>
                        </div>
                        <div class="text-center">
                             <div id="categorizer-consonant" class="mx-auto my-4 bg-white p-4 rounded-xl shadow-lg border-b-4 border-orange-500 w-40 h-40 flex items-center justify-center">?</div>
                        </div>
                        <div class="text-right">
                            <p class="text-lg font-bold text-blue-700">เวลา:</p>
                            <p id="categorizer-timer" class="text-3xl font-extrabold text-blue-800">00:00</p>
                        </div>
                    </div>

                    <div id="categorizer-buttons" class="grid grid-cols-3 gap-4 mb-6">
                        <button id="btn-high" class="categorizer-btn bg-yellow-400 text-gray-900 hover:bg-yellow-500" onclick="handleCategorizerGuess('high')" disabled>อักษรสูง</button>
                        <button id="btn-middle" class="categorizer-btn bg-green-400 text-gray-900 hover:bg-green-500" onclick="handleCategorizerGuess('middle')" disabled>อักษรกลาง</button>
                        <button id="btn-low" class="categorizer-btn bg-blue-400 text-white hover:bg-blue-500" onclick="handleCategorizerGuess('low')" disabled>อักษรต่ำ</button>
                    </div>

                    <div id="categorizer-message-box" class="text-center min-h-[30px] text-lg font-semibold text-gray-700"></div>

                    <div class="mt-8">
                        <h2 class="text-2xl font-bold text-center mb-4 text-gray-800 border-b pb-2">คะแนนสูงสุด</h2>
                        <div id="categorizer-high-scores" class="bg-white p-4 rounded-lg shadow">
                            <p id="loading-scores" class="text-center text-sm text-gray-500">กำลังโหลดคะแนน...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Logic -->
    <script>
        const CATEGORIZER_QUESTIONS = 20;
        const THAI_CONSONANTS_CATEGORIES = {
            high: ['ข', 'ฃ', 'ฉ', 'ฐ', 'ถ', 'ผ', 'ฝ', 'ศ', 'ษ', 'ส', 'ห'],
            middle: ['ก', 'จ', 'ฎ', 'ฏ', 'ด', 'ต', 'บ', 'ป', 'อ'],
            low: ['ค', 'ฅ', 'ฆ', 'ง', 'ช', 'ซ', 'ฌ', 'ญ', 'ฑ', 'ฒ', 'ณ', 'ท', 'ธ', 'น', 'พ', 'ฟ', 'ภ', 'ม', 'ย', 'ร', 'ล', 'ว', 'ฬ', 'ฮ']
        };

        let categorizerGameState = {
            questionCount: 0, currentConsonant: '', correctCategory: '', timerInterval: null,
            totalTime: 0, isGameActive: false, isPaused: false, lastTimeUpdate: 0, score: 0
        };

        const categorizerConsonantEl = document.getElementById('categorizer-consonant');
        const categorizerCounterEl = document.getElementById('categorizer-counter');
        const categorizerTimerEl = document.getElementById('categorizer-timer');
        const categorizerMessageBoxEl = document.getElementById('categorizer-message-box');
        const categorizerPauseButtonEl = document.getElementById('categorizer-pause-button');
        const categorizerHighScoresEl = document.getElementById('categorizer-high-scores');
        const categorizerButtons = [
            document.getElementById('btn-high'), document.getElementById('btn-middle'), document.getElementById('btn-low')
        ];

        function formatTime(ms) {
            const totalSeconds = Math.floor(ms / 1000);
            const minutes = Math.floor(totalSeconds / 60);
            const seconds = totalSeconds % 60;
            return `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
        }

        function getAllConsonants() {
            return [...THAI_CONSONANTS_CATEGORIES.high, ...THAI_CONSONANTS_CATEGORIES.middle, ...THAI_CONSONANTS_CATEGORIES.low];
        }
        
        function getCategory(consonant) {
            if (THAI_CONSONANTS_CATEGORIES.high.includes(consonant)) return 'high';
            if (THAI_CONSONANTS_CATEGORIES.middle.includes(consonant)) return 'middle';
            if (THAI_CONSONANTS_CATEGORIES.low.includes(consonant)) return 'low';
            return null;
        }

        function cleanupCategorizerGameState() {
            stopCategorizerTimer();
            categorizerGameState.isGameActive = false;
            categorizerGameState.isPaused = false;
            categorizerGameState.totalTime = 0;
            categorizerGameState.questionCount = 0;
            categorizerGameState.score = 0;
            categorizerGameState.currentConsonant = '?';
            categorizerGameState.correctCategory = '';

            categorizerTimerEl.textContent = '00:00';
            categorizerCounterEl.textContent = '0 / 20';
            categorizerConsonantEl.textContent = 'ก ข ค';
            categorizerMessageBoxEl.textContent = 'กด "เริ่มใหม่" เพื่อเริ่มเล่น';
            
            categorizerPauseButtonEl.disabled = true;
            categorizerPauseButtonEl.textContent = 'หยุดเวลา';
            categorizerPauseButtonEl.classList.replace('bg-gray-500', 'bg-red-500');
            categorizerButtons.forEach(btn => btn.disabled = true);
        }

        function prepareCategorizerGame() {
            cleanupCategorizerGameState();
        }

        window.handleCategorizerStartReset = function() {
            if (categorizerGameState.isGameActive) {
                cleanupCategorizerGameState();
                prepareCategorizerGame();
            } else {
                startCategorizerGame();
            }
        };

        function startCategorizerGame() {
            cleanupCategorizerGameState();
            categorizerGameState.isGameActive = true;
            categorizerPauseButtonEl.disabled = false;
            categorizerButtons.forEach(btn => btn.disabled = false);
            startCategorizerTimer();
            nextCategorizerQuestion();
        }

        function nextCategorizerQuestion() {
            if (categorizerGameState.questionCount >= CATEGORIZER_QUESTIONS) {
                endCategorizerGame();
                return;
            }
            categorizerGameState.questionCount++;
            categorizerCounterEl.textContent = `${categorizerGameState.questionCount} / ${CATEGORIZER_QUESTIONS}`;
            
            const allConsonants = getAllConsonants();
            const newConsonant = allConsonants[Math.floor(Math.random() * allConsonants.length)];

            categorizerGameState.currentConsonant = newConsonant;
            categorizerGameState.correctCategory = getCategory(newConsonant);
            categorizerConsonantEl.textContent = newConsonant;
            categorizerMessageBoxEl.textContent = 'จัดหมวดหมู่พยัญชนะนี้';
        }

        window.handleCategorizerGuess = function(guess) {
            if (!categorizerGameState.isGameActive || categorizerGameState.isPaused) return;

            // Disable all buttons immediately to prevent multiple clicks
            categorizerButtons.forEach(btn => btn.disabled = true);

            let isCorrect = guess === categorizerGameState.correctCategory;
            if (isCorrect) {
                categorizerGameState.score++;
                categorizerMessageBoxEl.textContent = '✅ ถูกต้อง!';
            } else {
                categorizerMessageBoxEl.textContent = `❌ ผิด! ตอบ: อักษร${categorizerGameState.correctCategory}`;
            }

            const targetBtn = document.getElementById(`btn-${guess}`);
            targetBtn.classList.add(isCorrect ? 'bg-green-600' : 'bg-red-600', 'text-white');
            setTimeout(() => {
                targetBtn.classList.remove('bg-green-600', 'bg-red-600', 'text-white');
                // Buttons will be re-enabled inside nextCategorizerQuestion -> endCategorizerGame check
                nextCategorizerQuestion();
                if (categorizerGameState.isGameActive) {
                     categorizerButtons.forEach(btn => btn.disabled = false);
                }
            }, 500);
        };

        function startCategorizerTimer() {
            if (!categorizerGameState.isGameActive || (!categorizerGameState.isPaused && categorizerGameState.timerInterval)) return;
            if (categorizerGameState.timerInterval) clearInterval(categorizerGameState.timerInterval);
            categorizerGameState.lastTimeUpdate = Date.now();
            categorizerGameState.isPaused = false;
            categorizerPauseButtonEl.textContent = 'หยุดเวลา';
            categorizerPauseButtonEl.classList.replace('bg-gray-500', 'bg-red-500');

            categorizerGameState.timerInterval = setInterval(() => {
                if (!categorizerGameState.isGameActive || categorizerGameState.isPaused) return;
                categorizerGameState.totalTime += (Date.now() - categorizerGameState.lastTimeUpdate);
                categorizerGameState.lastTimeUpdate = Date.now();
                categorizerTimerEl.textContent = formatTime(categorizerGameState.totalTime);
            }, 100);
        }

        window.pauseResumeCategorizer = function() {
            if (!categorizerGameState.isGameActive) return;
            if (categorizerGameState.isPaused) startCategorizerTimer();
            else {
                clearInterval(categorizerGameState.timerInterval);
                categorizerGameState.timerInterval = null;
                categorizerGameState.totalTime += Date.now() - categorizerGameState.lastTimeUpdate;
                categorizerGameState.isPaused = true;
                categorizerPauseButtonEl.textContent = 'เล่นต่อ';
                categorizerPauseButtonEl.classList.replace('bg-red-500', 'bg-gray-500');
                categorizerMessageBoxEl.textContent = 'เกมหยุดชั่วคราว...';
            }
        };

        function stopCategorizerTimer() {
            clearInterval(categorizerGameState.timerInterval);
            categorizerGameState.timerInterval = null;
            categorizerGameState.isPaused = false;
        }

        function endCategorizerGame() {
            stopCategorizerTimer();
            categorizerGameState.isGameActive = false;
            categorizerButtons.forEach(btn => btn.disabled = true);
            const finalTime = categorizerGameState.totalTime;
            const finalScore = categorizerGameState.score;
            categorizerConsonantEl.textContent = finalScore >= CATEGORIZER_QUESTIONS * 0.8 ? '🎉' : '⏱️';
            categorizerMessageBoxEl.innerHTML = `<span class="text-2xl font-extrabold text-blue-700">จบเกม!</span><br/>คะแนน ${finalScore}/${CATEGORIZER_QUESTIONS} เวลา ${formatTime(finalTime)}`;
            
            // Prompt Name
            setTimeout(() => {
                let playerName = prompt("กรุณาใส่ชื่อของคุณเพื่อบันทึกคะแนน:", "ผู้เล่น");
                if (playerName === null) return;
                playerName = playerName.trim();
                saveCategorizerGameResult(playerName, finalTime, finalScore);
            }, 500);
        }

        async function saveCategorizerGameResult(playerName, finalTime, finalScore) {
            try {
                const formData = new FormData();
                formData.append('action', 'save');
                formData.append('game', 'categorizer');
                formData.append('player_name', playerName);
                formData.append('score', finalScore);
                formData.append('time_ms', finalTime);

                const res = await fetch('score_api.php', { method: 'POST', body: formData });
                const data = await res.json();
                
                if (data.success) {
                    categorizerMessageBoxEl.textContent = `บันทึกคะแนนสำเร็จ! (${data.player_name})`;
                    loadCategorizerHighScores();
                } else {
                    console.error(data.error);
                }
            } catch(e) { console.error(e); }
        }

        async function loadCategorizerHighScores() {
            try {
                const res = await fetch('score_api.php?action=load&game=categorizer');
                const scores = await res.json();
                
                let html = `
                <div class="flex font-bold text-gray-600 border-b pb-2 mb-2">
                    <span class="w-1/6">#</span> <span class="w-3/6">ผู้เล่น</span> <span class="w-2/6 text-right">คะแนน</span>
                </div>`;
                if(scores.error || scores.length === 0) {
                    html += '<p class="text-center text-gray-500">ไม่มีคะแนน</p>';
                } else {
                    scores.forEach((d, idx) => {
                        html += `<div class="score-item flex items-center py-2 px-2 rounded-lg text-sm">
                            <span class="w-1/6 font-bold text-lg text-blue-500">${idx+1}</span>
                            <span class="w-3/6 truncate">${d.player_name}</span>
                            <span class="w-2/6 text-right font-mono text-green-700">${d.score}/${CATEGORIZER_QUESTIONS} (${formatTime(d.time_ms)})</span>
                        </div>`;
                    });
                }
                categorizerHighScoresEl.innerHTML = html;
            } catch(e) { console.error(e); }
        }
        
        prepareCategorizerGame();
        loadCategorizerHighScores();
    </script>
</body>
</html>
