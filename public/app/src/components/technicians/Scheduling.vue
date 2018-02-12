<template>
    <div class="tab-pane" id="scheduling">

    </div>
</template>
<script>
    export default {
        name: 'TechniciansList',
        data: function(){
            return {

            }
        },
        computed:{
            token(){
                return this.$store.state.token;
            },
            technicians(){
                return this.$store.state.technicians.technicians;
            }
        }
    }
</script>