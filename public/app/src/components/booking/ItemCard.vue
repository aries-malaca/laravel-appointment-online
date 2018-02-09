<template>
    <div>
        <div v-if="typeof(picture) !== 'object'">
            <img v-bind:src="'/images/'+type+'/'+picture" style="height:100px;" class="img img-thumbnail">
            <p v-if="description !== null ">{{ description }}</p>
        </div>
        <div v-else>
            <img v-for="pic in picture" v-bind:src="'/images/'+type+'/'+pic" style="height:80px;" class="img img-thumbnail">
            <p v-if="description !== null ">{{ description.join(", ") }}</p>
        </div>

    </div>
</template>
<script>
    export default {
        name: 'ItemCard',
        props:['type','picture','description']
    }
</script>