<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เกมสะกดคำ - Thai Spelling Game</title>
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

        /* Game Styles */
        .game-container {
            max-width: 900px;
            margin: 0 auto;
            background-color: var(--color-surface);
            border-radius: 16px;
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
            padding: 24px;
        }

        .target-box {
            min-width: 40px;
            min-height: 55px;
            line-height: 50px;
            border: 2px dashed #9ca3af;
            background-color: #f9fafb;
            font-size: 1.5rem;
            font-weight: 700;
            text-align: center;
            border-radius: 8px;
            transition: all 0.1s;
            cursor: default;
        }
        
        .target-box.filled {
            border: 2px solid var(--color-primary);
            background-color: #eff6ff;
            color: var(--color-primary);
        }
        
        .tile {
            width: 55px;
            height: 55px;
            line-height: 55px;
            background-color: var(--color-secondary);
            color: white;
            font-size: 1.5rem;
            font-weight: 700;
            text-align: center;
            border-radius: 8px;
            cursor: pointer;
            user-select: none;
            box-shadow: 0 4px #047857;
            transition: transform 0.1s, background-color 0.1s, box-shadow 0.1s;
            margin: 6px;
        }

        .tile:hover:not(.disabled) {
            background-color: #059669;
            transform: translateY(-2px);
            box-shadow: 0 6px #047857;
        }

        .tile:active:not(.disabled) {
            transform: translateY(2px);
            box-shadow: 0 2px #047857;
        }

        .tile.disabled {
            background-color: #d1d5db;
            color: #6b7280;
            cursor: not-allowed;
            box-shadow: none;
        }

        .correct-animation { animation: correctPulse 0.5s ease-in-out; }
        @keyframes correctPulse {
            0% { transform: scale(1); background-color: var(--color-secondary); }
            50% { transform: scale(1.1); background-color: #34d399; }
            100% { transform: scale(1); background-color: var(--color-secondary); }
        }

        .incorrect-shake { animation: shake 0.3s; }
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            20%, 60% { transform: translateX(-5px); }
            40%, 80% { transform: translateX(5px); }
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
    </style>
    
    <!-- Firebase -->
    <script type="module">
        import { initializeApp } from "https://www.gstatic.com/firebasejs/11.6.1/firebase-app.js";
        import { getAuth, signInAnonymously, onAuthStateChanged } from "https://www.gstatic.com/firebasejs/11.6.1/firebase-auth.js";
        import { getFirestore, collection, addDoc, query, orderBy, limit, onSnapshot, serverTimestamp } from "https://www.gstatic.com/firebasejs/11.6.1/firebase-firestore.js";

        // Firebase Config
        const firebaseConfig = {
             // Note: User didn't provide config in the file but used placeholder. 
             // Accessing from window if available or using placeholders.
             // For this output, I will assume the same loading mechanism as demo3g.php
        };
        // Reuse the logic from demo3g.php for initialization
        
        async function initializeFirebase() {
             // In a real scenario, these would be populated. 
             // Check if user provided config in demo3g.php via some variable? 
             // In demo3g.php step 53, it read `__firebase_config`. 
             // We will copy that logic.
        }
    </script>
</head>
<body>
    <div class="periphery">
        <?php include 'sidebar.php'; ?>
        
        <div id="content">
            <div class="game-container">
                <h1 class="text-3xl font-extrabold text-center mb-6 text-gray-800">เกมสะกดคำ (Spelling Game)</h1>
                
                <div id="spelling-game-content">
                    <div class="flex justify-between items-center bg-blue-50 p-4 rounded-lg mb-6 shadow-inner">
                        <div id="spelling-word-counter" class="text-lg font-bold text-blue-700">คำที่ 0 / 5</div>
                        <div class="flex items-center space-x-2">
                             <div class="text-2xl font-extrabold text-blue-800" id="spelling-timer">00:00</div>
                        </div>
                    </div>

                    <div id="spelling-target-word" class="flex justify-center flex-wrap gap-2 mb-8 p-4 bg-white rounded-lg border border-gray-200 min-h-[80px]"></div>
                    <div id="spelling-letter-tiles" class="flex justify-center flex-wrap gap-3 mb-6 p-4 bg-white rounded-lg border border-gray-200 min-h-[120px]"></div>

                    <div class="flex justify-center space-x-4 mb-6">
                        <button id="spelling-start-reset-button" class="btn-action bg-emerald-500 text-white hover:bg-emerald-600 w-32" onclick="handleSpellingStartResetClick()">เริ่มใหม่</button>
                        <button id="spelling-pause-resume-button" class="btn-action bg-red-500 text-white hover:bg-red-600 w-32" onclick="pauseResumeSpellingGame()" disabled>หยุดเวลา</button>
                        <button id="spelling-undo-button" class="btn-action bg-yellow-500 text-white hover:bg-yellow-600 w-32" onclick="undoLastSpellingTile()" disabled>ย้อนกลับ</button>
                    </div>

                    <div id="spelling-message-box" class="text-center min-h-[30px] text-lg font-semibold text-gray-700"></div>

                    <div class="mt-8">
                        <h2 class="text-2xl font-bold text-center mb-4 text-gray-800 border-b pb-2">คะแนนสูงสุด</h2>
                        <div id="high-scores" class="bg-white p-4 rounded-lg shadow">
                             <p id="loading-scores" class="text-center text-sm text-gray-500">กำลังโหลดคะแนน...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Game Logic -->
    <script>
        const SPELLING_WORDS_TO_PLAY = 5;
        const THAI_WORDS = ['สวัสดี', 'กล้วย', 'มะม่วง', 'คอมพิวเตอร์', 'ประเทศไทย', 'น่ารัก', 'โรงเรียน', 'อาหาร', 'วันหยุด', 'มหาสมุทร'];
        const THAI_ALPHABET = 'กขคงจฉชซฌญฎฏฐฑฒณดตถทธนบปผฝพฟภมยรลวศษสหฬอฮะาิีึืุูเแโใไ'.split('');
        const DISTRACTOR_POOL = THAI_ALPHABET;
        const MAX_DISTRACTORS = 3;

        let spellingGameState = {
            currentWord: '', targetLetters: [], builtWord: [], availableTiles: [], 
            targetBoxes: [], placedTiles: [], wordIndex: 0, timerInterval: null,
            totalTime: 0, isGameActive: false, isPaused: false, lastTimeUpdate: 0,
            processingWin: false
        };

// ... (abridged)

        function cleanupSpellingGameState() {
            stopSpellingTimer();
            spellingGameState.isGameActive = false;
            spellingGameState.isPaused = false;
            spellingGameState.processingWin = false; // Reset flag
            spellingGameState.totalTime = 0;
// ... (abridged)
            
            spellingGameState.currentWord = '';
            spellingGameState.targetLetters = [];
            spellingGameState.builtWord = [];
            spellingGameState.availableTiles = [];
            spellingGameState.targetBoxes = [];
            spellingGameState.placedTiles = [];
            updateSpellingStartResetButtonText();
        }

// ... (abridged)

        function startNewSpellingWord(updateWordIndex = true) {
            spellingGameState.processingWin = false; // Reset flag
            if (updateWordIndex) {
// ... (abridged)

        function checkSpellingWord() {
            if (spellingGameState.isPaused || spellingGameState.processingWin) return; // Check flag
            const currentGuess = spellingGameState.builtWord.join('');
            if (currentGuess === spellingGameState.currentWord) {
                spellingGameState.processingWin = true; // Set flag
                spellingMessageBoxEl.textContent = `ถูกต้อง! คำว่า: ${spellingGameState.currentWord}`;
                pauseSpellingTimer();
                spellingGameState.targetBoxes.forEach(box => box.classList.add('correct-animation'));
                setTimeout(() => {
                    spellingGameState.targetBoxes.forEach(box => box.classList.remove('correct-animation'));
                    startNewSpellingWord(true);
                    startSpellingTimer(); 
                }, 1500);
            } else {
// ...
                spellingMessageBoxEl.textContent = 'ผิด! ลองใหม่';
                spellingTargetWordEl.classList.add('incorrect-shake');
                setTimeout(() => spellingTargetWordEl.classList.remove('incorrect-shake'), 300);
            }
        }

        function endSpellingGame() {
            cleanupSpellingGameState();
            const finalTime = spellingGameState.totalTime;
            spellingMessageBoxEl.innerHTML = `<p class="text-2xl font-extrabold text-blue-700">เวลาทั้งหมด: ${formatTime(finalTime)}</p>`;
            
            // Prompt for Name
            setTimeout(() => {
                let playerName = prompt("ยินดีด้วย! กรุณาใส่ชื่อของคุณเพื่อบันทึกคะแนน:", "ผู้เล่น");
                if (playerName === null) return; // Cancelled
                playerName = playerName.trim();
                
                saveSpellingGameResult(playerName, finalTime);
            }, 500);
        }

        async function saveSpellingGameResult(playerName, finalTime) {
            try {
                const formData = new FormData();
                formData.append('action', 'save');
                formData.append('game', 'spelling');
                formData.append('player_name', playerName);
                formData.append('score', finalTime);

                const res = await fetch('score_api.php', { method: 'POST', body: formData });
                const data = await res.json();
                
                if (data.success) {
                    spellingMessageBoxEl.textContent = `บันทึกคะแนนสำเร็จ! (${data.player_name})`;
                    loadSpellingHighScores();
                } else {
                    console.error(data.error);
                }
            } catch (e) { console.error(e); }
        }

        async function loadSpellingHighScores() {
            try {
                const res = await fetch('score_api.php?action=load&game=spelling');
                const scores = await res.json();
                
                let html = `
                <div class="flex font-bold text-gray-600 border-b pb-2 mb-2">
                    <span class="w-1/6">#</span>
                    <span class="w-3/6">ผู้เล่น</span>
                    <span class="w-2/6 text-right">เวลา</span>
                </div>`;
                
                if (scores.error || scores.length === 0) {
                    html += '<p class="text-center text-gray-500 p-4">ไม่มีคะแนน</p>';
                } else {
                    scores.forEach((d, idx) => {
                        html += `
                        <div class="score-item flex items-center py-2 px-2 rounded-lg text-sm">
                            <span class="w-1/6 font-bold text-lg text-blue-500">${idx + 1}</span>
                            <span class="w-3/6 truncate">${d.player_name}</span>
                            <span class="w-2/6 text-right font-mono text-green-700">${formatTime(d.score)}</span>
                        </div>`;
                    });
                }
                highScoresEl.innerHTML = html;
            } catch (e) {
                highScoresEl.innerHTML = '<p class="text-center text-red-500">โหลดคะแนนไม่สำเร็จ</p>';
            }
        }

        // Init
        prepareFirstSpellingWordForStartScreen();
        loadSpellingHighScores();
    </script>
</body>
</html>
