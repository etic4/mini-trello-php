<?php

trait TitleTrait {

    private string $title;

    public function get_title(): string {
        return $this->title;
    }

    public function set_title(string $title): void {
        $this->title = $title;
    }

    public function get_truncated_title(): string {
        $title = $this->getTitle();
        $length = 14;
        if(strlen($title) <= $length) return $title;
        return trim(substr($title, 0, $length)) . "...";
    }

}