$(function () {
    function add_card_validation() {
        $('#card-add').validate({
            rules: {
                card_title: {
                    required: true,
                    minlength: 3,
                    maxlength: 20,
                    remote: {
                        url: 'card/card_title_is_unique_service',
                        type: 'post',
                        data:  {
                            card_title: function() {
                                return $("#card_title").val();
                            },
                            board_id: function() {
                                return $("#board-id").val()
                            }
                        }
                    },
                },
            },
            messages: {
                card_title: {
                    required: "requis",
                    minlength: 'minimum 3 characters',
                    maxlength: 'maximum 20 characters',
                    remote: "A card with the same title already exists"
                }
            },
            errorClass: "has-text-danger"
        });
    }

    function edite_card_validation() {
        $('#card-edit').validate({
            rules: {
                card_title: {
                    required: true,
                    minlength: 3,
                    maxlength: 20,
                    remote: {
                        url: 'card/column_title_is_unique_service',
                        type: 'post',
                        data:  {
                            card_title: function() {
                                return $("#card_title").val();
                            },
                            board_id: function() {
                                return $("#board-id").val();
                            },
                            card_id: function() {
                                return $("#card_id").val();
                            }
                        }
                    },
                },
                due_date: {

                }
            },
            messages: {
                card_title: {
                    required: "requis",
                    minlength: 'minimum 3 characters',
                    maxlength: 'maximum 20 characters',
                    remote: "A card with the same title already exists"
                }
            },
            errorClass: "has-text-danger"
        });
    }

    function card_validation_setup() {
        add_card_validation();
        edite_card_validation();
    }
});