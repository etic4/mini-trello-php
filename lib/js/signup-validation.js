$(function () {

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

    function signup_setup() {
        add_regex();
        signup_validation();
    }




});