<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เกมฝึกคำศัพท์ภาษาไทย 3 เกมใน 1</title>
    <!-- Import Firebase modules -->
    <script type="module">
        import { initializeApp } from "https://www.gstatic.com/firebasejs/11.6.1/firebase-app.js";
        import { getAuth, signInAnonymously, signInWithCustomToken, onAuthStateChanged } from "https://www.gstatic.com/firebasejs/11.6.1/firebase-auth.js";
        import { getFirestore, doc, addDoc, onSnapshot, collection, query, orderBy, limit, serverTimestamp, setLogLevel } from "https://www.gstatic.com/firebasejs/11.6.1/firebase-firestore.js";

        // ตัวแปรส่วนกลางที่ได้รับจากสภาพแวดล้อม Canvas
        const appId = typeof __app_id !== 'undefined' ? __app_id : 'default-app-id';
        const firebaseConfig = JSON.parse(typeof __firebase_config !== 'undefined' ? __firebase_config : '{}');
        const initialAuthToken = typeof __initial_auth_token !== 'undefined' ? __initial_auth_token : null;

        let db = null;
        let auth = null;
        let userId = null;

        // ฟังก์ชันสำหรับเริ่มต้น Firebase และการรับรองความถูกต้องของผู้ใช้
        async function initializeFirebase() {
            if (!firebaseConfig || Object.keys(firebaseConfig).length === 0) {
                console.error("Firebase configuration is missing.");
                // ใช้ spelling-message-box เป็นตัว fallback สำหรับแสดง error
                document.getElementById('spelling-message-box').innerHTML = '<p class="text-red-600 font-bold">Error: Firebase configuration is missing. High scores cannot be saved.</p>';
                return;
            }

            try {
                // กำหนดระดับดีบักสำหรับการบันทึก
                setLogLevel('debug');
                
                const app = initializeApp(firebaseConfig);
                db = getFirestore(app);
                auth = getAuth(app);
                
                // ลงชื่อเข้าใช้โดยใช้โทเค็นที่กำหนดเองหรือแบบไม่ระบุตัวตน
                if (initialAuthToken) {
                    await signInWithCustomToken(auth, initialAuthToken);
                } else {
                    await signInAnonymously(auth);
                }

                // รอการเปลี่ยนแปลงสถานะการรับรองความถูกต้องเพื่อรับ ID ผู้ใช้สุดท้าย
                onAuthStateChanged(auth, (user) => {
                    if (user) {
                        userId = user.uid;
                        console.log("Firebase Auth Ready. User ID:", userId);
                        window.db = db;
                        window.auth = auth;
                        window.userId = userId;
                        window.appId = appId;
                        
                        // เริ่มโหลดคะแนนสูงสุดเมื่อรับรองความถูกต้องเสร็จสิ้น
                        window.loadSpellingHighScores(); // โหลดคะแนนเริ่มต้น
                    } else {
                        // การสำรองสำหรับ ID ผู้ใช้ที่ไม่ระบุตัวตน
                        userId = `anon-${crypto.randomUUID()}`;
                        console.log("Firebase Auth Failed/Anonymous. Generated temporary User ID:", userId);
                        window.db = db;
                        window.auth = auth;
                        window.userId = userId;
                        window.appId = appId;
                        window.loadSpellingHighScores(); // โหลดคะแนนเริ่มต้น
                    }
                });

            } catch (error) {
                console.error("Firebase initialization or sign-in failed:", error);
                document.getElementById('spelling-message-box').innerHTML = `<p class="text-red-600 font-bold">Firebase Error: ${error.message}. High scores will not work.</p>`;
            }
        }

        // แนบฟังก์ชันที่จำเป็นกับ window
        window.initializeApp = initializeFirebase;
        window.getFirestore = getFirestore;
        window.doc = doc;
        window.addDoc = addDoc;
        window.onSnapshot = onSnapshot;
        window.collection = collection;
        window.query = query;
        window.orderBy = orderBy;
        window.limit = limit;
        window.serverTimestamp = serverTimestamp;

    </script>
    <!-- Tailwind CSS สำหรับคลาสยูทิลิตีสมัยใหม่ -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Sarabun:wght@400;700&display=swap');
        
        :root {
            --color-primary: #3b82f6; /* blue-500 */
            --color-secondary: #10b981; /* emerald-500 */
            --color-background: #f3f4f6; /* gray-100 */
            --color-surface: #ffffff;
            --color-text: #1f2937;
        }

        body {
            font-family: 'Sarabun', sans-serif;
            background-color: var(--color-background);
            color: var(--color-text);
            padding: 20px;
        }

        .game-container {
            max-width: 900px;
            margin: 0 auto;
            background-color: var(--color-surface);
            border-radius: 16px;
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
            padding: 24px;
        }
        
        /* --- Spelling Game Styles --- */

        .target-box {
            min-width: 40px;
            min-height: 55px;
            line-height: 50px;
            border: 2px dashed #9ca3af; /* gray-400 */
            background-color: #f9fafb; /* gray-50 */
            font-size: 1.5rem;
            font-weight: 700;
            text-align: center;
            border-radius: 8px;
            transition: all 0.1s;
            cursor: default;
        }
        
        .target-box.filled {
            border: 2px solid var(--color-primary);
            background-color: #eff6ff; /* blue-50 */
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
            box-shadow: 0 4px #047857; /* shadow for button effect */
            transition: transform 0.1s, background-color 0.1s, box-shadow 0.1s;
            margin: 6px;
        }

        .tile:hover:not(.disabled) {
            background-color: #059669; /* emerald-600 */
            transform: translateY(-2px);
            box-shadow: 0 6px #047857;
        }

        .tile:active:not(.disabled) {
            transform: translateY(2px);
            box-shadow: 0 2px #047857;
        }

        .tile.disabled {
            background-color: #d1d5db; /* gray-300 */
            color: #6b7280; /* gray-500 */
            cursor: not-allowed;
            box-shadow: none;
        }
        
        /* --- Shared Styles --- */

        .correct-animation {
            animation: correctPulse 0.5s ease-in-out;
        }
        
        @keyframes correctPulse {
            0% { transform: scale(1); background-color: var(--color-secondary); }
            50% { transform: scale(1.1); background-color: #34d399; }
            100% { transform: scale(1); background-color: var(--color-secondary); }
        }

        .incorrect-shake {
            animation: shake 0.3s;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            20%, 60% { transform: translateX(-5px); }
            40%, 80% { transform: translateX(5px); }
        }

        .score-item:nth-child(odd) {
            background-color: #f9fafb; /* gray-50 */
        }
        .score-item:nth-child(even) {
            background-color: #ffffff; /* white */
        }

        .btn-action {
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 700;
            transition: background-color 0.2s;
            box-shadow: 0 4px rgba(0, 0, 0, 0.1);
        }

        /* --- Hangman Specific Styles --- */
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
            min-height: 180px; /* Ensure space for the figure */
        }
        #hangman-guessed-letters span {
            font-size: 1.5rem;
            margin: 0 5px;
            font-weight: bold;
            color: #ef4444; /* Red */
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

        /* --- Categorizer Specific Styles --- */
        #categorizer-consonant {
            font-size: 8rem;
            font-weight: 900;
            line-height: 1;
            color: #f97316; /* orange-500 */
            text-shadow: 4px 4px 0 #ea580c; /* orange-600 */
        }
        .categorizer-btn {
            font-size: 1.5rem;
            padding: 15px 30px;
            width: 100%;
            transition: background-color 0.2s, transform 0.1s;
        }
        .categorizer-btn:active {
            transform: translateY(2px);
        }

    </style>
