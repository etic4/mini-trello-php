

// --- Board validation ---

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


// --- Column validation ---

function add_column_validation() {
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
}

function edit_column_validation() {
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
}

// --- Card validation ---

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

function edit_card_validation() {
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


// Signup validation

function add_regex() {
    $.validator.addMethod("passw_one_uppercase", function (value) {
        return /[A-Z]+/.test(value);
    }, "Password must contain at least 1 uppercase letter");

    $.validator.addMethod("passw_one_number", function (value) {
        return /[0-9]+/.test(value);
    }, "Password must contain at least 1 number");

    $.validator.addMethod("passw_one_special_char", function (value) {
        // return /['";:,.\/?\\-]+/.test(value);
        return /[^A-Za-z 0-9]/g.test(value);
    }, "Password must contain at least one special character");
}

function signup_validation() {
    $('#signup').validate({
        rules: {
            email: {
                required: true,
                email: true,
                remote: {
                    url: 'user/email_is_unique_service',
                    type: 'post',
                    data:  {
                        email: function() {
                            return $("#email").val();
                        }
                    }
                },
            },
            fullName : {
                required: true,
                minlength: 3,
                maxlength: 16
            },
            password: {
                required: true,
                minlength: 8,
                maxlength: 16,
                passw_one_uppercase: true,
                passw_one_number: true,
                passw_one_special_char: true
            },
            confirm: {
                required: true,
                minlength: 8,
                maxlength: 16,
                equalTo: "#password"
            }
        },
        messages: {
            email: {
                required: "requis",
                email: "invalid email",
                remote: "A user with the same email already exists"
            },
            fullName: {
                required: 'required',
                minlength: 'minimum 3 characters',
                maxlength: 'maximum 16 characters',
            },
            password: {
                required: 'required',
                minlength: 'minimum 8 characters',
                maxlength: 'maximum 16 characters',
            },
            confirm: {
                required: 'required',
                minlength: 'minimum 8 characters',
                maxlength: 'maximum 16 characters',
                equalTo: 'must be identical to password above',
            }
        },
        errorClass: "has-text-danger"
    });

    $("input:text:first").focus();
}

function setup_signup_validation() {
    add_regex();
    signup_validation();
}

