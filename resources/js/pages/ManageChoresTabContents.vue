<template>
    <div class="tabcontent">
        <div class="container">
            <div class="row">
                <div class="col">
                    <h2>chores</h2>
                    <div class="card">
                        <div class="card-body">
                            <button class="btn btn-primary" data-toggle="modal" data-target="#createChoreModal">Add chore</button>
                            <list-group>
                                <component v-for="choreData in choresList" :key="choreData.id" :is='listGroupContents' :listItem="choreData"></component>
                            </list-group>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <h2>Assign to:</h2>
                    <div class="card">
                        <div class="card-body">
                            <tab-component :tabsData="assignmentTabsData">
                                <list-group>
                                    <component v-for="assigneeData in assignmentTabContentData" :key="assigneeData.id" :is="assignmentTabContents" :listItem="assigneeData"></component>
                                </list-group>
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
import ListGroup from "../components/ListGroup.vue";
import ChoreListItem from "../components/ChoreListItem.vue";
import eventBus from "../eventBus.js";

export default {
    created() {
        eventBus.$on("assign-to-individuals-tab-click", (contents) => {
            this.assignmentTabContents = contents;
            this.assignmentTabContentData = this.users;
        });
        
        eventBus.$on("assign-to-group-tab-click", (contents) => {
            this.assignmentTabContents = contents;
            this.assignmentTabContentData = this.groups;
        });

        this.assignmentTabContentData = this.users;
    },

    data() {
        return {
            assignmentTabsData: [{
                'id': 1,
                'label': 'Individuals',
                'active': true,
                'firesEvent': 'assign-to-individuals-tab-click',
                'loadsContent': 'IndividualsSubView',
                'dataToPass': 'users'
            },
            {
                'id': 2,
                'label': 'Groups',
                'firesEvent': 'assign-to-group-tab-click',
                'loadsContent': 'GroupsSubView',
                'dataToPass': 'groups'
            }],

            choresList: [
                {
                    id: 1,
                    title: 'Wash the dishes',
                },
                {
                    id: 2,
                    title: 'clean the bathroom',
                },
                {
                    id: 3,
                    title: 'sweep the floor',
                },
                {
                    id: 4,
                    title: 'feed the dog',
                },
                {
                    id: 5,
                    title: 'clean room',
                },
                {
                    id: 6,
                    title: 'sweep the stairs'
                }
            ],

            users: [
                {
                    id: 1,
                    title: 'Adam'
                },
                {
                    id: 2,
                    title: 'Catie'
                },
                {
                    id: 1,
                    title: 'Nia'
                },
                {
                    id: 1,
                    title: 'Thatcher'
                }
            ],

            groups: [
                {
                    id: 1,
                    title: 'Whole family'
                },
                {
                    id: 2,
                    title: 'Boys team'
                },
                {
                    id: 3,
                    title: 'Girls team'
                },
                {
                    id: 4,
                    title: 'Parents'
                },
                {
                    id: 5,
                    title: 'Kids'
                }
            ],

            assignmentTabContents: 'IndividualsSubView',

            assignmentTabContentData: '',

            listGroupContents: 'ChoreListItem'
        }
    },

    components: {
        IndividualsSubView,
        GroupsSubView,
        TabComponent,
        ListGroup,
        ChoreListItem
    }
}
</script>