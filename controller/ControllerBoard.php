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

        if(isset($_POST["title"])) {
            $errors = $this->add();
        }

        if($user) {
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

    public function add() {
        $user = $this->get_user_or_redirect();
        $errors = [];

        if (isset($_POST["title"])) {
            $title = $_POST["title"];
            $board = new Board($title, $user);
            $errors = $board->validate();

            if(empty($errors)) {
                $board->insert();
                $this->redirect("board", "board", $board->get_id());
            }
        }
        return $errors;
    }




    public function board() {
        $user = $this->get_user_or_redirect();
        $board = [];
        $columns = [];
        $errors = [];

        if(isset($_POST["column"])) {
            $errors = $this->add_column();
            if(!empty($errors)) {
                $board_id = $errors["board_id"];
            }
        }

        elseif(isset($_GET["param1"])) {
            $board_id = $_GET["param1"];
        }

        $board = Board::get_by_id($board_id);
 
        if(!is_null($board)) {
            $columns = $board->get_columns();
            (new View("board"))->show(array(
                "user"=>$user, 
                "board" => $board, 
                "columns" => $columns,
                "errors" => $errors
                )
            );
        }
        
        else {
            $this->redirect("board", "index");
        }
    }

    public function add_column() {
        $user = $this->get_user_or_redirect();
        $errors = [];

        if (!empty($_POST["title"])) {
            $title = $_POST["title"];
            $board_id = $_POST["id"];
            $board = Board::get_by_id($board_id);
            $column = Column::create_new($title, $board);
            $errors = $column->validate();

            if(empty($errors)) {
                $column->insert();
                $this->redirect("board", "board", $board_id);
            }
        }
        return $errors;
    }




     // si pas de colonne -> delete -> redirect index
    // sinon -> delete_confirm
    public function delete() {
        $user = $this->get_user_or_redirect();
        if(isset($_POST['id'])) {
            $board_id = $_POST['id'];
            $instance = Board::get_by_id($board_id);
            $columns = $instance->get_columns();

            if (count($columns) == 0) { 
                $instance->delete();
                $this->redirect("board", "index");        
            }

            else {
                $this->redirect("board", "delete_confirm", $board_id);
            }
        } 
        else{
            $this->redirect("board", "index");
        }
    }

    //mise en place de view_delete_confirm
    public function delete_confirm() {
        $user = $this->get_user_or_redirect();
        if(!empty($_GET["param1"])) {
            $board_id = $_GET["param1"];
            $instance = Board::get_by_id($board_id);
            if(!is_null($instance)) {
                (new View("delete_confirm"))->show(array("user" => $user, "instance" => $instance));
                die;
            }
            else {
                $this->redirect("board", "board", $board_id);
            }
        }
        $this->redirect("board", "index");
    }


    //exécution du delete ou cancel de delete_confirm
    public function remove() {
        if(isset($_POST["id"])) {
            $board = Board::get_by_id($_POST["id"]);
            if(!isset($_POST["delete"])) {
                $board->delete();
                $this->redirect("board", "board", $_POST["id"]);
            }
            $board->delete();
        }
        $this->redirect("board", "index");
    }
}


