new Vue({
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
            mobile:'',
        },
        branches:[],
        agree:false,
        terms:''
    },
    methods:{
        listenKey:function(){

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
                    window.location.href = '../../login?message=registered&email='+u.newUser.email;
                },
                error:function(error){
                    if(error.status === 400){
                        let msg = error.responseJSON.error;
                        if(msg.toString().search("email has already") !== -1){
                            SweetConfirmation("The email you area trying to use is already registered, do you want to Login?",
                                function(){
                                    window.location.href = '../../login?email='+u.newUser.email;
                                }
                            )
                        }
                    }
                    $btn.button('reset');
                },
            });
        },
    },
    mounted:function(){
        this.token = $.cookie("login_cookie");

        if(this.token !== undefined)
            window.location.href = '../../';

        this.newUser.email = $("#email").val();
        this.newUser.first_name = $("#first_name").val();
        this.newUser.middle_name = $("#middle_name").val();
        this.newUser.last_name = $("#last_name").val();

        this.getTerms();
        this.getBranches();
    }
});