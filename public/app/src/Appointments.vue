<template>
    <div class="appointments">
        <div class="portlet light">
            <div class="portlet-title">
                <div class="caption">
                    <i class="icon-puzzle font-grey-gallery"></i>
                    <span class="caption-subject bold font-grey-gallery uppercase"> {{ title }} </span>
                </div> &nbsp;
                <button v-if="user.is_client == 1" data-toggle="modal" href="#booking-modal" type="button" class="btn green-meadow">Book Now</button>
                <div class="tools">
                    <a href="" class="collapse" data-original-title="" title=""> </a>
                    <a href="" class="reload" data-original-title="" title=""> </a>
                    <a href="" class="fullscreen" data-original-title="" title=""> </a>
                </div>
            </div>
            <div class="portlet-body">

            </div>
        </div>
        <booking-modal></booking-modal>
    </div>
</template>

<script>
    import BookingModal from "./modals/BookingModal.vue";
    export default {
        name: 'Appointments',
        props: ['user'],
        components: { BookingModal },
        data: function(){
            return {
                title: 'Appointments',
            }
        },
        methods:{
            emit: function() {
                this.$emit('update_title', this.title)
            }
        },
        mounted:function(){
            this.emit();
        }
    }
</script>
