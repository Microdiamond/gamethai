<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="index.css">
    <title>Game Thai</title>
</head>

<body>
    <!-- <audio id="myAudio">
        <source src="mouse-click-1.mp3" type="audio/mpeg">
    </audio> -->
    <div class="container">
        <div class="wrap">
            <div class="row showBox">
            </div>
            <div class="row">
                <!-- <button onclick="ranz()">Random</button> -->
                <div class="col">
                    <img onclick="ranz()" id="pawBtn" src="image/paw.png" alt="Button Text">
                    <div>กดเพื่อสุ่ม</div>
                </div>
            </div>
        </div>
    </div>

    <!-- <button id="startRecording">Start Recording</button>
    <button id="stopRecording" disabled>Stop Recording</button>

    <audio id="audioPlayer" controls></audio> -->
    <script>
        // var clickBox = document.getElementById("clickBox");
        function fetchData(newDivElement) {
            var xhr = new XMLHttpRequest();
            // Configure the request (GET method, URL, asynchronous)
            var htmlContent = '';
            var i = '';
            xhr.open('GET', 'getData.php', true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    // Parse the JSON response
                    var data = JSON.parse(xhr.responseText);
                    var randomNumber = Math.floor(Math.random() * data.length);
                    data = data[randomNumber].info_name
                    // console.log('data',data);
                    var rowElement = document.querySelector('.showBox');
                    var soundFileName = `${data}.mp3`;
                    // htmlContent += `<div class="col boxS tAlpa" onclick="playSound(myAudio${randomNumber})"><div>${data}</div></div>`;
                    // htmlContent += `<audio id="myAudio${randomNumber}"><source src="sound/${soundFileName}" type="audio/mpeg"></audio>`
                    newDivElement.innerHTML = `<div>${data}</div>`;
                    newDivElement.innerHTML += `<audio id="myAudio${randomNumber}"><source id="audioSourcemyAudio${randomNumber}" src="sound/${soundFileName}" type="audio/mpeg"></audio>`;
                    // rowElement.innerHTML = htmlContent
                    rowElement.appendChild(newDivElement);
                    var audioId = `myAudio${randomNumber}`;
                    // Add onclick event to the newDivElement
                    newDivElement.onclick = function() {
                        playSound(audioId);
                    };
                    // checkFileExists(`sound/${soundFileName}`)
                }
            };
            // Send the request
            xhr.send();
        }

        function ranz() {
            // Generate a random number between 1 and 4
            var randomNumber = Math.floor(Math.random() * 4) + 1;
            var rowElement = document.querySelector('.showBox');
            var boxS = document.getElementsByClassName('boxS');
            // Remove elements using a while loop
            while (boxS.length > 0) {
                rowElement.removeChild(boxS[0]);
            }

            for (let index = 0; index < randomNumber; index++) {
                // Get a reference to the element with the class "row"
                var rowElement = document.querySelector('.showBox');

                // Create a new div element
                var newDivElement = document.createElement('div');
                // Add three classes to the new element
                newDivElement.classList.add('col', 'boxS');
                fetchData(newDivElement)
                // newDivElement.textContent = 'ภาษาไทย';
                rowElement.appendChild(newDivElement);
            }
        }

        function playSound(audioId) {
            var audio = document.getElementById(audioId);
            var audioSource = document.getElementById(`audioSource${audioId}`);
            if (audio) {
                audio.play();
            }
            fetch(audioSource.src, {
                    method: 'HEAD'
                })
                .then(response => {
                    if (response.status == 404) {
                        console.log("HTTP 404 Not Found");
                    } else {
                        console.log("ไฟล์พร้อมใช้งาน");
                    }
                })
                .catch(error => console.error("เกิดข้อผิดพลาดในการเชื่อมต่อ: " + error));
        }
        ranz()
    </script>
</body>

</html>