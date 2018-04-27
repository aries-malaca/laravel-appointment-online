<template>
    <div class="calendar">
        <div class="portlet light" v-if="gate(user, 'promos', 'view')">
            <div class="portlet-title tabbable-line">
                <div class="caption">
                    <i class="icon-puzzle font-grey-gallery"></i>
                    <span class="caption-subject bold font-grey-gallery uppercase"> {{ title }} </span>
                </div>
                <ul class="nav nav-tabs">
                    <li class="active">
                        <a href="#promos" data-toggle="tab">Promos</a>
                    </li>
                    <li v-if="gate(user, 'perks', 'view')">
                        <a href="#perks" data-toggle="tab">Perks</a>
                    </li>
                    <li v-if="gate(user, 'surveys', 'view')">
                        <a href="#surveys" data-toggle="tab">Surveys</a>
                    </li>
                    <li v-if="gate(user, 'campaign_manager', 'view')">
                        <a href="#campaign-manager" data-toggle="tab">Campaign Manager</a>
                    </li>
                </ul>
            </div>
            <div class="portlet-body">
                <div class="tab-content">
                    <promos></promos>
                    <perks></perks>
                    <div class="tab-pane" id="surveys"></div>
                    <campaign-manager></campaign-manager>
                </div>
            </div>
        </div>
        <unauthorized-error v-else></unauthorized-error>
    </div>
</template>

<script>
    import Promos from './Promos.vue';
    import Perks from './Perks.vue';
    import CampaignManager from './campaign/CampaignManager.vue';
    import UnauthorizedError from '../errors/UnauthorizedError.vue';

    export default {
        name: 'Promotions',
        components:{ Promos, Perks, CampaignManager, UnauthorizedError },
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
            this.$store.commit('updateTitle', 'Promotions');
        },
        computed:{
            user(){
                return this.$store.state.user;
            },
            token(){
                return this.$store.state.token;
            },
            configs(){
                return this.$store.state.configs;
            },
        }
    }
</script>
