<?php

require_once "framework/Controller.php";
require_once "model/User.php";

class ControllerCard extends Controller {

    public function index() {
        // TODO
    }

    public function add() {
        $user = $this->get_user_or_redirect();
        if(!empty($_POST["title"])) {
            $title = $_POST["title"];
            $column = $_POST["column_id"];
            $author = $user->get_id();
            $card = Card::create_new($title, $author, $column); 
            $card->insert(); 
        }
        $board = $_POST["board_id"];
        $this->redirect("board", "board", $board);
    }

}
