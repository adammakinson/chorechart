<template>
    <ul v-bind:id="listId" class="list-group shadow-md w-full">
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