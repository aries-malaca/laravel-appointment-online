<template>
    <div class="portlet light bordered" v-else>
        <div class="portlet-body">
            <div class="alert alert-warning">
                Error: 403 - Unauthorized. No Permission to view this page.
                <br/>
            </div>
        </div>
    </div>
</template>
<script>
    export default {
        name: 'Unauthorized',
    }
</script>