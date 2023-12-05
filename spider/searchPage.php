<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Web Spider</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: black;
            background: black;
            margin: 0;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        .container {
            text-align: center;
        }

        h1 {
            color: blueviolet;
            font-size: 40px;
            text-shadow: 1px 1px 1px  #f4f4f4 ;
        }

        input {
            padding: 10px;
            margin: 10px;
            width: 300px;
            font-size: 16px;
            box-shadow:1px 1px 10px #f4f4f4;
        }

        button {
            padding: 10px 20px;
            font-size: 16px;
            background-color: #6ccb6f;
            color: white;
            border: none;
            cursor: pointer;
            box-shadow:1px 1px 10px #f4f4f4;
        }

        button:hover {
            background-color: #275c29;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>SpiderSearch</h1>
        <input type="text" id="urlInput" placeholder="Enter to spider....">
        <button onclick="startSpider()">search!</button>
    </div>

    <script>
        function startSpider() {
            const url = document.getElementById('urlInput').value;

            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'spider.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onload = function() {
                if (xhr.status === 200) {
                    const response = JSON.parse(xhr.responseText);
                    document.getElementById('result').innerHTML = response.result;
                } else {
                    console.error('Error:', xhr.statusText);
                }
            };
            xhr.send('url=' + url);
        }
    </script>
</body>
</html>
