<?php

require_once "autoload.php";

class ControllerCard extends ExtendedController {

    public function index() {
        $this->redirect();
    }

    public function add() {
        $column = $this->get_object_or_redirect("column_id", "Column");
        $user = $this->authorize_for_board_or_redirect($column->get_board());

        if (!Post::empty("title")) {
            $column_id = Post::get("column_id");
            $title = Post::get("title");

            $card = Card::new($title, $user, $column_id);

            $error = new DisplayableError($card, "add", $column_id);
            $error->set_messages(CardDao::validate($card));
            Session::set_error($error);

            if($error->is_empty()){
                CardDao::insert($card);
            }
        }
        $this->redirect("board", "view", $column->get_board_id());

    }

    public function view(){
        $card = $this->get_object_or_redirect("param1", "Card");
        $user = $this->authorize_for_board_or_redirect($card->get_board());

        $comments = $card->get_comments();

        (new View("card"))->show(array(
                "user" => $user,
                "card" => $card,
                "comment" => $comments,
                "breadcrumb" => new BreadCrumb(array($card->get_board(), $card)),
                "errors" => Session::get_error()
            )
        );
    }

    public function edit(){
        $param_name = "id";

        // Redirect (get, donc) en cas d'ajout de commentaires ou de participants pendant card/edit ou card/view
        if (Request::is_get()) {
            $param_name = "param1";
        }

        $card = $this->get_object_or_redirect($param_name, "Card");
        $user = $this->authorize_for_board_or_redirect($card->get_board());

        if (Post::isset("confirm")) {
            if (Post::isset("body")) {
                $card->set_body(Post::get("body"));
            }

            if (Post::isset("title")) {
                $card->set_title(Post::get("title"));
            }

            if(!Post::empty("due_date")) {
                $card->set_dueDate(new Datetime(Post::get("due_date")));
            }

            $error = new DisplayableError();
            $error->set_messages(CardDao::validate($card, $update=true));
            Session::set_error($error);

            if($error->is_empty()){
                $card->set_modifiedAt(new DateTime());
                CardDao::update($card);

                $params = $this->explode_params(Post::get("redirect_url"));
                $this->redirect(...$params);
            }
        }

        (new View("card_edit"))->show(array(
                "user" => $user,
                "card" => $card,
                "redirect_url" => Post::get("redirect_url", "board"),
                "breadcrumb" => new BreadCrumb(array($card->get_board(), $card)),
                "errors" => Session::get_error()
            )
        );
    }

    public function delete() {
        $card = $this->get_object_or_redirect("id", "Card");
        $user = $this->authorize_for_board_or_redirect($card->get_board());

        if(Post::isset("confirm")) {
            CardDao::decrement_following_cards_position($card);
            CardDao::delete($card);
            $this->redirect("board", "view", $card->get_board_id());
        }

        (new View("delete_confirm"))->show(array(
            "user" =>$user,
            "cancel_url" => "board/view/".$card->get_board_id(),
            "instance" => $card
        ));
    }


    /* --- Moves --- */

    public function left() {
        $card = $this->get_object_or_redirect("id", "Card");
        $this->authorize_for_board_or_redirect($card->get_board());

        $card->move_left();

        $this->redirect("board", "view", $card->get_board_id());
    }

    public function right() {
        $card = $this->get_object_or_redirect("id", "Card");
        $this->authorize_for_board_or_redirect($card->get_board());

        $card->move_right();

        $this->redirect("board", "view", $card->get_board_id());

    }

    public function up() {
        $card = $this->get_object_or_redirect("id", "Card");
        $this->authorize_for_board_or_redirect($card->get_board());

        $card->move_up();

        $this->redirect("board", "view", $card->get_board_id());
    }

    public function down() {
        $card = $this->get_object_or_redirect("id", "Card");
        $this->authorize_for_board_or_redirect($card->get_board());

        $card->move_down();

        $this->redirect("board", "view", $card->get_board_id());
    }
}
