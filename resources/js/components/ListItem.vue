<template>
    <li :id="listItem.id" class="list-group-item flex justify-between items-center" v-bind:data-itemId="listItem.id" v-on:click="handleChoreClick" v-on:dragstart="dragStart" draggable="draggable">
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
        'listItem',
        'draggable',
        'selectable'
    ],

    methods: {
        handleChoreClick(event) {
            event.stopPropagation();

            if (this.selectable) {
                let choreItem = event.target.closest('.list-group-item');
    
                choreItem.classList.toggle('active');
                choreItem.classList.toggle('bg-blue-400')
    
                eventBus.emit('chore-list-item-clicked');
            }
        },

        dragStart(e) {
            e.dataTransfer.setData('text/html', e.target.innerText);
            e.dataTransfer.setData('text/choreId', e.target.dataset.itemid);
        },
    }
}
</script>