<?php

require_once "framework/Controller.php";
require_once "model/Card.php";
require_once "model/User.php";
require_once "CtrlTools.php";
require_once "ValidationError.php";
require_once "Authorize.php";

class ControllerCard extends Controller {
    use Authorize;

    public function index() {
        $this->redirect();
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function left() {
        $user = $this->get_user_or_redirect();
        $card = CtrlTools::get_object_or_redirect($_POST, "id", "Card");
        $this->authorize_or_redirect($user, $card->get_board());

        $card->move_left();

        $this->redirect("board", "board", $card->get_board_id());
    }

    public function right() {
        $user = $this->get_user_or_redirect();
        $card = CtrlTools::get_object_or_redirect($_POST, "id", "Card");
        $this->authorize_or_redirect($user, $card->get_board());

        $card->move_right();

        $this->redirect("board", "board", $card->get_board_id());

    }

    public function up() {
        $user = $this->get_user_or_redirect();
        $card = CtrlTools::get_object_or_redirect($_POST, "id", "Card");
        $this->authorize_or_redirect($user, $card->get_board());

        $card->move_up();

        $this->redirect("board", "board", $card->get_board_id());
    }

    public function down() {
        $user = $this->get_user_or_redirect();
        $card = CtrlTools::get_object_or_redirect($_POST, "id", "Card");
        $this->authorize_or_redirect($user, $card->get_board());

        $card->move_down();

        $this->redirect("board", "board", $card->get_board_id());
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function add() {
        $user = $this->get_user_or_redirect();
        $board = CtrlTools::get_object_or_redirect($_POST, "board_id", "Board");
        $this->authorize_or_redirect($user, $board);


        if (!empty($_POST["title"])) {
            $column_id = $_POST["column_id"];
            $title = $_POST["title"];

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
        $user = $this->get_user_or_redirect();
        $card = CtrlTools::get_object_or_redirect($_POST, "id", "Card");
        $this->authorize_or_redirect($user, $card->get_board());

        if(isset($_POST['body'])){
            $body = $_POST['body'];
            $card->set_body($body);
        }

        if(isset($_POST['title'])){
            $title = $_POST['title'];
            $card->set_title($title);
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
        $user = $this->get_user_or_redirect();
        $card = CtrlTools::get_object_or_redirect($_POST, "id", "Card");
        $this->authorize_or_redirect($user, $card->get_board());

        if($card!=null){
            $this->redirect("card","delete_confirm",$card->get_id());
        }
    }

    public function delete_confirm(){
        $user = $this->get_user_or_redirect();
        $card = CtrlTools::get_object_or_redirect($_GET, "param1", "Card");
        $this->authorize_or_redirect($user, $card->get_board());

        (new View("delete_confirm"))->show(array(
            "user"=>$user,
            "instance"=>$card
        ));
    }

    public function remove() {
        $user = $this->get_user_or_redirect();
        $card = CtrlTools::get_object_or_redirect($_GET, "id", "Card");
        $this->authorize_or_redirect($user, $card->get_board());

        if(isset($_POST["delete"])) {
            Card::decrement_following_cards_position($card);
            $card->delete();
        }

        $this->redirect("board", "board", $card->get_column()->get_board_id());
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    
    public function edit_link(){
        $user = $this->get_user_or_redirect();
        $card = CtrlTools::get_object_or_redirect($_POST, "id", "Card");
        $this->authorize_or_redirect($user, $card->get_board());

        $this->redirect("card", "edit", $card->get_id());
    }

    public function edit(){
        $user = $this->get_user_or_redirect();
        $card = CtrlTools::get_object_or_redirect($_GET, "param1", "Card");
        $this->authorize_or_redirect($user, $card->get_board());

        $comments = $card->get_comments();
        $edit="yes";

        if(isset($_GET['param2'])){
            (new View("card_edit"))->show(array(
                "user" => $user,
                "card" => $card,
                "comment" => $comments,
                "show_comment" => $_GET['param2'],
                "edit" => $edit,
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
                "errors" => ValidationError::get_error_and_reset()
                )
            );
            die;
        }

    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function view(){
        $user = $this->get_user_or_redirect();
        $card = CtrlTools::get_object_or_redirect($_POST, "param1", "Card");
        $this->authorize_or_redirect($user, $card->get_board());

        $comments = $card->get_comments();

        if(isset($_GET['param2'])){
            (new View("card"))->show(array(
                "user" => $user,
                "card" => $card,
                "comment" => $comments,
                "show_comment" => $_GET['param2'],
                "errors" => ValidationError::get_error_and_reset()
                )
            );
            die;
        } else {
            (new View("card"))->show(array(
                "user" => $user,
                "card" => $card,
                "comment" => $comments,
                "errors" => ValidationError::get_error_and_reset()
                )
            );
            die;
        }

    }

}
