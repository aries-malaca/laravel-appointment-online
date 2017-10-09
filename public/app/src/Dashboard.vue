<template>
    <div class="dashboard">
        <client-dashboard v-if="user.is_client === 1"></client-dashboard>
    </div>
</template>

<script>
    import ClientDashboard from "./dashboards/ClientDashboard.vue";
    export default {
        name: 'Dashboard',
        props:['user','token','configs'],
        components:{ClientDashboard},
        data: function(){
            return {
                title: 'Dashboard',
            }
        },
        methods:{

        },
        mounted:function(){
            this.$emit('update_title', this.title);
            this.$emit('update_user');
        }
    }
</script>