
/* --- Confirmation delete --- */

function setup_delete_confirm() {
    setup_board_delete_confirm();
    setup_column_delete_confirm();
    setup_card_delete_confirm();
}

function setup_board_delete_confirm () {
    let click_handler_config = {
        class_name: "board",
        form_selector: "#board-delete-form",
        id_node_selector : "#board-delete-form input[name=id]"
    }

    $("#board-delete").click(get_delete_click_handler(click_handler_config));
}

function setup_column_delete_confirm () {
    let click_handler_config = {
        class_name: "column",
        form_selector: "#column-delete-form",
        id_node_selector : "#column-delete-form input[name=id]"
    }

    $("#column-delete").click(get_delete_click_handler(click_handler_config));
}

function setup_card_delete_confirm () {
    let click_handler_config = {
        class_name: "card",
        form_selector: "#card-delete-form",
        id_node_selector : "#card-delete-form input[name=id]"
    }

    $("#card-delete").click(get_delete_click_handler(click_handler_config));
}

function get_delete_click_handler(config) {
    return function (event) {
        event.preventDefault()
        console.log(config);

        // si carte, appel inutile à card/needs_delete_confirm_service card toujours true
        // je laisse comme ça.
        let confirm_url = document.baseURI + config.class_name + "/needs_delete_confirm_service/" + $(config.id_node_selector).val();

        $.get(confirm_url, function (response) {
                let delete_confirm = JSON.parse(response);

                if (delete_confirm === true) {
                    $("#delete-confirm").dialog(get_dialog_config(config.class_name, config.form_selector));
                } else if (delete_confirm === false) {
                    $(config.form_selector).submit();
                }
            }
        );
    }
}

function get_dialog_config(class_name, form_selector) {
    let config =  {
        resizable: false,
        modal: true,
        height: 350,
        width: 400,
        buttons: [
            {
                text: "Cancel",
                click: function () {
                    $(this).dialog("close");
                }
            },
            {
                text: "Delete",
                click: function () {
                    $(form_selector).find($("input[name=confirm]")).first().val("true");
                    $(form_selector).submit();
                }
            }
        ],
        open: function () {
            $(".ui-dialog-titlebar").hide();
            $("#delete-class-name").text(class_name);
            $(".ui-dialog-buttonpane").addClass("is-flex is-justify-content-center pt-3 pb-5");

            let button_pane_buttons = $(".ui-dialog .ui-dialog-buttonpane button");
            button_pane_buttons.addClass("button");
            button_pane_buttons.last().addClass("is-danger ml-4");
        }
    };
    console.log(config);
    return config;
}


