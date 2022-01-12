<template>
        <li class="list-group-item user-item" v-bind:data-userid="listItem.id" v-on:click="handleIndividualClick" v-on:dragenter="dragEnter" v-on:dragover="dragOver" v-on:dragleave="dragLeave" v-on:drop="drop">{{listItem.name}}</li>
</template>

<script>
export default {
    props: [
        'listItem'
    ],

    methods: {
        /**
         * Adds or removes the active class from the clicked list group item element
         */
        handleIndividualClick(event) {
            let individual = event.target.closest('.list-group-item');

            individual.classList.toggle('active');
        },

        dragEnter(e) {
            e.preventDefault();

            let dropTarget = e.toElement;

            dropTarget.closest('.user-item').style.border = 'solid 1px green';
        },

        dragOver(e) {
            e.preventDefault();

            let dropTarget = e.toElement;

            dropTarget.closest('.user-item').style.border = 'solid 1px green';
        },

        dragLeave(e) {
            let dropTarget = e.toElement;

            dropTarget.closest('.user-item').style.border = '1px solid rgba(0,0,0,0.125)';
        },

        drop(e) {
            e.stopPropagation();

            let dropTarget = e.toElement.closest('.user-item');
            let userChoresList;
            let userChore;
            let droppedChoreName = e.dataTransfer.getData('text/html'); 
            let droppedChoreId = e.dataTransfer.getData('text/choreId');
            
            userChore = document.createElement('li');
            userChore.classList.add('assignment');
            userChore.innerHTML = `<div style="display: flex; justify-content: space-between;"><div>${droppedChoreName}</div><div><span class="fas fa-trash text-danger discardAssignmentIcon" data-itemid="${droppedChoreId}"></span></div></div>`;
            userChore.dataset.choreid = droppedChoreId;

            if (!this.assignmentsStarted(dropTarget)) {
                userChoresList = document.createElement('ul');
                userChoresList.className = 'assignmentsList';
                dropTarget.appendChild(userChoresList);
            } else {
                userChoresList = dropTarget.querySelector('.assignmentsList');
            }
            
            // User shouldn't have duplicate chores
            if(!this.userListHasChore(userChoresList, userChore)) {
                userChoresList.appendChild(userChore);
            }

            // Create an event listener to handle discarding assignments
            let choreDiscardButton = userChore.querySelector('.discardAssignmentIcon');
            choreDiscardButton.addEventListener('click', this.discardAssignment);

            dropTarget.closest('.user-item').style.border = '1px solid rgba(0,0,0,0.125)';
            
            return false;
        },

        assignmentsStarted(dropTarget) {
            let assignmentsListNode = dropTarget.querySelector('.assignmentsList');

            return dropTarget.contains(assignmentsListNode);
        },

        userListHasChore(userChoresList, userChore) {
            let hasChore = false;

            userChoresList.childNodes.forEach((assignment) => {
                if(assignment.dataset.choreid === userChore.dataset.choreid) {
                    hasChore = true;
                }
            });

            return hasChore;
        },

        discardAssignment(e) {
            let assignment = e.target.closest('.assignment');

            assignment.remove();
        }
    }
}
</script>