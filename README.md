# SpiderSearch :spider:
SpiderSearch systematically browses the internet, following hyperlinks and collecting information from web pages, after collecting the information, it is stored in flat files, allowing for subsequent searches based on string matching.

# Overview 
- The web crawler systematically navigates the internet, starting from a seed URL, by maintaining a queue of URLs to crawl.
- It retrieves HTML content from URLs, parses the HTML to extract relevant information like titles and meta descriptions, and extracts additional URLs from the content.
- The crawler is designed with a depth limit to control the crawling depth. It includes a search module to find specified strings within crawled content and logs or displays matching URLs.
- The implementation respects robots.txt rules, handles errors, and optionally includes advanced features like concurrency, URL filtering, persistent data storage, and advanced search capabilities.

# Setting Up SpiderSearch:
## Setup Instructions
### Prerequisites

1. **XAMPP Installation:**
   - Download and install XAMPP from [https://www.apachefriends.org/index.html](https://www.apachefriends.org/index.html).
   - Ensure Apache server and MySQL services are running.

2. **Clone the Repository:**
   ```bash
   git clone https://github.com/Nida-Naveed/SpiderSearch-a-php-web-crawler.git
   cd SpiderSearch
### SpiderSearch UI:
#### Search Engine:
![image](https://github.com/Nida-Naveed/SpiderSearch-a-php-web-crawler/assets/142655903/d9de9e05-cf19-4d9f-9bf7-90908c9c52c7)


