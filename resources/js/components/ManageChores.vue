<template>
    <div>
        <appmenu></appmenu>
        <h1>Manage Chores</h1>
        <div id="choreManagement">
            <ul class="nav nav-tabs">
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="#" v-on:click="loadTab">Manage chores</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" v-on:click="loadTab">view assignments</a>
                </li>
            </ul>
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
                                    <ul class="nav nav-tabs">
                                        <li class="nav-item">
                                            <a class="nav-link active" aria-current="page" href="#" v-on:click="loadTab">Individuals</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" href="#" v-on:click="loadTab">Groups</a>
                                        </li>
                                    </ul>
                                    <div>
                                        <!-- same component as above -->
                                        <ul id="users-list-group" class="list-group mt-4">
                                             <li class="list-group-item">Adam</li>
                                            <li class="list-group-item">Catie</li>
                                            <li class="list-group-item">Nia</li>
                                            <li class="list-group-item">Thatcher</li>
                                        </ul>

                                        <button class="btn btn-primary mt-4" data-toggle="modal" data-target="#createChoreModal">Assign</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Appmenu from "./AppMenu.vue";


export default {
    
    components: {
        Appmenu
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