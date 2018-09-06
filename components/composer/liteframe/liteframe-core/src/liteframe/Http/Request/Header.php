<?php

namespace LiteFrame\Http\Request;

use ArrayIterator;
use DateTime;
use RuntimeException;

class Header
{
    private static $instance;
    protected $headers = array();
    protected $cacheControl = array();
    
    private function __construct()
    {
    }

    /**
     * Return singleton class instance.
     *
     * @return Header
     */
    public static function getInstance()
    {
        if (empty(static::$instance)) {
            static::$instance = new static();
            static::$instance->setHeadersFromServer();
        }

        return static::$instance;
    }

    public static function get($key, $default = null, $first = true)
    {
        return static::getInstance()->getHeaderValue($key, $default, $first);
    }
    
    /**
     * Returns a header value by name.
     *
     * @param string $key     The header name
     * @param mixed  $default The default value
     * @param bool   $first   Whether to return the first value or all header values
     *
     * @return string|array The first header value if $first is true, an array of values otherwise
     */
    public function getHeaderValue($key, $default = null, $first = true)
    {
        $key = $this->formatKey($key);
        if (!array_key_exists($key, $this->headers)) {
            if (null === $default) {
                return $first ? null : array();
            }

            return $first ? $default : array($default);
        }

        if ($first && is_array($this->headers[$key])) {
            return $this->headers[$key][0];
        } else {
            return $this->headers[$key];
        }
    }
    
    

    /**
     * Get all headers.
     *
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Gets the HTTP headers from superglobal $_SERVER.
     *
     * @return array
     */
    private function setHeadersFromServer()
    {
        $server = $_SERVER;
        $contentHeaders = array('CONTENT_LENGTH' => true, 'CONTENT_MD5' => true, 'CONTENT_TYPE' => true);
        foreach ($server as $key => $value) {
            if (0 === strpos($key, 'HTTP_')) {
                $this->headers[substr($key, 5)] = $value;
            }
            // CONTENT_* are not prefixed with HTTP_
            elseif (isset($contentHeaders[$key])) {
                $this->headers[$key] = $value;
            }
        }

        if (isset($server['PHP_AUTH_USER'])) {
            $this->headers['PHP_AUTH_USER'] = $server['PHP_AUTH_USER'];
            $this->headers['PHP_AUTH_PW'] = isset($server['PHP_AUTH_PW']) ? $server['PHP_AUTH_PW'] : '';
        } else {
            /*
             * php-cgi under Apache does not pass HTTP Basic user/pass to PHP by default
             * For this workaround to work, add these lines to your .htaccess file:
             * RewriteCond %{HTTP:Authorization} ^(.+)$
             * RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]
             *
             * A sample .htaccess file:
             * RewriteEngine On
             * RewriteCond %{HTTP:Authorization} ^(.+)$
             * RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]
             * RewriteCond %{REQUEST_FILENAME} !-f
             * RewriteRule ^(.*)$ app.php [QSA,L]
             */

            $authorizationHeader = null;
            if (isset($server['HTTP_AUTHORIZATION'])) {
                $authorizationHeader = $server['HTTP_AUTHORIZATION'];
            } elseif (isset($server['REDIRECT_HTTP_AUTHORIZATION'])) {
                $authorizationHeader = $server['REDIRECT_HTTP_AUTHORIZATION'];
            }

            if (null !== $authorizationHeader) {
                if (0 === stripos($authorizationHeader, 'basic ')) {
                    // Decode AUTHORIZATION header into PHP_AUTH_USER and PHP_AUTH_PW when authorization header is basic
                    $exploded = explode(':', base64_decode(substr($authorizationHeader, 6)), 2);
                    if (count($exploded) == 2) {
                        list($this->headers['PHP_AUTH_USER'], $this->headers['PHP_AUTH_PW']) = $exploded;
                    }
                } elseif (empty($server['PHP_AUTH_DIGEST']) && (0 === stripos($authorizationHeader, 'digest '))) {
                    // In some circumstances PHP_AUTH_DIGEST needs to be set
                    $this->headers['PHP_AUTH_DIGEST'] = $authorizationHeader;
                    $server['PHP_AUTH_DIGEST'] = $authorizationHeader;
                } elseif (0 === stripos($authorizationHeader, 'bearer ')) {
                    /*
                     * XXX: Since there is no PHP_AUTH_BEARER in PHP predefined variables,
                     *      I'll just set $this->headers['AUTHORIZATION'] here.
                     *      http://php.net/manual/en/reserved.variables.server.php
                     */
                    $this->headers['AUTHORIZATION'] = $authorizationHeader;
                }
            }
        }

        if (isset($this->headers['AUTHORIZATION'])) {
            return $this->headers;
        }

        // PHP_AUTH_USER/PHP_AUTH_PW
        if (isset($this->headers['PHP_AUTH_USER'])) {
            $this->headers['AUTHORIZATION'] = 'Basic '.base64_encode($this->headers['PHP_AUTH_USER'].':'.$this->headers['PHP_AUTH_PW']);
        } elseif (isset($this->headers['PHP_AUTH_DIGEST'])) {
            $this->headers['AUTHORIZATION'] = $this->headers['PHP_AUTH_DIGEST'];
        }
    }

