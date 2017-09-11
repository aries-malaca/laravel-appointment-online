new Vue({
    el:"#register",
    data:{
        token:undefined
    },
    methods:{

    },
    mounted:function(){
        this.token = $.cookie("login_cookie");

        if(this.token !== undefined){
            window.location.href = '../../';
        }
    }
});