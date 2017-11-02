<template>
    <div class="client">
        <branch-profile :show="true" :with_back="false"
                        :token="token" :configs="configs" :id="view_id" :user="user" />
    </div>
</template>

<script>
    import BranchProfile from './BranchProfile.vue';

    export default {
        name: 'BranchProfileContainer',
        components:{ BranchProfile},
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