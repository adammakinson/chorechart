<template>
    <div v-on="cardData['eventsObject']" class="card p-1 border rounded bg-light" v-bind:data-itemId="cardData.id" v-bind:data-eventToFire="cardData.eventToFire">
        <slot></slot>
    </div>
</template>

<script>

export default {

    props: [
        'cardData'
    ]
}
</script>