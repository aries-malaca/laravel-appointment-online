<template>
    <div class="tab-pane active" id="schedules">
    </div>
</template>
<script>
    export default {
        name: 'Schedules',
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