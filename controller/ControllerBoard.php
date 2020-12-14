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

        (new View("boardlist"))->show(array("user"=>$user, "owners" => $owners,
            "others" => $others));
    }

    public function board() {
        $user = $this->get_user_or_redirect();
        $board = [];
        $columns = [];

        if(isset($_GET["param1"])) {
            $id = $_GET["param1"];
            $board = Board::get_by_id($id);
            $columns = $board->get_columns();
        }

        (new View("board"))->show(array("user"=>$user, "board" => $board, "columns" => $columns));   

    }
}