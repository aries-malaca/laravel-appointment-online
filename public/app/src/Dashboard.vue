<template>
    <div class="dashboard">
        <div v-if="user.username !== undefined">
            <client-dashboard v-if="user.is_client === 1" :user="user" :configs="configs" :token="token" />
            <div v-else>
                <div v-if="user.level_data !== undefined">
                    <customer-service-dashboard v-if="user.level_data.dashboard === 'CustomerServiceDashboard'"
                                    :user="user" :configs="configs" :token="token" />
                    <admin-dashboard v-if="user.level_data.dashboard === 'AdminDashboard'" :user="user" :configs="configs"
                                    :token="token" />
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import ClientDashboard from "./dashboards/ClientDashboard.vue";
    import CustomerServiceDashboard from "./dashboards/CustomerServiceDashboard.vue";
    import AdminDashboard from "./dashboards/AdminDashboard.vue";

    export default {
        name: 'Dashboard',
        props:['user','token','configs'],
        components:{ClientDashboard, CustomerServiceDashboard, AdminDashboard},
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