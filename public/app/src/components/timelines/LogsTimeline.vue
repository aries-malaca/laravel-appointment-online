<template>
    <div class="timeline" style="max-height:450px;overflow-y: scroll">
        <!-- TIMELINE ITEM -->
        <div class="timeline-item" v-for="log in logs">
            <div class="timeline-badge">
                <div class="timeline-icon">
                    <i class="icon-user font-green-haze" v-if="log.category==='user'"></i>
                    <i class="icon-refresh font-green-haze" v-else-if="log.event==='updated'"></i>
                    <i class="icon-plus font-green-haze" v-else-if="log.event==='created'"></i>
                    <i class="icon-trash font-green-haze" v-else-if="log.event==='deleted'"></i>
                </div>
            </div>
            <div class="timeline-body">
                <div class="timeline-body-arrow"> </div>
                <div class="timeline-body-head">
                    <div class="timeline-body-head-caption">
                        <span class="timeline-body-alerttitle font-blue-madison">
                            {{ log.action }}
                        </span>
                        <span class="timeline-body-time font-grey-cascade">
                            <i class="fa fa-clock-o"></i>
                            {{ moment(log.created_at).format("MM/DD/YYYY hh:mm A") }},
                            IP Address: {{ log.ip_address }}
                        </span>
                    </div>
                </div>
                <div class="timeline-body-content">
                    <span class="font-grey-cascade">
                        <span>Reference ID: {{ log.reference_id }}</span>
                        <span v-if="log.body!=='[]' && 1===0">
                            <table class="table table-condensed table-hover table-bordered" style="margin-bottom: 0px;">
                                <tbody>
                                    <tr v-for="l in log.body">
                                        <th>{{ l.field }} : </th>
                                        <td>{{ l.old_value }}</td>
                                        <td>{{ l.new_value }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </span>
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