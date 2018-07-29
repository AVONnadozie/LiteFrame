<?php
namespace LiteFrame\Http\Response;

use LiteFrame\Http\Response;
use LiteFrame\View\View;

class ViewResponse extends Response {

    protected $view;

    protected function __construct($path, $data) {
        parent::__construct();

        $this->view = new View();
        $content = $this->view->fetch($path, $data);
        $this->setContent($content);
        $this->toHTML();
    }

}
