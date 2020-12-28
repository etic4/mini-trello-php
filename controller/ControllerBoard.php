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

        if ($user) {
            $owners = $user->get_own_boards();
            $others = $user->get_others_boards();
        }

        (new View("boardlist"))->show(array(
            "user"=>$user, 
            "owners" => $owners,
            "others" => $others)
        );
    }

    public function board() {
        $user = $this->get_user_or_redirect();

        if(isset($_GET["param1"])) {
            $id = $_GET["param1"];
            $board = Board::get_by_id($id);

            (new View("board"))->show(array(
                "user"=>$user, 
                "board" => $board)
            );
        }
        else {
            $this->redirect("board", "index");
        }
           
    }

    public function add() {
        //TODO validate title -> unique!!!
        $user = $this->get_user_or_redirect();
        if (!empty($_POST["title"])) {
            $board = new Board($_POST["title"], $user);
            $board->insert();
        }
        $this->redirect("board", "index");
    }

    public function delete() {
        $user = $this->get_user_or_redirect();
        if(isset($_POST['id'])) {
            $board = Board::get_by_id($_POST['id']);

            if (isset($_POST["delete"]) || Column::get_columns_count($board) == 0) {
                $board->delete();
                $this->redirect("board", "index");        
            }

            elseif (isset($_POST["cancel"])) {
                $this->redirect("board", "index");
            }

            else {
                (new View("delete_confirm"))->show(array("instance" => $board));
            } 
        } 

    }


}