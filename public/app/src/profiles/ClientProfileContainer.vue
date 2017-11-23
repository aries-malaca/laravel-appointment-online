<template>
    <div class="client">
        <client-profile :show="true" :with_back="false" :user="user" v-if="user.is_client !== 1"
                        :token="token" :configs="configs" :id="view_id" />

        <unauthorized-error v-else></unauthorized-error>
    </div>
</template>

<script>
    import UnauthorizedError from '../errors/UnauthorizedError.vue';
    import ClientProfile from './ClientProfile.vue';

    export default {
        name: 'ClientProfileContainer',
        components:{ ClientProfile, UnauthorizedError},
        props:['token','configs','user'],
        data: function(){
            return {
                title: 'Client Profile',
                view_id:Number(this.$route.params.id)
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