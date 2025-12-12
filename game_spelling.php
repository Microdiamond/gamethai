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

        const spellingTargetWordEl = document.getElementById('spelling-target-word');
        const spellingLetterTilesEl = document.getElementById('spelling-letter-tiles');
        const spellingTimerEl = document.getElementById('spelling-timer');
        const spellingWordCounterEl = document.getElementById('spelling-word-counter');
        const spellingMessageBoxEl = document.getElementById('spelling-message-box');
        const spellingStartResetButtonEl = document.getElementById('spelling-start-reset-button');
        const spellingPauseResumeButtonEl = document.getElementById('spelling-pause-resume-button');
        const spellingUndoButtonEl = document.getElementById('spelling-undo-button');
        const highScoresEl = document.getElementById('high-scores');

        function formatTime(ms) {
            const totalSeconds = Math.floor(ms / 1000);
            const minutes = Math.floor(totalSeconds / 60);
            const seconds = totalSeconds % 60;
            return `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
        }

        function shuffleArray(array) {
            for (let i = array.length - 1; i > 0; i--) {
                const j = Math.floor(Math.random() * (i + 1));
                [array[i], array[j]] = [array[j], array[i]];
            }
            return array;
        }

        function segmentThaiWord(word) {
            const segments = [];
            let currentSegment = '';
            const newSegmentStartRegex = /^[ก-ฮ\s\dเแโใไ]/;
            for (const char of word) {
                if (newSegmentStartRegex.test(char)) {
                    if (currentSegment) segments.push(currentSegment);
                    currentSegment = char;
                } else {
                    currentSegment += char;
                }
            }
            if (currentSegment) segments.push(currentSegment);
            return segments.filter(s => s.trim() !== '');
        }

        function updateSpellingStartResetButtonText() {
            if (spellingGameState.isGameActive) {
                spellingStartResetButtonEl.textContent = 'รีเซ็ตเกม';
                spellingStartResetButtonEl.classList.replace('bg-emerald-500', 'bg-gray-500');
                spellingStartResetButtonEl.classList.replace('hover:bg-emerald-600', 'hover:bg-gray-600');
            } else {
                spellingStartResetButtonEl.textContent = 'เริ่มใหม่';
                spellingStartResetButtonEl.classList.replace('bg-gray-500', 'bg-emerald-500');
                spellingStartResetButtonEl.classList.replace('hover:bg-gray-600', 'hover:bg-emerald-600');
            }
        }

        window.handleSpellingStartResetClick = function() {
            if (spellingGameState.isGameActive) {
                cleanupSpellingGameState();
                prepareFirstSpellingWordForStartScreen();
                spellingMessageBoxEl.textContent = 'เกมถูกรีเซ็ต กด "เริ่มใหม่" เพื่อเริ่ม';
            } else {
                startSpellingGame();
            }
        };

        function startSpellingGame() {
            if (spellingGameState.isGameActive) return;
            spellingGameState.isGameActive = true;
            spellingPauseResumeButtonEl.disabled = false;
            updateSpellingStartResetButtonText();
            startSpellingTimer();
        }

        function cleanupSpellingGameState() {
            stopSpellingTimer();
            spellingGameState.isGameActive = false;
            spellingGameState.isPaused = false;
            spellingGameState.processingWin = false;
            spellingGameState.totalTime = 0;
            spellingGameState.wordIndex = 0;
            spellingTimerEl.textContent = '00:00';
            spellingWordCounterEl.textContent = 'คำที่ 0 / 5';
            spellingTargetWordEl.innerHTML = '';
            spellingLetterTilesEl.innerHTML = '';
            spellingUndoButtonEl.disabled = true;
            spellingPauseResumeButtonEl.disabled = true;
            spellingPauseResumeButtonEl.textContent = 'หยุดเวลา';
            spellingPauseResumeButtonEl.classList.replace('bg-gray-500', 'bg-red-500');
            
            spellingGameState.currentWord = '';
            spellingGameState.targetLetters = [];
            spellingGameState.builtWord = [];
            spellingGameState.availableTiles = [];
            spellingGameState.targetBoxes = [];
            spellingGameState.placedTiles = [];
            updateSpellingStartResetButtonText();
        }

        function prepareFirstSpellingWordForStartScreen() {
            spellingGameState.wordIndex = 0;
            spellingGameState.currentWord = '';
            spellingGameState.targetLetters = [];
            spellingTargetWordEl.innerHTML = '';
            spellingLetterTilesEl.innerHTML = '';
            spellingWordCounterEl.textContent = `คำที่ 0 / ${SPELLING_WORDS_TO_PLAY}`;
            startNewSpellingWord(false);
            spellingMessageBoxEl.textContent = `คำศัพท์แรกพร้อมแล้ว! กด "เริ่มใหม่" เพื่อเล่น`;
        }

        function startSpellingTimer() {
            if (!spellingGameState.isGameActive || (!spellingGameState.isPaused && spellingGameState.timerInterval)) return;
            if (spellingGameState.timerInterval) clearInterval(spellingGameState.timerInterval);
            
            spellingGameState.lastTimeUpdate = Date.now();
            spellingGameState.isPaused = false;
            spellingPauseResumeButtonEl.textContent = 'หยุดเวลา';
            spellingPauseResumeButtonEl.classList.replace('bg-gray-500', 'bg-red-500');
            spellingMessageBoxEl.textContent = `สะกดคำศัพท์ ${spellingGameState.targetLetters.length} ส่วน`;

            spellingGameState.timerInterval = setInterval(() => {
                if (!spellingGameState.isGameActive || spellingGameState.isPaused) return;
                const currentTime = Date.now();
                spellingGameState.totalTime += (currentTime - spellingGameState.lastTimeUpdate);
                spellingGameState.lastTimeUpdate = currentTime;
                spellingTimerEl.textContent = formatTime(spellingGameState.totalTime);
            }, 100);
            
            enableSpellingTileInteraction();
        }

        function pauseSpellingTimer() {
            if (!spellingGameState.isGameActive || spellingGameState.isPaused) return;
            clearInterval(spellingGameState.timerInterval);
            spellingGameState.timerInterval = null;
            spellingGameState.totalTime += Date.now() - spellingGameState.lastTimeUpdate;
            spellingGameState.isPaused = true;
            spellingPauseResumeButtonEl.textContent = 'เล่นต่อ';
            spellingPauseResumeButtonEl.classList.replace('bg-red-500', 'bg-gray-500');
            spellingMessageBoxEl.textContent = 'เกมหยุดชั่วคราว...';
            spellingUndoButtonEl.disabled = true;
            spellingGameState.availableTiles.forEach(tile => tile.onclick = null);
        }

        window.pauseResumeSpellingGame = function() {
            if (!spellingGameState.isGameActive) return;
            if (spellingGameState.isPaused) startSpellingTimer();
            else pauseSpellingTimer();
        };

        function stopSpellingTimer() {
            clearInterval(spellingGameState.timerInterval);
            spellingGameState.timerInterval = null;
            spellingGameState.isPaused = false;
        }

        function startNewSpellingWord(updateWordIndex = true) {
            spellingGameState.processingWin = false;
            if (updateWordIndex) {
                if (spellingGameState.wordIndex >= SPELLING_WORDS_TO_PLAY) {
                    endSpellingGame();
                    return;
                }
                spellingGameState.wordIndex++;
                spellingWordCounterEl.textContent = `คำที่ ${spellingGameState.wordIndex} / ${SPELLING_WORDS_TO_PLAY}`;
            }

            spellingGameState.builtWord = [];
            spellingGameState.placedTiles = [];
            spellingTargetWordEl.innerHTML = '';
            spellingLetterTilesEl.innerHTML = '';
            
            const targetWord = THAI_WORDS[Math.floor(Math.random() * THAI_WORDS.length)];
            spellingGameState.currentWord = targetWord;
            spellingGameState.targetLetters = segmentThaiWord(targetWord);

            spellingGameState.targetBoxes = spellingGameState.targetLetters.map(() => {
                const box = document.createElement('div');
                box.className = 'target-box';
                spellingTargetWordEl.appendChild(box);
                return box;
            });

            let tileLetters = [...spellingGameState.targetLetters];
            const numDistractors = Math.min(MAX_DISTRACTORS, DISTRACTOR_POOL.length);
            for(let i=0; i<numDistractors; i++) {
                 tileLetters.push(DISTRACTOR_POOL[Math.floor(Math.random() * DISTRACTOR_POOL.length)]);
            }

            spellingGameState.availableTiles = [];
            const enableClick = spellingGameState.isGameActive && !spellingGameState.isPaused;
            shuffleArray(tileLetters).forEach(letter => {
                const tile = document.createElement('div');
                tile.className = 'tile p-1'; 
                tile.textContent = letter;
                tile.dataset.letter = letter;
                tile.onclick = enableClick ? () => handleSpellingTileClick(tile) : null;
                spellingLetterTilesEl.appendChild(tile);
                spellingGameState.availableTiles.push(tile);
            });
            
            spellingUndoButtonEl.disabled = true;
        }

        function enableSpellingTileInteraction() {
            spellingGameState.availableTiles.forEach(tile => {
                if (!tile.classList.contains('disabled')) {
                    tile.onclick = () => handleSpellingTileClick(tile);
                }
            });
            if (spellingGameState.placedTiles.length > 0) spellingUndoButtonEl.disabled = false;
        }

        function handleSpellingTileClick(tileEl) {
            if (!spellingGameState.isGameActive || spellingGameState.isPaused) return;
            const targetIndex = spellingGameState.builtWord.length;
            if (targetIndex >= spellingGameState.targetBoxes.length) return;

            const targetBox = spellingGameState.targetBoxes[targetIndex];
            const letter = tileEl.dataset.letter;

            targetBox.textContent = letter;
            targetBox.classList.add('filled');
            tileEl.classList.add('disabled');
            tileEl.onclick = null; 

            spellingGameState.builtWord.push(letter);
            spellingGameState.placedTiles.push({ tileEl, boxEl: targetBox });
            spellingUndoButtonEl.disabled = false;

            if (spellingGameState.builtWord.length === spellingGameState.targetBoxes.length) {
                checkSpellingWord();
            }
        }

        window.undoLastSpellingTile = function() {
            if (!spellingGameState.isGameActive || spellingGameState.placedTiles.length === 0 || spellingGameState.isPaused) return;
            const lastPlacement = spellingGameState.placedTiles.pop();
            const { tileEl, boxEl } = lastPlacement;
            boxEl.textContent = ''; boxEl.classList.remove('filled');
            tileEl.classList.remove('disabled');
            tileEl.onclick = () => handleSpellingTileClick(tileEl);
            spellingGameState.builtWord.pop();
            if (spellingGameState.placedTiles.length === 0) spellingUndoButtonEl.disabled = true;
        };

        function checkSpellingWord() {
            if (spellingGameState.isPaused || spellingGameState.processingWin) return;
            const currentGuess = spellingGameState.builtWord.join('');
            if (currentGuess === spellingGameState.currentWord) {
                spellingGameState.processingWin = true;
                spellingMessageBoxEl.textContent = `ถูกต้อง! คำว่า: ${spellingGameState.currentWord}`;
                pauseSpellingTimer();
                spellingGameState.targetBoxes.forEach(box => box.classList.add('correct-animation'));
                setTimeout(() => {
                    spellingGameState.targetBoxes.forEach(box => box.classList.remove('correct-animation'));
                    startNewSpellingWord(true);
                    startSpellingTimer(); 
                }, 1500);
            } else {
                spellingMessageBoxEl.textContent = 'ผิด! ลองใหม่';
                spellingTargetWordEl.classList.add('incorrect-shake');
                setTimeout(() => spellingTargetWordEl.classList.remove('incorrect-shake'), 300);
            }
        }

        function endSpellingGame() {
            stopSpellingTimer(); // Stop timer locally first to ensure no more updates
            const finalTime = spellingGameState.totalTime; // Capture time BEFORE cleanup
            cleanupSpellingGameState(); // Now cleanup (state reset)
            
            spellingMessageBoxEl.innerHTML = `<p class="text-2xl font-extrabold text-blue-700">เวลาทั้งหมด: ${formatTime(finalTime)}</p>`;
            
            // Prompt for Name
            setTimeout(() => {
                let playerName = prompt("ยินดีด้วย! กรุณาใส่ชื่อของคุณเพื่อบันทึกคะแนน:", "ผู้เล่น");
                if (playerName === null) return;
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