    /**
     * Returns the parameter keys.
     *
     * @return array An array of parameter keys
     *
     * @author Laravel
     */
    public function keys()
    {
        return array_keys($this->headers);
    }

    /**
     * Replaces the current HTTP headers by a new set.
     *
     * @param array $headers An array of HTTP headers
     *
     * @author Laravel
     */
    public function replace(array $headers = array())
    {
        $this->headers = array();
        $this->add($headers);
    }

    /**
     * Adds new headers the current HTTP headers set.
     *
     * @param array $headers An array of HTTP headers
     *
     * @author Laravel
     */
    public function add(array $headers)
    {
        foreach ($headers as $key => $values) {
            $this->set($key, $values);
        }
    }

    /**
     * Sets a header by name.
     *
     * @param string       $key     The key
     * @param string|array $values  The value or an array of values
     * @param bool         $replace Whether to replace the actual value or not (true by default)
     */
    public function set($key, $values, $replace = true)
    {
        $key = $this->formatKey($key);

        $values = array_values((array) $values);

        if (true === $replace || !isset($this->headers[$key])) {
            $this->headers[$key] = $values;
        } else {
            $this->headers[$key] = array_merge($this->headers[$key], $values);
        }

        if ('cache-control' === $key) {
            $this->cacheControl = $this->parseCacheControl($values[0]);
        }
    }

    private function setAll($headers)
    {
        foreach ($headers as $key => $values) {
            $this->set($key, $values);
        }
    }

    private function formatKey($key)
    {
        return str_replace('_', '-', strtoupper($key));
    }

    /**
     * Returns true if the HTTP header is defined.
     *
     * @param string $key The HTTP header
     *
     * @return bool true if the parameter exists, false otherwise
     *
     * @author Laravel
     */
    public function has($key)
    {
        return array_key_exists($this->formatKey($key), $this->headers);
    }

    /**
     * Returns true if the given HTTP header contains the given value.
     *
     * @param string $key   The HTTP header name
     * @param string $value The HTTP value
     *
     * @return bool true if the value is contained in the header, false otherwise
     *
     * @author Laravel
     */
    public function contains($key, $value)
    {
        return in_array($value, $this->getHeaderValue($key, null, false));
    }

    /**
     * Removes a header.
     *
     * @param string $key The HTTP header name
     */
    public function remove($key)
    {
        $key = $this->formatKey($key);

        unset($this->headers[$key]);

        if ('cache-control' === $key) {
            $this->cacheControl = array();
        }
    }

