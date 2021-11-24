<template>
        <li class="list-group-item">{{listItem.title}}</li>
</template>

<script>
export default {
    props: [
        'listItem'
    ]
}
</script>