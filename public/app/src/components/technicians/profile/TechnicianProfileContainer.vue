<template>
    <div>
        <technician-profile :with_back="false" v-if="user.is_client !== 1" />
        <unauthorized-error v-else></unauthorized-error>
    </div>
</template>

<script>
    import TechnicianProfile from './TechnicianProfile.vue';
    import UnauthorizedError from '../../errors/UnauthorizedError.vue';
    export default {
        name: 'TechnicianProfileContainer',
        components:{ TechnicianProfile, UnauthorizedError },
        data: function(){
            return {
                title: 'Technician Profile',
            }
        },
        mounted:function(){
            this.$store.commit('updateTitle', 'Technician Profile');
            this.$store.commit('technicians/updateViewingTechnician', {id:this.$route.params.id});
        },
        computed:{
            user(){
                return this.$store.state.user;
            },
        }
    }
</script>