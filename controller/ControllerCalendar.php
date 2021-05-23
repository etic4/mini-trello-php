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
                 "groupId" => "1",
                 "card_id" => "1",
                 "board_id" => "1",
                 "board_title" => "Test Premier board"
            ],
            [
                "id" => "2",
                "title"  => 'board_1 event2',
                "start"  => '2021-05-05',
                "groupId" => "1",
                "card_id" => "2",
                "board_id" => "1",
                "board_title" => "Test Premier board"
            ],
            [
                "id" => "3",
                "title"  => 'board_1 event3',
                "start"  => '2021-05-09',
                "groupId" => "1",
                "card_id" => "3",
                "board_id" => "1",
                "board_title" => "Test Premier board"
            ]
        ];

        $board_2 = [
            [
                "id" => "4",
                "title"  => 'board_2 event1',
                "start"  => '2021-05-01',
                "groupId" => "2",
                "card_id" => "4",
                "board_id" => "2",
                "board_title" => "Deuxième board"
            ],
            [
                "id" => "5",
                "title"  => 'board_2 event3',
                "start"  => '2021-05-09',
                "groupId" => "2",
                "card_id" => "5",
                "board_id" => "2",
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