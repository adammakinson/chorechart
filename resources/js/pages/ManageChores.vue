<template>
    <div class="container-fluid">
        <appmenu></appmenu>
        <h1>Manage Chores</h1>
        <div id="choreManagement">
            <component :is="mainTabContents"></component>
        </div>
    </div>
</template>

<script>
import Appmenu from "../components/AppMenu.vue";
import TabComponent from "../components/TabComponent";
import ManageChoresTabContents from "./ManageChoresTabContents.vue";
import ViewAssignmentsTabContents from "./ViewAssignmentsTabContents.vue";
import eventBus from '../eventBus';

export default {

    created() {
        eventBus.$on("manage-chores-tab-click", (contents) => {
            this.mainTabContents = contents;
        });
        
        eventBus.$on("view-assignments-tab-click", (contents) => {
            this.mainTabContents = contents;
        });
    },

    data() {
        return {
            mainTabsData: [{
                'id': 1,
                'label': 'Manage Chores',
                'active': true,
                'firesEvent': 'manage-chores-tab-click',
                'loadsContent': 'ManageChoresTabContents'
            },
            {
                'id': 2,
                'label': 'View Assignments',
                'firesEvent': 'view-assignments-tab-click',
                'loadsContent': 'ViewAssignmentsTabContents'
            }],

            mainTabContents: 'ManageChoresTabContents'
        }
    },
    
    components: {
        Appmenu,
        TabComponent,
        TabComponent,
        ManageChoresTabContents,
        ViewAssignmentsTabContents
    }
    
}
</script>