	$.validator.setDefaults({
		submitHandler: function() {
			form.submit();
		}
	});

	$().ready(function() {
		// validate signup form on keyup and submit
		$("#createUser").validate({
                rules: {
                    fname: {
                        required: true,
                        minlength: 2
                    },
                    lname: {
                        required: true,
                        minlength: 2
                    },
					uname: {
                        required: true,
                        minlength: 2
                    },
                    umail: {
                        required: true,
                        email: true
                    },
                    upass1: {
                        required: true,
                        minlength: 8
                    },
                    upass2: {
                        required: true,
                        minlength: 8,
						equalTo: "#upass1"
                    }					
                },
                messages: {
                    fname: "Please enter your firstname",
                    lname: "Please enter your lastname",
					uname: {
					required: "Please enter a username",
					minlength: "Your username must consist of at least 2 characters"
				},					
                upass1: {
                        required: "Please provide a password",
                        minlength: "Your password must be at least 8 characters long",
                    },
                upass2: {
						equalTo: "Please enter the same password as above"
                    },					
                    umail: "Please enter a valid email address"
                },
		});
		
		$("#editUser").validate({
                rules: {
                    fname: {
                        required: true,
                        minlength: 2
                    },
                    lname: {
                        required: true,
                        minlength: 2
                    },
					uname: {
                        required: true,
                        minlength: 2
                    },
                    umail: {
                        required: true,
                        email: true
                    },
                    upass1: {
                        minlength: 8
                    },
                    upass2: {
                        minlength: 8,
						equalTo: "#upass1"
                    }					
                },
                messages: {
                    fname: "Please enter your firstname",
                    lname: "Please enter your lastname",
					uname: {
					required: "Please enter a username",
					minlength: "Your username must consist of at least 2 characters"
				},					
                upass1: {
                        required: "Please provide a password",
                        minlength: "Your password must be at least 8 characters long",
                    },
                upass2: {
						equalTo: "Please enter the same password as above"
                    },					
                    umail: "Please enter a valid email address"
                },
		});		

		// propose username by combining first- and lastname
		$("#uname").focus(function() {
			var firstname = $("#fname").val();
			var lastname = $("#lname").val();
			var prefname = $("#pname").val();
			if(prefname.length == 0){prefname = firstname};
			if (prefname && lastname && !this.value) {
				this.value = prefname + "." + lastname;
			}
		});

	});