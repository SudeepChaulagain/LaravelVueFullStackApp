<template>
  <div>
    <div class="content">
      <div class="container-fluid">
        <!--~~~~~~~ TABLE ONE ~~~~~~~~~-->
        <div
          class="_1adminOverveiw_table_recent _box_shadow _border_radious _mar_b30 _p20"
        >
          <p class="_title0">
            Users
            <Button @click="addModal = true" v-if="isWritePermitted"
              ><Icon type="md-add" />Add User</Button
            >
          </p>

          <div class="_overflow _table_div">
            <table class="_table">
              <!-- TABLE TITLE -->
              <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>User type</th>
                <!-- <th>Created at</th> -->
                <th>Action</th>
              </tr>
              <!-- TABLE TITLE -->

              <!-- ITEMS -->
              <tr v-for="(user, i) in users" :key="user.i">
                <td>{{ user.id }}</td>
                <td class="_table_name">{{ user.fullname }}</td>
                <td class="">{{ user.email }}</td>
                <td class="">{{user.role_id}}</td>
                <!-- <td class="">{{ user.created_at }}</td> -->
                <td>
                  <Button
                    type="info"
                    size="small"
                    @click="showEditModal(user, i)"
                    v-if="isUpdatePermitted">Edit</Button
                  >
                  <Button
                    type="error"
                    size="small"
                    @click="showDeletingModal(user, i)"
                    :loading="user.isDeleting"
                    v-if="isDeletePermitted">Delete</Button
                  >
                </td>
              </tr>
              <!-- ITEMS -->
            </table>
          </div>
        </div>

        <!-- modal for adding tag -->
        <Modal
          v-model="addModal"
          title="Add User"
          :mask-closable="false"
          :closable="false"
        >
          <div class="space">
            <Input
              type="text"
              v-model="data.fullname"
              placeholder="Full name"
            />
          </div>
          <div class="space">
            <Input type="email" v-model="data.email" placeholder="Email" />
          </div>
          <div class="space">
            <Input
              type="password"
              v-model="data.password"
              placeholder="Password"
            />
          </div>
          <div class="space">
            <Select v-model="data.role_id" placeholder="Select admin type">
              <Option :value="r.id" v-for="(r,i) in roles" :key="i" v-if="roles.length">{{r.rolename}}</Option>
            </Select>
          </div>
          <div slot="footer">
            <Button type="default" @click="addModal = false">Close</Button>
            <Button
              type="primary"
              @click="addUser"
              :disabled="isAdding"
              :loading="isAdding"
              >{{ isAdding ? "Adding..." : "Add user" }}</Button
            >
          </div>
        </Modal>
        <!-- modal for editing tag -->
        <Modal
          v-model="editModal"
          title="Edit User"
          :mask-closable="false"
          :closable="false"
        >
          <div class="space">
            <Input
              type="text"
              v-model="editData.fullname"
              placeholder="Full name"
            />
          </div>
          <div class="space">
            <Input type="email" v-model="editData.email" placeholder="Email" />
          </div>
          <div class="space">
            <Input
              type="password"
              v-model="editData.password"
              placeholder="Password"
            />
          </div>
          <div class="space">
            <Select v-model="editData.role_id" placeholder="Select admin type">
              <Option :value="r.id" v-for="(r,i) in roles" :key="i" v-if="roles.length">{{r.rolename}}</Option>
            </Select>
          </div>
          <div slot="footer">
            <Button type="default" @click="editModal = false">Close</Button>
            <Button
              type="primary"
              @click="editUser"
              :disabled="isEditing"
              :loading="isEditing"
              >{{ isEditing ? "Editing..." : "Edit user" }}</Button
            >
          </div>
        </Modal>
        <DeleteModal></DeleteModal>
      </div>
    </div>
  </div>
</template>
<script>
import DeleteModal from "./components/deleteModal";
import { mapGetters } from "vuex";
export default {
  data() {
    return {
      data: {
        fullname: "",
        email: "",
        password: "",
        role_id: null,

      },
      addModal: false,
      editModal: false,
      showDeleteModal: false,
      isAdding: false,
      isEditing: false,
      isDeleting: false,
      users: [],
      editData: {
        fullname: "",
        email: "",
        password: "",
        role_id: null,

      },
      deleteItem: {},
      index: -1,
      roles:[]
    };
  },
  methods: {
    async addUser() {
      if (this.data.fullname.trim() == "")
        return this.error("Full name is required");
      if (this.data.email.trim() == "") return this.error("Email is required");
      if (this.data.password.trim() == "")
        return this.error("Password is required");
      if (!this.data.role_id)
        return this.error("User Type is required");
      const res = await this.callApi("post", "app/create_user", this.data);
      if (res.status === 201) {
        this.users.unshift(res.data);
        this.success("User has been added successfully!");
        this.addModal = false;
        // this.data.fullname = ""
        // this.data.email = ""
        // this.data.password = ""
        // this.data.role_id = null
      } else {
        if (res.status == 422) {
          for (let i in res.data.errors) {
            this.error(res.data.errors[i][0]); //see in network part when we get error
          }
        } else {
          this.somethingWentWrong();
        }
      }
    },

    async editUser() {
      if (this.editData.fullname.trim() == "")
        return this.error("Full name is required");
      if (this.editData.email.trim() == "") return this.error("Email is required");
      if (!this.editData.role_id)
        return this.error("User Type is required");

    //   if (!this.editData.role_id) return this.error("User type  is required");

      const res = await this.callApi("post", "app/edit_user", this.editData);
      if (res.status === 200) {
        this.users[this.index] = this.editData;
        this.success("User has been edited successfully!")
        this.editModal = false
        // this.editData.fullname = ""
        // this.editData.email = ""
        // this.editData.password = ""
        //  this.data.role_id = null
      } else {
        if (res.status == 422) {
          for (let i in res.data.errors) {
            this.error(res.data.errors[i][0]);
          }
        } else {
          this.somethingWentWrong();
        }
      }
    },

    showEditModal(user, i) {
      //this is done to prevent real time changing in display while typing in input box
      let editObj = {
        id: user.id,
        fullname: user.fullname,
        email: user.email,
        role_id: user.role_id
        // role_id: user.role_id,
      };
      this.editData = editObj;
      this.editModal = true;
      this.index = i;
    },

    showDeletingModal(user, i) {
      const deleteModalObj = {
        showDeleteModal: true,
        deleteUrl: "app/delete_user",
        data: user,
        index: i,
        isDeleted: false,
      };
      this.$store.commit("setDeleteModalObj", deleteModalObj);
    },
  },
  async created() {
    this.handleSpinCustom();
    const [res, resRole] = await Promise.all([
        this.callApi("get", "app/get_users"),
        this.callApi("get", "app/get_roles"),

    ])
    if (res.status === 200) {
      this.users = res.data;
    } else {
      this.somethingWentWrong();
    }
    if(resRole.status==200){
	  this.roles = resRole.data
		}else{
			this.swr()
		}
  },
  components: {
    DeleteModal,
  },
  computed: {
    ...mapGetters(["getDeleteModalObj"]),
  },
  watch: {
    getDeleteModalObj(obj) {
      if (obj.isDeleted) {
        this.users.splice(obj.index, 1);
      }
    },
  },
};
</script>
<style>
.demo-spin-icon-load {
  animation: ani-demo-spin 1s linear infinite;
}
</style>
