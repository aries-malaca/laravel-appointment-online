<template>
    <div class="client">
        <branch-profile :show="true" :with_back="false" v-if="user.is_client !== 1" :id="view_id" />
        <unauthorized-error v-else></unauthorized-error>
    </div>
</template>

<script>
    import BranchProfile from './BranchProfile.vue';
    import UnauthorizedError from '../errors/UnauthorizedError.vue';
    export default {
        name: 'BranchProfileContainer',
        components:{ BranchProfile, UnauthorizedError },
        data: function(){
            return {
                title: 'Branch Profile',
                view_id:Number(this.$route.params.id)
            }
        },
        mounted:function(){
            this.$store.commit('updateTitle', 'Branch Profile');
        },
        computed:{
            user(){
                return this.$store.state.user;
            },
        }
    }
</script>