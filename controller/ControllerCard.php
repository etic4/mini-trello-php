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
            $card = Card::get_by_id($_POST["id"]);
            $card->move_left();
            $this->redirect("board", "board", $card->get_board_id());
        }
    }

    public function right() {
        $user = $this->get_user_or_redirect();
        if (isset($_POST["id"])) {
            $card = Card::get_by_id($_POST["id"]);
            $card->move_right();
            $this->redirect("board", "board", $card->get_board_id());
        }

    }

    public function up() {
        $user = $this->get_user_or_redirect();
        if (isset($_POST["id"])) {
            $card = Card::get_by_id($_POST["id"]);
            $card->move_up();
            $this->redirect("board", "board", $card->get_board_id());
        }
    }

    public function down() {
        $user = $this->get_user_or_redirect();
        if (isset($_POST["id"])) {
            $card = Card::get_by_id($_POST["id"]);
            $card->move_down();
            $this->redirect("board", "board", $card->get_board_id());
        }
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function add() {
        $user = $this->get_user_or_redirect();
        if(!empty($_POST["title"])) {
            $column = Column::get_by_id($_POST["column_id"]);
            $card = Card::create_new($_POST["title"], $user, $column);
            $error = new ValidationError($card, "add");
            $error->set_messages($card->validate());
            $error->add_to_session();
            if($error->is_empty()){                
                $card->insert(); 
            }
            $this->redirect("board","board",$card->get_board_id());
        }
        $this->redirect();
    }
        
    public function update(){
        $user=$this->get_user_or_redirect();
        $card=null;
        if (isset($_POST['id'])) {
            $card=Card::get_by_id($_POST['id']);
            if(isset($_POST['body'])){
                $card->set_body($_POST['body']);
            }
            if(isset($_POST['title'])){
                $card->set_title($_POST['title']);
            }
            $error = new ValidationError($card, "update");
            $error->set_messages($card->validate_update());
            $error->add_to_session();
            if($error->is_empty()){  
                $card->update();
            }
            $this->redirect("card","view",$card->get_id());
        }
        $this->redirect("board","index");
    }

    public function delete() {
        $user=$this->get_user_or_redirect();
        if (isset($_POST['id'])) {
            $card=Card::get_by_id($_POST['id']);
            if(!is_null($card)){
                $this->redirect("card","delete_confirm",$card->get_id());
            }
        }
        $this->redirect("board","index");
    }

    public function delete_confirm(){
        $user=$this->get_user_or_redirect();
        if (isset($_GET['param1'])) {
            $card=Card::get_by_id($_GET['param1']);
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

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    
    public function edit_link(){
        $user=$this->get_user_or_redirect();
        if (isset($_POST['id'])) {
            $card=Card::get_by_id($_POST['id']);
            if($card!=null){
                $this->redirect("card","edit",$card->get_id());
            }
        }
        $this->redirect("board","index");
    }
    public function edit(){
        $user=$this->get_user_or_redirect();
        $card=null;
        $board=null;
        $column=null;
        if (isset($_GET['param1'])) { 
            $idcard=$_GET['param1'];
            $card=Card::get_by_id($idcard);
            if(!is_null($card)) {
                if(isset($_GET['param2']) && Comment::can_edit($_GET['param2'], $user)){
                    $column = Column::get_by_id($card->get_column_id());
                    $board = Board::get_by_id($column->get_board_id());
                    $comments = $card->get_comments();
                    $edit="yes";
                    (new View("card_edit"))->show(array(
                        "user" => $user, 
                        "board" => $board, 
                        "column" => $column, 
                        "card" => $card, 
                        "comment" => $comments,
                        "show_comment" => $_GET['param2'],
                        "edit" => $edit
                        )
                    );
                }else{
                    $column = Column::get_by_id($card->get_column_id());
                    $board = Board::get_by_id($column->get_board_id());
                    $comments = $card->get_comments();
                    $edit="yes";
                    (new View("card_edit"))->show(array(
                        "user" => $user, 
                        "board" => $board, 
                        "column" => $column, 
                        "card" => $card, 
                        "comment" => $comments,
                        "edit" => $edit
                        )
                    );
                }
            }
            else {
                $this->redirect();
            }
        }
        else {
        $this->redirect();
        }
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function view(){
        $user=$this->get_user_or_redirect();
        $card=null;
        $board=null;
        $column=null;
        if (isset($_GET['param1'])) { 
            $idcard=$_GET['param1'];
            $card=Card::get_by_id($idcard);
            if(!is_null($card)) {
                if(isset($_GET['param2']) && Comment::can_edit($_GET['param2'], $user)){
                    $column = Column::get_by_id($card->get_column_id());
                    $board = Board::get_by_id($column->get_board_id());
                    $comments = $card->get_comments();
                    (new View("card"))->show(array(
                        "user" => $user, 
                        "board" => $board, 
                        "column" => $column, 
                        "card" => $card, 
                        "comment" => $comments,
                        "show_comment" => $_GET['param2']
                        )
                    );
                }else {
                    $column = Column::get_by_id($card->get_column_id());
                    $board = Board::get_by_id($column->get_board_id());
                    $comments = $card->get_comments();
                    (new View("card"))->show(array(
                        "user" => $user, 
                        "board" => $board, 
                        "column" => $column, 
                        "card" => $card, 
                        "comment" => $comments
                        )
                    );
                }
            }
            else {
                $this->redirect();
            }
        }
        else {
            $this->redirect();
        }
    } 
    public function remove() {
        if(isset($_POST["id"])) {
            $card = Card::get_by_id($_POST["id"]);
            if(!is_null($card)){
                if(isset($_POST["delete"]) ) {
                    Card::decrement_following_cards_position($card);
                    $card->delete();
                }
                $this->redirect("board", "board", $card->get_column()->get_board_id());
            }
        }
        $this->redirect();
    }
}
