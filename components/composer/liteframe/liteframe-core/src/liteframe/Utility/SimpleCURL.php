<?php

namespace LiteFrame\Utility;

use Exception;

class SimpleCURL
{
    protected $headers = [];
    protected $options = [];
    protected $userAgent;
    protected $compression;
    protected $cookieFile;
    protected $proxy;
    protected $acceptsCookie;
    protected $requestInfo;
    protected $defaultCookieFile = 'cookies.txt';
    protected $assoc;

    /**
     * Setup Curl
     * @param boolean $accepts_cookie Set true to accept cookies, else false
     * @param type $cookie_file Set cookie file
     * @param type $compression Compression
     * @param type $proxy Proxy
     * @return string
     */
    public function __construct($accepts_cookie = true, $compression = 'gzip', $proxy = '')
    {
        $headers['Accept'] = 'image/gif, image/x-bitmap, image/jpeg, image/pjpeg';
        $headers['Connection'] = 'Keep-Alive';
        $this->setHeaders($headers);

        $userAgent = 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; .NET CLR 1.0.3705; .NET CLR 1.1.4322; Media Center PC 4.0)';
        $this->setUserAgent($userAgent);

        $this->setCompression($compression);
        $this->setProxy($proxy);
        $this->setAcceptsCookie($accepts_cookie);
    }

    public function getCookieFile()
    {
        return $this->cookieFile;
    }

    public function setCookieFile($cookie_file = null)
    {
        $_file = $cookie_file ?: storage_path("data/$this->defaultCookieFile");
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

    /**
     * Add value to header. Same header values will be replaced.
     *
     * @param string|array $key
     * @param mixed $value
     *
     * @return $this
     */
    public function setHeaders($key, $value = null)
    {
        if (is_array($key)) {
            foreach ($key as $a_key => $value) {
                $this->headers[strtolower($a_key)] = "$a_key: $value";
            }
        } elseif (empty($value)) {
            $this->headers[] = $key;
        } else {
            $this->headers[strtolower($key)] = "$key: $value";
        }

        return $this;
    }

    /**
     * Remove header values
     * @param string|int|array $keys
     */
    public function removeHeaders($keys)
    {
        $key_array = (array) $keys;
        foreach ($key_array as $key) {
            unset($this->headers[$key]);
        }
    }

    /**
     * Set user agent
     * @param string $userAgent
     * @return $this
     */
    public function setUserAgent($userAgent)
    {
        $this->userAgent = $userAgent;
        return $this;
    }

    /**
     * Set compression
     * @param string $compression
     * @return $this
     */
    public function setCompression($compression)
    {
        $this->compression = $compression;
        return $this;
    }

    /**
     * Set proxy
     * @param string $proxy
     * @return $this
     */
    public function setProxy($proxy)
    {
        $this->proxy = $proxy;
        return $this;
    }

    /**
     * Set cURL to accept cookie from sites
     * @param boolean $acceptsCookie
     * @return $this
     */
    public function setAcceptsCookie($acceptsCookie)
    {
        $this->acceptsCookie = $acceptsCookie;
        return $this;
    }

    /**
     * Set or override a cURL option
     * @param int|array $option
     * @param mixed $value
     * @return $this
     */
    public function setOption($option, $value = null)
    {
        if (is_array($option)) {
            $this->options += $option;
        } else {
            $this->options[$option] = $value;
        }

        return $this;
    }

    private function request($url, $method = 'GET', $data = [], $process = true)
    {
        $curl = curl_init();
        //Set headers
        curl_setopt($curl, CURLOPT_HTTPHEADER, array_values($this->headers));

        $this->setDefaultOptions($curl);

        $this->updateURLOption($curl, $method, $url, $data);

        //Set user defined options last to allow override of options
        foreach ($this->options as $key => $value) {
            curl_setopt($curl, $key, $value);
        }

        $response = curl_exec($curl);
        $this->requestInfo = curl_getinfo($curl);
        curl_close($curl);
        return $process ? $this->process($response) : $response;
    }

    private function setDefaultOptions($curl)
    {
        if ($this->acceptsCookie) {
            curl_setopt($curl, CURLOPT_COOKIEFILE, $this->cookieFile);
            curl_setopt($curl, CURLOPT_COOKIEJAR, $this->cookieFile);
        }

        if ($this->proxy) {
            curl_setopt($curl, CURLOPT_PROXY, $this->proxy);
        }

        curl_setopt($curl, CURLOPT_USERAGENT, $this->userAgent);
        curl_setopt($curl, CURLOPT_ENCODING, $this->compression);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
    }

    private function updateURLOption($curl, $method, $url, $data)
    {
        if (strcasecmp($method, 'POST') === 0) {
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_POST, 1);
            if (!empty($data)) {
                curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
            }
        } else {
            $url = $this->updateURLWithParameters($url, $data);
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_HEADER, 0);
        }
    }

    /**
     * Updates the URL provided with the new parameters
     * @param string $url
     * @param array $params
     * @return string updated URL
     */
    public function updateURLWithParameters($url, array $params)
    {
        if (empty($params)) {
            return $url;
        }

        $parts = parse_url($url);

        $scheme = isset($parts['scheme']) ? $parts['scheme'] : null;
        if ($scheme) {
            $scheme .= ':';
        }

        $host = isset($parts['host']) ? $parts['host'] : null;
        if ($host) {
            $host = "//$host";
        }
        $path = isset($parts['path']) ? $parts['path'] : null;

        //Merge parameters
        $query = isset($parts['query']) ? $parts['query'] : null;
        $params += $this->getParameterFromQuery($query);
        if (!empty($params)) {
            $query = '?' . http_build_query($params);
        }

        $fragment = isset($parts['fragment']) ? $parts['fragment'] : null;
        if ($fragment) {
            $fragment = "#$fragment";
        }

        return "{$scheme}{$host}{$path}{$query}$fragment";
    }

    private function getParameterFromQuery($query)
    {
        $params = [];
        if ($query) {
            $querySplit = explode('&', $query);
            foreach ($querySplit as $pair) {
                $pairSplit = explode('=', $pair);
                $params[$pairSplit[0]] = $pairSplit[1];
            }
        }
        return $params;
    }

    private function process($response)
    {
        $data = json_decode($response, $this->assoc);
        if (json_last_error() === JSON_ERROR_NONE) {
            $response = $data;
        }

        return is_array($response) ? new Collection($response) : $response;
    }

    /**
     * Get response as array.<br/>
     * SimpleCURL will try to return non-scalar cURL response as array if set to true,
     * otherwise an object is returned.
     * @param boolean $array
     * @return $this
     */
    public function returnArray($array = true)
    {
        $this->assoc = $array;
        return $this;
    }

    /**
     * Make HTTP GET request to a URL
     *
     * @param string $url
     * @param array $data
     * @return mixed
     */
    public function sendGET($url, $data = [])
    {
        return $this->request($url, 'GET', $data);
    }

    /**
     * Make HTTP POST request to a URL
     * @param type $url
     * @param type $data
     * @return string
     */
    public function sendPOST($url, $data = [])
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

    private function error($error)
    {
        throw new Exception($error);
    }

    /**
     * Quick POST request
     * @param type $url URL send POST request
     * @param type $data Data to post
     * @param boolean $accepts_cookie Set true to accept cookies, else false
     * @param type $compression Compression
     * @param type $proxy Proxy
     * @return string
     */
    public static function post($url, $data = null, $accepts_cookie = true, $compression = 'gzip', $proxy = '')
    {
        $self = new static($accepts_cookie, $compression, $proxy);
        return $self->sendPOST($url, $data);
    }

    /**
     * Quick GET request
     * @param type $url URL send POST request
     * @param boolean $accepts_cookie Set true to accept cookies, else false
     * @param type $compression Compression
     * @param type $proxy Proxy
     * @return string
     */
    public static function get($url, $data = [], $accepts_cookie = true, $compression = 'gzip', $proxy = '')
    {
        $self = new static($accepts_cookie, $compression, $proxy);
        return $self->sendGET($url, $data);
    }

    public function __destruct()
    {
        $file = $this->getCookieFile();
        if (file_exists($file)) {
            try {
                unlink($file);
            } catch (Exception $e) {
            }
        }
    }
}