</head>
<body onload="window.initializeApp()">
    <div class="game-container">
        <h1 class="text-3xl font-extrabold text-center mb-6 text-gray-800">เกมฝึกคำศัพท์ภาษาไทย</h1>
        <p class="text-center text-sm mb-4 text-gray-600">
            เลือกเกมที่คุณต้องการเล่นเพื่อฝึกคำศัพท์ <span id="user-info"></span>
        </p>
        
        <!-- 1. GAME MENU SCREEN (Default View) -->
        <div id="menu-screen" class="min-h-[500px] flex flex-col items-center justify-center p-8 space-y-6">
            <div class="flex flex-wrap justify-center gap-6 w-full max-w-xl">
                 <!-- Spelling Game Card -->
                <div class="menu-card bg-white p-6 rounded-xl shadow-2xl text-center border-b-4 border-emerald-500 transform transition duration-500 hover:scale-[1.05] w-full sm:w-64">
                    <h2 class="text-3xl font-extrabold text-gray-900 mb-2">เกมสะกดคำ</h2>
                    <p class="text-gray-600 mb-4 text-sm">เรียงตัวอักษรเพื่อแข่งกับเวลา (5 คำ)</p>
                    <button class="btn-action bg-emerald-500 text-white hover:bg-emerald-600 active:bg-emerald-700 w-full text-lg py-2" onclick="showGameView('spelling')">
                        เริ่มเล่น (สะกดคำ)
                    </button>
                </div>

                <!-- Hangman Game Card -->
                <div class="menu-card bg-white p-6 rounded-xl shadow-2xl text-center border-b-4 border-red-500 transform transition duration-500 hover:scale-[1.05] w-full sm:w-64">
                    <h2 class="text-3xl font-extrabold text-gray-900 mb-2">เกม Hangman</h2>
                    <p class="text-gray-600 mb-4 text-sm">ทายตัวอักษรทีละตัวพร้อมคำใบ้</p>
                    <button class="btn-action bg-red-500 text-white hover:bg-red-600 active:bg-red-700 w-full text-lg py-2" onclick="showGameView('hangman')">
                        เริ่มเล่น (Hangman)
                    </button>
                </div>

                <!-- NEW: Categorizer Game Card -->
                <div class="menu-card bg-white p-6 rounded-xl shadow-2xl text-center border-b-4 border-orange-500 transform transition duration-500 hover:scale-[1.05] w-full sm:w-64">
                    <h2 class="text-3xl font-extrabold text-gray-900 mb-2">จัดหมวดพยัญชนะ</h2>
                    <p class="text-gray-600 mb-4 text-sm">จัดหมวดอักษรสูง กลาง ต่ำ (20 ข้อ)</p>
                    <button class="btn-action bg-orange-500 text-white hover:bg-orange-600 active:bg-orange-700 w-full text-lg py-2" onclick="showGameView('categorizer')">
                        เริ่มเล่น (ไตรยางศ์)
                    </button>
                </div>
            </div>

        </div>

        <!-- 2. MAIN GAME VIEW CONTAINER (Contains the actual game logic, initially hidden) -->
        <div id="game-view" class="hidden">
            
            <!-- --- SPELLING GAME CONTENT (Hidden by default) --- -->
            <div id="spelling-game-content" class="hidden">
                <!-- Spelling Game Info & Timer -->
                <div class="flex justify-between items-center bg-blue-50 p-4 rounded-lg mb-6 shadow-inner">
                    <div id="spelling-word-counter" class="text-lg font-bold text-blue-700">
                        คำที่ 0 / 5
                    </div>
                    <div class="flex items-center space-x-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span id="spelling-timer" class="text-2xl font-extrabold text-blue-800">00:00</span>
                    </div>
                </div>

                <!-- Target Word Display -->
                <div id="spelling-target-word" class="flex justify-center flex-wrap gap-2 mb-8 p-4 bg-white rounded-lg border border-gray-200 min-h-[80px]">
                    <!-- Target boxes will be inserted here -->
                </div>

                <!-- Letter Tiles Source -->
                <div id="spelling-letter-tiles" class="flex justify-center flex-wrap gap-3 mb-6 p-4 bg-white rounded-lg border border-gray-200 min-h-[120px]">
                    <!-- Letter tiles will be inserted here -->
                </div>

                <!-- Controls and Feedback -->
                <div class="flex justify-center space-x-4 mb-6">
                    
                    <!-- DYNAMIC: Start New / Reset Game Button -->
                    <button id="spelling-start-reset-button" class="btn-action bg-emerald-500 text-white hover:bg-emerald-600 active:bg-emerald-700 w-32" onclick="handleSpellingStartResetClick()">
                        เริ่มใหม่
                    </button>
                    
                    <!-- Pause/Resume Button -->
                    <button id="spelling-pause-resume-button" class="btn-action bg-red-500 text-white hover:bg-red-600 active:bg-red-700 w-32 disabled:opacity-50 disabled:cursor-not-allowed" onclick="pauseResumeSpellingGame()" disabled>
                        หยุดเวลา
                    </button>

                    <!-- Undo Button -->
                    <button id="spelling-undo-button" class="btn-action bg-yellow-500 text-white hover:bg-yellow-600 active:bg-yellow-700 w-32 disabled:opacity-50 disabled:cursor-not-allowed" onclick="undoLastSpellingTile()" disabled>
                        ย้อนกลับ
                    </button>

                     <!-- Exit to Menu Button -->
                    <button class="btn-action bg-indigo-500 text-white hover:bg-indigo-600 active:bg-indigo-700 w-32" onclick="exitToMenu()">
                        เมนูหลัก
                    </button>
                </div>

                <!-- Message Box -->
                <div id="spelling-message-box" class="text-center min-h-[30px] text-lg font-semibold text-gray-700">
                    <!-- Game messages go here -->
                </div>

                <!-- High Scores Section (Now moved inside Spelling Game Content) -->
                <div class="mt-8">
                    <h2 class="text-2xl font-bold text-center mb-4 text-gray-800 border-b pb-2">คะแนนสูงสุด (เกมสะกดคำ)</h2>
                    <div id="high-scores" class="bg-white p-4 rounded-lg shadow">
                        <div class="flex font-bold text-gray-600 border-b pb-2 mb-2">
                            <span class="w-1/6">อันดับ</span>
                            <span class="w-3/6">ผู้เล่น</span>
                            <span class="w-2/6 text-right">เวลา</span>
                        </div>
                        <!-- Scores will be inserted here -->
                        <p id="loading-scores" class="text-center text-sm text-gray-500">กำลังโหลดคะแนน...</p>
                    </div>
                </div>
            </div> <!-- End of #spelling-game-content -->

            <!-- --- HANGMAN GAME CONTENT (Hidden by default) --- -->
            <div id="hangman-game-content" class="hidden p-4">
                <div class="flex justify-center space-x-4 mb-6">
                    <button class="btn-action bg-emerald-500 text-white hover:bg-emerald-600 active:bg-emerald-700 w-32" onclick="startHangmanGame()">
                        เริ่มใหม่
                    </button>
                    <button class="btn-action bg-indigo-500 text-white hover:bg-indigo-600 active:bg-indigo-700 w-32" onclick="exitToMenu()">
                        เมนูหลัก
                    </button>
                </div>

                <!-- Hint Display -->
                <div id="hangman-info" class="mb-4 p-3 bg-yellow-100 border border-yellow-300 rounded-lg text-lg text-gray-700">
                    <p class="font-bold text-yellow-700 mb-1">คำใบ้:</p>
                    <p id="hangman-hint-display" class="text-base italic">ยังไม่มีคำใบ้</p>
                </div>

                <div class="flex flex-col sm:flex-row justify-between items-start mb-6">
                    <div id="hangman-figure" class="w-full sm:w-1/3 mb-4 sm:mb-0 bg-gray-100 p-4 border border-gray-300 rounded-lg text-gray-700 text-xl">
                        <!-- Hangman ASCII Art will be here -->
                    </div>
                    <div class="w-full sm:w-2/3 flex flex-col items-center">
                         <div id="hangman-word-display" class="text-4xl tracking-widest font-mono mb-8 p-4 bg-white rounded-lg shadow-md min-h-[70px]">
                            <!-- Word blanks/letters here -->
                        </div>

                        <!-- Hangman Stats -->
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
                            <button id="hangman-guess-button" class="btn-action bg-red-500 text-white hover:bg-red-600 active:bg-red-700 text-lg w-28" onclick="handleHangmanGuess()" disabled>
                                ทาย
                            </button>
                        </div>
                        
                        <p class="text-lg font-semibold text-gray-700 mb-2">ตัวอักษรที่ทายผิด:</p>
                        <div id="hangman-guessed-letters" class="text-lg font-bold text-gray-500 min-h-[30px]">
                            <!-- Guessed letters here -->
                        </div>
                    </div>
                </div>
                
                <div id="hangman-message-box" class="text-center min-h-[30px] text-xl font-semibold text-gray-700 mt-4">
                    <!-- Hangman game messages go here -->
                </div>
                
                <!-- Hangman High Scores Section -->
                <div class="mt-8">
                    <h2 class="text-2xl font-bold text-center mb-4 text-gray-800 border-b pb-2">คะแนนสูงสุด (เกม Hangman)</h2>
                    <div id="hangman-high-scores" class="bg-white p-4 rounded-lg shadow">
                        <div class="flex font-bold text-gray-600 border-b pb-2 mb-2">
                            <span class="w-1/6">อันดับ</span>
                            <span class="w-3/6">ผู้เล่น</span>
                            <span class="w-2/6 text-right">การทาย</span>
                        </div>
                        <!-- Scores will be inserted here -->
                        <p id="hangman-loading-scores" class="text-center text-sm text-gray-500">กำลังโหลดคะแนน...</p>
                    </div>
                </div>

            </div> <!-- End of #hangman-game-content -->

            <!-- --- NEW CATEGORIZER GAME CONTENT (Hidden by default) --- -->
            <div id="categorizer-game-content" class="hidden p-4">
                <div class="flex justify-center space-x-4 mb-6">
                    <button class="btn-action bg-emerald-500 text-white hover:bg-emerald-600 active:bg-emerald-700 w-32" onclick="handleCategorizerStartReset()">
                        เริ่มใหม่
                    </button>
                    <button id="categorizer-pause-button" class="btn-action bg-red-500 text-white hover:bg-red-600 active:bg-red-700 w-32 disabled:opacity-50 disabled:cursor-not-allowed" onclick="pauseResumeCategorizer()" disabled>
                        หยุดเวลา
                    </button>
                    <button class="btn-action bg-indigo-500 text-white hover:bg-indigo-600 active:bg-indigo-700 w-32" onclick="exitToMenu()">
                        เมนูหลัก
                    </button>
                </div>

                <!-- Game Display and Stats -->
                <div class="bg-blue-50 p-6 rounded-lg shadow-inner mb-6 flex justify-between items-center flex-wrap">
                    <div class="text-left">
                        <p class="text-lg font-bold text-blue-700">ข้อที่:</p>
                        <p id="categorizer-counter" class="text-3xl font-extrabold text-blue-800">0 / 20</p>
                    </div>
                    <div class="text-center">
                         <div id="categorizer-consonant" class="mx-auto my-4 bg-white p-4 rounded-xl shadow-lg border-b-4 border-orange-500 w-40 h-40 flex items-center justify-center">
                            ก ข ค
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-lg font-bold text-blue-700">เวลา:</p>
                        <p id="categorizer-timer" class="text-3xl font-extrabold text-blue-800">00:00</p>
                    </div>
                </div>

                <!-- Category Buttons -->
                <div id="categorizer-buttons" class="grid grid-cols-3 gap-4 mb-6">
                    <button id="btn-high" class="categorizer-btn bg-yellow-400 text-gray-900 hover:bg-yellow-500" onclick="handleCategorizerGuess('high')" disabled>อักษรสูง</button>
                    <button id="btn-middle" class="categorizer-btn bg-green-400 text-gray-900 hover:bg-green-500" onclick="handleCategorizerGuess('middle')" disabled>อักษรกลาง</button>
                    <button id="btn-low" class="categorizer-btn bg-blue-400 text-white hover:bg-blue-500" onclick="handleCategorizerGuess('low')" disabled>อักษรต่ำ</button>
                </div>

                <div id="categorizer-message-box" class="text-center min-h-[30px] text-lg font-semibold text-gray-700">
                    <!-- Categorizer game messages go here -->
                </div>
            </div> <!-- End of #categorizer-game-content -->

        </div> <!-- End of #game-view -->

    </div>

    <script>
        // --- การตั้งค่าเกม ---
        const SPELLING_WORDS_TO_PLAY = 5;
        const CATEGORIZER_QUESTIONS = 20;

        // คำศัพท์สำหรับเกมสะกดคำ
        const THAI_WORDS = [
            'สวัสดี', 'กล้วย', 'มะม่วง', 'คอมพิวเตอร์', 'ประเทศไทย', 
            'น่ารัก', 'โรงเรียน', 'อาหาร', 'วันหยุด', 'มหาสมุทร'
        ];
        const THAI_ALPHABET = 'กขคงจฉชซฌญฎฏฐฑฒณดตถทธนบปผฝพฟภมยรลวศษสหฬอฮะาิีึืุูเแโใไ'.split('');
        const DISTRACTOR_POOL = THAI_ALPHABET;
        const MAX_DISTRACTORS = 3; 
        
        // คำศัพท์ Hangman พร้อมคำใบ้
        const HANGMAN_WORDS = [
            { word: 'กล้วย', hint: 'ผลไม้สีเหลือง เป็นอาหารลิง' },
            { word: 'มะม่วง', hint: 'ผลไม้หน้าร้อน พันธุ์อกร่องอร่อย' },
            { word: 'อาหาร', hint: 'สิ่งที่มนุษย์และสัตว์กินเพื่อดำรงชีวิต' },
            { word: 'โรงเรียน', hint: 'สถานที่เรียนรู้และพบปะเพื่อน' },
            { word: 'คอมพิวเตอร์', hint: 'อุปกรณ์อิเล็กทรอนิกส์สำหรับประมวลผลข้อมูล' },
            { word: 'ประเทศไทย', hint: 'ดินแดนแห่งรอยยิ้ม เมืองหลวงคือกรุงเทพฯ' }
        ];

        // พยัญชนะสำหรับเกมจัดหมวดหมู่ (ไตรยางศ์)
        const THAI_CONSONANTS_CATEGORIES = {
            high: ['ข', 'ฃ', 'ฉ', 'ฐ', 'ถ', 'ผ', 'ฝ', 'ศ', 'ษ', 'ส', 'ห'],
            middle: ['ก', 'จ', 'ฎ', 'ฏ', 'ด', 'ต', 'บ', 'ป', 'อ'],
            low: ['ค', 'ฅ', 'ฆ', 'ง', 'ช', 'ซ', 'ฌ', 'ญ', 'ฑ', 'ฒ', 'ณ', 'ท', 'ธ', 'น', 'พ', 'ฟ', 'ภ', 'ม', 'ย', 'ร', 'ล', 'ว', 'ฬ', 'ฮ']
        };

        // --- สถานะของเกมสะกดคำ ---
        let spellingGameState = {
            currentWord: '', targetLetters: [], builtWord: [], availableTiles: [], 
            targetBoxes: [], placedTiles: [], wordIndex: 0, timerInterval: null,
            totalTime: 0, isGameActive: false, isPaused: false, lastTimeUpdate: 0,
        };

        // --- สถานะของเกม Hangman ---
        let hangmanGameState = {
            word: '', hint: '', guessedLetters: new Set(), maxLives: 6,
            livesLeft: 6, attempts: 0, isGameActive: false,
        };
        
        // --- สถานะของเกมจัดหมวดหมู่ ---
        let categorizerGameState = {
            questionCount: 0,
            currentConsonant: '',
            correctCategory: '',
            timerInterval: null,
            totalTime: 0,
            isGameActive: false,
            isPaused: false,
            lastTimeUpdate: 0,
            score: 0
        };


        // --- องค์ประกอบ DOM ---
        const menuScreenEl = document.getElementById('menu-screen'); 
        const gameViewEl = document.getElementById('game-view'); 
        const spellingContentEl = document.getElementById('spelling-game-content');
        const hangmanContentEl = document.getElementById('hangman-game-content');
        const categorizerContentEl = document.getElementById('categorizer-game-content'); // NEW

        // Spelling Game DOM (ย่อ)
        const spellingTargetWordEl = document.getElementById('spelling-target-word');
        const spellingLetterTilesEl = document.getElementById('spelling-letter-tiles');
        const spellingTimerEl = document.getElementById('spelling-timer');
        const spellingWordCounterEl = document.getElementById('spelling-word-counter');
        const spellingMessageBoxEl = document.getElementById('spelling-message-box');
        const spellingStartResetButtonEl = document.getElementById('spelling-start-reset-button'); 
        const spellingPauseResumeButtonEl = document.getElementById('spelling-pause-resume-button'); 
        const spellingUndoButtonEl = document.getElementById('spelling-undo-button');
        const highScoresEl = document.getElementById('high-scores'); 
        const userInfoEl = document.getElementById('user-info');

        // Hangman Game DOM (ย่อ)
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
        const hangmanLoadingScoresEl = document.getElementById('hangman-loading-scores'); 

        // Categorizer Game DOM (NEW)
        const categorizerConsonantEl = document.getElementById('categorizer-consonant');
        const categorizerCounterEl = document.getElementById('categorizer-counter');
        const categorizerTimerEl = document.getElementById('categorizer-timer');
        const categorizerMessageBoxEl = document.getElementById('categorizer-message-box');
        const categorizerPauseButtonEl = document.getElementById('categorizer-pause-button');
        const categorizerButtons = [
            document.getElementById('btn-high'), 
            document.getElementById('btn-middle'), 
            document.getElementById('btn-low')
        ];


        // --- FIRESTORE LOGIC ---

        function formatTime(ms) {
            const totalSeconds = Math.floor(ms / 1000);
            const minutes = Math.floor(totalSeconds / 60);
            const seconds = totalSeconds % 60;
            return `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
        }

        async function saveSpellingGameResult(finalTime) { 
            if (!window.db || !window.userId) {
                console.warn("Firestore not ready. Cannot save spelling score.");
                return;
            }
            const username = `Player-${window.userId.substring(0, 8)}`;
            try {
                const collectionPath = `/artifacts/${window.appId}/public/data/spelling_high_scores`;
                await window.addDoc(window.collection(window.db, collectionPath), {
                    userId: window.userId,
                    score: finalTime, // Lower is better
                    timestamp: window.serverTimestamp(),
                    username: username
                });
                spellingMessageBoxEl.textContent = `เยี่ยมมาก! คุณสะกดครบ ${SPELLING_WORDS_TO_PLAY} คำ ในเวลา ${formatTime(finalTime)}`;
            } catch (error) {
                console.error("Error saving spelling high score:", error);
                spellingMessageBoxEl.textContent = 'บันทึกคะแนนล้มเหลว (เกิดข้อผิดพลาดกับฐานข้อมูล)';
            }
        }
        window.loadSpellingHighScores = function() { 
            if (!window.db) {
                if (loadingScoresEl) loadingScoresEl.textContent = 'ไม่สามารถโหลดคะแนน: Firebase ไม่พร้อมใช้งาน';
                return;
            }
            
            if(userInfoEl) userInfoEl.textContent = `(ID ผู้เล่น: ${window.userId ? window.userId : '...'})`;

            const collectionPath = `/artifacts/${window.appId}/public/data/spelling_high_scores`;
            const scoresQuery = window.query(
                window.collection(window.db, collectionPath),
                window.orderBy('score', 'asc'),
                window.limit(10)
            );

            window.onSnapshot(scoresQuery, (snapshot) => {
                const scores = [];
                snapshot.forEach(doc => {
                    scores.push(doc.data());
                });

                renderSpellingHighScores(scores);
            }, (error) => {
                console.error("Error listening to spelling high scores:", error);
                if (loadingScoresEl) loadingScoresEl.textContent = 'เกิดข้อผิดพลาดในการโหลดคะแนนสูงสุด';
            });
        };
        function renderSpellingHighScores(scores) {
            let html = `
                <div class="flex font-bold text-gray-600 border-b pb-2 mb-2">
                    <span class="w-1/6">อันดับ</span>
                    <span class="w-3/6">ผู้เล่น</span>
                    <span class="w-2/6 text-right">เวลา</span>
                </div>
            `;
            if (scores.length === 0) {
                html += '<p class="text-center text-gray-500 p-4">ยังไม่มีคะแนนสูงสุด</p>';
            } else {
                html += '<p id="loading-scores" class="hidden text-center text-gray-500 p-4">กำลังโหลดคะแนน...</p>';
                scores.forEach((score, index) => {
                    const timeString = formatTime(score.score);
                    const displayUserId = score.userId ? score.userId : 'N/A';
                    const displayUsername = score.username || `Player-${displayUserId.substring(0, 8)}`;

                    html += `
                        <div class="score-item flex items-center py-2 px-2 rounded-lg text-sm">
                            <span class="w-1/6 font-bold text-lg text-blue-500">${index + 1}</span>
                            <span class="w-3/6 truncate" title="ID: ${displayUserId}">${displayUsername}</span>
                            <span class="w-2/6 text-right font-mono text-green-700">${timeString}</span>
                        </div>
                    `;
                });
            }
            highScoresEl.innerHTML = html;
        }

        // --- HANGMAN GAME FIRESTORE LOGIC ---

        async function saveHangmanGameResult(attempts) { 
            if (!window.db || !window.userId) {
                console.warn("Firestore not ready. Cannot save hangman score.");
                return;
            }
            const username = `Player-${window.userId.substring(0, 8)}`;
            try {
                const collectionPath = `/artifacts/${window.appId}/public/data/hangman_high_scores`;
                await window.addDoc(window.collection(window.db, collectionPath), {
                    userId: window.userId,
                    score: attempts, // Lower is better
                    timestamp: window.serverTimestamp(),
                    username: username
                });
                console.log("Hangman score saved successfully:", attempts);
            } catch (error) {
                console.error("Error saving hangman high score:", error);
                hangmanMessageBoxEl.textContent = 'บันทึกคะแนนล้มเหลว (เกิดข้อผิดพลาดกับฐานข้อมูล)';
            }
        }

        /**
         * โหลดและแสดงคะแนนสูงสุดแบบเรียลไทม์ (ใช้สำหรับเกม Hangman)
         */
        window.loadHangmanHighScores = function() {
            if (!window.db) {
                if (hangmanLoadingScoresEl) hangmanLoadingScoresEl.textContent = 'ไม่สามารถโหลดคะแนน: Firebase ไม่พร้อมใช้งาน';
                return;
            }
            
            const collectionPath = `/artifacts/${window.appId}/public/data/hangman_high_scores`;
            const scoresQuery = window.query(
                window.collection(window.db, collectionPath),
                window.orderBy('score', 'asc'), // Ascending order (น้อยครั้งที่สุดคือดีที่สุด)
                window.limit(10)
            );

            window.onSnapshot(scoresQuery, (snapshot) => {
                const scores = [];
                snapshot.forEach(doc => {
                    scores.push(doc.data());
                });

                renderHangmanHighScores(scores);
            }, (error) => {
                console.error("Error listening to hangman high scores:", error);
                if (hangmanLoadingScoresEl) hangmanLoadingScoresEl.textContent = 'เกิดข้อผิดพลาดในการโหลดคะแนนสูงสุด';
            });
        };

        /**
         * แสดงผลคะแนนสูงสุด (เกม Hangman)
         */
        function renderHangmanHighScores(scores) {
            let html = `
                <div class="flex font-bold text-gray-600 border-b pb-2 mb-2">
                    <span class="w-1/6">อันดับ</span>
                    <span class="w-3/6">ผู้เล่น</span>
                    <span class="w-2/6 text-right">การทาย</span>
                </div>
            `;
            if (scores.length === 0) {
                html += '<p class="text-center text-gray-500 p-4">ยังไม่มีคะแนนสูงสุด</p>';
            } else {
                scores.forEach((score, index) => {
                    const displayUserId = score.userId ? score.userId : 'N/A';
                    const displayUsername = score.username || `Player-${displayUserId.substring(0, 8)}`;

                    html += `
                        <div class="score-item flex items-center py-2 px-2 rounded-lg text-sm">
                            <span class="w-1/6 font-bold text-lg text-red-500">${index + 1}</span>
                            <span class="w-3/6 truncate" title="ID: ${displayUserId}">${displayUsername}</span>
                            <span class="w-2/6 text-right font-mono text-blue-700">${score.score} ครั้ง</span>
                        </div>
                    `;
                });
            }
            hangmanHighScoresEl.innerHTML = html;
        }

        // --- NEW CATEGORIZER FIRESTORE LOGIC ---
        
        async function saveCategorizerGameResult(finalTime, correctCount) { /* ... (Logic remains the same) ... */
             if (!window.db || !window.userId) {
                console.warn("Firestore not ready. Cannot save categorizer score.");
                return;
            }
            const username = `Player-${window.userId.substring(0, 8)}`;
            try {
                const collectionPath = `/artifacts/${window.appId}/public/data/categorizer_high_scores`;
                await window.addDoc(window.collection(window.db, collectionPath), {
                    userId: window.userId,
                    time: finalTime, // Lower is better
                    score: correctCount, // Higher is better
                    ratio: correctCount / finalTime, // Score Ratio (Higher is better)
                    timestamp: window.serverTimestamp(),
                    username: username
                });
                categorizerMessageBoxEl.textContent = `เยี่ยมมาก! คะแนน: ${correctCount}/${CATEGORIZER_QUESTIONS} ในเวลา ${formatTime(finalTime)}`;
            } catch (error) {
                console.error("Error saving categorizer high score:", error);
                categorizerMessageBoxEl.textContent = 'บันทึกคะแนนล้มเหลว (เกิดข้อผิดพลาดกับฐานข้อมูล)';
            }
        }
        
        // --- ฟังก์ชันการจัดการหน้าจอ ---

        /**
         * แสดงหน้าจอเมนูหลัก
         */
        function showMenu() {
            gameViewEl.classList.add('hidden');
            spellingContentEl.classList.add('hidden');
            hangmanContentEl.classList.add('hidden');
            categorizerContentEl.classList.add('hidden'); // NEW
            menuScreenEl.classList.remove('hidden');
        }

        /**
         * เปลี่ยนไปยังหน้าจอเกมที่ระบุ
         * @param {'spelling'|'hangman'|'categorizer'} gameId 
         */
        window.showGameView = function(gameId) {
            menuScreenEl.classList.add('hidden');
            gameViewEl.classList.remove('hidden');
            spellingContentEl.classList.add('hidden');
            hangmanContentEl.classList.add('hidden');
            categorizerContentEl.classList.add('hidden'); // NEW

            if (gameId === 'spelling') {
                spellingContentEl.classList.remove('hidden');
                cleanupSpellingGameState();
                prepareFirstSpellingWordForStartScreen();
                window.loadSpellingHighScores();
            } else if (gameId === 'hangman') {
                hangmanContentEl.classList.remove('hidden');
                cleanupHangmanGameState();
                prepareHangmanGame();
                window.loadHangmanHighScores();
            } else if (gameId === 'categorizer') { // NEW
                categorizerContentEl.classList.remove('hidden');
                cleanupCategorizerGameState();
                prepareCategorizerGame();
                // Note: No high score display for Categorizer yet, but structure is ready.
            }
        }

        /**
         * รีเซ็ตสถานะเกมทั้งหมดและกลับไปที่หน้าจอเมนูหลัก
         */
        window.exitToMenu = function() {
            cleanupSpellingGameState();
            cleanupHangmanGameState();
            cleanupCategorizerGameState(); // NEW
            showMenu();
        }

        // --- ฟังก์ชันตรรกะทั่วไป ---

        /**
         * สลับตำแหน่งองค์ประกอบในอาร์เรย์
         */
        function shuffleArray(array) {
            for (let i = array.length - 1; i > 0; i--) {
                const j = Math.floor(Math.random() * (i + 1));
                [array[i], array[j]] = [array[j], array[i]];
            }
            return array;
        }

        // --- SPELLING GAME LOGIC (ย่อ) ---
        function segmentThaiWord(word) { /* ... (Logic remains the same) ... */
            const segments = [];
            let currentSegment = '';
            const newSegmentStartRegex = /^[ก-ฮ\s\dเแโใไ]/;
            for (const char of word) {
                if (newSegmentStartRegex.test(char)) {
                    if (currentSegment) {
                        segments.push(currentSegment);
                    }
                    currentSegment = char;
                } else {
                    currentSegment += char;
                }
            }
            if (currentSegment) {
                segments.push(currentSegment);
            }
            return segments.filter(s => s.trim() !== '');
        }
        function updateSpellingStartResetButtonText() { /* ... (Logic remains the same) ... */
            if (!spellingStartResetButtonEl) return;
            if (spellingGameState.isGameActive) {
                spellingStartResetButtonEl.textContent = 'รีเซ็ตเกม'; 
                spellingStartResetButtonEl.classList.remove('bg-emerald-500', 'hover:bg-emerald-600', 'active:bg-emerald-700');
                spellingStartResetButtonEl.classList.add('bg-gray-500', 'hover:bg-gray-600', 'active:bg-gray-700');
            } else {
                spellingStartResetButtonEl.textContent = 'เริ่มใหม่'; 
                spellingStartResetButtonEl.classList.remove('bg-gray-500', 'hover:bg-gray-600', 'active:bg-gray-700');
                spellingStartResetButtonEl.classList.add('bg-emerald-500', 'hover:bg-emerald-600', 'active:bg-emerald-700');
            }
        }
        window.handleSpellingStartResetClick = function() { /* ... (Logic remains the same) ... */
            if (spellingGameState.isGameActive) {
                cleanupSpellingGameState();
                prepareFirstSpellingWordForStartScreen();
                spellingMessageBoxEl.textContent = 'เกมถูกรีเซ็ต. กด "เริ่มใหม่" เพื่อเริ่มจับเวลาอีกครั้ง';
            } else {
                startSpellingGame();
            }
        }
        function startSpellingGame() { /* ... (Logic remains the same) ... */
            if (spellingGameState.isGameActive) return;
            spellingGameState.isGameActive = true;
            spellingPauseResumeButtonEl.disabled = false; 
            updateSpellingStartResetButtonText();
            startSpellingTimer();
        }
        function cleanupSpellingGameState() { /* ... (Logic remains the same) ... */
            stopSpellingTimer(); 
            spellingGameState.isGameActive = false;
            spellingGameState.isPaused = false;
            spellingGameState.totalTime = 0;
            spellingGameState.wordIndex = 0;
            spellingTimerEl.textContent = '00:00';
            spellingWordCounterEl.textContent = 'คำที่ 0 / 5';
            spellingTargetWordEl.innerHTML = '';
            spellingLetterTilesEl.innerHTML = '';
            spellingUndoButtonEl.disabled = true;
            spellingPauseResumeButtonEl.disabled = true;
            spellingPauseResumeButtonEl.textContent = 'หยุดเวลา';
            spellingPauseResumeButtonEl.classList.remove('bg-gray-500', 'hover:bg-gray-600', 'active:bg-gray-700');
            spellingPauseResumeButtonEl.classList.add('bg-red-500', 'hover:bg-red-600', 'active:bg-red-700');
            spellingGameState.currentWord = '';
            spellingGameState.targetLetters = [];
            spellingGameState.builtWord = [];
            spellingGameState.availableTiles = [];
            spellingGameState.targetBoxes = [];
            spellingGameState.placedTiles = [];
            updateSpellingStartResetButtonText();
        }
        function prepareFirstSpellingWordForStartScreen() { /* ... (Logic remains the same) ... */
            spellingGameState.wordIndex = 0;
            spellingGameState.currentWord = '';
            spellingGameState.targetLetters = [];
            spellingTargetWordEl.innerHTML = '';
            spellingLetterTilesEl.innerHTML = '';
            spellingWordCounterEl.textContent = `คำที่ 0 / ${SPELLING_WORDS_TO_PLAY}`;
            startNewSpellingWord(false); 
            spellingMessageBoxEl.textContent = `คำศัพท์แรกพร้อมแล้ว! กด "เริ่มใหม่" เพื่อเริ่มจับเวลา`;
        }
        function startSpellingTimer() { /* ... (Logic remains the same) ... */
            if (!spellingGameState.isGameActive || (!spellingGameState.isPaused && spellingGameState.timerInterval)) return;

            if (spellingGameState.timerInterval) {
                clearInterval(spellingGameState.timerInterval);
            }
            
            spellingGameState.lastTimeUpdate = Date.now();
            spellingGameState.isPaused = false;
            
            spellingPauseResumeButtonEl.textContent = 'หยุดเวลา';
            spellingPauseResumeButtonEl.classList.remove('bg-gray-500', 'hover:bg-gray-600', 'active:bg-gray-700');
            spellingPauseResumeButtonEl.classList.add('bg-red-500', 'hover:bg-red-600', 'active:bg-red-700');
            spellingMessageBoxEl.textContent = `สะกดคำศัพท์ ${spellingGameState.targetLetters.length} ส่วน`;

            function updateTimer() {
                if (!spellingGameState.isGameActive || spellingGameState.isPaused) return;

                const currentTime = Date.now();
                const segmentDuration = currentTime - spellingGameState.lastTimeUpdate;
                
                spellingGameState.totalTime += segmentDuration;
                spellingGameState.lastTimeUpdate = currentTime; 
                spellingTimerEl.textContent = formatTime(spellingGameState.totalTime);
            }

            spellingGameState.timerInterval = setInterval(updateTimer, 50); 
            updateTimer();
            
            enableSpellingTileInteraction();
        }
        function pauseSpellingTimer() { /* ... (Logic remains the same) ... */
            if (!spellingGameState.isGameActive || spellingGameState.isPaused) return;

            clearInterval(spellingGameState.timerInterval);
            spellingGameState.timerInterval = null;
            
            spellingGameState.totalTime += Date.now() - spellingGameState.lastTimeUpdate;

            spellingGameState.isPaused = true;

            spellingPauseResumeButtonEl.textContent = 'เล่นต่อ';
            spellingPauseResumeButtonEl.classList.remove('bg-red-500', 'hover:bg-red-600', 'active:bg-red-700');
            spellingPauseResumeButtonEl.classList.add('bg-gray-500', 'hover:bg-gray-600', 'active:bg-gray-700');
            spellingMessageBoxEl.textContent = 'เกมหยุดชั่วคราว...';
            
            spellingUndoButtonEl.disabled = true;
            spellingGameState.availableTiles.forEach(tile => tile.onclick = null);
        }
        window.pauseResumeSpellingGame = function() { /* ... (Logic remains the same) ... */
            if (!spellingGameState.isGameActive) return;

            if (spellingGameState.isPaused) {
                startSpellingTimer(); 
            } else {
                pauseSpellingTimer(); 
            }
        }
        function stopSpellingTimer() { /* ... (Logic remains the same) ... */
            clearInterval(spellingGameState.timerInterval);
            spellingGameState.timerInterval = null;
            spellingGameState.isPaused = false;
        }
        function startNewSpellingWord(updateWordIndex = true) { /* ... (Logic remains the same) ... */
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
            const numDistractors = Math.min(MAX_DISTRACTORS, 
                DISTRACTOR_POOL.length - tileLetters.length > 0 ? MAX_DISTRACTORS : 0);
            
            const usedLetters = new Set(tileLetters);
            let addedDistractors = 0;
            while (addedDistractors < numDistractors) {
                const randomLetter = DISTRACTOR_POOL[Math.floor(Math.random() * DISTRACTOR_POOL.length)];
                if (!usedLetters.has(randomLetter)) {
                    tileLetters.push(randomLetter);
                    addedDistractors++;
                    usedLetters.add(randomLetter);
                }
            }

            spellingGameState.availableTiles = [];
            const enableClick = spellingGameState.isGameActive && !spellingGameState.isPaused;
            shuffleArray(tileLetters).forEach(letter => {
                const tile = createSpellingTileElement(letter, enableClick); 
                spellingLetterTilesEl.appendChild(tile);
                spellingGameState.availableTiles.push(tile);
            });
            
            spellingUndoButtonEl.disabled = true;
            if (spellingGameState.isGameActive) {
                spellingMessageBoxEl.textContent = `สะกดคำศัพท์ ${spellingGameState.targetLetters.length} ส่วน`;
            }
        }
        function createSpellingTileElement(letter, enableClick) { /* ... (Logic remains the same) ... */
            const tile = document.createElement('div');
            tile.className = 'tile p-1'; 
            tile.textContent = letter;
            tile.dataset.letter = letter;
            
            tile.onclick = enableClick ? () => handleSpellingTileClick(tile) : null;

            return tile;
        }
        function enableSpellingTileInteraction() { /* ... (Logic remains the same) ... */
             spellingGameState.availableTiles.forEach(tile => {
                if (!tile.classList.contains('disabled')) {
                    if (tile.onclick === null) {
                        tile.onclick = () => handleSpellingTileClick(tile);
                    }
                }
            });
            if (spellingGameState.placedTiles.length > 0) {
                 spellingUndoButtonEl.disabled = false;
            }
        }
        function handleSpellingTileClick(tileEl) { /* ... (Logic remains the same) ... */
            if (!spellingGameState.isGameActive || spellingGameState.isPaused) return;

            const targetIndex = spellingGameState.builtWord.length;
            if (targetIndex >= spellingGameState.targetBoxes.length) {
                return;
            }

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
        window.undoLastSpellingTile = function() { /* ... (Logic remains the same) ... */
            if (!spellingGameState.isGameActive || spellingGameState.placedTiles.length === 0 || spellingGameState.isPaused) return;

            const lastPlacement = spellingGameState.placedTiles.pop();
            const { tileEl, boxEl } = lastPlacement;

            boxEl.textContent = '';
            boxEl.classList.remove('filled');
            
            tileEl.classList.remove('disabled');
            if (spellingGameState.isGameActive && !spellingGameState.isPaused) {
                tileEl.onclick = () => handleSpellingTileClick(tileEl);
            }
            
            spellingGameState.builtWord.pop();

            if (spellingGameState.placedTiles.length === 0) {
                spellingUndoButtonEl.disabled = true;
            }
        }
        function checkSpellingWord() { /* ... (Logic remains the same) ... */
            if (spellingGameState.isPaused) return;

            const currentGuess = spellingGameState.builtWord.join('');
            
            if (currentGuess === spellingGameState.currentWord) {
                spellingMessageBoxEl.textContent = `ถูกต้อง! คำว่า: ${spellingGameState.currentWord}`;
                
                pauseSpellingTimer();
                
                spellingGameState.targetBoxes.forEach(box => {
                    box.classList.add('correct-animation');
                });
                
                spellingGameState.availableTiles.forEach(tile => tile.onclick = null);
                spellingUndoButtonEl.disabled = true;
                spellingPauseResumeButtonEl.disabled = true;


                setTimeout(() => {
                    spellingGameState.targetBoxes.forEach(box => box.classList.remove('correct-animation'));
                    spellingPauseResumeButtonEl.disabled = false;
                    startNewSpellingWord(true);
                    startSpellingTimer(); 
                }, 1500);

            } else {
                spellingMessageBoxEl.textContent = 'ผิด! ลองใหม่';
                
                spellingTargetWordEl.classList.add('incorrect-shake');
                setTimeout(() => {
                    spellingTargetWordEl.classList.remove('incorrect-shake');
                }, 300);
            }
        }
        function endSpellingGame() { /* ... (Logic remains the same) ... */
            cleanupSpellingGameState();
            
            const finalTime = spellingGameState.totalTime;
            spellingMessageBoxEl.innerHTML = `<p class="text-2xl font-extrabold text-blue-700">เวลาทั้งหมด: ${formatTime(finalTime)}</p>`;
            
            saveSpellingGameResult(finalTime);

            setTimeout(showMenu, 3000);
        }
        
        // --- HANGMAN GAME LOGIC (ย่อ) ---
        const HANGMAN_FIGURES = [ /* ... (Figures remain the same) ... */ ];
        function cleanupHangmanGameState() { /* ... (Logic remains the same) ... */
            hangmanGameState.isGameActive = false;
            hangmanGameState.word = '';
            hangmanGameState.hint = '';
            hangmanGameState.guessedLetters = new Set();
            hangmanGameState.livesLeft = hangmanGameState.maxLives;
            hangmanGameState.attempts = 0;

            hangmanFigureEl.textContent = HANGMAN_FIGURES[0];
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
        function prepareHangmanGame() { /* ... (Logic remains the same) ... */
            cleanupHangmanGameState();
            
            const selectedWordObj = HANGMAN_WORDS[Math.floor(Math.random() * HANGMAN_WORDS.length)];
            
            hangmanGameState.word = selectedWordObj.word;
            hangmanGameState.hint = selectedWordObj.hint; 

            hangmanHintDisplayEl.textContent = hangmanGameState.hint;
            updateHangmanDisplay(); 
        }
        window.startHangmanGame = function() { /* ... (Logic remains the same) ... */
            prepareHangmanGame();
            
            hangmanGameState.isGameActive = true;
            
            hangmanGuessInputEl.disabled = false;
            hangmanGuessButtonEl.disabled = false;
            
            hangmanMessageBoxEl.textContent = `เริ่มเกม! คำมี ${hangmanGameState.word.length} ตัวอักษร ใช้คำใบ้เพื่อช่วย`;
            hangmanGuessInputEl.focus();
        }
        function updateWordDisplay() { /* ... (Logic remains the same) ... */
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
        function updateHangmanDisplay() { /* ... (Logic remains the same) ... */
            const figureIndex = hangmanGameState.maxLives - hangmanGameState.livesLeft;
            hangmanFigureEl.textContent = HANGMAN_FIGURES[figureIndex];
            
            updateWordDisplay();

            hangmanGuessedLettersEl.textContent = Array.from(hangmanGameState.guessedLetters)
                .filter(char => !hangmanGameState.word.includes(char)) 
                .sort()
                .join(' ');
            
            hangmanAttemptsEl.textContent = hangmanGameState.attempts;
            hangmanLivesLeftEl.textContent = hangmanGameState.livesLeft;
        }
        window.handleHangmanGuess = function() { /* ... (Logic remains the same) ... */
            if (!hangmanGameState.isGameActive) return;

            const guess = hangmanGuessInputEl.value.trim().toLowerCase();
            hangmanGuessInputEl.value = '';

            if (guess.length !== 1 || !THAI_ALPHABET.includes(guess)) {
                hangmanMessageBoxEl.textContent = 'กรุณาทายตัวอักษรไทยเพียงตัวเดียวเท่านั้น';
                hangmanGuessInputEl.focus();
                return;
            }

            if (hangmanGameState.guessedLetters.has(guess)) {
                hangmanMessageBoxEl.textContent = `คุณเคยทาย "${guess}" ไปแล้ว!`;
                hangmanGuessInputEl.focus();
                return;
            }
            
            hangmanGameState.attempts++;
            hangmanGameState.guessedLetters.add(guess);

            if (hangmanGameState.word.includes(guess)) {
                hangmanMessageBoxEl.textContent = `ถูกต้อง! ตัวอักษร "${guess}" มีอยู่ในคำ`;
            } else {
                hangmanGameState.livesLeft--;
                hangmanMessageBoxEl.textContent = `ผิด! ตัวอักษร "${guess}" ไม่มีในคำ เหลือ ${hangmanGameState.livesLeft} ครั้ง`;
                hangmanFigureEl.classList.add('incorrect-shake');
                setTimeout(() => hangmanFigureEl.classList.remove('incorrect-shake'), 300);
            }
            
            updateHangmanDisplay();
            checkHangmanWinLoss();
            hangmanGuessInputEl.focus();
        }
        function checkHangmanWinLoss() { /* ... (Logic remains the same) ... */
            if (!hangmanGameState.isGameActive) return;

            const isWordGuessed = [...hangmanGameState.word].every(char => hangmanGameState.guessedLetters.has(char));
            
            if (isWordGuessed) {
                hangmanGameState.isGameActive = false;
                hangmanMessageBoxEl.innerHTML = `<span class="text-green-600 font-extrabold">คุณชนะแล้ว! คำคือ "${hangmanGameState.word}" (ทายไป ${hangmanGameState.attempts} ครั้ง)</span>`;
                hangmanFigureEl.classList.add('correct-animation');
                hangmanGuessInputEl.disabled = true;
                hangmanGuessButtonEl.disabled = true;
                
                saveHangmanGameResult(hangmanGameState.attempts);

            } else if (hangmanGameState.livesLeft <= 0) {
                hangmanGameState.isGameActive = false;
                hangmanMessageBoxEl.innerHTML = `<span class="text-red-600 font-extrabold">คุณแพ้แล้ว! คำที่ถูกต้องคือ "${hangmanGameState.word}" (ทายไป ${hangmanGameState.attempts} ครั้ง)</span>`;
                updateHangmanDisplay();
                hangmanGuessInputEl.disabled = true;
                hangmanGuessButtonEl.disabled = true;
            }
        }
        
        // --- NEW CATEGORIZER GAME LOGIC ---

        function getAllConsonants() {
            return [
                ...THAI_CONSONANTS_CATEGORIES.high,
                ...THAI_CONSONANTS_CATEGORIES.middle,
                ...THAI_CONSONANTS_CATEGORIES.low
            ];
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
            categorizerConsonantEl.textContent = '?';
            categorizerMessageBoxEl.textContent = 'กด "เริ่มใหม่" เพื่อเริ่มจัดหมวดหมู่ 20 ข้อ';
            
            categorizerPauseButtonEl.disabled = true;
            categorizerPauseButtonEl.textContent = 'หยุดเวลา';
            categorizerPauseButtonEl.classList.remove('bg-gray-500', 'hover:bg-gray-600', 'active:bg-gray-700');
            categorizerPauseButtonEl.classList.add('bg-red-500', 'hover:bg-red-600', 'active:bg-red-700');
            
            categorizerButtons.forEach(btn => btn.disabled = true);
        }

        function prepareCategorizerGame() {
            cleanupCategorizerGameState();
            // FIX: เปลี่ยน Placeholder จาก 'A B C' เป็น 'ก ข ค'
            categorizerConsonantEl.textContent = 'ก ข ค'; 
            categorizerMessageBoxEl.textContent = 'ทดสอบความรู้ไตรยางศ์ของคุณ! มีทั้งหมด 20 ข้อ';
        }

        window.handleCategorizerStartReset = function() {
            if (categorizerGameState.isGameActive) {
                // Reset (Stop currently running game and prepare for new start)
                cleanupCategorizerGameState();
                prepareCategorizerGame();
            } else {
                // Start New Game
                startCategorizerGame();
            }
        }

        function startCategorizerGame() {
            cleanupCategorizerGameState(); // Ensure a clean start

            categorizerGameState.isGameActive = true;
            categorizerGameState.questionCount = 0;
            categorizerGameState.score = 0;
            
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
            const randomIndex = Math.floor(Math.random() * allConsonants.length);
            const newConsonant = allConsonants[randomIndex];

            categorizerGameState.currentConsonant = newConsonant;
            categorizerGameState.correctCategory = getCategory(newConsonant);

            categorizerConsonantEl.textContent = newConsonant;
            categorizerMessageBoxEl.textContent = 'จัดหมวดหมู่พยัญชนะนี้';
        }

        window.handleCategorizerGuess = function(guess) {
            if (!categorizerGameState.isGameActive || categorizerGameState.isPaused) return;

            let isCorrect = guess === categorizerGameState.correctCategory;

            if (isCorrect) {
                categorizerGameState.score++;
                categorizerMessageBoxEl.textContent = '✅ ถูกต้อง!';
            } else {
                categorizerMessageBoxEl.textContent = `❌ ผิด! คำตอบคือ อักษร${categorizerGameState.correctCategory === 'high' ? 'สูง' : categorizerGameState.correctCategory === 'middle' ? 'กลาง' : 'ต่ำ'}`;
            }

            // Quick visual feedback
            const targetBtn = document.getElementById(`btn-${guess}`);
            // Note: classList[1] might not be reliable for fetching the original color class
            // To ensure we retain the original color class:
            const originalColorMap = {
                high: 'bg-yellow-400',
                middle: 'bg-green-400',
                low: 'bg-blue-400'
            };
            const originalColorClass = originalColorMap[guess];
            
            // Remove original color before adding feedback color
            if (originalColorClass) {
                 targetBtn.classList.remove(originalColorClass);
            }
            
            targetBtn.classList.add(isCorrect ? 'bg-green-600' : 'bg-red-600');
            targetBtn.classList.add('text-white');

            setTimeout(() => {
                targetBtn.classList.remove('bg-green-600', 'bg-red-600', 'text-white');
                // Restore original color
                if (originalColorClass) {
                    targetBtn.classList.add(originalColorClass);
                }
                nextCategorizerQuestion();
            }, 500);
        }

        function startCategorizerTimer() {
            if (!categorizerGameState.isGameActive || (!categorizerGameState.isPaused && categorizerGameState.timerInterval)) return;

            if (categorizerGameState.timerInterval) {
                clearInterval(categorizerGameState.timerInterval);
            }
            
            categorizerGameState.lastTimeUpdate = Date.now();
            categorizerGameState.isPaused = false;
            
            categorizerPauseButtonEl.textContent = 'หยุดเวลา';
            categorizerPauseButtonEl.classList.remove('bg-gray-500', 'hover:bg-gray-600', 'active:bg-gray-700');
            categorizerPauseButtonEl.classList.add('bg-red-500', 'hover:bg-red-600', 'active:bg-red-700');
            categorizerButtons.forEach(btn => btn.disabled = false);


            function updateTimer() {
                if (!categorizerGameState.isGameActive || categorizerGameState.isPaused) return;
                const currentTime = Date.now();
                const segmentDuration = currentTime - categorizerGameState.lastTimeUpdate;
                categorizerGameState.totalTime += segmentDuration;
                categorizerGameState.lastTimeUpdate = currentTime; 
                categorizerTimerEl.textContent = formatTime(categorizerGameState.totalTime);
            }

            categorizerGameState.timerInterval = setInterval(updateTimer, 50); 
            updateTimer();
        }

        function pauseCategorizerTimer() {
            if (!categorizerGameState.isGameActive || categorizerGameState.isPaused) return;

            clearInterval(categorizerGameState.timerInterval);
            categorizerGameState.timerInterval = null;
            
            categorizerGameState.totalTime += Date.now() - categorizerGameState.lastTimeUpdate;
            categorizerGameState.isPaused = true;

            categorizerPauseButtonEl.textContent = 'เล่นต่อ';
            categorizerPauseButtonEl.classList.remove('bg-red-500', 'hover:bg-red-600', 'active:bg-red-700');
            categorizerPauseButtonEl.classList.add('bg-gray-500', 'hover:bg-gray-600', 'active:bg-gray-700');
            categorizerMessageBoxEl.textContent = 'เกมหยุดชั่วคราว...';
            
            categorizerButtons.forEach(btn => btn.disabled = true);
        }

        window.pauseResumeCategorizer = function() {
            if (!categorizerGameState.isGameActive) return;

            if (categorizerGameState.isPaused) {
                startCategorizerTimer();
            } else {
                pauseCategorizerTimer();
            }
        }

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

            categorizerMessageBoxEl.innerHTML = `<span class="text-2xl font-extrabold text-blue-700">จบเกม!</span><br/>คะแนน ${finalScore}/${CATEGORIZER_QUESTIONS} ในเวลา ${formatTime(finalTime)}`;
            
            saveCategorizerGameResult(finalTime, finalScore);
        }

        // --- INITIALIZATION ---
        
        /**
         * เริ่มต้นเมื่อโหลดหน้า
         */
        window.onload = function() {
            window.initializeApp();
            
            // กำหนดให้ Hangman input รับการกด Enter
            hangmanGuessInputEl.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    handleHangmanGuess();
                    e.preventDefault(); 
                }
            });
            
            // ตรวจสอบว่าควรแสดงเมนูหลักหรือไม่ (ควรเป็นค่าเริ่มต้น)
            showMenu();
        };

    </script>
</body>
</html>