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

    public function get_events() {
        $user = $this->get_user_or_redirect();

        $eventSources = [];

        foreach ($user->get_accessibles_boards() as $board) {
            $cards_with_dueDate = array_filter($board->get_cards(), fn($card) => $card->get_dueDate() != null);

            if (count($cards_with_dueDate) > 0) {
                $key = $this->get_key($board);

                $eventSources[$key] = [
                    "title" => $board->get_title(),
                    "eventSource" => [
                        "id" => $key,
                        "events" => $this->get_events_array($cards_with_dueDate)
                    ]
                ];
            }
        }
        echo json_encode($eventSources);
    }

    private function get_events_array(array $cards): array {
        $events = [];

        foreach ($cards as $card) {
            $events[] = [
                "id" => $card->get_id(),
                "title" => $card->get_title(),
                "start" => $card->get_dueDate()->format('Y-m-d')
            ];
        }

        return $events;
    }

    private function get_key(Board $board): string {
        return "board_" . $board->get_id();
    }
}