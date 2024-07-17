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
import ManageChoresTabContents from "./ManageChoresTabContents.vue";
import TitleBar from '../components/TitleBar.vue';

export default {

    created() {
        eventBus.on("mobileMainMenuIconClicked", () => {
            this.mainMenuIsOpen = !this.mainMenuIsOpen;
        });
    },

    data() {
        return {
            mainTabContents: 'ManageChoresTabContents',
            mainMenuIsOpen: false
        }
    },
    
    components: {
        Appmenu,
        ManageChoresTabContents,
        TitleBar
    }
    
}
</script>