var vue_login = new Vue({
    el:"#login",
    data:{
        email:'',
        password:'',
        remember:false
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
        }
    }
});