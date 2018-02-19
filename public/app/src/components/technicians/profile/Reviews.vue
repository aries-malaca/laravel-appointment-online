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
        computed:{
            token(){
                return this.$store.state.token;
            },
        }
    }
</script>