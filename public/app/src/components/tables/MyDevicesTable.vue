<template>
    <div>
        <h4>Device Management</h4>
        <table class="table-responsive table table-hover table-bordered">
            <thead>
            <tr>
                <th>Device</th>
                <th>Last Activity</th>
                <th>Address</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="device in devices">
                <td>{{ device.type }}</td>
                <td>
                    <span v-if="device.token != token">{{ moment(device.last_activity).fromNow() }}</span>
                    <span v-else>Currently In-use</span>
                </td>
                <td>
                    {{ getAddress(device) }}
                </td>
                <td>
                    <button v-if="device.token != token" class="btn btn-xs btn-danger" @click="destroyToken(device.token)">Logout</button>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
    export default {
        name: 'MyDevicesTable',
        props:['default_user'],
        methods:{
            destroyToken:function(token){
                let u = this;

                SweetConfirmation('Are you sure you want to logout from this session?', function(){
                    axios.post('/api/user/destroyToken', { token : token, user_id : u.default_user !== undefined ? u.default_user.id: u.user.id})
                        .then(function () {
                            toastr.success("Device has been logged out.");
                            if(u.default_user !== undefined)
                                u.$emit('emit_host');
                            else
                                u.$store.dispatch('fetchAuthenticatedUser');
                        })
                        .catch(function (error) {
                            XHRCatcher(error);
                        });
                });
            },
            getAddress:function(device){
                if(device.geolocation !== undefined)
                    for(var x=0;x<device.geolocation.length;x++){
                        if(device.geolocation[x].types.indexOf('locality') !== -1 )
                            return device.geolocation[x].formatted_address;
                    }

                return 'Unknown';
            }
        },
        computed:{
            user(){
                return this.$store.state.user;
            },
            token(){
                return this.$store.state.token;
            },
            devices(){
                if(this.default_user !== undefined)
                    return this.default_user.device_data;

                return this.user.device_data;
            }
        },
    }
</script>