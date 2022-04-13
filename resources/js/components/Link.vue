<template>
    <a class="text-blue-600 hover:text-blue-400 active:text-purple-400 visited:text-purple-400" href="href"><slot></slot></a>
</template>

<script>
export default {
    props: [
        'href'
    ]
}