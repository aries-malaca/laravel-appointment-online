<template>
    <div class="tab-pane" id="logs">
        <div class="tabbable-line">
            <ul class="nav nav-tabs">
                <li class="active">
                    <a href="#emails" data-toggle="tab">Audit Logs</a>
                </li>
                <li class="active">
                    <a href="#emails" data-toggle="tab">Email Logs</a>
                </li>
                <li>
                    <a href="#sms" data-toggle="tab">SMS Logs</a>
                </li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane active" id="emails">

                </div>
                <div id="sms" class="tab-pane">

                </div>
            </div>
        </div>
    </div>
</template>
<script>
    export default {
        name: 'Logs',
        data(){
            return {

            };
        },
        methods:{

        },
        mounted(){

        },
        computed:{

        }
    }
</script>