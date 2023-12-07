<?php

class WebCrawler {
    // Queue of URLs to be crawled
    private $urlQueue = [];

    // List of visited URLs
    private $visitedUrls = [];

    // Depth limit for crawling
    private $depthLimit = 3;

    private $concurrencyLimit = 5;

    // Extracted data from crawled pages
    private $extractedData = [];

    public function __construct($initialUrl) {
        // Initialize the URL queue with the seed URL
        $this->urlQueue[] = $initialUrl;
    }

    public function crawl() {
        // Initialize multi-cURL handle
        $multiCurlHandle = curl_multi_init();
        
        // Array to store cURL handles
        $curlHandles = [];

        // Main crawling loop
        while (!empty($this->urlQueue)) {
            // Limit the number of simultaneous requests (adjust as needed)
            while (count($curlHandles) < $this->concurrencyLimit && !empty($this->urlQueue)) {
                // Get the next URL from the queue
                $currentUrl = array_shift($this->urlQueue);

                // Skip URLs that shouldn't be visited
                if (!$this->isUrlVisitable($currentUrl)) {
                    continue;
                }

                // Initialize a new cURL handle for the current URL
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $currentUrl);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

                // Add more cURL options as needed

                // Add the cURL handle to the multi-cURL handle
                curl_multi_add_handle($multiCurlHandle, $ch);
                $curlHandles[$currentUrl] = $ch;
            }

            // Execute the multi-cURL requests
            $running = null;
            do {
                curl_multi_exec($multiCurlHandle, $running);
                curl_multi_select($multiCurlHandle);
            } while ($running > 0);

            // Process the completed requests
            foreach ($curlHandles as $url => $ch) {
                // Get the HTML content of the page
                $htmlContent = curl_multi_getcontent($ch);

                // Process the page content and extract data
                $data = $this->processPageContent($url, $htmlContent);

                // Mark the URL as visited
                $this->visitedUrls[] = $url;

                // Enqueue links from the page if depth limit allows
                if ($this->depthLimit > 0) {
                    $this->enqueueLinks($htmlContent);
                }

                // Decrease the depth limit
                $this->depthLimit--;

                // Store the extracted data
                $this->extractedData[] = $data;

                // Remove the cURL handle
                curl_multi_remove_handle($multiCurlHandle, $ch);
                curl_close($ch);

                unset($curlHandles[$url]);
            }

            // Respectful crawling delay
            usleep(1000000); // 1 second delay
        }

        // Close the multi-cURL handle
        curl_multi_close($multiCurlHandle);

