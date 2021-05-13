<?php

require_once "autoload.php";

class ControllerCalendar extends ExtendedController {

    public function index() {
        $user = $this->get_user_or_redirect();

        (new View("calendar"))->show(array(
                "user" => $user
            )
        );
    }


    public function test_events() {
        $board_1 = [
             [
                 "id" => "1",
                 "title"  => 'board_1 event1',
                 "start"  => '2021-05-03',
                 "board_title" => "Test Premier board"
            ],
            [
                "id" => "1",
                "title"  => 'board_1 event2',
                "start"  => '2021-05-05',
                "board_title" => "Test Premier board"
            ],
            [
                "id" => "1",
                "title"  => 'board_1 event3',
                "start"  => '2021-05-09',
                "board_title" => "Test Premier board"
            ]
        ];

        $board_2 = [
            [
                "id" => "2",
                "title"  => 'board_2 event1',
                "start"  => '2021-05-01',
                "board_title" => "Deuxième board"
            ],
            [
                "id" => "2",
                "title"  => 'board_2 event3',
                "start"  => '2021-05-09',
                "board_title" => "Deuxième board"
            ]
        ];

        if (Post::get("board_id") == "1") {
            echo json_encode($board_1);
        } else {
            echo json_encode($board_2);
        }
    }
}