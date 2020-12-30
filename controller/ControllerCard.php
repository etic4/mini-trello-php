<?php

require_once "framework/Controller.php";
require_once "model/Card.php";
require_once "model/User.php";
require_once "CtrlTools.php";


class ControllerCard extends Controller {

    public function index() {
        // TODO: Implement index() method.
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
            $card->insert(); 
        }
        $this->redirect("board", "board", $_POST["board_id"]);
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function delete(){
        $user=$this->get_user_or_redirect();
        
        if(isset($_POST['id'])){
            $card=Card::get_by_id($_POST['id']);
            $column=Column::get_by_id($card->get_column()->get_id());
            if(isset ($_POST['delete'])){
                Card::decrement_following_cards_position($card);
                $card->delete();
            }
            $this->redirect("board","board", $column->get_board()->get_id());
        }
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
            if( !($_REQUEST['edit']=="Cancel")){
                $card->update();
            }
        }
        $this->redirect("board","board", $card->get_board_id());
    }

    public function delete_confirm(){
        $user=$this->get_user_or_redirect();
        $card=null;
        if (isset($_POST['id'])) {
            $card=Card::get_by_id($_POST['id']);
        }
        (new View("delete_confirm"))->show(array(
            "user"=>$user, 
            "instance"=>$card
            ));
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function edit(){
        $user=$this->get_user_or_redirect();
        $card=null;
        $board=null;
        $column=null;
        if (isset($_GET['param1'])) { 
            $idcard=$_GET['param1'];
            $card=Card::get_by_id($idcard);
            if(!is_null($card)) {
                $column = Column::get_by_id($card->get_column_id());
                $board = Board::get_by_id($column->get_board_id());
                $comments = $card->get_comments();
                (new View("card_edit"))->show(array(
                    "user" => $user, 
                    "board" => $board, 
                    "column" => $column, 
                    "card" => $card, 
                    "comment" => $comments
                    )
                );
            }
            else {
                $this->redirect("board", "index");
            }
        }
        else {
        $this->redirect("board", "index");
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
                if(isset($_GET['param2'])){
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
                $this->redirect("board", "index");
            }
        }
        else {
        $this->redirect("board", "index");
        }
    } 
    
}
