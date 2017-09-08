<template>
    <div class="services">
        <div class="portlet light">
            <div class="portlet-title tabbable-line">
                <div class="caption">
                    <i class="icon-puzzle font-grey-gallery"></i>
                    <span class="caption-subject bold font-grey-gallery uppercase"> {{ title }} </span>
                </div>
                <ul class="nav nav-tabs">
                    <li class="active">
                        <a href="#services" data-toggle="tab">Services</a>
                    </li>
                    <li>
                        <a href="#service-types" data-toggle="tab">Service Types</a>
                    </li>
                    <li>
                        <a href="#service-packages" data-toggle="tab">Service Packages</a>
                    </li>
                    <li>
                        <a href="#products" data-toggle="tab">Products</a>
                    </li>
                </ul>
            </div>
            <div class="portlet-body">
                <div class="tab-content">
                    <div class="tab-pane active" id="services">

                    </div>
                    <div class="tab-pane" id="service-types">

                    </div>
                    <div class="tab-pane" id="service-packages">

                    </div>
                    <div class="tab-pane" id="products">

                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: 'Services',
        data: function(){
            return {
                title: 'Services',
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