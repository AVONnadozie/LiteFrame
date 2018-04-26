<?php

namespace LiteFrame\Exception;

use Exception;

/**
 * Describes an error
 */
class ErrorBag
{
    protected $code;
    protected $title;
    protected $content;
    protected $trace;
    protected $file;
    protected $line;
    protected $searchTerm;

    /**
     * Represent and exception
     *
     * @param Exception|array|int $code
     * @param string $title
     * @param string $content
     * @param string $trace
     * @param string $file
     * @param int $line
     */
    public function __construct($code, $title = null, $content = null, $trace = null, $file = null, $line = null)
    {
        if ($code instanceof Exception) {
            $this->setException($code);
        } elseif (is_array($code)) {
            $this->setFromArray($code);
        } else {
            $this->code = $code;
            $this->title = $title;
            $this->content = $content;
            $this->trace = $trace;
            $this->file = $file;
            $this->line = $line;
        }
        $this->searchTerm = [];
    }

    public function getTitle()
    {
        if (empty($this->title)) {
            $this->title = $this->getDefaultTitle();
        }

        return $this->title;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function getTrace()
    {
        return $this->trace;
    }

    public function getCode()
    {
        return $this->code;
    }

    public function getFile()
    {
        return $this->file;
    }

    public function getLine()
    {
        return $this->line;
    }

    private function setException(Exception $exception)
    {
        $this->code = $exception->getCode();
        $this->file = $exception->getFile();
        $this->line = $exception->getLine();
        $this->title = $exception->getMessage();
        $this->content = $exception->getMessage();
        $this->trace = $exception->getTraceAsString();
    }

    private function setFromArray(array $error)
    {
        $this->code = $error['code'];
        $this->title = isset($error['title']) ? $error['title'] : null;
        $this->content = isset($error['content']) ? $error['content'] : null;
        $this->trace = isset($error['trace']) ? $error['trace'] : null;
        $this->file = isset($error['file']) ? $error['file'] : null;
        $this->line = isset($error['line']) ? $error['line'] : null;
    }

    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    }

    public function setTrace($trace)
    {
        $this->trace = $trace;
        return $this;
    }

    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }

    public function getDefaultTitle()
    {
        return $this->code . ' - ' . getHttpResponseMessage($this->code);
    }

    public function getSearchTerm($engine = null)
    {
        if (!isset($this->searchTerm[$engine])) {
            $this->searchTerm[$engine] = $this->findSearchTerm($this->getTitle(), $engine);
        }

        return $this->searchTerm[$engine];
    }

    private function findSearchTerm($string, $engine = 'google')
    {
        $filtered = $this->removeErrorCode($string);
        $filtered = $this->removeErrorDate($filtered);
        $filtered = $this->removeErrorFile($filtered);
        switch ($engine) {
            //Define special filters here
            case 'so':
            case 'google':
            case 'duckduckgo':
            case 'bing':
                break;
            default:
                $filtered = 'LiteFrame ' . $filtered;
                break;
        }
        return trim($filtered);
    }

    private function removeErrorFile($string)
    {
        //Remove "[at] path/to/file line no "
        return preg_replace('/(at )?(\S*)\.php line \d+\s*/', '', $string);
    }

    private function removeErrorDate($string)
    {
        //Remove "[yyyy-mm-dd hh:mm:ss] "
        return preg_replace('/\[[\d\-]+ [\d:]+\]\s*/', '', $string);
    }

    private function removeErrorCode($string)
    {
        //Remove "Error (no) "
        return preg_replace('/Error \(\d+\):\s*/', '', $string);
    }
}
