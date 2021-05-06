$(function () {
    function add_board_validation() {
        $('#board-add').validate({
            rules: {
                board_title: {
                    required: true,
                    minlength: 3,
                    maxlength: 20,
                    remote: {
                        url: 'board/board_title_is_unique_service',
                        type: 'post',
                        data:  {
                            board_title: function() {
                                return $("#board-title").val();
                            }
                        }
                    },
                },
            },
            messages: {
                board_title: {
                    required: "requis",
                    minlength: 'minimum 3 characters',
                    maxlength: 'maximum 20 characters',
                    remote: "A board with the same title already exists"
                }
            },
            errorClass: "has-text-danger"
        });
    }

    function edit_board_validation() {
        $('#board-edit').validate({
            rules: {
                board_title: {
                    required: true,
                    minlength: 3,
                    maxlength: 20,
                    remote: {
                        url: 'board/board_title_is_unique_service',
                        type: 'post',
                        data:  {
                            board_title: function() {
                                return $("#board-title").val() ;
                            },
                            board_id: function() {
                                return $("#board-id").val();
                            }
                        }
                    },
                },
            },
            messages: {
                board_title: {
                    required: "requis",
                    minlength: 'minimum 3 characters',
                    maxlength: 'maximum 20 characters',
                    remote: "A board with the same title already exists"
                }
            },
            errorClass: "has-text-danger"
        });
    }

    function setup_board_validation() {
        add_board_validation();
        edit_board_validation()
    }
});