
function setup_drag_and_drop() {
    let board_id = $("#board-id").val();

    // --- columns ---

    let board_droppable = $("#board-droppable");

    board_droppable.sortable({
        update: function() {
            $.post(
                "column/update_columns_positions_service",
                {
                    board_id: board_id,
                    columns_list: get_all_columns_state()
                },
            );
        },
        distance: 5
    });

    $(".trello-column").each(function() {
        $(this).draggable({
            connectToSortable: "#board-droppable",
            revert: 'invalid'
        });
    });

    // -- cards ---

    let column_droppable = $(".column-droppable");

    column_droppable.each(function () {
        $(this).sortable({
            items: ".trello-card",
            update: function() {
                $.post(
                    "card/update_cards_positions_service",
                    {
                        board_id: board_id,
                        cards_list: get_all_cards_state()
                    },
                );
            },
            distance: 5
        });
    });

    $(".trello-card").each(function() {
        $(this).draggable({
            connectToSortable: ".column-droppable",
            revert: 'invalid'
        });
    });
}

function get_all_columns_state() {
    let columns_state = [];
    $(".trello-column").each(function(idx) {
        columns_state.push({
            column_id: $(this).attr("data-column-id"),
            column_position: idx
        });
    });
    return columns_state;
}


function get_all_cards_state() {
    let cards_state = [];

    $(".trello-column").each(function() {
        let column = $(this);
        let cards = column.find(".trello-card");

        for (let i = 0; i < cards.length; i++) {
            cards_state.push({
                card_id: $(cards[i]).attr("data-card-id"),
                card_position: i,
                column_id: column.attr("data-column-id")
            })
        }
    });

    return cards_state;
}

