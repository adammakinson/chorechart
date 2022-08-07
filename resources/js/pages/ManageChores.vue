<template>
    <div>
        <user-status-bar>
            <h1>Manage Chores</h1>
        </user-status-bar>
        <div class="sm:flex w-screen h-screen divide-x divide-solid divide-slate-100">
            <appmenu></appmenu>
            <div class="p-5 w-full">
                <div id="choreManagement">
                    <component :is="mainTabContents"></component>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Appmenu from "../components/AppMenu.vue";
import TabComponent from "../components/TabComponent";
import ManageChoresTabContents from "./ManageChoresTabContents.vue";
import ViewAssignmentsTabContents from "./ViewAssignmentsTabContents.vue";
import eventBus from '../eventBus';
import UserStatusBar from '../components/UserStatusBar.vue';

export default {

    created() {
        eventBus.on("manage-chores-tab-click", (contents) => {
            this.mainTabContents = contents;
        });
        
        eventBus.on("view-assignments-tab-click", (contents) => {
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
        ViewAssignmentsTabContents,
        UserStatusBar
    }
    
}
</script>