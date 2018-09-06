<?php

namespace LiteFrame\Utility;

class Paginator extends Collection
{
    protected $page;
    protected $limit;
    protected $total;
    protected $link_params = [];

    public function __construct($items, $page, $limit, $total) {
        parent::__construct($items);
        $this->page = $page;
        $this->limit = $limit;
        $this->total = $total;
    }

    public function appendToLinks(array $data)
    {
        $this->link_params = $data;
        return $this;
    }

    private function getLinkPrams($extra = []) {
        return array_merge($this->link_params, $extra);
    }

    public function getLinks($links_count = 3, $list_class = 'pagination')
    {
        $last = ceil($this->total / $this->limit);

        $start = (($this->page - $links_count) > 0) ? $this->page - $links_count : 1;
        $end = (($this->page + $links_count) < $last) ? $this->page + $links_count : $last;

        $html = '<ul class="' . $list_class . '">';

        $lclass = ($this->page == 1) ? "disabled" : "";
        $llinks = http_build_query($this->getLinkPrams(['page' => ($this->page - 1)]));
        $html .= "<li class='$lclass'><a href='?$llinks'>&laquo;</a></li>";

        if ($start > 1) {
            $links = http_build_query($this->getLinkPrams(['page' => 1]));
            $html .= "<li><a href='?$links'>1</a></li>";
            $html .= '<li class="disabled"><span>...</span></li>';
        }

        for ($i = $start; $i <= $end; $i++) {
            $links = http_build_query($this->getLinkPrams(['page' => $i]));
            $class = ($this->page == $i) ? "active" : "";
            $html .= "<li class='$class'><a href='?$links'>$i</a></li>";
        }

        if ($end < $last) {
            $links = http_build_query($this->getLinkPrams(['page' => $last]));
            $html .= '<li class="disabled"><span>...</span></li>';
            $html .= "<li><a href='?$links'>$last</a></li>";
        }

        $rclass = ($this->page == $last) ? "disabled" : "";
        $rlinks = http_build_query($this->getLinkPrams(['page' => ($this->page + 1)]));
        $html .= "<li class='$rclass'><a href='?$rlinks'>&raquo;</a></li>";
        $html .= '</ul>';

        return $html;
    }

    public function getPage()
    {
        return $this->page;
    }

    public function getLimit()
    {
        return $this->limit;
    }

    public function getTotal()
    {
        return $this->total;
    }
}
