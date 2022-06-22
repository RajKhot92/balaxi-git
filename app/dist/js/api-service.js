let apiResponse = {
    status_code:'',
    data:'',
    error:''
}

const PRODUCT_OWNER_ROLE = 3;
const EXECUTIVE_ROLE = 4;

// const serverURL = "http://localhost/balaxi/api/";
const serverURL = "http://localhost/balaxi/balaxi-git/api/";
// const serverURL = "https://klugerkopf.com/balaxi/api/";

function showLoader(){
    $("#loader").show();
    $("#mainContainer").prop("disabled","disabled");
    // alert("Loading...");
}

function hideLoader(){
    $("#loader").hide();
    $("#mainContainer").show();
    // alert("Hide loading...");
}

function callAPIService(url, formData){
    showLoader();
	let response = null;
    // $("body").addClass("loading");
    $.ajax({
        url: serverURL+url,
        type: "GET",
        async: false,
        cache: false,
        data: formData,
        success: function(result) {
            response = result;
            hideLoader();
        },
        error: function() {
            response = "API service is not responding. Please try again.";
            hideLoader();
        }
    });
    return response;
}

function callAPIServicePost(url, formData){
    showLoader();
    let response = null;
    $.ajax({
        url: serverURL+url,
        type: "POST",
        async: false,
        cache: false,
        processData: false,
        contentType: false,
        // dataType: 'json',
        data: formData,
        success: function(result) {
            response = result;
            hideLoader();
        },
        error: function() {
            response = "API service is not responding. Please try again.";
            hideLoader();
        }
    });
    return response;
}
