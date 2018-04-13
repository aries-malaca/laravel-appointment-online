var reg = new Vue({
    el:"#register",
    data:{
        token:undefined,
        newUser:{
            email:'',
            password:'',
            user_address:'',
            verify_password:'',
            first_name:'',
            middle_name:'',
            last_name:'',
            birth_date:'',
            gender:'female',
            home_branch:0,
            user_mobile:'',
            from_facebook:false,
            is_agreed:false,
            boss_id:null
        },
        branches:[],
        terms:'',
        aj:false,
        accounts:[],
        loading:false,
    },
    methods:{
        onBlur:function(){
            if(this.aj !== false){
                this.aj.abort();
                this.aj = false;
            }

            let u = this;
            if( this.newUser.first_name !== '' && this.newUser.last_name !== ''){
                this.loading = true;
                this.aj = $.get('https://api.lay-bare.com/transactions/getLastTransaction?birth_date='+u.newUser.birth_date+'&first_name='+u.newUser.first_name+'&last_name='+u.newUser.last_name,function(response){
                    u.accounts = response;
                    u.loading = false;
                });
            }
        },
        getBranches:function(){
            let u = this;
            $.get('/api/branch/getBranches/active',function(response){
                u.branches = response;
            });
        },
        getTerms:function(){
            let u = this;
            $.get('/api/config/getTerms',function(response){
                u.terms = response;
            });
        },
        agree:function(){
            this.newUser.is_agreed = true;
            $("#terms-modal").modal("hide");
        },
        chooseAccount(account){
            let u = this;
            SweetConfirmation("Do you want to choose this as your transaction record account? " +
                "Once validated, you'll be notified via email and credit this account's transaction into your account.",
                function(){
                    u.newUser.boss_id = account.custom_client_id;
                }
            )
        },
        resetForm(){
            SweetConfirmation("Are you sure?",
                function(){
                   window.location.reload();
                }
            )
        },
        removeBossID(){
            this.newUser.boss_id = null;
        },
        register:function(button){
            if(this.accounts.length > 0 && this.newUser.boss_id === null && this.aj !== false){
                if(!confirm("Proceed without selecting Transaction account?"))
                    return false;
            }

            var $btn = $(button.target);
            $btn.button('loading');
            let u = this;
            this.newUser.captcha_response = $("#g-recaptcha-response").val();
            this.newUser.captcha_secret = '6LcF8VIUAAAAAINfHkEFTD76JBSS_D0JJL6F5enS';
            $.ajax({
                url: '../../auth/register',
                method: 'POST',
                data: this.newUser,
                success: function (response){
                    $.ajax({
                        url: '/api/user/sendConfirmation?email='+u.newUser.email +'&token=' + response.token,
                        method: 'GET',
                        data: this.newUser,
                        complete: function (){
                            $.cookie("login_cookie", response.token, { path: '/', expires: 100000 });
                            window.location.reload();
                        },
                    });
                },
                error:function(error){
                    if(error.status === 400){
                        var msg = error.responseJSON.error;
                        if(msg.toString().search("email has already") !== -1){
                            SweetConfirmation("The email you area trying to use is already registered, do you want to Login?",
                                function(){
                                    window.location.href = '../../login?email='+u.newUser.email;
                                }
                            )
                        }
                        else{
                            if(msg.length>3)
                                msg.splice(3, msg.length);

                            toastr.error(msg);
                        }
                    }
                    $btn.button('reset');
                },
            });
        }
    },
    mounted:function(){

        this.token = $.cookie("login_cookie");
        let u = this;
        if(this.token !== undefined)
            window.location.href = '../../';

        if($("#fbid").val() !== '' && $("#accessToken").val() !== '')
            $.ajax({
                url: '../../api/user/fbLogin',
                method: 'POST',
                data: {userID: $("#fbid").val(), accessToken: $("#accessToken").val()},
                success: function (data) {
                    $.cookie("login_cookie", data, { path: '/' });
                    window.location.href = '../../';
                },
                error:function(error){

                    let user = error.responseJSON.user;
                    u.newUser.from_facebook = true;
                    u.newUser.first_name = user.first_name;
                    u.newUser.middle_name = user.middle_name!==undefined?user.middle_name:'';
                    u.newUser.last_name = user.last_name;
                    u.newUser.gender = user.gender;
                    u.newUser.email = user.email!==undefined?user.email:'';
                    if(user.email !== undefined ){
                        u.newUser.from_facebook = true;
                        u.newUser.fbid = $("#fbid").val();
                    }
                },
            });

        this.getTerms();
        this.getBranches();

        $("#autocomplete").change(function(event){
            setTimeout(function(){
                console.log(event);
                u.newUser.user_address = event.target.value;
            },500);
        });
    }
});
