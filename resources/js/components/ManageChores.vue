<template>
    <div>
        <appmenu></appmenu>
        <h1>Manage Chores</h1>
        <div id="choreManagement">
            <tab-component :tabsData="mainTabsData">
                <component :is="mainTabContents"></component>
            </tab-component>
            
        </div>
    </div>
</template>

<script>
import Appmenu from "./AppMenu.vue";
import TabComponent from "./TabComponent";
import ManageChoresTabContents from "../pages/ManageChoresTabContents.vue";
import ViewAssignmentsTabContents from "../pages/ViewAssignmentsTabContents.vue";
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
    },

    methods: {
        loadTab(event) {
            let allNavLinks = document.querySelectorAll('.nav-link');
            let clickedNavLink = event.target;

            allNavLinks.forEach((link) => {
                link.className = 'nav-link';
                link.removeAttribute('aria-current');
            });
            
            clickedNavLink.className = 'nav-link active';
            clickedNavLink.setAttribute('aria-current', 'page');

            console.log(event.target);
        }
    }
    
}
</script>