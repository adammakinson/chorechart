<template>
    <ul v-bind:id="listId" class="list-group shadow-md">
        <slot></slot>
    </ul>
</template>

<script>
export default {
    props: [
        'listId'
    ]
}
</script>