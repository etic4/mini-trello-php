$(function () {
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
                maxlength: 16
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
});