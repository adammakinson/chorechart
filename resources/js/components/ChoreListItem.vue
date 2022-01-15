<template>
    <li :id="'chore_'+listItem.id" class="list-group-item" v-bind:data-itemId="listItem.id" v-on:click="handleChoreClick" v-on:dragstart="dragStart" draggable="true" style="display:flex; justify-content: space-between">
        <div>
            {{listItem.chore}} ({{listItem.pointvalue}} points)
        </div>
        <div>
            <span class="fas fa-edit text-info" v-bind:data-itemId="listItem.id" v-on:click.stop="editChore"></span>
            <span class="fas fa-trash text-danger" v-bind:data-itemId="listItem.id" v-on:click.stop="deleteChore"></span>
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
            let choreItem = event.target.closest('.list-group-item');

            choreItem.classList.toggle('active');
        },

        editChore(el) {
            let modalwindow = document.getElementById("editChoreModal");
            let choreId = this.listItem.id;
            let choreBeingEdited = this.listItem;

            this.choreFieldValue = choreBeingEdited.chore;
            this.pointFieldValue = choreBeingEdited.pointvalue;
            this.activeElementId = choreId;

            eventBus.$emit('chore-edit-click', choreBeingEdited);

            modalwindow.style.display = 'block';
        },

        deleteChore(el) {

            axios.delete('/api/chores/' + this.listItem.id, {
                headers: {
                    authorization: this.$store.getters.getUserAuthToken
                }
            }).then((response) => {
                eventBus.$emit('refetch-chores');
            });
        },

        dragStart(e) {
            e.dataTransfer.setData('text/html', e.target.innerText);
            e.dataTransfer.setData('text/choreId', e.target.dataset.itemid);
        },
    }
}
</script>