<template>
    <div class="timeline">
        <!-- TIMELINE ITEM -->
        <div class="timeline-item">
            <div class="timeline-badge">
                <div class="timeline-icon">
                    <i class="icon-docs font-red-intense"></i>
                </div>
            </div>
            <div class="timeline-body">
                <div class="timeline-body-arrow"> </div>
                <div class="timeline-body-head">
                    <div class="timeline-body-head-caption">
                        <span class="timeline-body-alerttitle font-green-haze">Server Report</span>
                        <span class="timeline-body-time font-grey-cascade">Yesterday at 11:00 PM</span>
                    </div>
                </div>
                <div class="timeline-body-content">
                    <span class="font-grey-cascade"> Lorem ipsum dolore sit amet
                        <a href="javascript:;">Ispect</a>
                    </span>
                </div>
            </div>
        </div>
        <!-- END TIMELINE ITEM -->
    </div>
</template>
<script>
    export default {
        name: 'LogsTimeline',
        props: ['logs']
    }
</script>