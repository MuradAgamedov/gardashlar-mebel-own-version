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
        $maxDepth = $request->get('max_depth', 3); // Prevent infinite crawling
        // Get active languages from Language model
        $activeLanguages = Language::getActiveLanguages();
        $languages = $activeLanguages->isEmpty() ? ['az', 'en'] : $activeLanguages->pluck('lang_code')->toArray();
        
        try {
            $allLinks = [];
            
            foreach ($languages as $lang) {
                $url = rtrim($baseUrl, '/') . '/' . $lang;
                $langLinks = $this->crawlWebsite($url, $maxDepth);
                $allLinks = array_merge($allLinks, $langLinks);
            }
            
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
            
            // Crawl all paginated pages
            while ($currentPage && !in_array($currentPage, $visited)) {
                $visited[] = $currentPage;
                
                $response = Http::get($currentPage);
                
                if (!$response->successful()) {
                    break;
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
                
                // Find next pagination link
                $nextPage = $this->findNextPaginationLink($dom, $baseUrl);
                
                if ($nextPage && $nextPage !== $currentPage) {
                    $currentPage = $nextPage;
                } else {
                    break;
                }
            }
            
            return array_values(array_unique($allLinks));
            
        } catch (\Exception $e) {
            return [];
        }
    }
    
    /**
     * Find the next pagination link from the page.
     */
    private function findNextPaginationLink($dom, $baseUrl)
    {
        $paginationSelectors = [
            // Common pagination patterns
            'a[rel="next"]',
            'a[class*="next"]',
            'a[class*="Next"]',
            'a[contains(text(), "Next")]',
            'a[contains(text(), "Sonraki")]',
            'a[contains(text(), "next")]',
            'a[contains(text(), "sonraki")]',
            'a[class*="page-next"]',
            'a[class*="pagination-next"]',
            // Arrow indicators
            'a[class*="arrow"]',
            'a[class*="►"]',
            'a[class*="→"]',
        ];
        
        foreach ($paginationSelectors as $selector) {
            $xpath = new \DOMXPath($dom);
            
            // Convert CSS selector to XPath (simplified)
            if (strpos($selector, 'rel="next"') !== false) {
                $nodes = $xpath->query('//a[@rel="next"]');
            } elseif (strpos($selector, 'class*="') !== false) {
                $className = str_replace(['a[class*="', '"]', 'contains(text(), '], '', $selector);
                $nodes = $xpath->query('//a[contains(@class, "' . $className . '")]');
            } elseif (strpos($selector, 'contains(text(),') !== false) {
                $text = str_replace(['a[contains(text(), "', '")]'], '', $selector);
                $nodes = $xpath->query('//a[contains(text(), "' . $text . '")]');
            } else {
                continue;
            }
            
            if ($nodes->length > 0) {
                $href = $nodes->item(0)->getAttribute('href');
                if (!empty($href)) {
                    // Convert to absolute URL
                    if (strpos($href, 'http') !== 0) {
                        if ($href[0] === '/') {
                            return $baseUrl . $href;
                        } else {
                            return $baseUrl . '/' . $href;
                        }
                    }
                    return $href;
                }
            }
        }
        
        return null;
    }
}
