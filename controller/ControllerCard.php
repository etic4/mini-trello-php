<?php

require_once "autoload.php";

class ControllerCard extends ExtendedController {

    public function index() {
        $this->redirect();
    }

    public function add() {
        $column = $this->get_or_redirect("Column", "column_id");
        $user = $this->authorized_or_redirect(Permissions::view($column));

        if (!Post::empty("title")) {
            $column_id = Post::get("column_id");
            $title = Post::get("title");

            $card = Card::new($title, $user, $column_id);

            $this->authorized_or_redirect(Permissions::add($card));

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
        $card = $this->get_object_or_redirect();
        $user = $this->authorized_or_redirect(Permissions::view($card));

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
        $card = $this->get_object_or_redirect();
        $user = $this->authorized_or_redirect(Permissions::edit($card));

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

                $params = explode("/", Post::get("redirect_url"));
                $this->redirect(...$params);
            }
        }

        (new View("card_edit"))->show(array(
                "user" => $user,
                "card" => $card,
                "redirect_url" => str_replace("_", "/", Get::get("param2", "board/view/". $card->get_board_id())) ,
                "breadcrumb" => new BreadCrumb(array($card->get_board(), $card)),
                "errors" => Session::get_error()
            )
        );
    }

    public function delete() {
        $card = $this->get_object_or_redirect();
        $this->authorized_or_redirect(Permissions::delete($card));

        if(Post::isset("confirm")) {
            CardDao::decrement_following_cards_position($card);
            CardDao::delete($card);
            $this->redirect("board", "view", $card->get_board_id());
        }
        $this->redirect("card", "delete_confirm", $card->get_id());

    }

    public function delete_confirm() {
        $card = $this->get_object_or_redirect();
        $user = $this->authorized_or_redirect(Permissions::delete($card));

        (new View("delete_confirm"))->show(array(
            "user" =>$user,
            "cancel_url" => "board/view/".$card->get_board_id(),
            "instance" => $card
        ));
    }


    /* --- Moves --- */

    public function left() {
        $card = $this->get_object_or_redirect();
        $this->authorized_or_redirect(Permissions::view($card));

        $card->move_left();

        $this->redirect("board", "view", $card->get_board_id());
    }

    public function right() {
        $card = $this->get_object_or_redirect();
        $this->authorized_or_redirect(Permissions::view($card));

        $card->move_right();

        $this->redirect("board", "view", $card->get_board_id());

    }

    public function up() {
        $card = $this->get_object_or_redirect();
        $this->authorized_or_redirect(Permissions::view($card));

        $card->move_up();

        $this->redirect("board", "view", $card->get_board_id());
    }

    public function down() {
        $card = $this->get_object_or_redirect();
        $this->authorized_or_redirect(Permissions::view($card));

        $card->move_down();

        $this->redirect("board", "view", $card->get_board_id());
    }
}
