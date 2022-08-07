<template>
    <div class="tab-component">
        <ul class="nav nav-tabs">
            <li v-for="tabsDataItem in tabsData" :key="tabsDataItem.id" class="nav-item">
                <a v-bind:class="tabsDataItem.active ? 'nav-link active' : 'nav-link'" v-bind:aria-current="tabsDataItem.active ? 'page' : ''" v-bind:data-firesEvent="tabsDataItem.firesEvent" v-bind:data-loadsContent="tabsDataItem.loadsContent" v-on:click="loadTab" href="#">
                    {{tabsDataItem.label}}
                </a>
            </li>
        </ul>
        <slot></slot>
    </div>
</template>

<script>
import eventBus from '../eventBus';

export default {
    props: [
        'tabsData'
    ],

    data() {
        return {
            currentTabContents: this.tabsData[0].content
        }
    },

    methods: {
        loadTab(event) {
            let clickedNavLink = event.target;
            let allNavLinks = clickedNavLink.closest('.nav-tabs').querySelectorAll('.nav-link');

            console.log("loadTab function being run");

            allNavLinks.forEach((link) => {
                link.className = 'nav-link';
                link.removeAttribute('aria-current');
            });
            
            clickedNavLink.className = 'nav-link active';
            clickedNavLink.setAttribute('aria-current', 'page');
            
            /**
             * How this works is a little circly. I don't want the TabComponent component
             * to have to know about or care about what it gets and the intent is to
             * feed it the tabs and also the "tab content views". The click events
             * on the tab come from the TabComponent, which fire an event that needs
             * to be listened to on the parent page component, which then switches
             * which component gets fed into the TabComponent. If I could find a way
             * to feed both sub-components into the TabComponent and have the TabComponent
             * do the switching internally, that would be a lot cleaner.
             */
            eventBus.emit(clickedNavLink.getAttribute('data-firesevent'), clickedNavLink.getAttribute('data-loadscontent'));
        }
    }
}
</script>