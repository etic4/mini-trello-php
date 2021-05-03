$(function () {
    $('#column-add').validate({
        rules: {
            column_title: {
                required: true,
                minlength: 3,
                maxlength: 20,
                remote: {
                    url: 'column/column_title_is_unique_service',
                    type: 'post',
                    data:  {
                        column_title: function() {
                            return $("#column-title").val();
                        },
                        board_id: function() {
                            return $("#board-id").val()
                        }
                    }
                },
            },
        },
        messages: {
            column_title: {
                required: "requis",
                minlength: 'minimum 3 characters',
                maxlength: 'maximum 20 characters',
                remote: "A column with the same title already exists"
            }
        },
        errorClass: "has-text-danger"
    });

    $('#column-edit').validate({
        rules: {
            column_title: {
                required: true,
                minlength: 3,
                maxlength: 20,
                remote: {
                    url: 'column/column_title_is_unique_service',
                    type: 'post',
                    data:  {
                        column_title: function() {
                            return $("#column-title").val();
                        },
                        board_id: function() {
                            return $("#board-id").val();
                        },
                        column_id: function() {
                            return $("#column-id").val();
                        }
                    }
                },
            },
        },
        messages: {
            column_title: {
                required: "requis",
                minlength: 'minimum 3 characters',
                maxlength: 'maximum 20 characters',
                remote: "A column with the same title already exists"
            }
        },
        errorClass: "has-text-danger"
    });

});