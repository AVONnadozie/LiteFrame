<?php

namespace LiteFrame\Utility;

use Exception;

class SimpleCURL
{
    protected $headers;
    protected $userAgent;
    protected $compression;
    protected $cookieFile;
    protected $proxy;
    protected $acceptsCookie;
    protected $requestInfo;
    protected $defaultCookieFile = 'cookies.txt';

    /**
     * Setup Curl
     * @param boolean $accepts_cookie Set true to accept cookies, else false
     * @param type $cookie_file Set cookie file
     * @param type $compression Compression
     * @param type $proxy Proxy
     * @return string
     */
    public function __construct($accepts_cookie = true, $cookie_file = null, $compression = 'gzip', $proxy = '')
    {
        $headers[] = 'Accept: image/gif, image/x-bitmap, image/jpeg, image/pjpeg';
        $headers[] = 'Connection: Keep-Alive';
        $headers[] = 'Content-type: application/x-www-form-urlencoded;charset=UTF-8';
        $this->setHeaders($headers);
        
        $userAgent = 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; .NET CLR 1.0.3705; .NET CLR 1.1.4322; Media Center PC 4.0)';
        $this->setUserAgent($userAgent);
        
        $this->setCompression($compression);
        $this->setProxy($proxy);
        $this->setAcceptsCookie($accepts_cookie);
        if ($accepts_cookie) {
            $this->setCookieFile($cookie_file);
        }
    }
    
    public function getCookieFile()
    {
        return $this->cookieFile;
    }

    
    public function setCookieFile($cookie_file = null)
    {
        $_file = $cookie_file ?: privateStoragePath($this->defaultCookieFile);
        if (file_exists($_file)) {
            $this->cookieFile = $_file;
        } else {
            $handle = fopen($_file, 'w');
            if (!$handle) {
                $this->error('The cookie file could not be opened. Make sure this directory has the correct permissions');
            }
            $this->cookieFile = $_file;
            fclose($handle);
        }
    }
    
    public function getHeaders()
    {
        return $this->headers;
    }

    public function getUserAgent()
    {
        return $this->userAgent;
    }

    public function getCompression()
    {
        return $this->compression;
    }

    public function getProxy()
    {
        return $this->proxy;
    }

    public function getAcceptsCookie()
    {
        return $this->acceptsCookie;
    }

    public function setHeaders(array $headers)
    {
        $this->headers = $headers;
        return $this;
    }

    public function setUserAgent($userAgent)
    {
        $this->userAgent = $userAgent;
        return $this;
    }

    public function setCompression($compression)
    {
        $this->compression = $compression;
        return $this;
    }

    public function setProxy($proxy)
    {
        $this->proxy = $proxy;
        return $this;
    }

    public function setAcceptsCookie($acceptsCookie)
    {
        $this->acceptsCookie = $acceptsCookie;
        return $this;
    }

    private function request($url, $method = 'GET', $data = [], $process = true)
    {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $this->headers);
        curl_setopt($curl, CURLOPT_USERAGENT, $this->userAgent);
        if ($this->acceptsCookie) {
            curl_setopt($curl, CURLOPT_COOKIEFILE, $this->cookieFile);
            curl_setopt($curl, CURLOPT_COOKIEJAR, $this->cookieFile);
        }
        
        if ($this->proxy) {
            curl_setopt($curl, CURLOPT_PROXY, $this->proxy);
        }
        
        curl_setopt($curl, CURLOPT_ENCODING, $this->compression);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        
        if (strcasecmp($method, 'POST') === 0) {
            curl_setopt($curl, CURLOPT_HEADER, 1);
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        } else {
            curl_setopt($curl, CURLOPT_HEADER, 0);
        }
        
        $response = curl_exec($curl);
        $this->requestInfo = curl_getinfo($curl);
        curl_close($curl);
        return $process ? $this->process($response): $response;
    }
    
    private function process($response)
    {
        $data = json_decode($response);
        if (json_last_error() === JSON_ERROR_NONE) {
            $response = $data;
        }
        
        return is_array($response)? new Collection($response): $response;
    }
    
    /**
     * Make HTTP GET request to a URL
     * @param type $url
     * @return string
     */
    public function sendGET($url)
    {
        return $this->request($url);
    }

    /**
     * Make HTTP POST request to a URL
     * @param type $url
     * @param type $data
     * @return string
     */
    public function sendPOST($url, $data)
    {
        return $this->request($url, 'POST', $data);
    }

    /**
     * Get HTTP status code
     * @return int|null
     */
    public function getHttpStatusCode()
    {
        return isset($this->requestInfo['http_code']) ? $this->requestInfo['http_code'] : null;
    }

    /**
     * Get CURL transfer info
     * @return array
     */
    public function getRequestInfo()
    {
        return $this->requestInfo;
    }

    public function error($error)
    {
        throw new Exception($error);
    }

    /**
     * Quick POST request
     * @param type $url URL send POST request
     * @param type $data Data to post
     * @param boolean $accepts_cookie Set true to accept cookies, else false
     * @param type $cookie_file Set cookie file
     * @param type $compression Compression
     * @param type $proxy Proxy
     * @return string
     */
    public static function post($url, $data, $accepts_cookie = true, $cookie_file = null, $compression = 'gzip', $proxy = '')
    {
        $self = new static($accepts_cookie, $cookie_file, $compression, $proxy);
        return $self->sendPOST($url, $data);
    }

    /**
     * Quick GET request
     * @param type $url URL send POST request
     * @param boolean $accepts_cookie Set true to accept cookies, else false
     * @param type $cookie_file Set cookie file
     * @param type $compression Compression
     * @param type $proxy Proxy
     * @return string
     */
    public static function get($url, $accepts_cookie = true, $cookie_file = null, $compression = 'gzip', $proxy = '')
    {
        $self = new static($accepts_cookie, $cookie_file, $compression, $proxy);
        return $self->sendGET($url);
    }
    
    public function __destruct()
    {
        $file = $this->getCookieFile();
        if (file_exists($file)) {
            unlink($file);
        }
    }
}
