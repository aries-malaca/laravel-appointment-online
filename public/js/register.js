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
        },
        branches:[],
        agree:false,
        terms:'',
        clicked_yes:false
    },
    methods:{
        listenKey:function(event){

            let u = this;
            if(event.target.placeholder == 'First Name' || event.target.placeholder == 'Last Name' || event.target.type == 'date'){
             this.clicked_yes = false;
            }
            if( (this.newUser.first_name !== '' || this.newUser.last_name !== '' || this.newUser.birth_date !== '') && !this.clicked_yes){
                $.get('http://boss.lay-bare.com/laybare-online/API/get_last_transaction.php?birth_date='+u.newUser.birth_date+'&first_name='+u.newUser.first_name+'&last_name='+u.newUser.last_name,function(response){
                    if(response.length>0){
                        $.ajax({
                            url: '/api/client/searchClients?search=' + response[0].email_address,
                            method: 'GET',
                            data: this.newUser,
                            error: function (response1){
                                if(response1.responseJSON.result == 'failed'){
                                    let branch = Number(response.branch_id);
                                    u.clicked_yes = true;
                                    SweetConfirmation("Did you recently visited "+ u.branchName(branch) +"?",
                                        function(){
                                            u.boss_id = response.client_id;
                                        }
                                    )
                                }
                            },
                        });
                    }
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
        register:function(button){
            var $btn = $(button.target);
            $btn.button('loading');
            let u = this;

            $.ajax({
                url: '../../auth/register',
                method: 'POST',
                data: this.newUser,
                success: function (){
                    $.ajax({
                        url: '/api/user/sendConfirmation?email='+u.newUser.email,
                        method: 'GET',
                        data: this.newUser,
                        complete: function (){
                            window.location.href = '../../login?message=registered&email='+u.newUser.email;
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
                            if(msg.length>3){
                                msg.splice(3, msg.length);
                            }
                            toastr.error(msg);
                        }
                    }
                    $btn.button('reset');
                },
            });
        },
        branchName:function(id){
            var branch = 'Default';
            for(var x=0;x<this.branches.length;x++){
                if(id == this.branches[x].id){
                    branch = this.branches[x].branch_name;
                }
            }
            return branch;
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
