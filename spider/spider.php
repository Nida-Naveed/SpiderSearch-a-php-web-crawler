<?php

class WebSpider {
    private $urlQueue;
    private $crawledUrls;
    private $depthLimit;
    private $contentIndex;
  
    public function __construct($seedUrl, $depthLimit) {
      $this->urlQueue = new SplPriorityQueue();
      $this->crawledUrls = [];
      $this->depthLimit = $depthLimit;
  
      $this->urlQueue->insert($seedUrl, 0); // Wikipedia as seed URL
    }
  
    public function crawl() {
      while (!$this->urlQueue->isEmpty()) {
        $queueItem = $this->urlQueue->extract();

        if ($queueItem === null) {
            continue; // Skip if the queue item is null
        }

        $url = $queueItem->getValue();
        $depth = $queueItem->getPriority();
      
          // Add additional checks to ensure $url is set and not empty before using it
        if (!isset($url) || empty($url) || $depth > $this->depthLimit || in_array($url, $this->crawledUrls)) {
          continue;
        }
  
        $this->crawledUrls[] = $url;
  
        $htmlContent = $this->fetchPage($url);
        if (!$htmlContent) {
          continue;
        }

        // Add a check before error_log
         if (!empty($url) && !empty($htmlContent)) {
          error_log("URL: $url");
          error_log("HTML Content: $htmlContent");
        }

        $extractedInfo = $this->parseHTML($htmlContent, $url);

        $this->indexContent($extractedInfo);
  
        $extractedUrls = $this->extractURLs($htmlContent);
        
        foreach ($extractedUrls as $extractedUrl) {
          if (!in_array($extractedUrl, $this->crawledUrls) && !in_array($extractedUrl, $this->urlQueue)) {
              $this->urlQueue->insert($extractedUrl, $depth + 1);
          }
      }
      
      }
    }
  
    private function fetchPage($url) {
      try {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  
        $htmlContent = curl_exec($ch);
        if ($htmlContent === false) {
          return null;
        }
  
        $responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($responseCode != 200) {
          return null;
        }
  
        curl_close($ch);
        return $htmlContent;
      } catch (Exception $e) {
        return null;
      }
    }
  
    private function parseHTML($htmlContent, $url) {
      $dom = new DOMDocument();
      @$dom->loadHTML($htmlContent);
  
      $extractedInfo = [];
      
      // Set the 'url' key in the $extractedInfo array
      $extractedInfo['url'] = $url;

      $titleTag = $dom->getElementsByTagName('title')->item(0);
      if ($titleTag) {
        $extractedInfo['title'] = $titleTag->nodeValue;
      }
  
      $descriptionMetaTag = $dom->getElementsByTagName('meta')->item(0);
      if ($descriptionMetaTag && $descriptionMetaTag->getAttribute('name') === 'description') {
        $extractedInfo['description'] = $descriptionMetaTag->getAttribute('content');
      }
  
      return $extractedInfo;
    }
      
    private function indexContent($extractedInfo) {
        // Connect to the MySQL database
        // Create connection
        $conn = new mysqli('localhost', 'root', 'umac2894', 'SpiderSearchData');
      
        // Prepare the SQL query to insert the extracted information
        $sql = "INSERT INTO crawled_pages (url, title, description) VALUES (?, ?, ?)";
      
        // Prepare the statement and bind the extracted information
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('sss', $extractedInfo['url'], $extractedInfo['title'], $extractedInfo['description']);
      
        // Execute the query to store the extracted information
        $stmt->execute();
      
        // Close the statement and database connection
        $stmt->close();
        $conn->close();
      }
      
  
    private function extractURLs($htmlContent) {
      $dom = new DOMDocument();
      @$dom->loadHTML($htmlContent);
  
      $xpath = new DOMXPath($dom);
      $extractedUrls = [];
  
      $hrefNodes = $xpath->query('//a[@href]');
      foreach ($hrefNodes as $node) {
        $href = $node->getAttribute('href');
        if (filter_var($href, FILTER_VALIDATE_URL) && strpos($href, 'wikipedia.org') !== false) {
          $extractedUrls[] = $href;
        }
      }
  
      return $extractedUrls;
    }
  }

$seedUrl = $_POST['url']; // Get the URL from the POST request
$depthLimit = 2; // Set the crawling depth limit

$spider = new WebSpider($seedUrl, $depthLimit);
$spider->crawl();

$result = array(
    'result' => 'Crawling completed.'
);

echo json_encode($result);
