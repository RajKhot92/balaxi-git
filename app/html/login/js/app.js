function signIn() {
	var username = $("#txtUsername").val();
	var password = $("#password-field").val();

	if(username == ""){
		alert("Username can not be blank.");
		$("#txtUsername").focus();
		return false;
	}
	if(password == ""){
		alert("Password can not be blank.");
		$("#password-field").focus();
		return false;
	}

	let response = callAPIService("validate_login.php","username="+username+"&password="+password);
	// console.log(response);
	apiResponse = JSON.parse(response);
	// console.log(apiResponse);

	if(apiResponse.status_code == 500){
		alert(apiResponse.error);
	}else if(apiResponse.status_code == 200){
		let data = apiResponse.data;
		// console.log(data);
		if(data.length > 0){
			let userDetails = data[0];
			localStorage.setItem("balaxi_user_id",userDetails.user_id);
			localStorage.setItem("balaxi_user_name",userDetails.user_name);
			localStorage.setItem("balaxi_contact_no",userDetails.contact_no);
			localStorage.setItem("balaxi_email_id",userDetails.email_id);
			localStorage.setItem("balaxi_user_role",userDetails.user_role);
			window.location.href="../index.html";
		}else{
			alert("User details not found for entered credentials.");
		}
	}else{
		//Nothing
	}
}

function resetPassword() {
	window.location.href="forgot-password.html";
}

function sendOTP() {
	var email = $("#txtEmail").val();

	if(email == ""){
		alert("Email can not be blank.");
		$("#txtEmail").focus();
		return false;
	}

	let response = callAPIService("send_otp_password.php","email="+email);
	// console.log(response);
	apiResponse = JSON.parse(response);
	// console.log(apiResponse);

	if(apiResponse.status_code == 500){
		alert(apiResponse.error);
	}else if(apiResponse.status_code == 200){
		let data = apiResponse.data;
		// console.log(data);
		if(String(data) == "1"){
			alert("We have sent OTP on your registered email id.");
			$(".stepOne").hide();
			$(".stepTwo").slideDown();
			// window.location.href="../index.html";
		}else{
			alert("User details not found for entered credentials.");
		}
	}else{
		//Nothing
	}
}

function validateOTP() {
	var email = $("#txtEmail").val();
	var otp = $("#txtOTP").val();

	if(email == ""){
		alert("OTP can not be blank.");
		$("#txtOTP").focus();
		return false;
	}

	let response = callAPIService("validate_otp.php","email="+email+"&otp="+otp);
	// console.log(response);
	apiResponse = JSON.parse(response);
	// console.log(apiResponse);

	if(apiResponse.status_code == 500){
		alert(apiResponse.error);
	}else if(apiResponse.status_code == 200){
		// let data = apiResponse.data;
		$("#txtUserId").val(apiResponse.data);
		// console.log(data);
		$(".stepTwo").hide();
		$(".stepThree").slideDown();
	}else{
		//Nothing
	}
}

function updatePassword() {
	var userId = $("#txtUserId").val();
	var pswd = $("#txtPassword").val();
	var cpswd = $("#txtCPassword").val();

	if(pswd == ""){
		alert("Password can not be blank.");
		$("#txtPassword").focus();
		return false;
	}

	if(cpswd == ""){
		alert("Please confirm your password.");
		$("#txtCPassword").focus();
		return false;
	}

	if(pswd != cpswd){
		alert("Password mismatch. Kindly check entered password.");
		return false;
	}

	let response = callAPIService("update_password.php","user_id="+userId+"&password="+pswd);
	// console.log(response);
	if(String(response) != "1"){
		alert(response);
	}else{
		alert("Password has been successfully reset. Kindly login again.");
		window.location.href="index.html";
	}
}