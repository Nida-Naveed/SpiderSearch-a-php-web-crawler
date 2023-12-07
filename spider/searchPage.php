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
        <button onclick="initiateCrawling()">search!</button>
    </div>

    <script>
    // Function to initiate crawling
    function initiateCrawling() {
        // Get the seed URL from the input field
        var userProvidedUrl = document.getElementById("urlInput").value;

        // Create an XMLHttpRequest object to send data to the server
        var request = new XMLHttpRequest();

        // Set up the request
        request.open("POST", "crawl.php", true);
        request.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

        // Define a callback function to handle the response from the server
        request.onreadystatechange = function () {
            // Check if the request is complete and successful
            if (request.readyState == 4 && request.status == 200) {
                // Display an alert indicating that crawling is complete
                alert("Crawling complete. Check the server logs for details.");
            }
        };

        // Encode and send the seed URL as a parameter to the server
        request.send("userProvidedUrl=" + encodeURIComponent(userProvidedUrl));
    }
</script>

</body>
</html>
