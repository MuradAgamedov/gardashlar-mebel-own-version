<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class LinkExtractorController extends Controller
{
    /**
     * Extract all links from a given URL recursively with language support.
     */
    public function extractLinks(Request $request)
    {
        $baseUrl = $request->get('url', 'https://gardashlar.com/');
        $maxDepth = $request->get('max_depth', 6); // Increased depth for better crawling
        // Get active languages from Language model
        $activeLanguages = Language::getActiveLanguages();
        $languages = $activeLanguages->isEmpty() ? ['az', 'en'] : $activeLanguages->pluck('lang_code')->toArray();
        
        try {
            $allLinks = [];
            
            // First, try to get sitemap URLs
            $sitemapLinks = $this->getSitemapLinks($baseUrl);
            $allLinks = array_merge($allLinks, $sitemapLinks);
            
            // Then crawl each language
            foreach ($languages as $lang) {
                $url = rtrim($baseUrl, '/') . '/' . $lang;
                $langLinks = $this->crawlWebsite($url, $maxDepth);
                $allLinks = array_merge($allLinks, $langLinks);
            }
            
            // Finally, get API-based product URLs if available
            $apiLinks = $this->getApiProductLinks($baseUrl);
            $allLinks = array_merge($allLinks, $apiLinks);
            
            // Remove duplicates and return
            $uniqueLinks = array_values(array_unique($allLinks));
            
            return response()->json([
                'success' => true,
                'base_url' => $baseUrl,
                'languages' => $languages,
                'max_depth' => $maxDepth,
                'total_links' => count($uniqueLinks),
                'links' => $uniqueLinks
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Recursively crawl website and extract all links.
     */
    private function crawlWebsite($startUrl, $maxDepth, $currentDepth = 0, &$visited = [])
    {
        if ($currentDepth >= $maxDepth) {
            return [];
        }
        
        $allLinks = [];
        $parsedUrl = parse_url($startUrl);
        $baseUrl = $parsedUrl['scheme'] . '://' . $parsedUrl['host'];
        
        // Get links from current page
        $pageLinks = $this->extractLinksFromPage($startUrl, $baseUrl);
        
        foreach ($pageLinks as $link) {
            // Skip if already visited
            if (in_array($link, $visited)) {
                continue;
            }
            
            $visited[] = $link;
            $allLinks[] = $link;
            
            // Only crawl internal links (same domain)
            if (strpos($link, $baseUrl) === 0) {
                // Recursively crawl this page
                $subLinks = $this->crawlWebsite($link, $maxDepth, $currentDepth + 1, $visited);
                $allLinks = array_merge($allLinks, $subLinks);
            }
        }
        
        return array_values(array_unique($allLinks));
    }
    
    /**
     * Extract links from a single page with pagination support.
     */
    private function extractLinksFromPage($url, $baseUrl, &$visited = [])
    {
        try {
            $allLinks = [];
            $currentPage = $url;
            
            // Crawl all paginated pages using queue approach
            $pagesToCrawl = [$url];
            $crawledPages = [];
            
            while (!empty($pagesToCrawl)) {
                $currentPage = array_shift($pagesToCrawl);
                
                if (in_array($currentPage, $visited) || in_array($currentPage, $crawledPages)) {
                    continue;
                }
                
                $visited[] = $currentPage;
                $crawledPages[] = $currentPage;
                
                $response = Http::withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/120 Safari/537.36',
                    'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
                    'Accept-Language' => 'az,en-US;q=0.9,en;q=0.8',
                    'Accept-Encoding' => 'gzip, deflate, br',
                    'Connection' => 'keep-alive',
                    'Upgrade-Insecure-Requests' => '1'
                ])->timeout(20)->get($currentPage);
                
                if (!$response->successful()) {
                    continue;
                }
                
                $html = $response->body();
                
                // Parse HTML and extract links
                libxml_use_internal_errors(true);
                
                $dom = new \DOMDocument();
                $dom->loadHTML($html);
                
                $pageLinks = [];
                
                foreach ($dom->getElementsByTagName('a') as $tag) {
                    $href = $tag->getAttribute('href');
                    
                    if (!empty($href)) {
                        // Convert relative URLs to absolute URLs
                        if (strpos($href, 'http') !== 0) {
                            // Handle different types of relative URLs
                            if ($href[0] === '/') {
                                $fullUrl = $baseUrl . $href;
                            } else {
                                $fullUrl = $baseUrl . '/' . $href;
                            }
                            $pageLinks[] = $fullUrl;
                        } else {
                            // Already absolute URL
                            $pageLinks[] = $href;
                        }
                    }
                }
                
                // Filter out excluded domains
                $excludedDomains = [
                    'facebook.com', 'twitter.com', 'instagram.com', 
                    'linkedin.com', 'youtube.com', 'tiktok.com',
                    'pinterest.com', 'reddit.com', 'whatsapp.com',
                    'telegram.org', 'vk.com', 'ok.ru',
                    'mailto:', 'tel:', 'cdn-cgi/l/email-protection',
                    'maps.app.goo.gl', 'google.com/maps', 'maps.google.com',
                    'yandex.com/maps', 'yandex.az/maps', 'bing.com/maps'
                ];
                
                $filteredLinks = [];
                foreach ($pageLinks as $link) {
                    $isExcluded = false;
                    
                    foreach ($excludedDomains as $domain) {
                        if (strpos($link, $domain) !== false) {
                            $isExcluded = true;
                            break;
                        }
                    }
                    
                    if (!$isExcluded) {
                        $filteredLinks[] = $link;
                    }
                }
                
                $allLinks = array_merge($allLinks, $filteredLinks);
                
                // Find all pagination links from current page
                $paginationLinks = $this->findPaginationLinks($dom, $baseUrl, $currentPage);
                
                // Add new pagination links to queue
                foreach ($paginationLinks as $paginationLink) {
                    if (!in_array($paginationLink, $visited) && !in_array($paginationLink, $crawledPages) && !in_array($paginationLink, $pagesToCrawl)) {
                        $pagesToCrawl[] = $paginationLink;
                    }
                }
            }
            
            return array_values(array_unique($allLinks));
            
        } catch (\Exception $e) {
            return [];
        }
    }
    
    /**
     * Find all pagination links from the page.
     */
    private function findPaginationLinks($dom, $baseUrl, $currentUrl)
    {
        $paginationLinks = [];
        $xpath = new \DOMXPath($dom);
        
        // Method 1: Find pagination container first
        $paginationContainers = [
            '//div[contains(@class, "pagination")]',
            '//ul[contains(@class, "pagination")]',
            '//nav[contains(@class, "pagination")]',
            '//div[contains(@class, "paging")]',
            '//ul[contains(@class, "paging")]',
            '//nav[contains(@class, "paging")]',
            '//div[contains(@class, "pager")]',
            '//ul[contains(@class, "pager")]',
        ];
        
        $allLinks = [];
        
        // Try to find pagination container
        foreach ($paginationContainers as $containerSelector) {
            $containers = $xpath->query($containerSelector);
            if ($containers->length > 0) {
                // Get all links from pagination container
                $links = $xpath->query('.//a', $containers->item(0));
                foreach ($links as $link) {
                    $href = $link->getAttribute('href');
                    if (!empty($href)) {
                        $allLinks[] = $href;
                    }
                }
                break; // Found pagination container, no need to check others
            }
        }
        
        // Method 2: If no container found, look for common pagination patterns
        if (empty($allLinks)) {
            $paginationPatterns = [
                '//a[contains(@class, "page")]',
                '//a[contains(@class, "pagination")]', 
                '//a[contains(@href, "page")]',
                '//a[contains(@href, "?page")]',
                '//a[contains(@href, "&page")]',
                '//a[contains(text(), "1")]', '//a[contains(text(), "2")]', '//a[contains(text(), "3")]', 
                '//a[contains(text(), "4")]', '//a[contains(text(), "5")]', '//a[contains(text(), "6")]',
                '//a[contains(text(), "7")]', '//a[contains(text(), "8")]', '//a[contains(text(), "9")]',
                '//a[contains(text(), "10")]',
                '//a[contains(text(), "Sonraki")]', '//a[contains(text(), "Next")]',
                '//a[contains(text(), "sonraki")]', '//a[contains(text(), "next")]',
                '//a[@rel="next"]',
                '//a[contains(@class, "next")]',
            ];
            
            foreach ($paginationPatterns as $pattern) {
                $links = $xpath->query($pattern);
                foreach ($links as $link) {
                    $href = $link->getAttribute('href');
                    if (!empty($href)) {
                        $allLinks[] = $href;
                    }
                }
            }
        }
        
        // Convert to absolute URLs and filter
        foreach ($allLinks as $href) {
            if (empty($href)) continue;
            
            // Skip non-pagination links
            if (strpos($href, '#') === 0 || strpos($href, 'javascript:') === 0) {
                continue;
            }
            
            // Convert to absolute URL
            if (strpos($href, 'http') !== 0) {
                if ($href[0] === '/') {
                    $fullUrl = $baseUrl . $href;
                } else {
                    $fullUrl = $baseUrl . '/' . $href;
                }
            } else {
                $fullUrl = $href;
            }
            
            // Only include links from the same domain and different from current
            if (strpos($fullUrl, $baseUrl) === 0 && $fullUrl !== $currentUrl) {
                $paginationLinks[] = $fullUrl;
            }
        }
        
        return array_values(array_unique($paginationLinks));
    }
    
    /**
     * Get URLs from sitemap.xml
     */
    private function getSitemapLinks($baseUrl)
    {
        $sitemapUrls = [
            rtrim($baseUrl, '/') . '/sitemap.xml',
            rtrim($baseUrl, '/') . '/sitemap_index.xml',
            rtrim($baseUrl, '/') . '/sitemap/sitemap.xml'
        ];
        
        $links = [];
        
        foreach ($sitemapUrls as $sitemapUrl) {
            try {
                $response = Http::withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/120 Safari/537.36',
                    'Accept' => 'application/xml,text/xml'
                ])->timeout(20)->get($sitemapUrl);
                
                if ($response->successful()) {
                    $xml = $response->body();
                    $dom = new \DOMDocument();
                    $dom->loadXML($xml);
                    
                    $urlElements = $dom->getElementsByTagName('url');
                    foreach ($urlElements as $urlElement) {
                        $locElements = $urlElement->getElementsByTagName('loc');
                        if ($locElements->length > 0) {
                            $url = $locElements->item(0)->nodeValue;
                            if (!empty($url) && strpos($url, $baseUrl) === 0) {
                                $links[] = $url;
                            }
                        }
                    }
                    
                    // Also check for sitemap index
                    $sitemapElements = $dom->getElementsByTagName('sitemap');
                    foreach ($sitemapElements as $sitemapElement) {
                        $locElements = $sitemapElement->getElementsByTagName('loc');
                        if ($locElements->length > 0) {
                            $childSitemapUrl = $locElements->item(0)->nodeValue;
                            $childLinks = $this->parseChildSitemap($childSitemapUrl, $baseUrl);
                            $links = array_merge($links, $childLinks);
                        }
                    }
                    
                    break; // Found a valid sitemap
                }
            } catch (\Exception $e) {
                continue; // Try next sitemap URL
            }
        }
        
        return array_values(array_unique($links));
    }
    
    /**
     * Parse child sitemap
     */
    private function parseChildSitemap($sitemapUrl, $baseUrl)
    {
        try {
            $response = Http::withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/120 Safari/537.36',
                'Accept' => 'application/xml,text/xml'
            ])->timeout(20)->get($sitemapUrl);
            
            if ($response->successful()) {
                $xml = $response->body();
                $dom = new \DOMDocument();
                $dom->loadXML($xml);
                
                $links = [];
                $urlElements = $dom->getElementsByTagName('url');
                foreach ($urlElements as $urlElement) {
                    $locElements = $urlElement->getElementsByTagName('loc');
                    if ($locElements->length > 0) {
                        $url = $locElements->item(0)->nodeValue;
                        if (!empty($url) && strpos($url, $baseUrl) === 0) {
                            $links[] = $url;
                        }
                    }
                }
                
                return $links;
            }
        } catch (\Exception $e) {
            return [];
        }
        
        return [];
    }
    
    /**
     * Get product URLs from API endpoints
     */
    private function getApiProductLinks($baseUrl)
    {
        $links = [];
        
        // Try common API endpoints for products
        $apiEndpoints = [
            '/api/products',
            '/api/v1/products', 
            '/api/products/all',
            '/api/catalog/products',
            '/api/mebel/products'
        ];
        
        foreach ($apiEndpoints as $endpoint) {
            try {
                $apiUrl = rtrim($baseUrl, '/') . $endpoint;
                $response = Http::withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/120 Safari/537.36',
                    'Accept' => 'application/json',
                    'Accept-Language' => 'az,en-US;q=0.9,en;q=0.8'
                ])->timeout(20)->get($apiUrl);
                
                if ($response->successful()) {
                    $data = $response->json();
                    
                    // Handle different API response structures
                    if (isset($data['data'])) {
                        $products = $data['data'];
                    } elseif (isset($data['products'])) {
                        $products = $data['products'];
                    } elseif (is_array($data)) {
                        $products = $data;
                    } else {
                        $products = [];
                    }
                    
                    foreach ($products as $product) {
                        if (isset($product['url']) || isset($product['slug'])) {
                            $productUrl = isset($product['url']) ? $product['url'] : 
                                         (rtrim($baseUrl, '/') . '/az/product/' . $product['slug']);
                            
                            if (strpos($productUrl, 'http') !== 0) {
                                $productUrl = rtrim($baseUrl, '/') . '/' . ltrim($productUrl, '/');
                            }
                            
                            if (strpos($productUrl, $baseUrl) === 0) {
                                $links[] = $productUrl;
                            }
                        }
                    }
                }
            } catch (\Exception $e) {
                continue; // Try next endpoint
            }
        }
        
        return array_values(array_unique($links));
    }
}