    /**
     * Returns the HTTP header value converted to a date.
     *
     * @param string   $key     The parameter key
     * @param DateTime $default The default value
     *
     * @return null|DateTime The parsed DateTime or the default value if the header does not exist
     *
     * @throws RuntimeException When the HTTP header is not parseable
     *
     * @author Laravel
     */
    public function getDate($key, DateTime $default = null)
    {
        if (null === $value = $this->getHeaderValue($key)) {
            return $default;
        }

        if (false === $date = DateTime::createFromFormat(DATE_RFC2822, $value)) {
            throw new RuntimeException(sprintf('The %s HTTP header is not parseable (%s).', $key, $value));
        }

        return $date;
    }

    /**
     * Adds a custom Cache-Control directive.
     *
     * @param string $key   The Cache-Control directive name
     * @param mixed  $value The Cache-Control directive value
     *
     * @author Laravel
     */
    public function addCacheControlDirective($key, $value = true)
    {
        $this->cacheControl[$key] = $value;

        $this->set('Cache-Control', $this->getCacheControlHeader());
    }

    /**
     * Returns true if the Cache-Control directive is defined.
     *
     * @param string $key The Cache-Control directive
     *
     * @return bool true if the directive exists, false otherwise
     *
     * @author Laravel
     */
    public function hasCacheControlDirective($key)
    {
        return array_key_exists($key, $this->cacheControl);
    }

    /**
     * Returns a Cache-Control directive value by name.
     *
     * @param string $key The directive name
     *
     * @return mixed|null The directive value if defined, null otherwise
     *
     * @author Laravel
     */
    public function getCacheControlDirective($key)
    {
        return array_key_exists($key, $this->cacheControl) ? $this->cacheControl[$key] : null;
    }

    /**
     * Removes a Cache-Control directive.
     *
     * @param string $key The Cache-Control directive
     *
     * @author Laravel
     */
    public function removeCacheControlDirective($key)
    {
        unset($this->cacheControl[$key]);

        $this->set('Cache-Control', $this->getCacheControlHeader());
    }

    /**
     * Returns an iterator for headers.
     *
     * @return ArrayIterator An \ArrayIterator instance
     *
     * @author Laravel
     */
    public function getIterator()
    {
        return new ArrayIterator($this->headers);
    }

    /**
     * Returns the number of headers.
     *
     * @return int The number of headers
     *
     * @author Laravel
     */
    public function count()
    {
        return count($this->headers);
    }

    /**
     * @return type
     *
     * @author Laravel
     */
    protected function getCacheControlHeader()
    {
        $parts = array();
        ksort($this->cacheControl);
        foreach ($this->cacheControl as $key => $value) {
            if (true === $value) {
                $parts[] = $key;
            } else {
                if (preg_match('#[^a-zA-Z0-9._-]#', $value)) {
                    $value = '"'.$value.'"';
                }

                $parts[] = "$key=$value";
            }
        }

        return implode(', ', $parts);
    }

    /**
     * Parses a Cache-Control HTTP header.
     *
     * @param string $header The value of the Cache-Control HTTP header
     *
     * @return array An array representing the attribute values
     *
     * @author Laravel
     */
    protected function parseCacheControl($header)
    {
        $cacheControl = array();
        preg_match_all('#([a-zA-Z][a-zA-Z_-]*)\s*(?:=(?:"([^"]*)"|([^ \t",;]*)))?#', $header, $matches, PREG_SET_ORDER);
        foreach ($matches as $match) {
            $cacheControl[strtolower($match[1])] = isset($match[3]) ? $match[3] : (isset($match[2]) ? $match[2] : true);
        }

        return $cacheControl;
    }

    /**
     * Returns the headers as a string.
     *
     * @return string The headers
     *
     * @author Laravel
     */
    public function __toString()
    {
        if (!$this->headers) {
            return '';
        }

        $max = max(array_map('strlen', array_keys($this->headers))) + 1;
        $content = '';
        ksort($this->headers);
        foreach ($this->headers as $name => $values) {
            $name = implode('-', array_map('ucfirst', explode('-', $name)));
            foreach ($values as $value) {
                $content .= sprintf("%-{$max}s %s\r\n", $name.':', $value);
            }
        }

        return $content;
    }
}
