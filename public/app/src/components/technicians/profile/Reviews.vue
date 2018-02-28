<template>
    <div class="tab-pane active" id="reviews">
    </div>
</template>
<script>
    export default {
        name: 'Reviews',
        data: function(){
            return {
            }
        },
        methods:{
            getReviews(){
                let u = this;
                axios.get('/api/review/getReviews/technician/'+ this.technician.id)
                    .then(function (response) {
                        u.$store.commit('technicians/updateReviews', response.data);
                    });
            }
        },
        computed:{
            token(){
                return this.$store.state.token;
            },
            technician(){
                return this.$store.state.technicians.viewing_technician;
            }
        },
        mounted(){
            this.getReviews();
        }
    }
</script>