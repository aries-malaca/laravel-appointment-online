new Vue({
    el:"#register",
    data:{
        token:undefined,
        newUser:{
            email:'',
            password:'',
            first_name:'',
            middle_name:'',
            last_name:'',
            agree:false
        }
    },
    methods:{

    },
    mounted:function(){
        this.token = $.cookie("login_cookie");

        if(this.token !== undefined){
            //window.location.href = '../../';
        }

        this.newUser.email = $("#email").val();
        this.newUser.first_name = $("#first_name").val();
        this.newUser.middle_name = $("#middle_name").val();
        this.newUser.last_name = $("#last_name").val();
    }
});