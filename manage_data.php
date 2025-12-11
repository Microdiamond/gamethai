<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการข้อมูล (Data Management) - Game Thai</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Sarabun:wght@400;700&display=swap');
        
        body {
            font-family: 'Sarabun', sans-serif;
            background-color: #f3f4f6;
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
        }
    </style>
</head>
<body>
    <div class="periphery">
        <?php include 'sidebar.php'; ?>
        
        <div id="content">
            <div class="max-w-6xl mx-auto bg-white rounded-lg shadow-lg p-6">
                <h1 class="text-3xl font-extrabold text-gray-800 mb-6 border-b pb-4">จัดการข้อมูลคะแนน (Data Management)</h1>

                <!-- Tabs -->
                <div class="flex border-b mb-6">
                    <button id="tab-spelling" class="px-6 py-2 text-lg font-bold text-gray-600 border-b-2 border-transparent hover:text-emerald-500 hover:border-emerald-300 focus:outline-none focus:text-emerald-600 focus:border-emerald-500 transition-colors" onclick="switchTab('spelling')">
                        เกมสะกดคำ
                    </button>
                    <button id="tab-hangman" class="px-6 py-2 text-lg font-bold text-gray-600 border-b-2 border-transparent hover:text-red-500 hover:border-red-300 focus:outline-none focus:text-red-600 focus:border-red-500 transition-colors" onclick="switchTab('hangman')">
                        เกม Hangman
                    </button>
                    <button id="tab-categorizer" class="px-6 py-2 text-lg font-bold text-gray-600 border-b-2 border-transparent hover:text-orange-500 hover:border-orange-300 focus:outline-none focus:text-orange-600 focus:border-orange-500 transition-colors" onclick="switchTab('categorizer')">
                        เกมไตรยางศ์
                    </button>
                </div>

                <!-- Content Area -->
                <div id="data-container" class="overflow-x-auto">
                    <p class="text-center text-gray-500 py-8">กรุณาเลือกแท็บเพื่อดูข้อมูล</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentGame = 'spelling';

        function formatTime(ms) {
            if (!ms && ms !== 0) return '-';
            const totalSeconds = Math.floor(ms / 1000);
            const minutes = Math.floor(totalSeconds / 60);
            const seconds = totalSeconds % 60;
            return `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
        }

        function switchTab(game) {
            currentGame = game;
            
            // Update Tab Styles
            ['spelling', 'hangman', 'categorizer'].forEach(g => {
                const tab = document.getElementById(`tab-${g}`);
                if(g === game) {
                    tab.classList.remove('text-gray-600', 'border-transparent');
                    if(g === 'spelling') tab.classList.add('text-emerald-600', 'border-emerald-500');
                    if(g === 'hangman') tab.classList.add('text-red-600', 'border-red-500');
                    if(g === 'categorizer') tab.classList.add('text-orange-600', 'border-orange-500');
                } else {
                    tab.classList.add('text-gray-600', 'border-transparent');
                    tab.classList.remove('text-emerald-600', 'border-emerald-500', 'text-red-600', 'border-red-500', 'text-orange-600', 'border-orange-500');
                }
            });

            loadData();
        }

        async function loadData() {
            const container = document.getElementById('data-container');
            container.innerHTML = '<p class="text-center text-gray-500 py-8">กำลังโหลดข้อมูล...</p>';

            try {
                const res = await fetch(`score_api.php?action=load_all&game=${currentGame}`);
                const data = await res.json();

                if (data.error) {
                    container.innerHTML = `<p class="text-center text-red-500 font-bold">เกิดข้อผิดพลาด: ${data.error}</p>`;
                    return;
                }

                if (data.length === 0) {
                    container.innerHTML = '<p class="text-center text-gray-500 py-8">ไม่มีข้อมูลคะแนน</p>';
                    return;
                }

                renderTable(data);
            } catch (e) {
                console.error(e);
                container.innerHTML = '<p class="text-center text-red-500 font-bold">ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้</p>';
            }
        }

        function renderTable(data) {
            let html = `
            <table class="min-w-full bg-white border border-gray-200">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="py-3 px-4 border-b text-left text-sm font-semibold text-gray-600">ID</th>
                        <th class="py-3 px-4 border-b text-left text-sm font-semibold text-gray-600">ชื่อผู้เล่น</th>
                        <th class="py-3 px-4 border-b text-right text-sm font-semibold text-gray-600">คะแนน/เวลา</th>
                        <th class="py-3 px-4 border-b text-right text-sm font-semibold text-gray-600">วันที่</th>
                        <th class="py-3 px-4 border-b text-center text-sm font-semibold text-gray-600">จัดการ</th>
                    </tr>
                </thead>
                <tbody>`;
            
            data.forEach(row => {
                let scoreDisplay = '';
                if (currentGame === 'spelling') {
                    scoreDisplay = formatTime(row.score);
                } else if (currentGame === 'hangman') {
                    scoreDisplay = `${row.score} ครั้ง`;
                } else if (currentGame === 'categorizer') {
                    scoreDisplay = `${row.score} คะแนน (${formatTime(row.time_ms)})`;
                }

                html += `
                <tr class="hover:bg-gray-50">
                    <td class="py-3 px-4 border-b text-sm text-gray-700">${row.id}</td>
                    <td class="py-3 px-4 border-b text-sm font-bold text-gray-900">${row.player_name}</td>
                    <td class="py-3 px-4 border-b text-sm text-right font-mono text-blue-600">${scoreDisplay}</td>
                    <td class="py-3 px-4 border-b text-sm text-right text-gray-500">${row.created_at}</td>
                    <td class="py-3 px-4 border-b text-center">
                        <button onclick="deleteScore(${row.id})" class="text-red-500 hover:text-red-700 font-bold text-sm bg-red-50 hover:bg-red-100 py-1 px-3 rounded transition-colors">ลบ</button>
                    </td>
                </tr>`;
            });

            html += '</tbody></table>';
            document.getElementById('data-container').innerHTML = html;
        }

        async function deleteScore(id) {
            if (!confirm(`คุณต้องการลบข้อมูล ID ${id} ใช่หรือไม่?`)) return;

            try {
                const formData = new FormData();
                formData.append('action', 'delete');
                formData.append('game', currentGame);
                formData.append('id', id);

                const res = await fetch('score_api.php', { method: 'POST', body: formData });
                const result = await res.json();

                if (result.success) {
                    loadData(); // Reload table
                } else {
                    alert(`ลบไม่สำเร็จ: ${result.error}`);
                }
            } catch (e) {
                console.error(e);
                alert('เกิดข้อผิดพลาดในการลบข้อมูล');
            }
        }

        // Initial Load
        switchTab('spelling');
    </script>
</body>
</html>
