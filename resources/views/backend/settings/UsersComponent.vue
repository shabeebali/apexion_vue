<template>
    <div>
        <v-subheader class="headline">Users</v-subheader>
        <data-table :route="listRoute"></data-table>
    </div>
</template>
<script>
import DataTableComponent from '../../../js/components/DataTableComponent.vue'

export default{
    components:{
        'data-table':DataTableComponent,
    },
    data(){
        return{
            search: '',
            listRoute:'/users'
        }
    },
    created:function(){
    },
}
</script>
