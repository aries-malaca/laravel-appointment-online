new Vue({
    el:"#login",
    data:{
        email:'',
        password:'',
        remember:false,
        token:undefined,
        attempts:0
    },
    methods:{
        login:function(button){
            var $btn = $(button.target);
            $btn.button('loading');
            let u = this;
            $.ajax({
                url: '../../auth/login',
                method: 'POST',
                data: {email:this.email, password:this.password, remember:this.remember, attempts:this.attempts},
                success: function (data) {
                    $.cookie("login_cookie", data.token, { path: '/', expires: 100000 });
                    window.location.href = '../../';
                    $btn.dataset.loadingText = 'Redirecting...';
                },
                error:function(error){
                    if(error.status === 400){
                        toastr.error("An error occurs, " + error.responseJSON.error);
                        if(error.responseJSON.attempts !== undefined)
                            u.attempts = error.responseJSON.attempts;
                    }
                    $btn.button('reset');
                },
            });
        },
        listenKey:function(event){
            if(event.keyCode == 13){
                this.login($("#btn-login"));
            }
        },

    },
    mounted:function(){
        $("#email").focus();
        this.token = $.cookie("login_cookie");

        if(this.token !== undefined){
           window.location.href = '../../';
        }

        this.email = $("#email").val();
    }
});

function checkLoginState(){
        if(response.status === 'connected'){
            $.ajax({
                url: '../../api/user/fbLogin',
                method: 'POST',
                data: response.authResponse,
                success: function (data) {
                    $.cookie("login_cookie", data.token, { path: '/', expires: 100000 });
                    window.location.href = '../../';
                },
                error:function(error){
                    if(error.status === 300){
                        toastr.info("It seems your Facebook Account not linked yet to LBO. You may register instead.");
                        let user = error.responseJSON.user;
                        setTimeout(function(){
                            window.location.href = '../../register?accessToken='+response.authResponse.accessToken+'&fbid=' + user.id;
                        },2000);
                    }
                    else if(error.status === 400){
                        toastr.warning(error.responseJSON.message);
                    }
                },
            });
        }
    });
}