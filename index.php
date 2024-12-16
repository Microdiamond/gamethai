<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="index.css">
    <title>Document</title>
</head>
<style>
    body {
        margin: 0;
        padding: 0;
        font-family: Arial, sans-serif;
        justify-content: unset;
    }

    #sidebar {
        width: 250px;
        height: 100%;
        background-color: #333;
        display: block;
        /* position: fixed; */
        /* left: -250px; */
        transition: all 0.3s;
    }

    #sidebar a {
        padding: 15px;
        text-decoration: none;
        font-size: 18px;
        color: white;
        display: block;
        transition: color 0.3s;
    }

    #sidebar a:hover {
        color: #90caf9;
    }

    #content {
        margin-left: 250px;
        transition: margin-left 0.3s;
        padding: 15px;
    }

    #toggle-btn {
        position: fixed;
        left: 10px;
        top: 10px;
        cursor: pointer;
        font-size: 20px;
        color: white;
        background-color: #333;
        border: none;
    }
</style>

<body>
    <!-- <div class="containerHome"> -->
    <div id="sidebar">
        <a href="/gamethai/index.php" onclick="">Home</a>
        <a href="/gamethai/randomvocab" onclick="goto('rdv')">randomvocab</a>
        <a href="/gamethai/board" onclick="goto('rdv')">ThaiAlphabet</a>
    </div>

    <div id="content">
        <h1>Welcome to My PHP Web App!</h1>
        <!-- Your page content goes here -->
    </div>
    <!-- </div> -->

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const content = document.getElementById('content');
            const toggleBtn = document.getElementById('toggle-btn');
            console.log(sidebar.style);
            if (sidebar.style.width === '50px') {
                // sidebar.style.left = '-250px';
                // content.style.marginLeft = '0';
                // toggleBtn.style.left = '10px';
                sidebar.style.width = '250'
            } else {
                sidebar.style.width = '50'
            }
        }

        function goto(param) {
            console.log(param)
        }
    </script>

</body>

</html>