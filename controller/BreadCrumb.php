<?php

require_once "autoload.php";

class BreadCrumb {
    private array $breadcrumb;
    private ?string $last_elem;

    public function __construct(array $trace, string $last_elem=null) {
        $this->breadcrumb = $trace;
        $this->last_elem = $last_elem;
    }

    // Construit et retourne le breadcrumb
    // construit d'abord le premier élément (home), puis le dernier, s'il existe, puis les intermédiaires s'ils existent
    public function get_trace() {
        $home = $this->get_home();
        $last = $this->get_last();
        $middle = $this->get_middle();

        $trace = implode("", array_filter(array($home, $middle, $last)));

        return "<nav class='breadcrumb' aria-label='breadcrumbs'><ul>$trace</ul></nav>";
    }

    private function get_home(): string {
        $home = "";
        if (count($this->breadcrumb) > 0 || !is_null($this->last_elem)) {
            $home="<li><a href='board/index'>Boards</a></li>";
        }
        return $home;
    }

    private function get_last(): string {
        $last_elem = null;

        if (!is_null($this->last_elem)) {
            $last_elem = $this->last_elem;
        }
        else if (count($this->breadcrumb) > 0) {
            $elem = array_pop($this->breadcrumb);
            $name = get_class($elem);
            $last_elem = $name . " \"" . $elem->get_title() . "\"";
        }
        return $last_elem == null ? "" : "<li class='is-active'><a href='#' aria-current='page'>$last_elem</a></li>";
    }

    private function get_middle(): string {
        $trace = "";

        if (count($this->breadcrumb) > 0) {
            $crumbs = [];

            foreach ($this->breadcrumb as $idx=>$elem) {
                $name = get_class($elem);
                $title = $name . " \"" . $elem->get_title() . "\"";
                $controller = strtolower($name);
                $method = "view";
                $id = $elem->get_id();


                $crumbs[] = "<li><a href='$controller/$method/$id'>$title</a></li>";
            }
            $trace = implode("",  $crumbs);
        }
        return $trace;
    }

}
