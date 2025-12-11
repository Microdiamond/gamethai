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
        height: 100vh;
        overflow: hidden;
        display: flex;
        flex-direction: column;
    }

    .periphery {
        display: flex;
        width: 100%;
        height: 100%;
        overflow: hidden;
    }

    #content {
        transition: margin-left 0.3s;
        width: 100%;
        height: 100%;
        display: flex;
        justify-content: center;
        align-items: center;
        background: linear-gradient(to right, #ff7e5f, #feb47b); /* Ensure background covers content area */
    }

    .container {
        width: 100%;
        height: 100%;
        box-sizing: border-box;
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 2vh;
    }

    .wrap {
        width: 100%;
        height: 100%;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .asx {
        display: grid;
        grid-template-columns: repeat(8, 1fr);
        grid-template-rows: repeat(6, 1fr);
        gap: 0.5vh;
        width: 95%;
        height: 95%;
        max-width: 1400px;
        justify-items: stretch;
        align-content: stretch;
    }

    .asx .boxS {
        width: auto !important;
        height: auto !important;
        margin: 0 !important;
        font-size: min(8vh, 8vw) !important; /* Increased font size */
        font-weight: bold;
        line-height: 1; /* Keep it tight */
        display: flex;
        justify-content: center;
        align-items: center;
        border-radius: 8px;
        background: rgba(255, 255, 255, 0.25);
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        cursor: pointer;
        transition: transform 0.2s, background 0.2s;
    }

    /* Mobile/Portrait responsiveness */
    @media (max-width: 768px) or (max-aspect-ratio: 1/1) {
        .asx {
             grid-template-columns: repeat(5, 1fr);
             grid-template-rows: repeat(10, 1fr);
        }
    }

    .asx .boxS:hover {
        transform: scale(1.05);
        background: rgba(255, 255, 255, 0.5);
        z-index: 10;
    }

    .tAlpa {
        width: auto !important;
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
        z-index: 100;
        padding: 5px 10px;
        border-radius: 5px;
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