        // Return the extracted data
        return $this->extractedData;
    }

    private function isUrlVisitable($url) {
        $parsedUrl = parse_url($url);
    
        if (!empty($parsedUrl['host'])) {
            $robotsTxtUrl = $parsedUrl['scheme'] . '://' . $parsedUrl['host'] . '/robots.txt';
    
            // Fetch and parse the robots.txt file
            $robotsTxtContent = $this->fetchPageContent($robotsTxtUrl);
    
            if ($robotsTxtContent !== false) {
                try {
                    // Check if the user-agent is allowed to crawl the URL
                    $userAgent = '*'; // Assume the user-agent is '*'
    
                    // can adjust this based on requirements
                    if (preg_match('/User-agent: (.*)/i', $robotsTxtContent, $userAgentMatch)) {
                        $userAgent = trim($userAgentMatch[1]);
                    }
    
                    // Check if the URL is allowed for the specified user-agent
                    if (preg_match('/Disallow: (.*)/i', $robotsTxtContent, $disallowMatch)) {
                        $disallowedPaths = explode("\n", $disallowMatch[1]);
                        foreach ($disallowedPaths as $disallowedPath) {
                            $disallowedPath = trim($disallowedPath);
                            if ($disallowedPath !== '' && strpos($parsedUrl['path'], $disallowedPath) === 0) {
                                return false; // URL is disallowed
                            }
                        }
                    }
    
                    return true; // Default to allowing the URL if no specific rules are found
                } catch (Exception $e) {
                    // Handle any exceptions that might occur during parsing
                    // Log the error or perform additional error handling as needed
                    error_log("Error parsing robots.txt: " . $e->getMessage());
                    return true; // Default to allowing the URL if an error occurs
                }
            } else {
                // Log or handle the case where fetching robots.txt fails
                error_log("Error fetching robots.txt from $robotsTxtUrl");
            }
        }
    
        return true; // Default to allowing the URL if there is no host or if fetching the robots.txt fails
    }

    private function fetchPageContent($url) {
        // Initialize cURL session
        $curl = curl_init();
    
        // Set cURL options
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true); // Follow redirects
    
        // can add more cURL options here, such as setting headers, timeouts, etc.
    
        // Execute cURL session
        $htmlContent = curl_exec($curl);
    
        // Check for cURL errors
        if (curl_errno($curl)) {
            // Handle cURL errors
            // Log the error or perform additional error handling as needed
            error_log("cURL Error: " . curl_error($curl));
    
            // Return false to indicate failure
            return false;
        }
    
        // Close cURL session
        curl_close($curl);
    
        return $htmlContent;
    }

    private function processPageContent($url, $htmlContent) {
        // Initialize DOMDocument
        $dom = new DOMDocument;
        @$dom->loadHTML($htmlContent);

        // Extract title
        $titleNode = $dom->getElementsByTagName('title')->item(0);
        $pageTitle = ($titleNode !== null) ? $titleNode->nodeValue : '';

        // Extract meta description
        $metaDescription = '';
        $metaNodes = $dom->getElementsByTagName('meta');
        foreach ($metaNodes as $metaNode) {
            if ($metaNode->getAttribute('name') == 'description') {
                $metaDescription = $metaNode->getAttribute('content');
                break;
            }
        }

        // Return the extracted data
        return [
            'url' => $url,
            'pageTitle' => $pageTitle,
            'metaDescription' => $metaDescription,
        ];
    }

    private function enqueueLinks($htmlContent) {
        // Use DOMDocument for more robust HTML parsing
        $dom = new DOMDocument;
        @$dom->loadHTML($htmlContent);

        // Find all anchor (a) tags
        $anchorNodes = $dom->getElementsByTagName('a');

        foreach ($anchorNodes as $anchorNode) {
            $link = $anchorNode->getAttribute('href');
            $absoluteUrl = $this->makeAbsoluteUrl($link);

            if ($absoluteUrl && !in_array($absoluteUrl, $this->visitedUrls) && !in_array($absoluteUrl, $this->urlQueue)) {
                $this->urlQueue[] = $absoluteUrl;
            }
        }
    }

    private function makeAbsoluteUrl($url, $baseUrl = null) {
        $parsedUrl = parse_url($url);

        if (empty($parsedUrl['scheme']) || empty($parsedUrl['host'])) {
            // Handle relative URLs
            if ($baseUrl) {
                $parsedBaseUrl = parse_url($baseUrl);

                // Ensure the base URL has a scheme and host
                if (!empty($parsedBaseUrl['scheme']) && !empty($parsedBaseUrl['host'])) {
                    $absoluteUrl = $parsedBaseUrl['scheme'] . '://' . $parsedBaseUrl['host'];

                    // Handle paths and queries
                    if (!empty($parsedBaseUrl['path'])) {
                        $absoluteUrl .= rtrim($parsedBaseUrl['path'], '/') . '/';
                    }

                    if (!empty($parsedBaseUrl['query'])) {
                        $absoluteUrl .= '?' . $parsedBaseUrl['query'];
                    }

                    // Handle relative paths
                    if (!empty($parsedUrl['path'])) {
                        $absoluteUrl .= ltrim($parsedUrl['path'], '/');
                    }

                    // Handle fragments
                    if (!empty($parsedUrl['fragment'])) {
                        $absoluteUrl .= '#' . $parsedUrl['fragment'];
                    }

                    return $absoluteUrl;
                }
            }

            // Unable to make an absolute URL
            return null;
        }

        // URL is already absolute
        return $url;
    }
}
