import Vue from 'vue';
import App from './App.vue';
import router from './router';

Vue.config.productionTip = false;

new Vue({
    el: '#app',
    router,
    template: '<App/>',
    components: { App },
    data:{
        user:{},
        title:'Dashboard',
        token:''
    },
    methods:{
        getAuthenticatedUser:function(){
            $.get('/api/getUser?token=' + this.token ,function(data){
                console.log(data);
            }).error(function(){
                window.location.href = '/auth/logout';
            });
        },
        eventChild: function(title) {
            this.title = title;
        },
    },
    mounted:function(){
        this.getAuthenticatedUser();
        this.token = $.cookie("login_cookie");
    }
});
