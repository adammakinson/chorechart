<template>
    <div>
        <title-bar></title-bar>
        <div class="grid min-h-screen transition-all duration-500 ease-in-out">
            <appmenu></appmenu>
                <div id="choreManagement" class="p-5 w-full">
                    <component :is="mainTabContents"></component>
                </div>
        </div>
    </div>
</template>

<script>
import eventBus from '../eventBus';
import Appmenu from "../components/AppMenu.vue";
import TabComponent from "../components/TabComponent";
import ManageChoresTabContents from "./ManageChoresTabContents.vue";
import ViewAssignmentsTabContents from "./ViewAssignmentsTabContents.vue";
import TitleBar from '../components/TitleBar.vue';

export default {

    created() {
        eventBus.on("manage-chores-tab-click", (contents) => {
            this.mainTabContents = contents;
        });
        
        eventBus.on("view-assignments-tab-click", (contents) => {
            this.mainTabContents = contents;
        });

        eventBus.on("mobileMainMenuIconClicked", () => {
            this.mainMenuIsOpen = !this.mainMenuIsOpen;
        });

        if (this.windowWidth < 640) {
            this.mainMenuIsOpen = false;
        } else {
            this.mainMenuIsOpen = true;
        }

        window.matchMedia("(orientation: portrait)").addEventListener("change", e => {
            const portrait = e.matches;

            if (this.windowWidth < 640) {
                this.mainMenuIsOpen = false;
            } else {
                this.mainMenuIsOpen = true;
            }
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

            mainTabContents: 'ManageChoresTabContents',
            mainMenuIsOpen: false
        }
    },

    computed: {
        windowWidth() {
            return this.$store.getters.getWindowWidth;
        }
    },
    
    components: {
        Appmenu,
        ManageChoresTabContents,
        ViewAssignmentsTabContents,
        TitleBar
    }
    
}
</script>