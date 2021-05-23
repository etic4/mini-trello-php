<?php

require_once "autoload.php";

class ControllerCard extends ExtendedController {

    public function index() {
        $this->redirect();
    }

    public function add() {
        $column = $this->get_or_redirect_post("Column", "column_id");
        $user = $this->authorize_or_redirect(Permissions::view($column));

        if (!Post::empty("card_title")) {
            $column_id = Post::get("column_id");
            $title = Post::get("card_title");
            $card = Card::new($title, $user, $column_id);

            $this->authorize_or_redirect(Permissions::add($card));

            $error = new DisplayableError($card, "add", $column_id);
            $error->set_messages((new CardValidation())->validate_add($title, $column->get_board()));
            Session::set_error($error);

            if($error->is_empty()){
                CardDao::insert($card);
            }
        }
        $this->redirect("board", "view", $column->get_board_id());
    }

    public function view(){
        $card = $this->get_or_redirect_default();
        $user = $this->authorize_or_redirect(Permissions::view($card));

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
        $card = $this->get_or_redirect("Card", "card_id", "param1");
        $user = $this->authorize_or_redirect(Permissions::edit($card));

        $card_title = Post::get("card_title",$card->get_title());
        $body = Post::get("body", $card->get_body());

        if (Post::get("reset_date") == "on") {
            $due_date = null;
        } else {
            $due_date = Post::empty("due_date") ? $card->get_dueDate() : new Datetime(Post::get("due_date"));
        }

        if (Post::get("confirm") == "true") {
            $error = new DisplayableError();
            $error->set_messages((new CardValidation())->validate_edit($card_title, $due_date, $card));
            Session::set_error($error);

            if($error->is_empty()){
                $card->set_title($card_title);
                $card->set_body($body);
                $card->set_dueDate($due_date);
                $card->set_modifiedAt(new DateTime());

                CardDao::update($card);

                $params = explode("/", Post::get("redirect_url"));
                $this->redirect(...$params);
            }
        }

        (new View("card_edit"))->show(array(
                "user" => $user,
                "card" => $card,
                "card_title" => $card_title,
                "body" => $body,
                "due_date" => $due_date,
                "redirect_url" => str_replace("_", "/", Get::get("param2", "board/view/". $card->get_board_id())) ,
                "breadcrumb" => new BreadCrumb(array($card->get_board(), $card)),
                "errors" => Session::get_error()
            )
        );
    }

    public function delete() {
        $card = $this->get_or_redirect_default();
        $this->authorize_or_redirect(Permissions::delete($card));

        if(Post::get("confirm") == "true") {
            CardDao::decrement_following_cards_position($card);
            CardDao::delete($card);
            $this->redirect("board", "view", $card->get_board_id());
        }
        $this->redirect("card", "delete_confirm", $card->get_id());

    }

    public function delete_confirm() {
        $card = $this->get_or_redirect_default();
        $user = $this->authorize_or_redirect(Permissions::delete($card));

        (new View("delete_confirm"))->show(array(
            "user" =>$user,
            "cancel_url" => "board/view/".$card->get_board_id(),
            "instance" => $card
        ));
    }


    /* --- Moves --- */

    public function left() {
        $card = $this->get_or_redirect_default();
        $this->authorize_or_redirect(Permissions::view($card));

        $card->move_left();

        $this->redirect("board", "view", $card->get_board_id());
    }

    public function right() {
        $card = $this->get_or_redirect_default();
        $this->authorize_or_redirect(Permissions::view($card));

        $card->move_right();

        $this->redirect("board", "view", $card->get_board_id());

    }

    public function up() {
        $card = $this->get_or_redirect_default();
        $this->authorize_or_redirect(Permissions::view($card));

        $card->move_up();

        $this->redirect("board", "view", $card->get_board_id());
    }

    public function down() {
        $card = $this->get_or_redirect_default();
        $this->authorize_or_redirect(Permissions::view($card));

        $card->move_down();

        $this->redirect("board", "view", $card->get_board_id());
    }


    /* --- Services --- */

    public function card_title_is_unique_service() {
        if (!Post::all_non_empty("card_title", "board_id")) {
            echo "false";
            die;
        }

        $card_title = Post::get("card_title");

        if (!Post::empty("card_id")) {
            $card = $this->get_or_redirect_post("Card", "card_id");
            $this->authorize_or_redirect(Permissions::edit($card));

            $errors = (new CardValidation())->validate_title_unicity($card_title, $card->get_board(), $card);
        } else {
            $board = $this->get_or_redirect_post("Board", "board_id");
            $this->authorize_or_redirect(Permissions::view($board));

            $errors = (new CardValidation())->validate_title_unicity($card_title, $board);
        }

        echo count($errors) == 0 ? "true" : "false";
    }

    public function update_cards_positions_service() {
        $board = $this->get_or_redirect_post("Board", "board_id");
        $this->authorize_or_redirect(Permissions::view($board));

        if (!Post::empty("cards_list")) {
            CardDao::update_cards_position(Post::get("cards_list"));
        }
    }

    public function needs_delete_confirm_service() {
        $card = $this->get_or_redirect_default();
        $this->authorize_or_redirect(Permissions::delete($card));

        echo  "true";
    }

    public function get_card_service() {
        $card = $this->get_or_redirect_default();
        $this->authorize_or_redirect(Permissions::view($card));

        $response = [
            "title" => $card->get_title(),
            "author" => $card->get_author_fullName(),
            "created_interval" => ViewUtils::created_intvl($card),
            "modified_interval" => ViewUtils::modified_intvl($card),
            "board_url" => "board/view/" . $card->get_board_id(),
            "board_title" => $card->get_board_title(),
            "column_title" => $card->get_column_title(),
            "position" => $card->get_position(),
            "body" => $card->get_body(),
            "due_date" => ViewUtils::due_date_string($card->get_dueDate()),
            "participants" => array_map(fn($participant) => [
                "name" => $participant->get_fullName(),
                "email" => $participant->get_email()
                ],
                $card->get_participants()),
            "comments" => array_map(fn($comment) => [
                "body" => $comment->get_body(),
                "author" => $comment->get_author_fullName(),
                "time_published" => ViewUtils::most_recent_interval($comment)
                ],
                $card->get_comments()),
        ];
        echo json_encode($response);
    }
}
