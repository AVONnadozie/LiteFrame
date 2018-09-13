<?php

namespace LiteFrame\Http;

use LiteFrame\Http\Response\FileResponse;
use LiteFrame\Http\Response\ViewResponse;
use LiteFrame\Utility\Collection;

/**
 * Description of Response.
 *
 * @author Victor Anuebunwa
 */
class Response
{
    protected $headers = [];
    protected $content = '';
    protected $json;
    protected $statusCode;
    protected $request;
    protected static $instance;

    protected function __construct()
    {
        $this->request = Request::getInstance();
    }

    /**
     * Return singleton class instance.
     *
     * @return Response
     */
    public static function getInstance()
    {
        if (empty(static::$instance)) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    /**
     * Set a view as response.
     *
     * @param string $path path to view file
     * @param string $data view data
     *
     * @return $this
     */
    public function view($path, $data = [])
    {
        return new ViewResponse($path, $data);
    }

    /**
     * Set response content.
     *
     * @param type $content
     * @param type $code
     *
     * @return $this
     */
    public function setContent($content, $code = 200)
    {
        $this->content = $content ? $content : '';
        $this->statusCode = $code;
        if (!is_scalar($content)) {
            $this->toJson();
        }

        return $this;
    }

    /**
     * Append content to response.
     *
     * @param type $content
     * @param type $code
     *
     * @return $this
     */
    public function appendContent($content, $code = 200)
    {
        if (!empty($content)) {
            $oldContent = $this->getContent();

            if (!empty($oldContent)) {
                if (!is_scalar($content)) {
                    $content = json_encode($content);
                }

                $this->toHTML();
                $content = $oldContent . $content;
            }

            $this->setContent($content, $code);
        }
        return $this;
    }

    /**
     * Prepend content to response.
     *
     * @param type $content
     * @param type $code
     *
     * @return $this
     */
    public function prependContent($content, $code = 200)
    {
        if (!empty($content)) {
            $oldContent = $this->getContent();

            if (!empty($oldContent)) {
                if (!is_scalar($content)) {
                    $content = json_encode($content);
                }

                $this->toHTML();
                $content .= $oldContent;
            }

            $this->setContent($content, $code);
        }
        return $this;
    }

    /**
     * Add value to header. headers with the same key will be replaced.
     *
     * @param type $key
     * @param type $value
     *
     * @return $this
     */
    public function header($key, $value = null)
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
     * Set a json content as response.
     *
     * @param type $content
     *
     * @return $this
     */
    public function json($content)
    {
        $this->header('Content-Type', 'application/json');
        $this->json = true;
        $this->content = $content;

        return $this;
    }

    /**
     * Return file as response
     * @param type $path
     */
    public function file($path)
    {
        return new FileResponse($path);
    }

    /**
     * Force download of file
     * @param type $path
     */
    public function download($path, $name = null)
    {
        $fResponse = new FileResponse($path);
        return $fResponse->forceDownload($name);
    }

    /**
     * Force response to return as json
     * @return $this
     */
    public function toJson()
    {
        $this->header('Content-Type', 'application/json');
        $this->json = true;
        return $this;
    }

    /**
     * Force response to return as html
     * @return $this
     */
    public function toHTML()
    {
        $this->header('Content-Type', 'text/html');
        $this->json = false;
        return $this;
    }

    /**
     * Force response to return as html
     * @return $this
     */
    public function toPlain()
    {
        $this->header('Content-Type', 'text/plain');
        $this->json = false;
        return $this;
    }

    public function isJson()
    {
        return $this->json;
    }

    /**
     * Redirect to route.
     *
     * @param type $name
     * @param type $code
     *
     * @return type
     */
    public function redirectToRoute($name, $code = 302)
    {
        return $this->redirect(route($name), $code);
    }

    /**
     * Redirect to url.
     *
     * @param type $url
     * @param type $code
     *
     * @return type
     */
    public function redirect($url, $code = 302)
    {
        $this->statusCode = $code;

        return $this->header('Location', $url);
    }

    public function __toString()
    {
        return $this->output();
    }

    /**
     * Outputs the content of this response object. All headers will be sent in the order they were added.
     *
     * @return type
     */
    public function output()
    {
        $content = $this->getContent();

        //Set headers
        header($this->getStatusText());
        foreach ($this->headers as $header) {
            header($header);
        }

        return $content;
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
     * Get content of this response object.
     *
     * @return string
     */
    public function getContent()
    {
        //Convert non scalar responses
        if ($this->content && !is_scalar($this->content) && !$this->json) {
            $this->toJson();
        }

        if (($this->json || $this->request->wantsJson()) && !is_scalar($this->content)) {
            if ($this->content instanceof Collection) {
                return json_encode($this->content->toArray());
            } else {
                return json_encode((array) $this->content);
            }
        } else {
            return $this->content;
        }
    }

    /**
     * Get content of this response object as is.
     *
     * @return string
     */
    public function getRawContent()
    {
        return $this->content;
    }

    /**
     * Get the response status code.
     *
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    public function setStatusCode($code)
    {
        $this->statusCode = $code;
        return $this;
    }

    /**
     * Get the response status text.
     *
     * @return string
     */
    protected function getStatusText()
    {
        $code = $this->statusCode;
        if (empty($code)) {
            $code = 200;
        }

        return "HTTP/1.1 $code " . $this->getHttpResponseMessage($code);
    }

    public function getHttpResponseMessage($code)
    {
        switch ($code) {
            case 100: $text = 'Continue';
                break;
            case 101: $text = 'Switching Protocols';
                break;
            case 200: $text = 'OK';
                break;
            case 201: $text = 'Created';
                break;
            case 202: $text = 'Accepted';
                break;
            case 203: $text = 'Non-Authoritative Information';
                break;
            case 204: $text = 'No Content';
                break;
            case 205: $text = 'Reset Content';
                break;
            case 206: $text = 'Partial Content';
                break;
            case 300: $text = 'Multiple Choices';
                break;
            case 301: $text = 'Moved Permanently';
                break;
            case 302: $text = 'Moved Temporarily';
                break;
            case 303: $text = 'See Other';
                break;
            case 304: $text = 'Not Modified';
                break;
            case 305: $text = 'Use Proxy';
                break;
            case 400: $text = 'Bad Request';
                break;
            case 401: $text = 'Unauthorized';
                break;
            case 402: $text = 'Payment Required';
                break;
            case 403: $text = 'Forbidden';
                break;
            case 404: $text = 'Not Found';
                break;
            case 405: $text = 'Method Not Allowed';
                break;
            case 406: $text = 'Not Acceptable';
                break;
            case 407: $text = 'Proxy Authentication Required';
                break;
            case 408: $text = 'Request Time-out';
                break;
            case 409: $text = 'Conflict';
                break;
            case 410: $text = 'Gone';
                break;
            case 411: $text = 'Length Required';
                break;
            case 412: $text = 'Precondition Failed';
                break;
            case 413: $text = 'Request Entity Too Large';
                break;
            case 414: $text = 'Request-URI Too Large';
                break;
            case 415: $text = 'Unsupported Media Type';
                break;
            case 500: $text = 'Internal Server Error';
                break;
            case 501: $text = 'Not Implemented';
                break;
            case 502: $text = 'Bad Gateway';
                break;
            case 503: $text = 'Service Unavailable';
                break;
            case 504: $text = 'Gateway Time-out';
                break;
            case 505: $text = 'HTTP Version not supported';
                break;
            default:
                $text = 'Unknown';
        }

        return $text;
    }
}
