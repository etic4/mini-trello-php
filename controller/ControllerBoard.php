<?php
/**/
require_once "framework/Controller.php";
require_once "model/User.php";
require_once "model/Board.php";
require_once "CtrlTools.php";


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

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function board() {
        $user = $this->get_user_or_redirect();
        $board = [];
        $columns = [];
        $errors = [];

        if(isset($_POST['instance'])) {
            $board_id = $_POST['id'];

            if($_POST['instance'] == "board") {
                $errors = $this->edit();
            }

            if($_POST['instance'] == "column") {
                if($_POST['action'] == "add") {
                    $errors = $this->add_column();
                }

                elseif($_POST['action'] == 'edit') {
                    $errors = $this->edit_column();
                }

            }

            if($_POST['instance'] == "card") {

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

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    // ajout nouveau Board
    public function add() {
        $user = $this->get_user_or_redirect();
        $errors = [];

        if (isset($_POST["title"])) {
            $title = $_POST["title"];
            $board = new Board($title, $user, null, new DateTime(), null);
            $errors = $board->validate();

            if(empty($errors)) {
                $board->insert();
                $this->redirect("board", "board", $board->get_id());
            }
        }
        return $errors;
    }

    // ajout nouvelle Column
    public function add_column() {
        $user = $this->get_user_or_redirect();
        $errors = [];

        if (!empty($_POST["title"])) {
            $title = $_POST["title"];
            $board_id = $_POST["id"];
            $action = $_POST["action"];
            $board = Board::get_by_id($board_id);
            $column = Column::create_new($title, $board);
            $errors = $column->validate($action);

            if(empty($errors)) {
                $column->insert();
                $this->redirect("board", "board", $board_id);
            }
        }
        return $errors;
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    //edit titre Board
    public function edit() {
        $user = $this->get_user_or_redirect();
        $errors = [];

        if (!empty($_POST["title"])) {
            $title = $_POST["title"];
            $board_id = $_POST["id"];
            $board = Board::get_by_id($board_id);
            $board->set_title($title);
            $errors = $board->validate();

            if(empty($errors)) {
                $board->update();
                $this->redirect("board", "board", $board_id);
            }

        }
        return $errors;
    }

    // edit titre Column
    public function edit_column() {
        $user = $this->get_user_or_redirect();
        $errors = [];

        if (!empty($_POST["title"])) {
            $title = $_POST["title"];
            $column_id = $_POST["column_id"];
            $action = $_POST["action"];
            $column = Column::get_by_id($column_id);
            $column->set_title($title);
            $errors = $column->validate($action);

            if(empty($errors)) {
                $column->update();
                $this->redirect("board", "board", $column->get_board_id());
            }

        }
        return $errors;
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

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


