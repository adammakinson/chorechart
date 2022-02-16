<template>
    <li :id="'list-item_'+listItem.id" class="list-group-item" v-bind:data-itemId="listItem.id" v-on:click="handleChoreClick" v-on:dragstart="dragStart" draggable="true" style="display:flex; justify-content: space-between">
        <slot></slot>
        <div>
            <slot name="actions"></slot>
        </div>
    </li>
</template>

<script>
import eventBus from "../eventBus.js";

export default {
    props: [
        'listItem'
    ],

    data() {
        return {

        }
    },

    methods: {
        handleChoreClick(event) {
            event.stopPropagation();

            let choreItem = event.target.closest('.list-group-item');

            choreItem.classList.toggle('active');
        },

        dragStart(e) {
            e.dataTransfer.setData('text/html', e.target.innerText);
            e.dataTransfer.setData('text/choreId', e.target.dataset.itemid);
        },
    }
}
</script>