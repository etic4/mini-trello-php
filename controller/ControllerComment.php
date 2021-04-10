<?php

require_once "autoload.php";


class ControllerComment extends ExtendedController {

    public function index() {
        $this->redirect();
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

        $params = $this->explode_params(Post::get("redirect_url"));
        $this->redirect(...$params);
    }

    public function edit() {
        $param_name = "id";
        if(Request::is_get()) {
            $param_name = "param1";
        }
        $comment = $this->get_object_or_redirect($param_name, "Comment");
        $user = $this->authorize_for_board_or_redirect($comment->get_board());

        if (Post::isset("confirm")) {
            $body = Post::get("body");

            /* TODO: validation comments: ne doit pas accepter comments vides ou composés d'espaces*/
            if (!empty($body) && $body != $comment->get_body()) {
                $comment->set_body($body);
                $comment->set_modifiedAt(new DateTime());
                CommentDao::update($comment);
            }
            $params = $this->explode_params(Post::get("redirect_url"));
            $this->redirect(...$params);
        }

        (new View("comment_edit"))->show(array(
                "user" => $user,
                "comment" => $comment,
                "redirect_url" => Post::get("redirect_url"),
                "breadcrumb" => new BreadCrumb(array($comment->get_board(), $comment->get_card()), "Edit comment"),
                "errors" => Session::get_error()
            )
        );
    }

    public function delete() {
        $comment = $this->get_object_or_redirect("id", "Comment");
        $user = $this->authorize_for_board_or_redirect($comment->get_board());

        if ($user->can_delete_comment($comment)) {
            CommentDao::delete($comment);
        }

        $params = $this->explode_params(Post::get("redirect_url"));
        $this->redirect(...$params);
    }



}
