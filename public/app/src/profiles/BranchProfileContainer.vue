<template>
    <div class="client">
        <branch-profile :show="true" :with_back="false" v-if="user.is_client !== 1"
                        :token="token" :configs="configs" :id="view_id" :user="user" />
        <unauthorized-error v-else></unauthorized-error>
    </div>
</template>

<script>
    import BranchProfile from './BranchProfile.vue';
    import UnauthorizedError from '../errors/UnauthorizedError.vue';
    export default {
        name: 'BranchProfileContainer',
        components:{ BranchProfile, UnauthorizedError },
        props:['token','configs','user'],
        data: function(){
            return {
                title: 'Branch Profile',
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