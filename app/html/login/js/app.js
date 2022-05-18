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