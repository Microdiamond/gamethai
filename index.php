<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="index.css">
    <title>Game Thai - Home</title>
</head>
<style>
    @import url('https://fonts.googleapis.com/css2?family=Sarabun:wght@400;700&display=swap');

    body {
        margin: 0;
        padding: 0;
        font-family: 'Sarabun', Arial, sans-serif;
        display: flex; /* Use flex for sidebar layout */
    }

    /* Reuse periphery style if needed, or just let body flex handle it */
    .periphery {
        display: flex;
        width: 100%;
        min-height: 100vh;
    }

    #content {
        flex-grow: 1;
        padding: 40px;
        background-color: #f3f4f6; /* Gray background for content area */
    }
    
    .menu-card {
        transition: transform 0.3s, box-shadow 0.3s;
    }
    .menu-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }
</style>

<body>
    <div class="periphery">
        <?php include 'sidebar.php'; ?>

        <div id="content">
            <h1 class="text-4xl font-extrabold text-center mb-10 text-gray-800">ยินดีต้อนรับสู่ GameThai</h1>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 justify-items-center">
                
                <!-- Random Vocab Card -->
                <a href="randomvocab.php" class="menu-card bg-white p-6 rounded-xl shadow-lg text-center border-t-8 border-indigo-500 w-full max-w-sm block hover:no-underline">
                    <div class="text-indigo-500 mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">คำศัพท์สุ่ม</h2>
                    <p class="text-gray-600">เรียนรู้คำศัพท์ใหม่ๆ แบบสุ่ม</p>
                </a>

                <!-- Thai Alphabet Board Card -->
                <a href="board.php" class="menu-card bg-white p-6 rounded-xl shadow-lg text-center border-t-8 border-purple-500 w-full max-w-sm block hover:no-underline">
                    <div class="text-purple-500 mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">กระดาน ก-ฮ</h2>
                    <p class="text-gray-600">ฝึกท่องจำพยัญชนะไทย ก-ฮ</p>
                </a>

                <!-- Spelling Game Card -->
                <a href="game_spelling.php" class="menu-card bg-white p-6 rounded-xl shadow-lg text-center border-t-8 border-emerald-500 w-full max-w-sm block hover:no-underline">
                    <div class="text-emerald-500 mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">เกมสะกดคำ</h2>
                    <p class="text-gray-600">เรียงตัวอักษรเพื่อสร้างคำให้ถูกต้อง</p>
                </a>

                <!-- Hangman Game Card -->
                <a href="game_hangman.php" class="menu-card bg-white p-6 rounded-xl shadow-lg text-center border-t-8 border-red-500 w-full max-w-sm block hover:no-underline">
                    <div class="text-red-500 mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">เกม Hangman</h2>
                    <p class="text-gray-600">ทายคำศัพท์ให้ถูกก่อนเวลาหมด</p>
                </a>

                <!-- Categorizer Game Card -->
                <a href="game_categorizer.php" class="menu-card bg-white p-6 rounded-xl shadow-lg text-center border-t-8 border-orange-500 w-full max-w-sm block hover:no-underline">
                    <div class="text-orange-500 mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">เกมไตรยางศ์</h2>
                    <p class="text-gray-600">จัดหมวดหมู่พยัญชนะ สูง กลาง ต่ำ</p>
                </a>

            </div>
        </div>
    </div>
</body>

</html>