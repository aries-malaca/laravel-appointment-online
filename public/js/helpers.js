var XHRCatcher=function(error){if(error.status===500){if(error.response!==undefined){toastr.error(error.response.statusText);return!1}
    toastr.error("Internal Server Error: 500");return!1}
    var response_data;if(error.responseJSON===undefined)
        response_data=error.response.data.error;else response_data=error.responseJSON.error;e=response_data;if(typeof(e)==='object')
        e=response_data.join("<br/> ");if(typeof(response_data)!=='object'){if(response_data.search('token')!==-1){$.removeCookie('login_cookie');window.location.href='/../../login';return!1}}
    toastr.error(e)};var SweetConfirmation=function(text,confirm_callback){swal({title:"Confirmation",text:text,showCancelButton:!0,closeOnCancel:!0,cancelButtonClass:'btn-sm red',confirmButtonClass:'btn-sm blue',confirmButtonText:'YES',cancelButtonText:'NO',},function(t){if(t){confirm_callback()}})}