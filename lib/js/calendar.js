function setup_calendar() {

    build();

    let eventSources;
    let calendar;

    function build() {
        $.post({
            url: "calendar/get_events",
            success: function(data) {
                eventSources = JSON.parse(data);

                let i = 1;
                for (let source_id in eventSources) {
                    let color = getRandomColor(i);
                    let source = eventSources[source_id];

                    source.color = color;
                    source.eventSource.backgroundColor = color;
                    source.eventSource.textColor = "black";

                    i++;
                }

                build_sources_selector();
                show_calendar();

                for (let source_id in eventSources) {
                    calendar.addEventSource(eventSources[source_id].eventSource);
                }
            },
        });
    }

    function build_sources_selector() {
        for (let id in eventSources) {
            let source = eventSources[id]

            let checkbox = $('<input type="checkbox" class="mr-1" data-board-id="' + id + '" checked><span>' + source.title + '</span>');
            let label = $('<label class="checkbox ml-2" style="color: ' + source.color +'"></label>');

            label.append(checkbox);
            $("#boards_list").append(label);

            checkbox.change(function() {
                let source_id = $(this).attr("data-board-id");
                let eventSource = calendar.getEventSourceById(source_id);

                if (!this.checked) {
                    eventSource.remove();
                } else {
                    calendar.addEventSource(eventSources[source_id].eventSource)
                }
            })
        }
    }

    function show_calendar() {
        // fullCalendar semble avoir "quelque chose" avec jquery, une histoire de "double appel" ... ;
        let calendar_node = document.getElementById('calendar');

        calendar = new FullCalendar.Calendar(calendar_node, {
            initialView: 'dayGridMonth',
            navLinks: true,
            headerToolbar: {
                left: 'prev, next, today',
                center: 'title',
                right: 'dayGridMonth, timeGridWeek, listYear'
            },
            eventClick: function (eventClickInfo) {
                show_card(eventClickInfo.event.id);
            }
        });
        calendar.render();
    }

/************* Card popup ************/

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
                $.post( "card/get_card_service", { id: card_id}, function(data) {
                    prepare_card_html(JSON.parse(data))
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

    /***** Utilitaires ******/

    // cf. https://stackoverflow.com/questions/10014271/generate-random-color-distinguishable-to-humans#answer-20129594
    function getRandomColor(number) {
        const hue = number * 137.508; // use golden angle approximation
        return `hsl(${hue},100%,50%)`;
    }
}