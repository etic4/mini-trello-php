e<?php

require_once "framework/Controller.php";
require_once "model/Card.php";
require_once "model/User.php";


class ControllerComment extends Controller {

    public function index() {
        $this->redirect();
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    
    public function delete() {
        $user = $this->get_user_or_redirect();

        if(isset($_POST['id'])) {   
            $comment_id = $_POST['id'];       
            $comment = Comment::get_by_id($comment_id);
            $comment->delete(); 
            $this->redirect("card", "view", $comment->get_card_id());
        }

        $this->redirect();
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function edit() {
        $user = $this->get_user_or_redirect();

        if(isset($_POST['id'])) {            
            $comment_id = $_POST['id'];
            $comment = Comment::get_by_id($comment_id);

            if(isset($_POST['edit'])) {
                $this->redirect("card","edit", $comment->get_card_id(), $comment_id);
            } else {
                $this->redirect("card","view", $comment->get_card_id(), $comment_id);
            }
        } else {
            $this->redirect();
        }
    }

    public function edit_confirm() {
        $user = $this->get_user_or_redirect();

        if(isset($_POST['id'])){            
            $comment_id = $_POST['id'];
            $comment = Comment::get_by_id($comment_id);

            if(isset($_POST['validate'])){

                if(isset($_POST['body'])){
                    $body = $_POST['body'];
                    $comment->set_body($body);
                    $comment->update();
                }
            }

            if(isset($_POST['edit'])){
                $this->redirect("card", "edit", $comment->get_card_id());
            } else {
                $this->redirect("card", "view", $comment->get_card_id());
            }

        } else {
            $this->redirect();
        }
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function add(){
        $user = $this->get_user_or_redirect();

        if(isset($_POST['card_id'])) {
            $card_id = $_POST['card_id'];
            if(!empty($_POST['body'])) {
                $body = $_POST['body'];

                $card = Card::get_by_id($card_id);  
                $comment = new Comment($body, $user, $card);
                $comment->insert();
            }

            if(isset($_POST['edit'])){
                $this->redirect("card", "edit", $card_id);
            } else {
                $this->redirect("card", "view", $card_id);
            }
        }

        $this->redirect();
    }
}

?>