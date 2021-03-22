<?php

require_once "autoload.php";

class ControllerCard extends ExtendedController {

    public function index() {
        $this->redirect();
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function left() {
        $card = $this->get_object_or_redirect("id", "Card");
        $this->authorize_for_board_or_redirect($card->get_board());

        $card->move_left();

        $this->redirect("board", "board", $card->get_board_id());
    }

    public function right() {
        $card = $this->get_object_or_redirect("id", "Card");
        $this->authorize_for_board_or_redirect($card->get_board());

        $card->move_right();

        $this->redirect("board", "board", $card->get_board_id());

    }

    public function up() {
        $card = $this->get_object_or_redirect("id", "Card");
        $this->authorize_for_board_or_redirect($card->get_board());

        $card->move_up();

        $this->redirect("board", "board", $card->get_board_id());
    }

    public function down() {
        $card = $this->get_object_or_redirect("id", "Card");
        $this->authorize_for_board_or_redirect($card->get_board());

        $card->move_down();

        $this->redirect("board", "board", $card->get_board_id());
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function add() {
        $column = $this->get_object_or_redirect("column_id", "Column");
        $user = $this->authorize_for_board_or_redirect($column->get_board());

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
        $this->redirect("board", "board", $column->get_board_id());

    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function update(){
        $card = $this->get_object_or_redirect("id", "Card");
        $this->authorize_for_board_or_redirect($card->get_board());

        if (Post::isset("body")) {
            $card->set_body(Post::get("body"));
        }

        if (Post::isset("title")) {
            $card->set_title(Post::get("title"));
        }

        if(Post::isset("due-date")) {
            $card->set_dueDate(new Datetime(Post::get("due_date")));
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
        $card = $this->get_object_or_redirect("id", "Card");
        $this->authorize_for_board_or_redirect($card->get_board());

        if($card!=null){
            $this->redirect("card","delete_confirm",$card->get_id());
        }
    }

    public function delete_confirm(){
        $card = $this->get_object_or_redirect("param1", "Card");
        $user = $this->authorize_for_board_or_redirect($card->get_board());

        (new View("delete_confirm"))->show(array(
            "user"=>$user,
            "instance"=>$card
        ));
    }

    public function remove() {
        $card = $this->get_object_or_redirect("id", "Card");
        $this->authorize_for_board_or_redirect($card->get_board());

        if(Post::isset("delete")) {
            Card::decrement_following_cards_position($card);
            $card->delete();
        }

        $this->redirect("board", "board", $card->get_board_id());
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    
    public function edit_link(){
        $card = $this->get_object_or_redirect("id", "Card");
        $this->authorize_for_board_or_redirect($card->get_board());

        $this->redirect("card", "edit", $card->get_id());
    }

    public function edit(){
        $card = $this->get_object_or_redirect("param1", "Card");
        $user = $this->authorize_for_board_or_redirect($card->get_board());

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
        $card = $this->get_object_or_redirect("param1", "Card");
        $user = $this->authorize_for_board_or_redirect($card->get_board());

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
