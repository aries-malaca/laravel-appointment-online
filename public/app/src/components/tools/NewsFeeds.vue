<template>
    <div class="portlet light">
        <div class="portlet-title">
            <div class="caption">
                <i class="icon-puzzle font-grey-gallery"></i>
                <span class="caption-subject bold font-grey-gallery uppercase"> {{ title }} </span>
            </div>
            <div class="tools">
                <a href="" class="collapse" data-original-title="" title=""> </a>
                <a href="" class="reload" data-original-title="" title=""> </a>
                <a href="" class="fullscreen" data-original-title="" title=""> </a>
            </div>
        </div>
        <div class="portlet-body">

        </div>
    </div>
</template>

<script>
    export default {
        name: 'NewsFeeds',
        data: function(){
            return {
                title: 'News Feeds',
            }
        },
        methods:{

        },
        mounted:function(){
            this.$store.commit('updateTitle', 'News Feeds');
        }
    }
</script>