<?php

require_once "autoload.php";


class ControllerComment extends ExtendedController {

    public function index() {
        $this->redirect();
    }

    public function add(){
        $card = $this->get_or_redirect_post("Card", "card_id");
        $user = $this->authorized_or_redirect(Permissions::view($card));

        // si 'body' est vide, ne fait rien
        if(!Post::empty("body") && !Validation::str_contains_only_spaces(Post::get("body"))) {
            $comment = new Comment(Post::get("body"), $user, $card);
            CommentDao::insert($comment);
        }

        $params = explode("/", Post::get("redirect_url"));
        $this->redirect(...$params);
    }

    public function edit() {
        $comment = $this->get_or_redirect_default();
        $user = $this->authorized_or_redirect(Permissions::edit($comment));

        if (Post::get("confirm") == "true") {
            $body = Post::get("body");

            if (!empty($body) && $body != $comment->get_body()  && !Validation::str_contains_only_spaces(Post::get("body"))) {
                $comment->set_body($body);
                $comment->set_modifiedAt(new DateTime());
                CommentDao::update($comment);
            }
            $params = explode("/", Post::get("redirect_url"));
            $this->redirect(...$params);
        }

        $redirect_url = str_replace("_", "/", Get::get("param2")) . "#comments";
        (new View("comment_edit"))->show(array(
                "user" => $user,
                "comment" => $comment,
                "redirect_url" => $redirect_url,
                "breadcrumb" => new BreadCrumb(array($comment->get_board(), $comment->get_card()), "Edit comment"),
                "errors" => Session::get_error()
            )
        );
    }

    public function delete() {
        $comment = $this->get_or_redirect_default();
        $user = $this->authorized_or_redirect(Permissions::delete($comment));

        if ($user->can_delete_comment($comment)) {
            CommentDao::delete($comment);
        }

        $params = explode("/", Post::get("redirect_url"));
        $this->redirect(...$params);
    }



}
