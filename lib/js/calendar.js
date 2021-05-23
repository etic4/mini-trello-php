function setup_calendar() {
    // fullCalendar semble avoir "quelque chose" avec jquery, une histoire de "double appel";


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
                textColor: 'black', // a non-ajax option
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
                textColor: 'white', // a non-ajax option
            }
        ],
        eventsSet: function(events) {
            for (let event of this.getEvents()) {
                boards[event.extendedProps["board_id"]] = {
                    board_id: event.extendedProps["board_id"],
                    board_title: event.extendedProps["board_title"]
                };
            }

            // TODO: manifestement eventsSet appelé après que chaque source soit affichée
            // TODO: extraire de là.
            $("#boards_list").html("");

            for (let board_id in boards ) {
                console.log(board_id);
                console.log("ajout " + boards[board_id].board_title +" à #boards_list");
                $("#boards_list").append('<label class="checkbox ml-2" data-board-id="' + board_id + '"><input type="checkbox" class="mr-1">' + boards[board_id].board_title + '</label>');
            }
        },
        eventClick: function (eventClickInfo) {
            console.log(eventClickInfo.event);
            show_card(eventClickInfo.event.extendedProps.card_id);
        }
    });

    calendar.render();

/*
    ************ Fonctions utilitaires ***********
*/

    function get_events() {
        $.post("calendar/test_events")
    }
    function getRandomColor() {
        const colors = ["aqua", "beige", "blue", "chocolate", "blueviolet", "coral", "cyan", "salmon", "gold"];
        return Math.floor(Math.random() * colors.length)
    }

    function show_card(card_id) {
        let config =  {
            resizable: false,
            modal: true,
            height: 700,
            width: "auto",
            buttons: [
                {
                    text: "Cancel",
                    click: function () {
                        $(this).dialog("close");
                    }
                }
            ],
            open: function () {
                $.post( "card/get_card_service", { id: card_id}).done(function(data) {
                    console.log(data);
                    data =JSON.parse(data);
                    console.log(data);

                    prepare_card_html(data)

                });
                $(".ui-dialog-titlebar").hide();

                let button_pane_buttons = $(".ui-dialog .ui-dialog-buttonpane button");
                button_pane_buttons.addClass("button");
            }
        };

        $("#show-card").dialog(config);
    }

    function prepare_card_html(data) {
        $("#card-title").text(data.title);
        $("#card-created-intvl").text(data.created_interval);
        $("#card-author").text(data.author);
        $("#card-modified-intvl").text(data.modified_interval);
        $("#card-board-url").attr("href", data.board_url);
        $("#card-board-title").text(data.board_title);
        $("#card-column-title").text(data.column_title);
        $("#card-position").text(data.position);
        $("#card-body").text(data.body);
        $("#card-due-date").text(data.due_date);

        let ul_participants = $("#card-participants")
        ul_participants.html("");
        for (let participant of data.participants ) {
            ul_participants.append(participant_template(participant.name, participant.email));
        }

        let ul_comments = $("#card-comments")
        ul_comments.html("");
        for (let comment of data.comments ) {
            ul_comments.append(comment_template(comment.body, comment.author, comment.time_published));
        }
    }

    function participant_template(pname, pemail) {
        return '<li class="is-flex is-flex-direction-row mb-1 is-align-items-baseline">\n' +
            '    <span class="icon">\n' +
            '        <i class="far fa-user"></i>\n' +
            '    </span>\n' +
            '    <span class="has-text-info">\n' +
            '        <strong class="has-text-info">' + pname +'</strong> (' + pemail +')' +
            '    </span>\n' +
            '</li>'
    }

    function comment_template(body, author, published) {
        return '<li class="is-flex is-flex-direction-row mb-1 is-align-items-baseline">\n' +
            '     <span class="icon">\n' +
            '        <i class="far fa-comment"></i>\n' +
            '     </span>\n' +
            '     <span class="mr-1">' + body +'</span>\n' +
            '     <span class="mr-1">by <strong class="has-text-info">' + author + '</strong> </span>\n' +
            '     <span>' + published +'</span>\n' +
            '    </li>'
    }


}