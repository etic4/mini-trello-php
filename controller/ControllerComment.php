<?php

require_once "autoload.php";


class ControllerComment extends EController {

    public function index() {
        $this->redirect();
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    
    public function delete() {
        list($_, $comment) = $this->authorize_or_redirect("id", "Comment");

        $comment->delete();
        $this->redirect("card", "view", $comment->get_card_id());

    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function edit() {
        list($_, $comment) = $this->authorize_or_redirect("id", "Comment");

        if(Post::isset("edit")) {
            $this->redirect("card","edit", $comment->get_card_id(), $comment->get_id());
        } else {
            $this->redirect("card","view", $comment->get_card_id(), $comment->get_id());
        }

    }

    public function edit_confirm() {
        list($_, $comment) = $this->authorize_or_redirect("id", "Comment");

        if(Post::all_sets("validate", "body")){
            $body = Post::get("body");
            $comment->set_body($body);
            $comment->update();
        }

       $this->card_redirect($comment->get_card_id());

    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function add(){
        list($user, $card) = $this->authorize_or_redirect("card_id", "Card");


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

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    private function card_redirect($card_id) {
        if(Post::isset("edit")){
            $this->redirect("card", "edit", $card_id);
        } else {
            $this->redirect("card", "view", $card_id);
        }
    }
}
