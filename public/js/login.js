new Vue({
    el:"#login",
    data:{
        email:'',
        password:'',
        remember:false,
        token:undefined
    },
    methods:{
        login:function(button){
            var $btn = $(button.target);
            $btn.button('loading');
            
            $.ajax({
                url: '../../auth/login',
                method: 'POST',
                data: {email:this.email, password:this.password, remember:this.remember},
                success: function (data) {
                    if (data.result == 'failed'){
                        toastr.error("Incorrect Email or Password.");
                        $btn.button('reset');
                    }
                    else{
                        $.cookie("login_cookie", data, { path: '/' });
                        window.location.href = '../../';
                        $btn.dataset.loadingText = 'Redirecting...';
                    }
                },
                error:function(error, status, message){
                    toastr.error("An error occurs, " + message);
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
    }
});

function checkLoginState(){
    FB.getLoginStatus(function(response) {
        if(response.status == 'connected'){
            $.ajax({
                url: '../../api/user/fbLogin',
                method: 'POST',
                data: response.authResponse,
                success: function (data) {
                    $.cookie("login_cookie", data, { path: '/' });
                    window.location.href = '../../';
                },
                error:function(error){
                    if(error.status === 300){
                        toastr.info("It seems your Facebook Account not linked yet to LBO. You may register instead.");
                    }
                },
            });
        }
    });
}