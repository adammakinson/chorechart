<template>
    <li class="list-group-item" v-bind:data-itemId="listItem.id" v-on:click="handleChoreClick">
        <div style="display:flex; justify-content: space-between">
            <div>
                {{listItem.title}}
            </div>
            <div>
                <span class="fas fa-edit text-info" v-bind:data-itemId="listItem.id" v-on:click.stop="handleChoreEditClick"></span>
                <span class="fas fa-trash text-danger" v-bind:data-itemId="listItem.id" v-on:click.stop="handleChoreDeleteClick"></span>
            </div>
        </div>
    </li>
</template>

<script>
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
           console.log('clicked a general chore item');
        },

        handleChoreEditClick() {
            console.log('clicked a chore edit button');
        },

        handleChoreDeleteClick() {
            console.log('clicked a chore delete button');
        }
    }
}
</script>