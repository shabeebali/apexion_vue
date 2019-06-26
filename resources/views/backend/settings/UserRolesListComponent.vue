<template>
    <div>
        <v-content>
            <v-container fluid>
                <list-component
                listHeadline="User Roles"
                listClass="roles-list"
                :baseRoute="baseRoute"
                :filterables="filterables"
                :listSettingsRoute="listSettingsRoute"
                :triggerUpdate="triggerUpdate"
                v-on:triggered-update="triggerUpdate=false"
                v-on:open-add-dialog="redirectAdd"
                v-on:open-edit-dialog="editItem"
                :addFlag="addFlag"
                ></list-component>
            </v-container>
        </v-content>
    </div>
</template>
<script>
import ListComponent from '../../../js/components/ListComponent.vue'
export default{
    
    components:{
        'list-component':ListComponent,
    },
    data(){
        return{
            baseRoute:'/settings/users/roles?',
            listSettingsRoute:'',
            filterables:[],
            addDialog:false,
            triggerUpdate:false,
            addFlag:true,
            editDialog:false,
            editId:0,
        }
    },
    mounted(){
    },
    methods:{
        editItem($event){
            this.$router.push('edit/'+$event)
        },
        redirectAdd(){
            this.$router.push('add')
        }
    }   
}
</script>
