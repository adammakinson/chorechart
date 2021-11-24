<template>
    <li class="list-group-item">
        <div style="display:flex; justify-content: space-between">
            <div>
                {{listItem.title}}
            </div>
            <div>
                <span class="fas fa-edit text-info"></span>
                <span class="fas fa-trash text-danger"></span>
            </div>
        </div>
    </li>
</template>

<script>
export default {
    props: [
        'listItem'
    ],

    data() {
        return {

        }
    }
}
</script>