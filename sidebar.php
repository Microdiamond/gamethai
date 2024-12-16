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
        <a href="/gamethai/" onclick="">Home</a>
        <a href="/gamethai/randomvocab" onclick="goto('rdv')">randomvocab</a>
        <a href="/gamethai/board" onclick="goto('rdv')">ThaiAlphabet</a>
    </div>
</body>

</html>