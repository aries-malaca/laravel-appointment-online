new Vue({
    el:"#forgot",
    data:{
        token:undefined,
        email:'',
        birth_date:'',
        sent:false
    },
    methods:{
        requestPassword:function(button){
            var $btn = $(button.target);
            $btn.button('loading');
            let u = this;

            $.ajax({
                url: '../../forgot/requestPassword',
                method: 'POST',
                data: { email:this.email, birth_date:this.birth_date },
                success: function (data) {
                    if(data.result == 'success'){
                        u.sent = true;
                    }
                    else{
                        toastr.error("Email with the corresponding birth date not found.");
                    }

                    $btn.button('reset');
                },
                error:function(error, status, message){
                    toastr.error("An error occurs, " + error.responseJSON.error);
                    $btn.button('reset');
                },
            });
        }
    },
    mounted:function(){
        this.token = $.cookie("login_cookie");

        if(this.token !== undefined){
            window.location.href = '../../';
        }
    }
});