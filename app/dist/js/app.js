var menu_id = 0, sub_menu_id = 0, retval = 0;

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

function loadSubPageContent(menuLink) {
	$("#pageContent").load(menuLink);
}