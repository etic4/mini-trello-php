<?php

require_once "autoload.php";


class ControllerComment extends ExtendedController {

    public function index() {
        $this->redirect();
    }

    public function delete() {
        $comment = $this->get_object_or_redirect("id", "Comment");
        $this->authorize_for_board_or_redirect($comment);

        $comment->delete();
        $this->redirect("card", "view", $comment->get_card_id());

    }


    public function edit() {
        $comment = $this->get_object_or_redirect("id", "Comment");
        $this->authorize_for_board_or_redirect($comment);

        if(Post::isset("edit")) {
            $this->redirect("card","edit", $comment->get_card_id(), $comment->get_id());
        } else {
            $this->redirect("card","view", $comment->get_card_id(), $comment->get_id());
        }

    }

    public function edit_confirm() {
        $comment = $this->get_object_or_redirect("id", "Comment");
        $this->authorize_for_board_or_redirect($comment);

        if(Post::all_sets("validate", "body")){
            $body = Post::get("body");
            $comment->set_body($body);
            $comment->update();
        }

       $this->card_redirect($comment->get_card_id());

    }

    public function add(){
        $card = $this->get_object_or_redirect("card_id", "Card");
        $user = $this->authorize_for_board_or_redirect($card->get_board());


        if(!Post::empty("body")) {
            $body = Post::get("body");
            $comment = new Comment($body, $user, $card);
            $comment->insert();
        }else{
           /* gestion des erreurs. si comment vide
            $error = new ValidationError($card, "add_comment");
            $err[] = "Comment cannot be void";
            $error->set_messages_and_add_to_session($err);
            */
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
