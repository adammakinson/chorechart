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

            choresList: [],

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
    },

    mounted() {
        this.fetchChoresCollection();
    },

    methods: {
        fetchChoresCollection() {

            /**
             * Im sure this can be cleaned up
             */

            axios.get('/api/chores', {
                headers: {
                    authorization: this.$store.getters.getUserAuthToken
                }
            }).then((response) => {
                
                this.choresList = this.processFetchedChores(response.data);

                console.log(this.choresList);
            });
        },

        processFetchedChores(data) {

            /**
             * I'm sure this can be cleaned up
             */

            data.forEach((row) => {

                row.submittable = false;
                row.editable = true;
                row.deletable = true;

                if (row.user && row.user.length) {
                    row.user = row.user[0];
                    row.pivot = row.user.pivot;
                }
                
                if (this.userIsAdmin) {
                    
                    if(row.assigner && row.assigner.length) {
                        row.assigner = row.assigner[0];
                    }

                    // Hrm, I think the thought here was if a chore was assigned to multiple users. I'm not sure I want 
                    // to go that route, but rather perhaps have chore instances? would need to stay an array for reduce 
                    // to work
                    // row.assignedUsers = row.user.reduce((total, user) => {
                    //     return user.name + ', ';
                    // }, '');
                    row.assignedUsers = row.user.name;

                    if (this.choreIsSelfAssigned(row) || this.choreIsReadyForInspection(row)) {
                        row.submittable = true;
                    }

                    if (this.choreIsReadyForInspection(row)) {
                        row.editable = false;
                    }

                    if (this.choreIsFinished(row)) {
                        row.deletable = false;
                    }
                } else {
                    /**
                     * I think we can do this because a non admin user wont have unassigned chores in their list.
                     * In addition, the user is not returned because as a non admin user, all the chores
                     * should belong to the currently logged in user and passing back a user should be
                     * unneccessary.
                     */
                    row.submittable = true;

                    if(!row.user || Array.isArray(row.user)){
                        row.user = {};
                    }

                    if(!row.user.id && row.pivot){
                        row.user.id = row.pivot.user_id;
                    }
                }
            });

            return data;
        },
    }
}
</script>