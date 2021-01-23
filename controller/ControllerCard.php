<?php

require_once "framework/Controller.php";
require_once "model/Card.php";
require_once "model/User.php";
require_once "CtrlTools.php";
require_once "ValidationError.php";

class ControllerCard extends Controller {

    public function index() {
        $this->redirect();
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function left() {
        $user = $this->get_user_or_redirect();
        if (isset($_POST["id"])) {
            $card_id = $_POST["id"];
            $card = Card::get_by_id($card_id);

            $card->move_left();

            $this->redirect("board", "board", $card->get_board_id());
        }
    }

    public function right() {
        $user = $this->get_user_or_redirect();
        if (isset($_POST["id"])) {
            $card_id = $_POST["id"];
            $card = Card::get_by_id($card_id);

            $card->move_right();

            $this->redirect("board", "board", $card->get_board_id());
        }

    }

    public function up() {
        $user = $this->get_user_or_redirect();
        if (isset($_POST["id"])) {
            $card_id = $_POST["id"];
            $card = Card::get_by_id($card_id);

            $card->move_up();

            $this->redirect("board", "board", $card->get_board_id());
        }
    }

    public function down() {
        $user = $this->get_user_or_redirect();
        if (isset($_POST["id"])) {
            $card_id = $_POST["id"];
            $card = Card::get_by_id($card_id);

            $card->move_down();

            $this->redirect("board", "board", $card->get_board_id());
        }
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function add() {
        $user = $this->get_user_or_redirect();
        if (isset($_POST["board_id"])) {
            $board_id = $_POST["board_id"];
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
            $this->redirect("board", "board", $board_id);
        }
        $this->redirect();
    }
        
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function update(){
        $user = $this->get_user_or_redirect();
        $card = null;
        if (isset($_POST['id'])) {
            $card_id = $_POST['id'];
            $card = Card::get_by_id($card_id);

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
                $this->redirect("card", "view", $card_id);
            }

            $this->redirect("card", "edit", $card_id);
        }

        $this->redirect();
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function delete() {
        $user = $this->get_user_or_redirect();
        if (isset($_POST['id'])) {
            $card_id = $_POST['id'];
            $card = Card::get_by_id($card_id);

            if($card!=null){
                $this->redirect("card","delete_confirm",$card->get_id());
            }
        }
        $this->redirect();
    }

    public function delete_confirm(){
        $user = $this->get_user_or_redirect();
        if (isset($_GET['param1'])) {
            $card_id = $_GET['param1'];
            $card = Card::get_by_id($card_id);

            if(!is_null($card)){
                (new View("delete_confirm"))->show(array(
                    "user"=>$user, 
                    "instance"=>$card
                    ));
                die;
            }
        }

        $this->redirect();
    }

    public function remove() {
        if(isset($_POST["id"])) {
            $card_id = $_POST["id"];
            $card = Card::get_by_id($card_id);

            if(isset($_POST["delete"])) {
                Card::decrement_following_cards_position($card);
                $card->delete();
            }

            $this->redirect("board", "board", $card->get_column()->get_board_id());
        }

        $this->redirect();
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    
    public function edit_link(){
        $user = $this->get_user_or_redirect();
        if (isset($_POST['id'])) {
            $card_id = $_POST['id'];
            $card = Card::get_by_id($card_id);

            if($card != null) {
                $this->redirect("card", "edit", $card->get_id());
            }
        }
        $this->redirect();
    }

    public function edit(){
        $user = $this->get_user_or_redirect();
        $card = null;

        if (isset($_GET['param1'])) { 
            $card_id = $_GET['param1'];
            $card = Card::get_by_id($card_id);

            if(!is_null($card)) {
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
        } else {
            $this->redirect();
        }
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function view(){
        $user = $this->get_user_or_redirect();
        $card = null;

        if (isset($_GET['param1'])) { 
            $card_id = $_GET['param1'];
            $card = Card::get_by_id($card_id);

            if(!is_null($card)) {
                $comments = $card->get_comments();

                if(isset($_GET['param2'])){
                    (new View("card"))->show(array(
                        "user" => $user, 
                        "card" => $card, 
                        "comment" => $comments,
                        "show_comment" => $_GET['param2']
                        )
                    );
                    die;
                } else {
                    (new View("card"))->show(array(
                        "user" => $user, 
                        "card" => $card, 
                        "comment" => $comments
                        )
                    );
                    die;
                }
            }
        } else {
            $this->redirect();
        }
    } 
}
