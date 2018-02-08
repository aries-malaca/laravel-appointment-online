<template>
    <div class="dashboard">
        <div v-if="user.username !== undefined">
            <client-dashboard v-if="user.is_client === 1" />
            <div v-else>
                <div v-if="user.level_data !== undefined">
                    <customer-service-dashboard v-if="user.level_data.dashboard === 'CustomerServiceDashboard'" />
                    <admin-dashboard v-if="user.level_data.dashboard === 'AdminDashboard'" />
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
        components:{ClientDashboard, CustomerServiceDashboard, AdminDashboard},
        computed:{
            user(){
                return this.$store.state.user;
            },
            token(){
                return this.$store.state.token;
            },
            configs(){
                return this.$store.state.configs;
            }
        },
        mounted:function(){
            this.$store.commit('updateTitle', 'Dashboard');
        }
    }
</script>