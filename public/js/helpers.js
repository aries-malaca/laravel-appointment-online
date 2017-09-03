var XHRCatcher = function(error){
    var response_data = error.response.data.error;
    toastr.error("An error occurs: "+response_data);
    if(typeof(response_data) !== 'object'){
        if(response_data.search('token') !== -1 ){
            window.location.href='/auth/logout';
        }
    }
}