<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เกม Hangman - Thai Hangman Game</title>
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

        /* Hangman Specific Styles */
        #hangman-figure {
            font-family: monospace;
            white-space: pre;
            text-align: left;
            margin: 20px auto;
            border: 1px solid #e5e7eb;
            background-color: #f9fafb;
            padding: 10px;
            border-radius: 8px;
            line-height: 1.1;
            font-size: 1.2rem;
            min-height: 180px;
        }
        #hangman-guessed-letters span {
            font-size: 1.5rem;
            margin: 0 5px;
            font-weight: bold;
            color: #ef4444;
        }
        #hangman-guess-input {
            width: 60px;
            text-align: center;
            font-size: 1.5rem;
            text-transform: uppercase;
            border: 2px solid #ccc;
            border-radius: 8px;
            padding: 5px;
        }
        .blank-letter {
            display: inline-block;
            width: 30px;
            height: 35px;
            line-height: 35px;
            border-bottom: 3px solid #374151;
            margin: 0 5px;
            font-size: 2rem;
            font-weight: bold;
            text-align: center;
            color: var(--color-primary);
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
    </style>
</head>
<body>
    <div class="periphery">
        <?php include 'sidebar.php'; ?>
        
        <div id="content">
            <div class="game-container">
                <h1 class="text-3xl font-extrabold text-center mb-6 text-gray-800">เกม Hangman (ทายคำ)</h1>
                
                <div id="hangman-game-content" class="p-4">
                    <div class="flex justify-center space-x-4 mb-6">
                        <button class="btn-action bg-emerald-500 text-white hover:bg-emerald-600 w-32" onclick="startHangmanGame()">เริ่มใหม่</button>
                    </div>

                    <div id="hangman-info" class="mb-4 p-3 bg-yellow-100 border border-yellow-300 rounded-lg text-lg text-gray-700">
                        <p class="font-bold text-yellow-700 mb-1">คำใบ้:</p>
                        <p id="hangman-hint-display" class="text-base italic">ยังไม่มีคำใบ้</p>
                    </div>

                    <div class="flex flex-col sm:flex-row justify-between items-start mb-6">
                        <div id="hangman-figure" class="w-full sm:w-1/3 mb-4 sm:mb-0 bg-gray-100 p-4 border border-gray-300 rounded-lg text-gray-700 text-xl"></div>
                        <div class="w-full sm:w-2/3 flex flex-col items-center">
                             <div id="hangman-word-display" class="text-4xl tracking-widest font-mono mb-8 p-4 bg-white rounded-lg shadow-md min-h-[70px]"></div>

                            <div id="hangman-stats" class="flex justify-around w-full max-w-sm mt-4">
                                <div class="text-center">
                                    <p class="text-lg font-semibold text-gray-700">ทายผิดเหลือ:</p>
                                    <p id="hangman-lives-left" class="text-3xl font-extrabold text-red-500">6</p>
                                </div>
                                <div class="text-center">
                                    <p class="text-lg font-semibold text-gray-700">ทายทั้งหมด:</p>
                                    <p id="hangman-attempts" class="text-3xl font-extrabold text-blue-500">0</p>
                                </div>
                            </div>

                            <div id="hangman-guess-area" class="flex space-x-2 items-center mb-6 mt-6">
                                <input type="text" id="hangman-guess-input" maxlength="1" placeholder="ทาย..." class="focus:ring-2 focus:ring-red-500" disabled>
                                <button id="hangman-guess-button" class="btn-action bg-red-500 text-white hover:bg-red-600 w-28" onclick="handleHangmanGuess()" disabled>ทาย</button>
                            </div>
                            
                            <p class="text-lg font-semibold text-gray-700 mb-2">ตัวอักษรที่ทายผิด:</p>
                            <div id="hangman-guessed-letters" class="text-lg font-bold text-gray-500 min-h-[30px]"></div>
                        </div>
                    </div>
                    
                    <div id="hangman-message-box" class="text-center min-h-[30px] text-xl font-semibold text-gray-700 mt-4"></div>
                    
                    <div class="mt-8">
                        <h2 class="text-2xl font-bold text-center mb-4 text-gray-800 border-b pb-2">คะแนนสูงสุด</h2>
                        <div id="hangman-high-scores" class="bg-white p-4 rounded-lg shadow">
                            <p id="hangman-loading-scores" class="text-center text-sm text-gray-500">กำลังโหลดคะแนน...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Logic -->
    <script>
        const THAI_ALPHABET = 'กขคงจฉชซฌญฎฏฐฑฒณดตถทธนบปผฝพฟภมยรลวศษสหฬอฮะาิีึืุูเแโใไ'.split('');
        const HANGMAN_WORDS = [
            { word: 'กล้วย', hint: 'ผลไม้สีเหลือง เป็นอาหารลิง' },
            { word: 'มะม่วง', hint: 'ผลไม้หน้าร้อน พันธุ์อกร่องอร่อย' },
            { word: 'อาหาร', hint: 'สิ่งที่มนุษย์และสัตว์กินเพื่อดำรงชีวิต' },
            { word: 'โรงเรียน', hint: 'สถานที่เรียนรู้และพบปะเพื่อน' },
            { word: 'คอมพิวเตอร์', hint: 'อุปกรณ์อิเล็กทรอนิกส์สำหรับประมวลผลข้อมูล' },
            { word: 'ประเทศไทย', hint: 'ดินแดนแห่งรอยยิ้ม เมืองหลวงคือกรุงเทพฯ' }
        ];

        let hangmanGameState = {
            word: '', hint: '', guessedLetters: new Set(), maxLives: 6,
            livesLeft: 6, attempts: 0, isGameActive: false,
        };

        const hangmanFigureEl = document.getElementById('hangman-figure');
        const hangmanWordDisplayEl = document.getElementById('hangman-word-display');
        const hangmanGuessInputEl = document.getElementById('hangman-guess-input');
        const hangmanGuessButtonEl = document.getElementById('hangman-guess-button');
        const hangmanGuessedLettersEl = document.getElementById('hangman-guessed-letters');
        const hangmanMessageBoxEl = document.getElementById('hangman-message-box');
        const hangmanHintDisplayEl = document.getElementById('hangman-hint-display'); 
        const hangmanAttemptsEl = document.getElementById('hangman-attempts'); 
        const hangmanLivesLeftEl = document.getElementById('hangman-lives-left'); 
        const hangmanHighScoresEl = document.getElementById('hangman-high-scores');

        const ACTUAL_HANGMAN_FIGURES = [
`
  +---+
  |   |
      |
      |
      |
      |
=========`,
`
  +---+
  |   |
  O   |
      |
      |
      |
=========`,
`
  +---+
  |   |
  O   |
  |   |
      |
      |
=========`,
`
  +---+
  |   |
  O   |
 /|   |
      |
      |
=========`,
`
  +---+
  |   |
  O   |
 /|\\  |
      |
      |
=========`,
`
  +---+
  |   |
  O   |
 /|\\  |
 /    |
      |
=========`,
`
  +---+
  |   |
  O   |
 /|\\  |
 / \\  |
      |
=========`
        ];

        function cleanupHangmanGameState() {
            hangmanGameState.isGameActive = false;
            hangmanGameState.word = '';
            hangmanGameState.hint = '';
            hangmanGameState.guessedLetters = new Set();
            hangmanGameState.livesLeft = hangmanGameState.maxLives;
            hangmanGameState.attempts = 0;

            hangmanFigureEl.textContent = ACTUAL_HANGMAN_FIGURES[0];
            hangmanWordDisplayEl.innerHTML = '';
            hangmanGuessedLettersEl.textContent = '';
            hangmanMessageBoxEl.textContent = 'กด "เริ่มใหม่" เพื่อเริ่มทายคำ';
            hangmanGuessInputEl.value = '';
            hangmanGuessInputEl.disabled = true;
            hangmanGuessButtonEl.disabled = true;
            hangmanHintDisplayEl.textContent = 'ยังไม่มีคำใบ้';
            hangmanAttemptsEl.textContent = '0';
            hangmanLivesLeftEl.textContent = hangmanGameState.maxLives;
        }

        function prepareHangmanGame() {
            cleanupHangmanGameState();
            const selectedWordObj = HANGMAN_WORDS[Math.floor(Math.random() * HANGMAN_WORDS.length)];
            hangmanGameState.word = selectedWordObj.word;
            hangmanGameState.hint = selectedWordObj.hint; 
            hangmanHintDisplayEl.textContent = hangmanGameState.hint;
            updateHangmanDisplay(); 
        }

        window.startHangmanGame = function() {
            prepareHangmanGame();
            hangmanGameState.isGameActive = true;
            hangmanGuessInputEl.disabled = false;
            hangmanGuessButtonEl.disabled = false;
            hangmanMessageBoxEl.textContent = `เริ่มเกม! คำมี ${hangmanGameState.word.length} ตัวอักษร`;
            hangmanGuessInputEl.focus();
        };

        function updateWordDisplay() {
            let display = '';
            for (const char of hangmanGameState.word) {
                if (hangmanGameState.guessedLetters.has(char)) {
                    display += `<span class="blank-letter">${char}</span>`;
                } else {
                    display += `<span class="blank-letter">_</span>`;
                }
            }
            hangmanWordDisplayEl.innerHTML = display;
        }

        function updateHangmanDisplay() {
            // maxLives = 6. livesLeft goes 6..0. 
            // Index 0 to 6.
            const figureIndex = hangmanGameState.maxLives - hangmanGameState.livesLeft;
            hangmanFigureEl.textContent = ACTUAL_HANGMAN_FIGURES[Math.min(figureIndex, ACTUAL_HANGMAN_FIGURES.length-1)];
            
            updateWordDisplay();
            hangmanGuessedLettersEl.textContent = Array.from(hangmanGameState.guessedLetters).filter(char => !hangmanGameState.word.includes(char)).sort().join(' ');
            hangmanAttemptsEl.textContent = hangmanGameState.attempts;
            hangmanLivesLeftEl.textContent = hangmanGameState.livesLeft;
        }

        window.handleHangmanGuess = function() {
            if (!hangmanGameState.isGameActive) return;
            const guess = hangmanGuessInputEl.value.trim();
            hangmanGuessInputEl.value = '';
            if (guess.length !== 1 || !THAI_ALPHABET.includes(guess)) {
                hangmanMessageBoxEl.textContent = 'กรุณาทายตัวอักษรไทย 1 ตัว';
                return;
            }
            if (hangmanGameState.guessedLetters.has(guess)) {
                hangmanMessageBoxEl.textContent = `ทาย "${guess}" ไปแล้ว`;
                return;
            }
            
            hangmanGameState.attempts++;
            hangmanGameState.guessedLetters.add(guess);

            if (hangmanGameState.word.includes(guess)) {
                hangmanMessageBoxEl.textContent = `ถูกต้อง! "${guess}" มีในคำ`;
            } else {
                hangmanGameState.livesLeft--;
                hangmanMessageBoxEl.textContent = `ผิด! "${guess}" ไม่มีในคำ`;
                hangmanFigureEl.classList.add('incorrect-shake');
                setTimeout(() => hangmanFigureEl.classList.remove('incorrect-shake'), 300);
            }
            updateHangmanDisplay();
            checkHangmanWinLoss();
            hangmanGuessInputEl.focus();
        };

        function checkHangmanWinLoss() {
            if (!hangmanGameState.isGameActive) return;
            const isWordGuessed = [...hangmanGameState.word].every(char => hangmanGameState.guessedLetters.has(char));
            if (isWordGuessed) {
                hangmanGameState.isGameActive = false;
                hangmanMessageBoxEl.innerHTML = `<span class="text-green-600 font-extrabold">ชนะแล้ว! คำคือ "${hangmanGameState.word}"</span>`;
                hangmanGuessInputEl.disabled = true; hangmanGuessButtonEl.disabled = true;
                
                // Prompt Name
                setTimeout(() => {
                    let playerName = prompt("ยินดีด้วย! กรุณาใส่ชื่อของคุณเพื่อบันทึกคะแนน:", "ผู้เล่น");
                    if (playerName === null) return;
                    playerName = playerName.trim();
                    saveHangmanGameResult(playerName, hangmanGameState.attempts);
                }, 500);

            } else if (hangmanGameState.livesLeft <= 0) {
                hangmanGameState.isGameActive = false;
                hangmanMessageBoxEl.innerHTML = `<span class="text-red-600 font-extrabold">แพ้แล้ว! คำคือ "${hangmanGameState.word}"</span>`;
                updateHangmanDisplay();
                hangmanGuessInputEl.disabled = true; hangmanGuessButtonEl.disabled = true;
            }
        }

        async function saveHangmanGameResult(playerName, attempts) {
            try {
                const formData = new FormData();
                formData.append('action', 'save');
                formData.append('game', 'hangman');
                formData.append('player_name', playerName);
                formData.append('score', attempts);

                const res = await fetch('score_api.php', { method: 'POST', body: formData });
                const data = await res.json();

                if (data.success) {
                    hangmanMessageBoxEl.textContent = `บันทึกคะแนนสำเร็จ! (${data.player_name})`;
                    loadHangmanHighScores();
                } else {
                    console.error(data.error);
                }
            } catch(e) { console.error(e); }
        }

        async function loadHangmanHighScores() {
            try {
                const res = await fetch('score_api.php?action=load&game=hangman');
                const scores = await res.json();

                let html = `
                <div class="flex font-bold text-gray-600 border-b pb-2 mb-2">
                    <span class="w-1/6">#</span> <span class="w-3/6">ผู้เล่น</span> <span class="w-2/6 text-right">ทาย</span>
                </div>`;
                if(scores.error || scores.length === 0) {
                    html += '<p class="text-center text-gray-500">ไม่มีคะแนน</p>';
                } else {
                    scores.forEach((d, idx) => {
                        html += `<div class="score-item flex items-center py-2 px-2 rounded-lg text-sm">
                            <span class="w-1/6 font-bold text-lg text-red-500">${idx+1}</span>
                            <span class="w-3/6 truncate">${d.player_name}</span>
                            <span class="w-2/6 text-right font-mono text-blue-700">${d.score} ครั้ง</span>
                        </div>`;
                    });
                }
                hangmanHighScoresEl.innerHTML = html;
            } catch(e) { console.error(e); }
        }

        hangmanGuessInputEl.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') { handleHangmanGuess(); e.preventDefault(); }
        });
        
        // Start init
        prepareHangmanGame();
        loadHangmanHighScores();
    </script>
</body>
</html>
