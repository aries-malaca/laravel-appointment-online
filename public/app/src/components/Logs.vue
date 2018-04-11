<template>
    <div class="portlet light">
        <div class="portlet-title">
            <div class="caption">
                <i class="icon-puzzle font-grey-gallery"></i>
                <span class="caption-subject bold font-green uppercase">Activity Logs</span>
            </div>
        </div>
        <div class="portlet-body">
            <logs-timeline :logs="logs"></logs-timeline>
        </div>
    </div>
</template>
<script>
    import LogsTimeline from './timelines/LogsTimeline.vue';
    export default {
        name: 'Logs',
        components:{ LogsTimeline },
        data(){
            return{
                logs:[]
            }
        },
        methods:{
            getUserLogs(){
                let u = this;
                axios.get('/api/audits/getAudits/'+ this.user.id + '?token='+ this.token)
                    .then(function (response) {
                        u.logs = response.data;
                    })
                    .catch(function (error) {
                        XHRCatcher(error);
                    });
            }
        },
        computed:{
            user(){
                return this.$store.state.user;
            },
            token(){
                return this.$store.state.token;
            }
        },
        mounted(){
            this.getUserLogs()
        }
    }
</script>