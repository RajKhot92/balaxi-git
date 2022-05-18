let apiResponse = {
    status_code:'',
    data:'',
    error:''
}

const PRODUCT_OWNER_ROLE = 3;
const EXECUTIVE_ROLE = 4;

//const serverURL = "http://localhost/balaxi/api/";
const serverURL = "https://klugerkopf.com/balaxi/api/";

function callAPIService(url, formData){
	let response = null;
    $.ajax({
        url: serverURL+url,
        type: "GET",
        async: false,
        cache: false,
        data: formData,
        success: function(result) {
            response = result;
        },
        error: function() {
            response = "API service is not responding. Please try again.";
        }
    });
    return response;
}
