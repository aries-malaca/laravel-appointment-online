<template>
    <div class="dashboard">
        Dashboard
    </div>
</template>

<script>
    export default {
        name: 'Dashboard',
        data: function(){
            return {
                title: 'Dashboard',
            }
        },
        methods:{
            emit: function() {
                this.$emit('event_child', this.title)
            }
        },
        mounted:function(){
            this.emit();
        }
    }
</script>
