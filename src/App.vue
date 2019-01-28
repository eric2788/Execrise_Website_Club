<template>
  <v-app>
    <v-toolbar class="success darken-3">
      <v-toolbar-title class="headline white--text">Execrise - CLUB table</v-toolbar-title>
    </v-toolbar>
    <v-container>
      <v-card>
        <v-card-title class="headline success darken-2 white--text">Club Members
        <v-spacer></v-spacer>
          <v-btn :loading="loading"  :fab="loading" :small="loading" @click="getMembers" dark outline><v-icon left >autorenew</v-icon>Refresh</v-btn>
          <!--v-dialog>
            <v-btn dark outline slot="activator">Create</v-btn>
            <v-card>
              <v-card-title class="headline">Create</v-card-title>
              <v-card-text>
                <v-text-field label="Given Name" v-model="create.givenname"></v-text-field>
                <v-text-field label="Family Name" v-model="create.familyname"></v-text-field>
                <v-text-field label="Phone" v-model="create.phone" mask="############"></v-text-field>
                <v-select :items="['M','F']" label="Sex" v-model="create.sex"></v-select>
                <v-text-field label="Sport" v-model="create.sport"></v-text-field>
                <v-select :items="['Intermediate','Advanced','Beginner']" label="Level" v-model="create.level"></v-select>
              </v-card-text>
              <v-card-actions>
                <v-btn class="primary" @click="createItem()">Submit</v-btn>
              </v-card-actions>
            </v-card>
          </v-dialog-->

        </v-card-title>
        <v-card-text>
          <v-container pa-0 fluid grid-list-md>
            <v-layout :class="isMobile ? 'column' : 'row wrap'">
              <v-flex xs6>
                <v-select multiple clearable label="Select Sport" :items="getSports" v-model="selected.sport"></v-select>
                <v-select multiple clearable :items="getLevels" label="Select Level" v-model="selected.level"></v-select>
              </v-flex>
              <v-flex xs6>
                <v-select multiple clearable :items="getSexs" label="Select Sex" v-model="selected.sex"></v-select>
                <v-text-field label="search" append-icon="search" v-model="search"></v-text-field>
              </v-flex>
            </v-layout>
          </v-container>
          <v-data-table class="elevation-2" :items="items" :rows-per-page-items="rowsPerPageItems" :pagination.sync="pagination" :headers="headers" :loading="loading" :custom-filter="filteredItems" :search="search">
            <v-progress-linear slot="progress" color="green" height="3px" indeterminate></v-progress-linear>
              <template slot="items" slot-scope="props">
                <td>{{props.item.familyname}}</td>
                <td>{{props.item.givename}}</td>
                <td>{{props.item.sex}}</td>
                <td>{{props.item.phone}}</td>
                <td>{{props.item.sport}}</td>
                <td>{{props.item.level}}</td>
                <!--td class="justify-center layout px-0">
                  <v-icon small @click="deleteItem(Number.parseInt(props.item.phone))" v-if="!loading">delete</v-icon>
                  <v-progress-circular indeterminate width="3" v-else color="black" size="16"></v-progress-circular>
                </td-->
              </template>
          </v-data-table>
        </v-card-text>
      </v-card>
      <v-snackbar v-model="operation.fail" :timeout="3000" bottom>Failed. Please try again later. <v-btn flat color="error" @click="operation.fail = false">Back</v-btn></v-snackbar>
      <v-snackbar v-model="operation.success" :timeout="3000" bottom>Success !<v-btn flat color="success" @click="operation.success = false">Back</v-btn></v-snackbar>
    </v-container>
  </v-app>
</template>

<script>

export default {
  name: 'App',
  components: {

  },
  data () {
    return {
      /*create:{
        sex: '',
        level: '',
        sport: '',
        familyname: '',
        givenname: '',
        phone: ''
      },*/
      operation:{
        fail: false,
        success: false
      },
      selected:{
        sex: [],
        level: [],
        sport: []
      },
      search: '',
      loading: false,
      headers:[ //table headers
        {
          text: "Family Name", value: 'familyname',align: 'left'
        },
        {text: "Given Name", value: "givename"},
        {text: "Sex",value: 'sex',sortable: false},
        {text: "Phone",value: 'phone'},
        {text: "Sport",value: 'sport'},
        {text: "Level",value: 'level'},
        //{text: "Action",value: 'level',sortable: false,align: 'center'}
      ],
      items: [],
      rowsPerPageItems:[20,50,70,{text:"All",value:-1}],
      pagination: {
        rowsPerPage: 20,
        sortBy: "familyname", //first sorting
      }
    }
  },
  computed:{ //get all of the one of each name from database
    getLevels(){
      const items = Array.from(this.items); //assign the array
      if (items.length === 0) return [];
      let result = [];
      items.forEach(data=>{ //push the column
        const level = data.level;
        if (!result.includes(level)) result.push(level) //avoid duplicate
      });
      this.selected.level = result; //assign into dropdown box
      return result;
    },
    getSexs(){
      const items = Array.from(this.items);
      if (items.length === 0) return [];
      let result = [];
      items.forEach(data=>{
        const sex = data.sex;
        if (!result.includes(sex)) result.push(sex)
      });
      this.selected.sex = result;
      return result;
    },
    getSports(){
      const items = Array.from(this.items);
      if (items.length === 0)return [];
      let result = [];
      items.forEach(data=>{
        const sport = data.sport;
        if (!result.includes(sport)) result.push(sport)
      });
      this.selected.sport = result;
      return result;
    },
    isMobile(){
      return this.$vuetify.breakpoint.mdAndDown
    }
  },
  methods:{
    //Caz records is below 500, so i get all of them
    //if there is over 500, i will definitely use WHERE syntax
    async getMembers(){
      this.loading = true; // make loading animation
      this.$axios({ // kind like ajax
        method: 'get', // method name
      }).then(res=>{ //if there has response
        this.items = res.data; //put response array into local data
      }).catch(()=>{ //catch error
        this.operation.fail = true; // make fail animation
      }).finally(()=>this.loading=false) // make loading animation stop
    },
    filteredItems(items,search){
      let result = items; //filter the item in data table
      const sport = this.selected.sport;
      const sex = this.selected.sex;
      const level = this.selected.level;
      result = result.filter(data=> sport.includes(data.sport) && sex.includes(data.sex) && level.includes(data.level));//filter with selected box
      result = result.filter(data=>{ // searching filter
        const sport = data.sport.match(search);
        const level = data.level.match(search);
        const givenname = data.givename.match(search);
        const familyname = data.familyname.match(search);
        const phone = data.phone.toString().match(search);
        return sport || level || givenname || familyname || phone
      });
      return result; // return final result (array)
    },
    /*async deleteItem(phone){ // not tested yet
      this.loading = true;
      this.$axios({
        method: 'delete',
        data: {phone: phone}
      }).then(res=>{
        window.alert(res.data.success);
        if (res.data.success){
          this.operation.success = true;
          this.getMembers();
        }else{
          this.operation.fail = true;
        }
      }).catch(err=>window.console.log(err)).finally(()=>this.loading = false);
    },
    async createItem(){
      this.loading = true;
      this.$axios({
        method: 'post',
        data: this.create
      }).then(res=>{
        window.alert(res.data.success);
        if (res.data.success){
          this.operation.success = true;
          this.getMembers();
        }else{
          this.operation.fail = true;
        }
      }).catch(err=>window.console.log(err)).finally(()=>this.loading = false);
    }*/
  },
  mounted() {
    this.getMembers(); // get all data when website created
  }
}
</script>
