<?php

// Allow CORS (Cross-Origin Resource Sharing)
header('Access-Control-Allow-Origin: http://localhost/demofiles/spider/spider/searchPage.php'); 
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle pre-flight CORS requests (OPTIONS method)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Check if a seed URL is provided through POST request
if (isset($_POST['userProvidedUrl'])) {
    // Retrieve the seed URL from the POST data
    $userProvidedUrl = $_POST['userProvidedUrl'];

    // Include the WebCrawler class and initiate crawling
    require_once('WebCrawler.php'); // Adjust the path based on your project structure

    // Create an instance of the WebCrawler class
    $webCrawler = new WebCrawler($userProvidedUrl);

    // Initiate crawling
    $webCrawler->crawl();
} else {
    // Handle the case when the seed URL is not provided
    echo "Seed URL is missing.";
}

?>
