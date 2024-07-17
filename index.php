<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Display Camera</title>
    <link rel="stylesheet" href="{{ url_for('static', filename='main.css') }}">
</head>
<body>
    <div>
    <a href="auth.php" class="btn">LOGIN/LOGOUT</a>
    </div>
    <div>
    <a href="setting.php" class="btn">Setting</a>
    </div>
    <h1>Display Webcam Stream</h1>
    <div id="container">
        <video autoplay="true" id="videoElement"></video>
    </div>
    <div id="emotionResult"></div>
    <button id="resetButton" onclick="resetDetection()" style="display:none;">Reset</button>
    <script>
        let video = document.querySelector("#videoElement");
        let emotionResult = document.getElementById('emotionResult');
        let captureInterval;

        if (navigator.mediaDevices.getUserMedia) {
            navigator.mediaDevices.getUserMedia({ video: true })
                .then(function (stream) {
                    video.srcObject = stream;
                    captureInterval = setInterval(captureImage, 500);
                })
                .catch(function (error) {
                    console.log("Something Wrong!!");
                });
        } else {
            console.log("getUserMedia not supported");
        }

        function captureImage() {
            let canvas = document.createElement('canvas');
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            let context = canvas.getContext('2d');
            context.drawImage(video, 0, 0, canvas.width, canvas.height);
            let dataURL = canvas.toDataURL('image/jpeg');
            
            fetch('/predict', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ image: dataURL })
            })
            .then(response => response.json())
            .then(data => {
                emotionResult.innerHTML = `Detected Emotion: ${data.emotion}`;
                if (data.done) {
                    clearInterval(captureInterval);
                    document.getElementById('resetButton').style.display = 'block';
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }

        function resetDetection() {
            clearInterval(captureInterval);
            emotionResult.innerHTML = '';
            document.getElementById('resetButton').style.display = 'none';
            fetch('/reset', { method: 'POST' }) // Send a request to reset emotion counts on the server
                .then(response => response.json())
                .then(data => {
                    if (data.reset) {
                        captureInterval = setInterval(captureImage, 500);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }
    </script>
</body>
</html>
