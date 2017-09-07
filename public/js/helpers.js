var XHRCatcher = function(error){
    if(error.response.status === 500){
        toastr.error(error.response.statusText);
        return false;
    }

    var response_data = error.response.data.error;
    toastr.error("An error occurs: "+response_data);

    if(typeof(response_data) !== 'object'){
        if(response_data.search('token') !== -1 ){
            $.removeCookie('login_cookie');
            window.location.href='/../../login';
        }
    }
};