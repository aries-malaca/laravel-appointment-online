<template>
    <div class="branches">
        <div class="portlet light" v-show="view === false" v-if="user.is_client !== 1">
            <div class="portlet-title tabbable-line">
                <div class="caption">
                    <i class="icon-puzzle font-grey-gallery"></i>
                    <span class="caption-subject bold font-grey-gallery uppercase"> Branches and Clusters </span>
                </div>
                <ul class="nav nav-tabs">
                    <li class="active">
                        <a href="#branches" data-toggle="tab">Branches</a>
                    </li>
                    <li>
                        <a href="#clusters" data-toggle="tab">Clusters</a>
                    </li>
                </ul>
            </div>
            <div class="portlet-body">
                <div class="tab-content">
                    <branches-list></branches-list>
                    <clusters></clusters>
                </div>
            </div>
        </div>
        <unauthorized-error v-else></unauthorized-error>
        <branch-profile v-if="view" :with_back="true"></branch-profile>
    </div>
</template>

<script>
    import UnauthorizedError from './errors/UnauthorizedError.vue';
    import BranchProfile from './branches/profile/BranchProfile.vue';
    import Clusters from './branches/Clusters.vue';
    import BranchesList from './branches/BranchesList.vue';

    export default {
        name: 'Branches',
        components:{  BranchProfile, UnauthorizedError, Clusters, BranchesList },
        data: function(){
            return{};
        },
        mounted:function(){
            this.$store.commit('updateTitle', 'Branches');
            this.$store.dispatch('branches/fetchBranches');
            this.$store.dispatch('branches/fetchClusters');
            this.$store.dispatch('branches/fetchCities');
            this.$store.dispatch('branches/fetchRegions');
            this.$store.commit('branches/updateViewingBranch', false);
            this.$store.commit('branches/updateEditingBranch', false);
        },
        computed:{
            view(){
                return this.$store.state.branches.viewing_branch;
            },
            user(){
                return this.$store.state.user;
            }
        }
    }
</script>