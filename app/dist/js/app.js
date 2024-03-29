var menu_id = 0, sub_menu_id = 0, retval = 0;

const DATA_CAPTURE_MENU_ID = 9;
const VENDOR_FINALIZE_MENU_ID = 10;
const DOCUMENT_SAMPLE_COLLECTION_MENU_ID = 11;
const SAMPLE_FINALIZATION_MENU_ID = 12;
const DOSSIER_DEVELOPMENT_MENU_ID = 13;
const SHIPMENT_MENU_ID = 14;
const SUBMISSION_MENU_ID = 15;

$(document).ready(function(){
	$("#loggedInPerson").html("Hi, "+localStorage.getItem("balaxi_user_name"));

	// const params = new URLSearchParams(window.location.search);
	// if(params.has('menu_id')){
	// 	// console.log(params.get('menu_id'));
	// 	menu_id = params.get('menu_id');
	// }

	// if(params.has('sub_menu_id')){
	// 	// console.log(params.get('sub_menu_id'));
	// 	sub_menu_id = params.get('sub_menu_id');
	// }

	// if(params.has('retval')){
	// 	// console.log(params.get('retval'));
	// 	retval = params.get('retval');
	// }

	loadUserMenus();
});

function popupwindow(url, title, target, w, h) {
    var left = (screen.width/2)-(w/2);
    var top = (screen.height/2)-(h/2);
    return window.open(url, title, target, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width='+w+', height='+h+', top='+top+', left='+left);
}

function loadUserMenus() {
	let firstPageToLoad = "";
	let firstMenuIdToLoad = "";
	let response = callAPIService("get_menu.php","login_id="+localStorage.getItem("balaxi_user_id"));
	// console.log(response);
	var jsonMenu = JSON.parse(response);
	if(jsonMenu.length > 0){
		var menuContent = "";
		for(var i=0; i<jsonMenu.length; i++){
			var menu = jsonMenu[i];
			// console.log(menu);
			var subMenuContent = "";
			//Mostly dashboard menu, so will make it active
			if(menu.menu_name === "Dashboard"){
				menuContent += "<li class=\"sidebar-item\">"+
	                		"<a id=\""+menu.menu_id+"\" class=\"sidebar-link waves-effect waves-dark sidebar-link theme-menu active-menu\""+
	                  		"href=\"#\""+
	                  		"onclick=\"loadPageContent("+menu.menu_id+",'"+menu.menu_link+"')\""+
	                  		"aria-expanded=\"false\">"+
	                  		"<i class=\""+menu.menu_icon+"\"></i>&nbsp;"+
	                  		"<span class=\"hide-menu\">"+menu.menu_name+"</span></a>"+
              			"</li>";
              	
              	// if(menu_id == 0){
              	// 	
              	// }else{
              	// 	//nothing
              	// }
              	firstMenuIdToLoad = menu.menu_id;
              	firstPageToLoad = menu.menu_link;
			}else{
				if(menu.sub_menu.length > 0){
              		for(var j=0; j<menu.sub_menu.length; j++){
						var sub_menu = menu.sub_menu[j];
						// console.log(sub_menu);
	              		subMenuContent += "<li class=\"sidebar-item\">"+
				                    "<a id=\""+sub_menu.menu_id+"\" href=\"#\""+
				                     "onclick=\"loadPageContent("+sub_menu.menu_id+",'"+sub_menu.menu_link+"')\""+ 
				                      "class=\"sidebar-link\""+
				                      "><i class=\""+sub_menu.menu_icon+"\"></i>&nbsp;"+
				                      "<span class=\"hide-menu\">"+sub_menu.menu_name+"</span></a"+
				                    ">"+
				                  "</li>";
				        
				        // if(menu_id != 0 && menu_id == sub_menu.menu_id ){
		          //     		firstMenuIdToLoad = sub_menu.menu_id;
	           //    			firstPageToLoad = sub_menu.menu_link;
		          //     	}
	                }

	                menuContent += "<li class=\"sidebar-item\">"+
	                		"<a class=\"sidebar-link has-arrow waves-effect waves-dark\""+
                  			"href=\"javascript:void(0)\""+
	                  		"aria-expanded=\"false\">"+
	                  		"<i class=\""+menu.menu_icon+"\"></i>&nbsp;"+
	                  		"<span class=\"hide-menu\">"+menu.menu_name+"</span></a>"+
	              			"<ul aria-expanded=\"false\" class=\"collapse first-level\">"+
	              			subMenuContent+
	              			"</ul>"+
	              			"</li>";

	              	// if(menu_id != 0 && menu_id == sub_menu.menu_id ){

	              	// }
				}else{
					if(menu.menu_name === "Reports"){
						let menuLinkDynamic = "";
						if(localStorage.getItem("balaxi_user_role") == 4 ||
							localStorage.getItem("balaxi_user_role") == 9){
							menuLinkDynamic = "menu-report-executive.html";
						}else{
							menuLinkDynamic = "menu-report.html";
						}
						menuContent += "<li class=\"sidebar-item\">"+
					                		"<a id=\""+menu.menu_id+"\" class=\"sidebar-link waves-effect waves-dark sidebar-link theme-menu\""+
					                  		"href=\"#\""+
					                  		"onclick=\"loadPageContent("+menu.menu_id+",'"+menuLinkDynamic+"')\""+
					                  		"aria-expanded=\"false\">"+
					                  		"<i class=\""+menu.menu_icon+"\"></i>&nbsp;"+
					                  		"<span class=\"hide-menu\">"+menu.menu_name+"</span></a>"+
				              			"</li>";
					}else{
						menuContent += "<li class=\"sidebar-item\">"+
					                		"<a id=\""+menu.menu_id+"\" class=\"sidebar-link waves-effect waves-dark sidebar-link theme-menu\""+
					                  		"href=\"#\""+
					                  		"onclick=\"loadPageContent("+menu.menu_id+",'"+menu.menu_link+"')\""+
					                  		"aria-expanded=\"false\">"+
					                  		"<i class=\""+menu.menu_icon+"\"></i>&nbsp;"+
					                  		"<span class=\"hide-menu\">"+menu.menu_name+"</span></a>"+
				              			"</li>";
					}
				}
			}
		}
		
		var finalMenuContent = "<ul id=\"sidebarnav\" class=\"pt-4\">"+menuContent+"<ul>";
		$("#customMenu").html(finalMenuContent);

		loadPageContent(firstMenuIdToLoad,firstPageToLoad);
	}else{
		alert("It looks like there is no any menu assigned to your role. Please talk with system administrator.");
		window.location.href="../index.html";
		return false;
	}
}

function loadPageContent(menuId,menuLink) {
	if(menuLink === "signout.html"){
		window.location.href = "../../app/index.html";
	}else{
		$('.sidebar-link').removeClass('active-menu');
		// console.log('.sidebar-link #'+menuId);
		$('#'+menuId+'.sidebar-link').addClass('active-menu');
		$("#pageContent").load(menuLink);

		sessionStorage.setItem("formFlag", 1);
	}
}

function loadPageContentFromDashboard(menuId,menuLink) {
	if(menuLink === "signout.html"){
		window.location.href = "../../app/index.html";
	}else{
		$('.sidebar-link').removeClass('active-menu');
		// console.log('.sidebar-link #'+menuId);
		$('#'+menuId+'.sidebar-link').addClass('active-menu');
		$("#pageContent").load(menuLink);
	}
}

function loadPageContentFromBackButton(menuId,menuLink){
	if(menuLink === "signout.html"){
		window.location.href = "../../app/index.html";
	}else{
		$('.sidebar-link').removeClass('active-menu');
		// console.log('.sidebar-link #'+menuId);
		$('#'+menuId+'.sidebar-link').addClass('active-menu');
		$("#pageContent").load(menuLink);

		sessionStorage.setItem("formFlag", 0);
	}
}

function loadPageContentFromBackButtonDashboard(menuId,menuLink){
	if(menuLink === "signout.html"){
		window.location.href = "../../app/index.html";
	}else{
		$('.sidebar-link').removeClass('active-menu');
		// console.log('.sidebar-link #'+menuId);
		$('#'+menuId+'.sidebar-link').addClass('active-menu');
		$("#pageContent").load(menuLink);

		sessionStorage.setItem("formFlag", 2);
	}
}

function loadSubPageContent(menuLink) {
	$("#pageContent").load(menuLink);
}

function downloadFile(tbl,col,col1,val,format) {
    var queryString = "uploaded-file.html?tbl="+tbl+"&col="+col+"&col1="+col1+"&val="+val+"&format="+format;
    popupwindow(queryString, "Report", "_blank", 1000, 700);
}

function downloadFileByURL(url){
	const a = document.createElement('a');
	a.href = serverURL+url;
	a.target = "_blank";
	a.click();
}

function loadUploadedFile(tbl,col,col1,val,format) {
	let fileResult = getUploadedFile(tbl,col,col1,val);
    // console.log(fileResult);
    if(fileResult == "Invalid"){
		alert("No certificate uploaded by client yet.");
    }else{
    	jsonFileData = JSON.parse(fileResult);
    	if(jsonFileData.length > 0){
            for(i=0; i<jsonFileData.length; i++){
                var counter = jsonFileData[i];
                var report = "";
                if(format == "pdf"){
					report = "<iframe title='document' width='100%' height='100%' src='data:application/pdf;base64,"+encodeURI(counter.file_content)+"'></iframe>";
                }else if(format == "jpg"){
                	report = "<img src=\"data:application/jpg;base64,"+counter.file_content+"\" height=\"100%\" width=\"100%\" />";
                }else{
                	//Nothing
                }
                $("#fileContent").html(report);
            }
        }
    }
}

function getUploadedFile(tbl,col,col1,val) {
	return callAPIService("get_uploaded_file.php","tbl="+tbl+"&col="+col+"&col1="+col1+"&val="+val);
}

function fileValidation(fileInput,format){
	if (!window.FileReader) { // This is VERY unlikely, browser support is near-universal
        alert("The file API isn't supported on this browser yet.");
        return false;
    }
    var input = document.getElementById(fileInput);
    if (!input.files) { // This is VERY unlikely, browser support is near-universal
        alert("This browser doesn't seem to support the `files` property of file inputs.");
        return false;
    } else if (!input.files[0]) {
        alert("Please upload file.");
        return false;
    } else {
        var file = input.files[0];
        if(format == "pdf"){
        	if(file.size >= 5242880){
        		alert("Sorry! You can upload a file which is less than 5 MB in size.");
	        	$("#"+fileInput).focus();
	        	return false;
	        }
        	if(!file.name.toUpperCase().endsWith(format.toUpperCase())){
	        	alert("Kindly upload a "+format+" file.");
	        	$("#"+fileInput).focus();
	        	return false;
	        }
	    }else if(format == "dossier"){
	    	console.log(file.size);
	    	console.log(104857600);
	        if(file.size >= 104857600){
        		alert("Sorry! You can upload a file which is less than 100 MB in size.");
	        	$("#"+fileInput).focus();
	        	return false;
	        }
        	if(!(file.name.toUpperCase().endsWith("PDF")
        		|| file.name.toUpperCase().endsWith("DOC")
        		|| file.name.toUpperCase().endsWith("DOCX")
        		|| file.name.toUpperCase().endsWith("ZIP")
        		|| file.name.toUpperCase().endsWith("RAR")) ){
	        	alert("Kindly upload a pdf/word/zip/rar document file.");
	        	$("#"+fileInput).focus();
	        	return false;
	        }
        }else if(format == "testReport"){
	        if(file.size >= 5242880){
        		alert("Sorry! You can upload a file which is less than 5 MB in size.");
	        	$("#"+fileInput).focus();
	        	return false;
	        }
        	if(!(file.name.toUpperCase().endsWith("PDF")
        		|| file.name.toUpperCase().endsWith("PNG")
        		|| file.name.toUpperCase().endsWith("JPG")
        		|| file.name.toUpperCase().endsWith("JPEG")
        		|| file.name.toUpperCase().endsWith("BMP")
        		|| file.name.toUpperCase().endsWith("GIF")
        		|| file.name.toUpperCase().endsWith("SVG")) ){
	        	alert("Kindly upload a pdf/jpg document file.");
	        	$("#"+fileInput).focus();
	        	return false;
	        }
        }else if(format == "msds"){
	        if(file.size >= 5242880){
        		alert("Sorry! You can upload a file which is less than 5 MB in size.");
	        	$("#"+fileInput).focus();
	        	return false;
	        }
        	if(!(file.name.toUpperCase().endsWith("PDF")
        		|| file.name.toUpperCase().endsWith("DOC")
        		|| file.name.toUpperCase().endsWith("DOCX")) ){
	        	alert("Kindly upload a pdf/word document file.");
	        	$("#"+fileInput).focus();
	        	return false;
	        }
        }else if(format == "monogram"){
	        if(file.size >= 5242880){
        		alert("Sorry! You can upload a file which is less than 5 MB in size.");
	        	$("#"+fileInput).focus();
	        	return false;
	        }
        	if(!(file.name.toUpperCase().endsWith("PDF")
        		|| file.name.toUpperCase().endsWith("PNG")
        		|| file.name.toUpperCase().endsWith("JPG")
        		|| file.name.toUpperCase().endsWith("JPEG")
        		|| file.name.toUpperCase().endsWith("BMP")
        		|| file.name.toUpperCase().endsWith("GIF")
        		|| file.name.toUpperCase().endsWith("SVG")) ){
	        	alert("Kindly upload a pdf/jpg document file.");
	        	$("#"+fileInput).focus();
	        	return false;
	        }
        }else{
        	if(file.size >= 3145728){
	        	alert("Sorry! You can upload a file which is less than 3 MB in size.");
	        	$("#"+fileInput).focus();
	        	return false;
	        }
        	if(!(file.name.toUpperCase().endsWith("PNG")
        		|| file.name.toUpperCase().endsWith("JPG")
        		|| file.name.toUpperCase().endsWith("JPEG")
        		|| file.name.toUpperCase().endsWith("BMP")
        		|| file.name.toUpperCase().endsWith("GIF")
        		|| file.name.toUpperCase().endsWith("SVG")) ){
	        	alert("Kindly upload a image file.");
	        	$("#"+fileInput).focus();
	        	return false;
	        }
        }
    }
    return true;
}

function onlyNumberKey(evt) {
    // Only ASCII character in that range allowed
    var ASCIICode = (evt.which) ? evt.which : evt.keyCode
    if (ASCIICode > 31 && (ASCIICode < 48 || ASCIICode > 57))
        return false;
    return true;
}

function deleteExecutiveEntry(tblNm,colId,colVal) {
	let response = 0;
	if(localStorage.getItem("balaxi_user_role") == 1 ||
		localStorage.getItem("balaxi_user_role") == 2 ||
		localStorage.getItem("balaxi_user_role") == 3){

		if(confirm("Are you sure want to delete this entry?")){
			let deleteResult = deleteEntry(tblNm,colId,colVal);
		    // console.log(deleteResult);
		    if(deleteResult == "1" || deleteResult == '1' || deleteResult == 1){
				alert("Executive entry successfully deleted.");
				response = 1;
		    }else{
		    	alert(deleteResult);
		    	response = 0;
		    }
		}

	}else{
		alert("You don't have access to delete this entry.");
		response = 0;
	}
    return response;
}

function deleteEntry(tblNm,colId,colVal) {
	return callAPIService("delete_executive_entry.php","login_id="+localStorage.getItem("balaxi_user_id")+"&tbl_nm="+tblNm+"&col_id="+colId+"&col_val="+colVal);
}

function loadNotifications(){
    let notificationResult = getNotifications();
    // console.log(notificationResult);
    var jsonNotificationData = JSON.parse(notificationResult);
    var notificationTbody = "";
    if(jsonNotificationData.length > 0){
        for(i=0; i<jsonNotificationData.length; i++){
        	var counter = jsonNotificationData[i];
        	if(i==5){
        		notificationTbody += "<a class=\"dropdown-item text-center\" href=\"#\" onclick=\"loadPageContent(98,'notifications.html')\">"+
	                                "Click here to view all"+
	                                "</a>";
        		break;
        	}else{
	            notificationTbody += "<a class=\"dropdown-item\" href=\"#\" onclick=\"loadProductDetailsFromNotifications("+counter.product_id+","+counter.notify_id+")\">"+
	                                "Product <b>"+counter.notify_type+"</b> has been completed for Product: <b>"+counter.product_name+" ["+counter.country_name+"]</b>"+
	                                "</a>";
        	}
        }

        $("#notification").html(jsonNotificationData.length);
        $("#notification").show();
    }else{
        notificationTbody += "<a class=\"dropdown-item\" href=\"#\">"+
        						"No notification for you."+
        					"</a>";
        $("#notification").hide();
    }
    $(".notification-list").html(notificationTbody);
}

function getNotifications() {
	return callAPIService("get_notifications.php","user_id="+localStorage.getItem("balaxi_user_id"));
}

function loadProductDetailsFromNotifications(productId,notifyId){

	let notificationResult = markNotificationRead(notifyId);
	if(String(notificationResult).trim() != "1"){
		alert(notificationResult);
		return false;
	}
	
    sessionStorage.clear();
    sessionStorage.removeItem("product_details_form_flag");
    sessionStorage.setItem("product_details_form_flag",3);
    sessionStorage.setItem("product_details_product_id",productId);
    sessionStorage.setItem("current_report","dashboard.html");
    loadSubPageContent("product-assign-details.html");
}

function markNotificationRead(notifyId) {
	return callAPIService("update_notification.php","notify_id="+notifyId);
}

function ExportToExcel(tableData, tableId) {
	tableData.page.len( -1 ).draw();
	window.open('data:application/vnd.ms-excel,' + 
	encodeURIComponent($("#"+tableId).parent().html()));
	setTimeout(function(){
	  tableData.page.len(10).draw();
	}, 1000);
}