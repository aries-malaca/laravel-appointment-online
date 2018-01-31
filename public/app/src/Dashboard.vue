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

            let u = this;
            setTimeout(function(){
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(function(position) {
                        u.geolocation = {
                            lat: position.coords.latitude,
                            lng: position.coords.longitude
                        };
                        var geocoder = new google.maps.Geocoder;
                        geocoder.geocode({'location': u.geolocation}, function(results, status) {
                            if (status === 'OK') {
                                axios({url:'/api/user/saveLocation?token=' + u.token, method:'post', data:{ geolocation:results }})
                                    .then(function () {
                                        console.log("Saved");
                                    });
                            }
                        });

                        axios({url:'/api/user/saveLocation?token=' + u.token, method:'post', data:{ geolocation:u.geolocation }})
                            .then(function () {
                                console.log("Saved");
                            });

                    });
                }
            },1000);

        }
    }
</script>