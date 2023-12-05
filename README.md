# SpiderSearch
SpiderSearch systematically browses the internet, following hyperlinks and collecting information from web pages, after collecting the information, it is stored in flat files, allowing for subsequent searches based on string matching.
#Overview
#Web Crawler Functionality:
The web crawler systematically navigates the internet, starting from a seed URL, by maintaining a queue of URLs to crawl.

#Data Collection and Parsing:
It retrieves HTML content from URLs, parses the HTML to extract relevant information like titles and meta descriptions, and extracts additional URLs from the content.

#Depth Limit and Search Module:
The crawler is designed with a depth limit to control the crawling depth. It includes a search module to find specified strings within crawled content and logs or displays matching URLs.

#Robots.txt Compliance and Bonus Features:
The implementation respects robots.txt rules, handles errors, and optionally includes advanced features like concurrency, URL filtering, persistent data storage, and advanced search capabilities.

SpiderSearch UI:
![image](https://github.com/Nida-Naveed/SpiderSearch-a-php-web-crawler/assets/142655903/d9de9e05-cf19-4d9f-9bf7-90908c9c52c7)
