<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Web Spider</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            background: url('search_bg.jpg');
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
        #result{
            color: rgb(36, 91, 201);
            font-size: large;
            text-shadow: .5px .5px .5px  #f4f4f4 ;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>SpiderSearch</h1>
        <input type="text" id="urlInput" placeholder="Enter to spider....">
        <button onclick="startSpider()">search!</button>
        <div id="result"><b>ready to spider?</b></div>
    </div>

    <script>
        function startSpider() {
            // Replace this with your web spider logic
            const url = document.getElementById('urlInput').value;
    

            // Display results in the 'result' div
            document.getElementById('result').innerHTML = `<p><b>Spidering "${url}"...</b></p>`;
        }
    </script>
</body>
</html>
