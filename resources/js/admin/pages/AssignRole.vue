<template>
  <div>
    <div class="content">
      <div class="container-fluid">
        <!--~~~~~~~ TABLE ONE ~~~~~~~~~-->
        <div
          class="_1adminOverveiw_table_recent _box_shadow _border_radious _mar_b30 _p20"
        >
          <p class="_title0">
            Role Assignment
            <Select v-model="data.id" placeholder="Select admin type" style="width:300px" @on-change="changeAdmin">
              <Option :value="r.id" v-for="(r, i) in roles" :key="i">
                  {{r.rolename}}
              </Option>
            </Select>
          </p>

          <div class="_overflow _table_div">
            <table class="_table">
              <!-- TABLE TITLE -->
              <tr>
                <th>Resources</th>
                <th>Read</th>
                <th>Write</th>
                <th>Update</th>
                <th>Delete</th>
              </tr>
              <!-- TABLE TITLE -->

              <!-- ITEMS -->
              <tr v-for="(r, i) in resources" :key="i">
                <td>{{r.resourceName}}</td>
                <td><Checkbox v-model="r.read"></Checkbox></td>
                <td><Checkbox v-model="r.write"></Checkbox></td>
                <td><Checkbox v-model="r.update"></Checkbox></td>
                <td><Checkbox v-model="r.delete"></Checkbox></td>
              </tr>
              <!-- ITEMS -->
               <div class="space">
              <div class="center_button">
                  <Button type="primary" :loading="isSending" :disabled="isSending" @click="assignRoles">Assign</Button>
              </div>
               </div>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>


<script>
export default {
  data() {
    return {
      data: {
        id: null
      },
      isSending: false,
      resources:[
          {resourceName: 'Home', read: false, write: false, update: false, delete: false, name: '/', icon:'ios-home-outline'},
          {resourceName: 'Tags', read: false, write: false, update: false, delete: false, name: 'tags', icon:'ios-pricetag-outline'},
          {resourceName: 'Category', read: false, write: false, update: false, delete: false, name: 'category', icon:'ios-apps-outline'},
          {resourceName: 'Admin users', read: false, write: false, update: false, delete: false, name: 'users', icon:'ios-people'},
          {resourceName: 'Role', read: false, write: false, update: false, delete: false, name: 'role', icon:'ios-people-outline'},
          {resourceName: 'Assign Role', read: false, write: false, update: false, delete: false, name: 'assignRole', icon:'ios-person-add-outline'},
          ],
       defaultResourcesPermission:[
          {resourceName: 'Home', read: false, write: false, update: false, delete: false, name: '/', icon:'ios-home-outline'},
          {resourceName: 'Tags', read: false, write: false, update: false, delete: false, name: 'tags', icon:'ios-pricetag-outline'},
          {resourceName: 'Category', read: false, write: false, update: false, delete: false, name: 'category', icon:'ios-apps-outline'},
          {resourceName: 'Admin users', read: false, write: false, update: false, delete: false, name: 'users', icon:'ios-people'},
          {resourceName: 'Role', read: false, write: false, update: false, delete: false, name: 'role', icon:'ios-people-outline'},
          {resourceName: 'Assign Role', read: false, write: false, update: false, delete: false, name: 'assignRole', icon:'ios-person-add-outline'},
       ],
      roles: [],
    }
  },
  methods: {
      async assignRoles(){
          let data = JSON.stringify(this.resources)
          const res = await this.callApi('post', 'app/assign_roles', {'permission':data, id: this.data.id})
          if (res.status==200) {
              this.success('Role has been assigned sucessfully')
              let index = this.roles.findIndex(role=>role.id==this.data.id)
              this.roles[index].permission = data
          }
          else{
              this.somethingWentWrong()
          }
      },
      changeAdmin(){
          let index = this.roles.findIndex(role => role.id == this.data.id)
          let permission = this.roles[index].permission
          if (!permission) {
            this.resources = this.defaultResourcesPermission
          }
          else{
              this.resources = JSON.parse(permission)
          }
      }
  },
  async created() {
    this.handleSpinCustom();
    const res = await this.callApi("get", "app/get_roles");
    if (res.status === 200) {
      this.roles = res.data;
      if (res.data.length) {
          this.data.id = res.data[0].id
      }
      if (res.data[0].permission) {
          this.resources = JSON.parse(res.data[0].permission)
      }
    } else {
      this.somethingWentWrong();
    }
  },
};
</script>
