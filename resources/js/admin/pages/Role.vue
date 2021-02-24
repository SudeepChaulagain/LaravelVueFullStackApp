<template>
    <div>
       <div class="content">
			<div class="container-fluid">

				<!--~~~~~~~ TABLE ONE ~~~~~~~~~-->
				<div class="_1adminOverveiw_table_recent _box_shadow _border_radious _mar_b30 _p20">
					<p class="_title0">Role Manangement <Button @click="addModal=true"><Icon type="md-add" /> Add a new role</Button></p>

					<div class="_overflow _table_div">
						<table class="_table">
								<!-- TABLE TITLE -->
							<tr>
								<th>ID</th>
								<th>Role type</th>
								<th>Created at</th>
								<th>Action</th>
							</tr>
								<!-- TABLE TITLE -->


								<!-- ITEMS -->
							<tr v-for="(role, i) in roles" :key="i" v-if="roles.length">
								<td>{{role.id}}</td>
								<td class="_table_name">{{role.rolename}}</td>
								<td>{{role.created_at}}</td>
								<td>
									<Button type="info" size="small" @click="showEditModal(role, i)">Edit</Button>
									<Button type="error" size="small" @click="showDeletingModal(role, i)"  :loading="role.isDeleting">Delete</Button>
								</td>
							</tr>
								<!-- ITEMS -->
					</table>
					</div>
				</div>


				<!-- tag adding modal -->
				<Modal
					v-model="addModal"
					title="Add role"
					:mask-closable="false"
					:closable="false"

					>
					<Input v-model="data.rolename" placeholder="Role name"  />

					<div slot="footer">
						<Button type="default" @click="addModal=false">Close</Button>
						<Button type="primary" @click="add" :disabled="isAdding" :loading="isAdding">{{isAdding ? 'Adding..' : 'Add Role'}}</Button>
					</div>

				</Modal>
				<!-- tag editing modal -->
				<Modal
					v-model="editModal"
					title="Edit role"
					:mask-closable="false"
					:closable="false"

					>
					<Input v-model="editData.rolename" placeholder="Edit tag name"  />

					<div slot="footer">
						<Button type="default" @click="editModal=false">Close</Button>
						<Button type="primary" @click="edit" :disabled="isAdding" :loading="isAdding">{{isAdding ? 'Editing..' : 'Edit Role'}}</Button>
					</div>

				</Modal>
				<deleteModal></deleteModal>

			</div>
		</div>
    </div>
</template>


<script>
import DeleteModal from './components/deleteModal'
import {mapGetters} from 'vuex'
export default {
  data() {
    return {
      data: {
        rolename: "",
      },
      addModal: false,
      editModal: false,
      showDeleteModal: false,
      isAdding: false,
      isEditing: false,
      isDeleting: false,
      roles: [],
      editData: {
        rolename: "",
      },
      deleteItem:{},
      index: -1,
    };
  },
  methods: {
    async add() {
      if (this.data.rolename.trim() == "") {
        return this.error("Role Name is required!");
      }
      const res = await this.callApi("post", "app/create_role", this.data);
      if (res.status === 201) {
        this.roles.unshift(res.data)
        this.success("Role has been added successfully!");
        this.addModal = false;
        this.data.rolename = "";
      } else {
        if (res.status == 422) {
          if (res.data.errors.rolename) {
            this.info(res.data.errors.rolename[0]); //see in network part when we get error
          }
        } else {
          this.somethingWentWrong();
        }
      }
    },

    async edit() {
      if (this.editData.rolename.trim() == "") {
        return this.error("Role name is required!");
      }
      const res = await this.callApi("post", "app/edit_role", this.editData);
      if (res.status === 200) {
        this.roles[this.index].rolename = this.editData.rolename
        this.success("Role has been edited successfully!");
        this.editModal = false;
        this.editData.rolename = "";
      } else {
        if (res.status == 422) {
          if (res.data.errors.rolename) {
            this.info(res.data.errors.rolename[0]); //see in network part when we get error
          }
        } else {
          this.somethingWentWrong();
        }
      }
    },

    showEditModal(role, i){
        //this is done to prevent real time changing in display while typing in input box
        let editObj = {
            id: role.id,
            rolename: role.rolename
        }
        this.editData = editObj
        this.editModal = true
        this.index = i
    },

    showDeletingModal(role, i){
          const deleteModalObj = {
            showDeleteModal: true,
            deleteUrl: 'app/delete_role',
            data: role,
            index: i,
            isDeleted: false
        }
        this.$store.commit('setDeleteModalObj',deleteModalObj )
    },
  },
  async created() {
    this.handleSpinCustom()
    const res = await this.callApi("get", "app/get_roles");
    if (res.status === 200) {
      this.roles = res.data;
    } else {
      this.somethingWentWrong();
    }
  },
   components:{
      DeleteModal
  },
  computed : {
		...mapGetters(['getDeleteModalObj'])
	},
	watch : {
		getDeleteModalObj(obj){
			if(obj.isDeleted){
				this.roles.splice(obj.index, 1)
			}
		}
	}
};
</script>
