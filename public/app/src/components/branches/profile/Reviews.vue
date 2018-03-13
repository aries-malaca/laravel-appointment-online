<template>
    <div class="tab-pane" id="reviews">

    </div>
</template>
<script>
    export default {
        name: 'Reviews',
        data(){
            return{
            };
        },
        methods:{
            getReviews(){
                let u = this;
                axios.get('/api/review/getReviews/branch/'+ this.branch.id)
                    .then(function (response) {
                        u.$store.commit('branches/updateReviews', response.data);
                    });
            }
        },
        mounted(){
            this.getReviews();
        },
        computed:{
            branch(){
                return this.$store.state.branches.viewing_branch;
            }
        }
    }
</script>