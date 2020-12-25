<?php

require_once "framework/Controller.php";
require_once "model/Card.php";
require_once "model/User.php";


class ControllerCard extends Controller {

    public function index() {
        // TODO: Implement index() method.
    }

    public function left() {
        $user = $this->get_user_or_redirect();
        if (isset($_POST["id"])) {
            $cardId = $_POST["id"];
            $card = Card::get_by_id($cardId);
            $column = $card->get_column_inst();
            $column->move_left($card);
            $this->redirect("board", "board", $column->get_board_inst()->get_id());
        }
    }

    public function right() {
        $user = $this->get_user_or_redirect();
        if (isset($_POST["id"])) {
            $cardId = $_POST["id"];
            $card = Card::get_by_id($cardId);
            $column = $card->get_column_inst();
            $column->move_right($card);

            $this->redirect("board", "board", $column->get_board_inst()->get_id());
        }

    }

    public function up() {
        $user = $this->get_user_or_redirect();
        if (isset($_POST["id"])) {
            $cardId = $_POST["id"];
            $card = Card::get_by_id($cardId);
            $column = $card->get_column_inst();
            $column->move_up($card);

            $this->redirect("board", "board", $column->get_board_inst()->get_id());
        }
    }

    public function down() {
        $user = $this->get_user_or_redirect();
        if (isset($_POST["id"])) {
            $cardId = $_POST["id"];
            $card = Card::get_by_id($cardId);
            $column = $card->get_column_inst();
            $column->move_down($card);

            $this->redirect("board", "board", $column->get_board_inst()->get_id());
        }
    }

    public function add() {
        $user = $this->get_user_or_redirect();
        if(!empty($_POST["title"])) {
            $title = $_POST["title"];
            $column_id = $_POST["column_id"];
            $card = Card::create_new($title, $user, $column_id); 
            $card->insert(); 
        }
        $board = $_POST["board_id"];
        $this->redirect("board", "board", $board);
    }

    public function delete(){
        $user=$this->get_user_or_redirect();
        
        if(isset($_POST['id'])){            
            $idcard=$_POST['id'];
            $instance=Card::get_by_id($idcard);
            $column=Column::get_by_id($instance->get_column());
            $owner=Board::get_board_owner($instance);
            if(isset ($_POST['delete']) && ( $user == $instance->get_author() || $user == $owner)){
                Card::update_card_position($instance);
                $instance->delete();
            }
            
            $this->redirect("board","board",$column->get_board());
        }
    }
    
    public function update(){
        $user=$this->get_user_or_redirect();
        $card=null;
        $column=null;
        if (isset($_POST['id'])) { 
            $idcard=$_POST['id'];
            $card=Card::get_by_id($idcard);
            if(isset($_POST['body'])){
                $card->set_body($_POST['body']);
            }
            if(isset($_POST['title'])){
                $card->set_title($_POST['title']);
            }
            if( !($_REQUEST['edit']=="Cancel")){
                $card->update();
            }
            $column=Column::get_by_id($card->get_column());
        }
        $this->redirect("board","board",$column->get_board()); 
    }

    public function delete_confirm(){
        $user=$this->get_user_or_redirect();
        $instance=null;
        $cant_delete=true;
        $owner=null;
        if (isset($_POST['id'])) { 
            $idcard=$_POST['id'];
            $instance=Card::get_by_id($idcard);
            $comments=Comment::get_comments_from_card($idcard);
            $instance->set_comments($comments);
            $owner=Board::get_board_owner($instance);
            /*
            on peut supprimer toutes les cartes sans restriction de propriété
            if( $user == $instance->get_author() || $user == $owner) {
                $cant_delete=false;
            }
            */
        }
        (new View("delete_confirm"))->show(array(
            "user"=>$user, 
            "instance"=>$instance
            //, "cant_delete"=>$cant_delete
            ));
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
                $column=Column::get_by_id($card->get_column());
                $board=Board::get_by_id($column->get_board());
                $comments=Comment::get_comments_from_card($idcard);
                $card->set_comments($comments);
                (new View("card_edit"))->show(array("user"=>$user, "card"=>$card, "board"=>$board, "column"=>$column));
            }
            else {
                $this->redirect("board", "index");
            }
        }
        else {
        $this->redirect("board", "index");
        }
    }

    public function view(){

        $user=$this->get_user_or_redirect();
        $card=null;
        $board=null;
        $column=null;
        if (isset($_GET['param1'])) { 
            $idcard=$_GET['param1'];
            $card=Card::get_by_id($idcard);
            if(!is_null($card)) {
                $column=Column::get_by_id($card->get_column());
                $board=Board::get_by_id($column->get_board());
                $comments=Comment::get_comments_from_card($idcard);
                $card->set_comments($comments);
                (new View("card"))->show(array("user"=>$user, "card"=>$card, "board"=>$board, "column"=>$column));
            }
            else {
                $this->redirect("board", "index");
            }
        }
        else {
        $this->redirect("board", "index");
        }
    } 
    
}
