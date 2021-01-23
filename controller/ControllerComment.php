e<?php

require_once "framework/Controller.php";
require_once "model/Card.php";
require_once "model/User.php";


class ControllerComment extends Controller {

    public function index() {
        // TODO: Implement index() method.
    }
    
    public function delete(){
        $user=$this->get_user_or_redirect();

        if(isset($_POST['id'])){            
            $idcomment = $_POST['id'];
            $instance = Comment::get_by_id($idcomment);
            $instance->delete(); 
            $this->redirect("card","view",$instance->get_card()->get_id());
        }

        $this->redirect("board","index");
    }

    public function edit(){
        $user=$this->get_user_or_redirect();

        if(isset($_POST['id'])){            
            $idcomment = $_POST['id'];
            $instance = Comment::get_by_id($idcomment);

            if(isset($_POST['edit'])){
                $this->redirect("card","edit",$instance->get_card()->get_id(),$idcomment);
            }else{
                $this->redirect("card","view",$instance->get_card()->get_id(),$idcomment);
            }
        }else{
            $this->redirect("board","index");
        }
    }

    public function edit_confirm(){
        $user=$this->get_user_or_redirect();

        if(isset($_POST['id'])){            
            $idcomment = $_POST['id'];
            $instance = Comment::get_by_id($idcomment);

            if(isset($_POST['validate'])){
                if(isset($_POST['body'])){
                    $instance->set_body($_POST['body']);
                    $instance->update();
                }
            }
            if(isset($_POST['edit'])){
                $this->redirect("card","edit",$instance->get_card()->get_id());
            }else{
                $this->redirect("card","view",$instance->get_card()->get_id());
            }
        }else{
            $this->redirect("board","index");
        }
    }

    public function add(){
        $user=$this->get_user_or_redirect();

        if(isset($_POST['idcard'])&& !empty($_POST['body'])){
            $card = Card::get_by_id($_POST['idcard']);  
            $instance = new Comment($_POST['body'],$user,$card);
            $instance->insert();

            if(isset($_POST['edit'])){
                $this->redirect("card","edit",$instance->get_card()->get_id());
            }else{
                $this->redirect("card","view",$instance->get_card()->get_id());
            }
        }
        $this->redirect("board","index");
    }
    
}

?>