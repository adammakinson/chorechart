<template>
    <a class="underline text-blue-600 hover:text-blue-400 visited:text-purple-600" :href="href">
        <slot></slot>
    </a>
</template>

<script>
export default {
    props: [
        'href'
    ]
}
</script>