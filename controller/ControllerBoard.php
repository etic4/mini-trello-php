<?php
/**/
require_once "framework/Controller.php";
require_once "model/User.php";
require_once "model/Board.php";


class ControllerBoard extends Controller {

    public function index() {
        $user = $this->get_user_or_false();
        $owners = [];
        $others = [];
        $errors = [];

        if ($user) {
            $owners = $user->get_own_boards();
            $others = $user->get_others_boards();
        }

        (new View("boardlist"))->show(array(
            "user"=>$user, 
            "owners" => $owners,
            "others" => $others,
            "errors" => $errors
            )
        );
    }

    public function board() {
        $user = $this->get_user_or_redirect();
        $board = [];
        $columns = [];

        if(isset($_GET["param1"])) {
            $id = $_GET["param1"];
            $board = Board::get_by_id($id);
            if(!is_null($board)) {
                $columns = $board->get_columns();
                (new View("board"))->show(array(
                    "user"=>$user, 
                    "board" => $board, 
                    "columns" => $columns
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

    public function add() {
        $user = $this->get_user_or_redirect();
        $errors = [];
        if (isset($_POST["title"])) {
            $title = $_POST["title"];
            $board = new Board($title, $user, null, new DateTime(), null);
            $errors = $board->validate();
            if(empty($errors)) {
                $board = $board->insert();
                $this->redirect("board", "add_board", $board->get_id());
            }
            else{
            $owners = $user->get_own_boards();
            $others = $user->get_others_boards();
            (new View("boardlist"))->show(array(
                "user"=>$user, 
                "owners" => $owners,
                "others" => $others,
                "errors" => $errors
                ));
            }
        }
    }

    public function add_board() {
        $user = $this->get_user_or_redirect();
        if(!empty($_GET["param1"])) {
            $board_id = $_GET["param1"];
            $this->redirect("board", "board", $board_id);
        }
    }

    public function delete() {
        $user = $this->get_user_or_redirect();
        if(isset($_POST['id'])) {
            $board_id = $_POST['id'];
            $instance = Board::get_by_id($board_id);
            $columns = $instance->get_columns();

            if (count($columns) == 0) { 
                $instance->delete();
                $this->redirect("board", "delete_board", $board_id);        
            }

            (new View("delete_confirm"))->show(array("user" => $user, "instance" => $instance));
             
        } 
        echo "hello";
        //$this->redirect("board", "index");
    }

    public function delete_board() {
        if(!empty($_GET["param1"])) {
            $board_id = $_GET["param1"];
            $this->redirect("board", "board", $board_id);
        }
        $this->redirect("board", "index");
    }

    public function delete_confirm() {
        if(!empty($_POST["id"])) {
            $board_id = $_POST['id'];
            $instance = Board::get_by_id($board_id);
            $columns = $instance->get_columns();
            if(isset($_POST["delete"])) {
                $instance->delete();
                $this->redirect("board", "delete_board");
            }
            elseif(isset($_POST["cancel"])) {
                $this->redirect("board", "delete_board", $board_id);
            }
        }
        $this->redirect("board", "index");
    }


}


