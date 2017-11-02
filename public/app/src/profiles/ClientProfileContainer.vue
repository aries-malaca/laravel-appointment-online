<template>
    <div class="client">
        <client-profile :show="true" :with_back="false" :user="user"
                        :token="token" :configs="configs" :id="view_id" />
    </div>
</template>

<script>
    import ClientProfile from './ClientProfile.vue';

    export default {
        name: 'ClientProfileContainer',
        components:{ ClientProfile},
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