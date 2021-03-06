<?php

require_once "autoload.php";


class ControllerComment extends Controller {
    use Authorize;

    public function index() {
        $this->redirect();
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    
    public function delete() {
        $user = $this->get_user_or_redirect();
        $comment = CtrlTools::get_object_or_redirect($_POST, "id", "Comment");
        $this->authorize_or_redirect($user, $comment->get_board());

        $comment->delete();
        $this->redirect("card", "view", $comment->get_card_id());

    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function edit() {
        $user = $this->get_user_or_redirect();
        $comment = CtrlTools::get_object_or_redirect($_POST, "id", "Comment");
        $this->authorize_or_redirect($user, $comment->get_board());

        if(isset($_POST['edit'])) {
            $this->redirect("card","edit", $comment->get_card_id(), $comment->get_id());
        } else {
            $this->redirect("card","view", $comment->get_card_id(), $comment->get_id());
        }

    }

    public function edit_confirm() {
        $user = $this->get_user_or_redirect();
        $comment = CtrlTools::get_object_or_redirect($_POST, "id", "Comment");
        $this->authorize_or_redirect($user, $comment->get_board());

        if(isset($_POST['validate'])){

            if(isset($_POST['body'])){
                $body = $_POST['body'];
                $comment->set_body($body);
                $comment->update();
            }
        }
       $this->card_redirect($comment->get_card_id());


    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function add(){
        $user = $this->get_user_or_redirect();
        $card = CtrlTools::get_object_or_redirect($_POST, "card_id", "Card");
        $this->authorize_or_redirect($user, $card->get_board());


        if(!empty($_POST['body'])) {
            $body = $_POST['body'];
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
        if(isset($_POST['edit'])){
            $this->redirect("card", "edit", $card_id);
        } else {
            $this->redirect("card", "view", $card_id);
        }
    }
}
