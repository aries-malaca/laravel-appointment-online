<template>
    <div class="calendar">
        <div class="portlet light">
            <div class="portlet-title tabbable-line">
                <div class="caption">
                    <i class="icon-puzzle font-grey-gallery"></i>
                    <span class="caption-subject bold font-grey-gallery uppercase"> {{ title }} </span>
                </div>
                <ul class="nav nav-tabs">
                    <li class="active">
                        <a href="#promos" data-toggle="tab">Promos</a>
                    </li>
                    <li>
                        <a href="#perks" data-toggle="tab">Perks</a>
                    </li>
                    <li>
                        <a href="#surveys" data-toggle="tab">Surveys</a>
                    </li>
                </ul>
            </div>
            <div class="portlet-body">
                <div class="tab-content">
                    <promos :token="token" :configs="configs" :user="user"></promos>
                    <perks :token="token" :configs="configs" :user="user"></perks>

                    <div class="tab-pane" id="surveys">
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import Promos from '../promotions/Promos.vue';
    import Perks from '../promotions/Perks.vue';

    export default {
        name: 'Promotions',
        components:{ Promos, Perks },
        props: ['token','configs','user'],
        data: function(){
            return {
                title: 'Promotions',
                surveys:[],
                surveyTable:{
                    columns: [
                        { label: 'Title', field: 'title', filterable: true },
                    ],
                    rowClicked: this.viewSurvey,
                },
            }
        },
        methods:{
            makeRequest:function(url, method, data, success_callback, error_callback){
                axios({url:url, method:method, data:data})
                    .then(function () {
                        success_callback();
                    })
                    .catch(function (error) {
                        error_callback(error);
                    });
            },
            getData:function(url, field){
                let u = this;
                axios.get(url)
                    .then(function (response) {
                        u[field] = [];
                        response.data.forEach(function(item){
                            u[field].push(item);
                        });
                    });
            },
            viewSurvey:function(survey){

            },
        },
        mounted:function(){
            this.$emit('update_title', this.title);
            this.$emit('update_user');
        }
    }
</script>
