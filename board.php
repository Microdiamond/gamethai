<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="index.css">
    <title>Thai Alphabet Board</title>
</head>
<style>
    body {
        margin: 0;
        padding: 0;
        font-family: Arial, sans-serif;
        justify-content: unset;
    }

    #content {
        transition: margin-left 0.3s;
        width: 100%;
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

    .periphery{
        display: flex;
        width: 100%;
    }
</style>

<body>
    <div class = "periphery">
        <?php include 'sidebar.php'; ?>
        <div id="content">
            <!-- <div class="container">
                <div class="wrap">
                    <div class="row showBox fWrap">
                        <div class="asx"> -->
            <!-- <div class="col boxS tAlpa">
                                1
                            </div> -->
            <!-- </div>
                        </div>
                    </div>
                </div> -->

            <div class="container">
                <div class="wrap">
                    <div class="asx">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function fetchAndDisplayJson() {
            var rowElement = document.querySelector('.asx');
            var xhr = new XMLHttpRequest();
            // Configure the request (GET method, URL, asynchronous)
            xhr.open('GET', 'alpbet.json', true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    // Parse the JSON response
                    var data = JSON.parse(xhr.responseText);
                    var htmlContent = '';
                    let i = 0
                    data.forEach(item => {
                        i++
                        htmlContent += `<div class="col boxS tAlpa" onclick="playSound('myAudio${i}')"><div>${item.letter}</div></div>`;
                        htmlContent += `<audio id="myAudio${i}"><source src="sound/${item.letter}.mp3" type="audio/mpeg"></audio>`
                    });
                    rowElement.innerHTML = htmlContent
                }
            };
            // Send the request
            xhr.send();
        }

        function playSound(audioId) {
            var audio = document.getElementById(audioId);
            audio.play();
        }
        // haddleclick()
        fetchAndDisplayJson()
    </script>
</body>

</html>