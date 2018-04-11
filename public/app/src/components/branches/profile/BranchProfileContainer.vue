<template>
    <div class="client">
        <branch-profile :with_back="false" />
    </div>
</template>

<script>
    import BranchProfile from './BranchProfile.vue';

    export default {
        name: 'BranchProfileContainer',
        components:{ BranchProfile},
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