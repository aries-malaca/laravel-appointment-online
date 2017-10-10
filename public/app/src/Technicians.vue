<template>
    <div class="technicians">
        <div class="portlet light">
            <div class="portlet-title">
                <div class="caption">
                    <i class="icon-puzzle font-grey-gallery"></i>
                    <span class="caption-subject bold font-grey-gallery uppercase">
                        {{ title }}
                    </span>
                    <button class="btn btn-info" @click="fetchEMSTechnicians">Fetch From EMS</button>
                </div>
            </div>
            <div class="portlet-body">

            </div>
        </div>
    </div>
</template>
<script>
    export default {
        name: 'Technicians',
        props:['token','user'],
        data: function(){
            return {
                title: 'Technicians',
                technicians:[]
            }
        },
        methods:{
            getTechnicians:function(){
                let u = this;
                axios.get('/api/technician/getTechnicians')
                    .then(function (response) {
                        u.technicians = response.data;
                    })
                    .catch(function (error) {
                        XHRCatcher(error);
                    });
            }
        },
        mounted:function(){
            this.$emit('update_title', this.title);
            this.$emit('update_user');

            this.getTechnicians();
        }
    }
</script>