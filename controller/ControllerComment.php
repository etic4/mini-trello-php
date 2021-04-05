<?php

require_once "autoload.php";


class ControllerComment extends ExtendedController {

    public function index() {
        $this->redirect();
    }

    public function delete() {
        $comment = $this->get_object_or_redirect("id", "Comment");
        $this->authorize_for_board_or_redirect($comment);

        CommentDao::delete($comment);
        $this->redirect("card", "view", $comment->get_card_id());
    }


    public function edit() {
        $comment = $this->get_object_or_redirect("id", "Comment");
        $this->authorize_for_board_or_redirect($comment->get_board());

        if(Post::isset("edit")) {
            $this->redirect("card","edit", $comment->get_card_id(), $comment->get_id());
        } else {
            $this->redirect("card","view", $comment->get_card_id(), $comment->get_id());
        }
    }

    public function edit_confirm() {
        $comment = $this->get_object_or_redirect("id", "Comment");
        $this->authorize_for_board_or_redirect($comment->get_board());

        if(Post::all_sets("validate", "body")){
            $body = Post::get("body");
            $comment->set_body($body);
            CommentDao::update($comment);
        }

       $this->card_redirect($comment->get_card_id());
    }

    public function add(){
        $card = $this->get_object_or_redirect("card_id", "Card");
        $user = $this->authorize_for_board_or_redirect($card->get_board());


        // si 'body' est vide, ne fait rien, pas besoin de message
        if(!Post::empty("body")) {
            $comment = new Comment(Post::get("body"), $user, $card);
            CommentDao::insert($comment);
        }
        $this->card_redirect($card->get_id());
    }


    private function card_redirect($card_id) {
        if(Post::isset("edit")){
            $this->redirect("card", "edit", $card_id);
        } else {
            $this->redirect("card", "view", $card_id);
        }
    }
}
