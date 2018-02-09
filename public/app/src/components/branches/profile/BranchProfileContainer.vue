<template>
    <div class="client">
        <branch-profile :with_back="false" v-if="user.is_client !== 1" />
        <unauthorized-error v-else></unauthorized-error>
    </div>
</template>

<script>
    import BranchProfile from './BranchProfile.vue';
    import UnauthorizedError from '../../errors/UnauthorizedError.vue';
    export default {
        name: 'BranchProfileContainer',
        components:{ BranchProfile, UnauthorizedError },
        data: function(){
            return {
                title: 'Branch Profile',
            }
        },
        mounted:function(){
            this.$store.commit('updateTitle', 'Branch Profile');
            this.$store.commit('branches/updateViewingBranch', {id:this.$route.params.id});
        },
        computed:{
            user(){
                return this.$store.state.user;
            },
        }
    }
</script>