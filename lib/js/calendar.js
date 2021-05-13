function setup_calendar() {
    // fullCalendar semble avoir des problèmes avec jquery, une histoire de "double appel";

    let boards = {};

    let calendar_node = document.getElementById('calendar');

    let calendar = new FullCalendar.Calendar(calendar_node, {
        initialView: 'dayGridMonth',
        navLinks: true,
        headerToolbar: {
            left: 'prev,next,today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,listWeek'
        },

        //TODO: récupérer toutes les infos en ajax,
        // organiser ça par board, attribuer un couleur dans une liste prédéfinie
        // ajouter les labels
        // puis ajouter une array par board en spécifiant la couleur
        // cf: https://fullcalendar.io/docs/events-array
        //  + event toggle sur checkbox associé à event source display
        eventSources: [
            {
                url: 'calendar/test_events',
                method: 'POST',
                extraParams: {
                    board_id: '1',
                },
                failure: function() {
                    alert('there was an error while fetching events!');
                },
                color: 'yellow',   // a non-ajax option
                textColor: 'black' // a non-ajax option
            },
            {
                url: 'calendar/test_events',
                method: 'POST',
                extraParams: {
                    board_id: '2',
                },
                failure: function() {
                    alert('there was an error while fetching events!');
                },
                color: 'green',   // a non-ajax option
                textColor: 'white' // a non-ajax option
            }
        ],
        eventsSet: function(events) {
            for (let event of this.getEvents()) {
                boards[event.id] = {
                    board_id: event.id,
                    board_title: event.extendedProps["board_title"]
                };
            }

            // TODO: manifestement eventsSet appelé après que chaque source soit affichée
            // TODO: extraire de là.
            $("#boards_list").html("");

            for (let board_id in boards ) {
                console.log(board_id);
                console.log("ajout " + boards[board_id].board_title +" à #boards_list");
                $("#boards_list").append('<label class="checkbox ml-2" data-board-id="' + board_id + '"><input type="checkbox">' + boards[board_id].board_title + '</label>');
            }
        }
    });



    calendar.render();

}