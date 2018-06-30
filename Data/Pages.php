<?php

namespace Data\Catalog;

class Pages {
    public $pages = 0, $current_page = 0, $elements = array();
    public function __construct($elements = null, $pages = 1, $page = 1) {
        $this->pages = $pages;
        $this->current_page = $page;
        $this->elements = $elements;
    }

    public function getResponse() {
        foreach ($this->elements as $key => $element) {
            $this->elements[$key] = $element->getPretty();
        }
    }
}