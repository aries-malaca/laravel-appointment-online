<template>
    <div class="tab-pane" id="reviews">
        <div class="row">
            <div class="col-md-3">

            </div>
            <div class="col-md-9">

            </div>
        </div>
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