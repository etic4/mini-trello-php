<?php

require_once "autoload.php";


class ControllerComment extends ExtendedController {

    public function index() {
        $this->redirect();
    }

    public function delete() {
        $comment = $this->get_object_or_redirect("id", "Comment");
        $user = $this->authorize_for_board_or_redirect($comment->get_board());

        if ($user->can_delete_comment($comment)) {
            CommentDao::delete($comment);
        }

        $this->redirect("card", "view", $comment->get_card_id());
    }


    public function edit() {
        if (Request::is_get()) {
            $comment = $this->get_object_or_redirect("param1", "Comment");
            $board = $comment->get_board();
            $user = $this->authorize_for_board_or_redirect($board);

            (new View("comment_edit"))->show(array(
                    "user" => $user,
                    "comment" => $comment,
                    "breadcrumb" => new BreadCrumb(array($board, $comment->get_card()), "Edit comment"),
                    "errors" => Session::get_error()
                )
            );
        }
        else {
            $comment = $this->get_object_or_redirect("id", "Comment");
            $this->authorize_for_board_or_redirect($comment->get_board());

            $body = Post::get("body");

            /* TODO: validation comments: ne doit pas accepter comments vides ou composés d'espaces*/
            if (!empty($body) && $body != $comment->get_body()) {
                $comment->set_body($body);
                CommentDao::update($comment);
            }
            $this->redirect("card", "view", $comment->get_card_id());
        }
    }

    public function add(){
        $card = $this->get_object_or_redirect("card_id", "Card");
        $user = $this->authorize_for_board_or_redirect($card->get_board());

        // si 'body' est vide, ne fait rien, pas besoin de message
        // TODO: quand-même vérifier que pas que espaces
        if(!Post::empty("body")) {
            $comment = new Comment(Post::get("body"), $user, $card);
            CommentDao::insert($comment);
        }
        $this->redirect("card", "view", $comment->get_card_id());
    }
}
