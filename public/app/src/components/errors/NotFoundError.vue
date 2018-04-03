<template>
    <div class="portlet light bordered" v-else>
        <div class="portlet-body">
            <div class="alert alert-warning">
                Error: 404 - Not Found. Requested page not found on the system.
                <br/>
            </div>
        </div>
    </div>
</template>
<script>
    export default {
        name: 'NotFoundError',
    }
</script>