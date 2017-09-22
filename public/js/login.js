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
                    $.cookie("login_cookie", data.token, { path: '/', expires: 100000 });
                    window.location.href = '../../';
                    $btn.dataset.loadingText = 'Redirecting...';
                },
                error:function(error){
                    console.log(error);
                    if(error.status === 400){
                        toastr.error("An error occurs, " + error.responseJSON.error);
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
                        let user = error.responseJSON.user;
                        setTimeout(function(){
                            window.location.href = '../../register?email='+user.email+'&first_name='+user.first_name+'&middle_name='+user.middle_name+'&last_name='+user.last_name;
                        },2000);
                    }
                },
            });
        }
    });
}