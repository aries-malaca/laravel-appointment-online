var XHRCatcher = function(error){
    if(error.status === 500){
        if(error.response !== undefined){
            toastr.error(error.response.statusText);
            return false;
        }

        toastr.error("Internal Server Error: 500");
        return false;
    }

    var response_data;
    if(error.responseJSON === undefined){
        response_data = error.response.data.error;
    }
    else{
        response_data = error.responseJSON.error;
    }
    toastr.error("An error occurs: "+response_data);

    if(typeof(response_data) !== 'object'){
        if(response_data.search('token') !== -1 ){
            $.removeCookie('login_cookie');
            window.location.href='/../../login';
        }
    }
};