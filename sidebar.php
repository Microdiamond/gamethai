<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<style>
    #sidebar {
        width: 250px;
        /* height: 100%; */
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
</style>

<body>
    <div id="sidebar">
        <a href="/index.php" onclick="">Home</a>
        <a href="/randomvocab.php" onclick="goto('rdv')">randomvocab</a>
        <a href="/board.php" onclick="goto('rdv')">ThaiAlphabet</a>
        <hr style="margin: 10px 0; border: 0; border-top: 1px solid #555;">
        <span style="padding-left: 15px; color: #888; font-size: 14px; font-weight: bold;">Mini Games</span>
        <a href="/game_spelling.php">เกมสะกดคำ</a>
        <a href="/game_hangman.php">เกม Hangman</a>
        <a href="/game_categorizer.php">เกมไตรยางศ์</a>
        <hr style="margin: 10px 0; border: 0; border-top: 1px solid #555;">
        <a href="/manage_data.php" style="color: #fbbf24;">จัดการข้อมูล</a>
    </div>
</body>

</html>