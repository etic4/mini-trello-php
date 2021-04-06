<?php

require_once "autoload.php";

class BreadCrumb {
    const SEPARATOR = "<span class='breadcrumb-separator'>&gt;</span>";
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

        $trace = implode(self::SEPARATOR, array_filter(array($home, $middle, $last)));

        return "<div class='breadcrumb'>$trace</div>";
    }

    private function get_home(): string {
        $home = "";
        if (count($this->breadcrumb) > 0 || !is_null($this->last_elem)) {
            $home = "<span><a href='board/index'> Boards</a></span>";
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
        return $last_elem == null ? "" : "<span class='breadcrumb-current'>$last_elem</span>";
    }

    private function get_middle(): string {
        $trace = "";

        if (count($this->breadcrumb) > 0) {
            $crumbs = [];

            foreach ($this->breadcrumb as $idx=>$elem) {
                $name = get_class($elem);
                $title = $name . " \"" . $elem->get_title() . "\"";
                $controller = strtolower($name);
//                $method = $controller == "board" ? "board" : "view";
                $method = "view";
                $id = $elem->get_id();

                $crumbs[] = "<span><a href='$controller/$method/$id'>$title</a></span>";
            }
            $trace = implode(self::SEPARATOR,  $crumbs);
        }
        return $trace;
    }

}
