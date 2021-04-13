<?php

require_once "autoload.php";


class ControllerComment extends ExtendedController {

    public function index() {
        $this->redirect();
    }

    public function add(){
        $card = $this->get_or_redirect("Card", "card_id");
        $user = $this->authorized_or_redirect(Permissions::view($card));

        // si 'body' est vide, ne fait rien, pas besoin de message
        // TODO: quand-même vérifier que pas que espaces
        if(!Post::empty("body")) {
            $comment = new Comment(Post::get("body"), $user, $card);
            CommentDao::insert($comment);
        }

        $params = explode("/", Post::get("redirect_url"));
        $this->redirect(...$params);
    }

    public function edit() {
        $comment = $this->get_object_or_redirect();
        $user = $this->authorized_or_redirect(Permissions::edit($comment));

        if (Post::isset("confirm")) {
            $body = Post::get("body");

            /* TODO: validation comments: ne doit pas accepter comments vides ou composés d'espaces*/
            if (!empty($body) && $body != $comment->get_body()) {
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
        $comment = $this->get_object_or_redirect();
        $user = $this->authorized_or_redirect(Permissions::delete($comment));

        if ($user->can_delete_comment($comment)) {
            CommentDao::delete($comment);
        }

        $params = explode("/", Post::get("redirect_url"));
        $this->redirect(...$params);
    }



}
