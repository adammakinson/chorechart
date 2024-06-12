<template>
    <div v-on="cardData['eventsObject']" 
         class="card border rounded bg-light max-w shadow-md" 
         v-bind:data-itemId="cardData.id" 
         v-bind:data-eventToFire="cardData.eventToFire">
        
        <slot name="header"></slot>
        
        <div class="flex justify-between p-4">
            <slot></slot>
        </div>
        
        <slot name="footer"></slot>
    </div>
</template>

<script>

export default {

    props: [
        'cardData'
    ]
}
</script>