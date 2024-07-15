<template>
    <ul v-bind:id="listId" class="list-group w-full">
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