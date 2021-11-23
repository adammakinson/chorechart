<template>
    <div class="tabcontent">
        <div class="container">
            <div class="row">
                <div class="col">
                    <h2>chores</h2>
                    <div class="card">
                        <div class="card-body">
                            <button class="btn btn-primary" data-toggle="modal" data-target="#createChoreModal">Add chore</button>

                            <!-- this should be a component that accepts props -->
                            <ul id="chores-list-group" class="list-group mt-4">
                                <li class="list-group-item">
                                    <div style="display:flex; justify-content: space-between">
                                        <div>
                                            An item
                                        </div>
                                        <div>
                                            <span class="fas fa-edit text-info"></span>
                                            <span class="fas fa-trash text-danger"></span>
                                        </div>
                                    </div>
                                </li>
                                <li class="list-group-item">
                                    <div style="display:flex; justify-content: space-between;">
                                        <div>
                                            A second item
                                        </div>
                                        <div>
                                            <span class="fas fa-edit text-info"></span>
                                            <span class="fas fa-trash text-danger"></span>
                                        </div>
                                    </div>
                                </li>
                                <li class="list-group-item">
                                    <div style="display:flex; justify-content: space-between;">
                                        <div>
                                            A third item
                                        </div>
                                        <div>
                                            <span class="fas fa-edit text-info"></span>
                                            <span class="fas fa-trash text-danger"></span>
                                        </div>
                                    </div>
                                </li>
                                <li class="list-group-item">
                                    <div style="display:flex; justify-content: space-between;">
                                        <div>
                                            A fourth item
                                        </div>
                                        <div>
                                            <span class="fas fa-edit text-info"></span>
                                            <span class="fas fa-trash text-danger"></span>
                                        </div>
                                    </div>
                                </li>
                                <li class="list-group-item">
                                    <div style="display:flex; justify-content: space-between;">
                                        <div>
                                            And a fifth one
                                        </div>
                                        <div>
                                            <span class="fas fa-edit text-info"></span>
                                            <span class="fas fa-trash text-danger"></span>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <h2>Assign to:</h2>
                    <div class="card">
                        <div class="card-body">
                            <tab-component :tabsData="assignmentTabsData">
                                <component :is="assignmentTabContents"></component>
                            </tab-component>
                            <button class="btn btn-primary mt-4" data-toggle="modal" data-target="#createChoreModal">Assign</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import IndividualsSubView from "./IndividualsSubView.vue";
import GroupsSubView from "./GroupsSubView.vue";
import TabComponent from "../components/TabComponent.vue";
import eventBus from "../eventBus.js";

export default {
    created() {
        eventBus.$on("assign-to-individuals-tab-click", (contents) => {
            this.assignmentTabContents = contents;
        });
        
        eventBus.$on("assign-to-group-tab-click", (contents) => {
            this.assignmentTabContents = contents;
        });
    },

    data() {
        return {
            assignmentTabsData: [{
                'id': 1,
                'label': 'Individuals',
                'active': true,
                'firesEvent': 'assign-to-individuals-tab-click',
                'loadsContent': 'IndividualsSubView'
            },
            {
                'id': 2,
                'label': 'Groups',
                'firesEvent': 'assign-to-group-tab-click',
                'loadsContent': 'GroupsSubView'
            }],

            assignmentTabContents: 'IndividualsSubView'
        }
    },

    components: {
        IndividualsSubView,
        GroupsSubView,
        TabComponent
    }
}
</script>