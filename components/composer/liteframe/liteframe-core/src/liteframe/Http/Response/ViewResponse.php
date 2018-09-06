<?php
namespace LiteFrame\Http\Response;

use LiteFrame\Http\Response;
use LiteFrame\View\View;

class ViewResponse extends Response
{
    protected $path;
    protected $data;

    public function __construct($path = null, $data = [], $code = 200) {
        $this->path = $path;
        $this->data = $data;
        $this->statusCode = $code;

        $content = View::fetch($path, $data);
        $this->setContent($content, $code);
    }

    public function getPath()
    {
        return $this->path;
    }

    public function getData()
    {
        return $this->data;
    }

    public function setPath($path)
    {
        $this->path = $path;
        return $this;
    }

    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }

    /**
     * Get content of this response object.
     *
     * @return string
     */
    public function getContent()
    {
        $this->toHTML();
        return $this->content;
    }

    public function setContent($content, $code = 200)
    {
        $this->content = $content ? $content : '';
        $this->statusCode = $code;

        return $this;
    }
}
