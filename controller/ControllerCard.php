<?php

require_once "autoload.php";

class ControllerCard extends EController {

    public function index() {
        $this->redirect();
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function left() {
        list($_, $card) = $this->authorize_or_redirect("id", "Card");

        $card->move_left();

        $this->redirect("board", "board", $card->get_board_id());
    }

    public function right() {
        list($_, $card) = $this->authorize_or_redirect("id", "Card");

        $card->move_right();

        $this->redirect("board", "board", $card->get_board_id());

    }

    public function up() {
        list($_, $card) = $this->authorize_or_redirect("id", "Card");

        $card->move_up();

        $this->redirect("board", "board", $card->get_board_id());
    }

    public function down() {
        list($_, $card) = $this->authorize_or_redirect("id", "Card");

        $card->move_down();

        $this->redirect("board", "board", $card->get_board_id());
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function add() {
        list($user, $board) = $this->authorize_or_redirect("board_id", "Board");

        if (!Post::empty("title")) {
            $column_id = Post::get("column_id");
            $title = Post::get("title");

            $card = Card::create_new($title, $user, $column_id);

            $error = new ValidationError($card, "add");
            $error->set_messages_and_add_to_session($card->validate());
            $error->set_id($column_id);

            if($error->is_empty()){
                $card->insert();
            }
        }
        $this->redirect("board", "board", $board->get_id());

    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function update(){
        list($_, $card) = $this->authorize_or_redirect("id", "Card");

        if (Post::isset("body")) {
            $card->set_body(Post::get("body"));
        }

        if (Post::isset("title")) {
            $card->set_title(Post::get("title"));
        }

        if(Post::isset("due-date")) {
            $card->set_dueDate(new Datetime(Post::get("due-date")));
        }

        $error = new ValidationError($card, "update");
        $error->set_messages_and_add_to_session($card->validate_update());

        if($error->is_empty()){
            $card->update();
            $this->redirect("card", "view", $card->get_id());
        }

        $this->redirect("card", "edit", $card->get_id());

    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function delete() {
        list($_, $card) = $this->authorize_or_redirect("id", "Card");

        if($card!=null){
            $this->redirect("card","delete_confirm",$card->get_id());
        }
    }

    public function delete_confirm(){
        list($user, $card) = $this->authorize_or_redirect("param1", "Card");

        (new View("delete_confirm"))->show(array(
            "user"=>$user,
            "instance"=>$card
        ));
    }

    public function remove() {
        list($_, $card) = $this->authorize_or_redirect("id", "Card");

        if(Post::isset("delete")) {
            Card::decrement_following_cards_position($card);
            $card->delete();
        }

        $this->redirect("board", "board", $card->get_board_id());
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    
    public function edit_link(){
        list($user, $card) = $this->authorize_or_redirect("id", "Card");

        $this->redirect("card", "edit", $card->get_id());
    }

    public function edit(){
        list($user, $card) = $this->authorize_or_redirect("param1", "Card");

        $comments = $card->get_comments();
        $edit="yes";

        if(isset($_GET['param2'])){
            (new View("card_edit"))->show(array(
                "user" => $user,
                "card" => $card,
                "comment" => $comments,
                "show_comment" => $_GET['param2'],
                "edit" => $edit,
                "breadcrumb" => new BreadCrumb(array($card->get_board(), $card)),
                "errors" => ValidationError::get_error_and_reset()
                )
            );
            die;
        } else {
            (new View("card_edit"))->show(array(
                "user" => $user,
                "card" => $card,
                "comment" => $comments,
                "edit" => $edit,
                "breadcrumb" => new BreadCrumb(array($card->get_board(), $card)),
                "errors" => ValidationError::get_error_and_reset()
                )
            );
            die;
        }

    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function view(){
        list($user, $card) = $this->authorize_or_redirect("param1", "Card");

        $comments = $card->get_comments();

        if(isset($_GET['param2'])){
            (new View("card"))->show(array(
                "user" => $user,
                "card" => $card,
                "comment" => $comments,
                "show_comment" => $_GET['param2'],
                "breadcrumb" => new BreadCrumb(array($card->get_board(), $card)),
                "errors" => ValidationError::get_error_and_reset()
                )
            );
            die;
        } else {
            (new View("card"))->show(array(
                "user" => $user,
                "card" => $card,
                "comment" => $comments,
                "breadcrumb" => new BreadCrumb(array($card->get_board(), $card)),
                "errors" => ValidationError::get_error_and_reset()
                )
            );
            die;
        }
    }

}
