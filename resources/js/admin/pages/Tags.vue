<template>
  <div>
    <div class="content">
      <div class="container-fluid">
        <!--~~~~~~~ TABLE ONE ~~~~~~~~~-->
        <div
          class="_1adminOverveiw_table_recent _box_shadow _border_radious _mar_b30 _p20"
        >
          <p class="_title0">
            Tags <Button @click="addModal = true"><Icon type="md-add" />Add tag</Button>
          </p>

          <div class="_overflow _table_div">
            <table class="_table">
              <!-- TABLE TITLE -->
              <tr>
                <th>ID</th>
                <th>Tag name</th>
                <th>Created at</th>
                <th>Action</th>
              </tr>
              <!-- TABLE TITLE -->

              <!-- ITEMS -->
              <tr v-for="(tag, i) in tags" :key="i" v-if="tags.length">
                <td>{{tag.id}}</td>
                <td class="_table_name">
                 {{tag.tagName}}
                </td>
                <td>{{tag.created_at}}</td>
                <td>
                  <Button type="info" size="small" @click="showEditModal(tag,i)"> Edit </Button>
                  <Button type="error" size="small" @click="showDeletingModal(tag, i)"> Delete </Button>
                </td>
              </tr>
              <!-- ITEMS -->
            </table>
          </div>
        </div>

        <!-- modal for adding tag -->
        <Modal
          v-model="addModal"
          title="Add Tag"
          :mask-closable="false"
          :closable="false"
        >
        <Input v-model="data.tagName" placeholder="Enter tag name..." style="width:300px"/>
          <div slot="footer">
            <Button type="default" @click="addModal=false">Close</Button>
            <Button type="primary" @click="addTag"
            :disabled= "isAdding"
            :loading = "isAdding"
            >{{isAdding ? 'Adding...' : 'Add tag'}}</Button>
          </div>
        </Modal>
        <!-- modal for editing tag -->
           <Modal
          v-model="editModal"
          title="Edit Tag"
          :mask-closable="false"
          :closable="false"
        >
        <Input v-model="editData.tagName" placeholder="Enter tag name to edit..."/>
          <div slot="footer">
            <Button type="default" @click="editModal=false">Close</Button>
            <Button type="primary" @click="editTag"
            :disabled= "isEditing"
            :loading = "isEditing"
            >{{isEditing ? 'Editing...' : 'Edit tag'}}</Button>
          </div>
        </Modal>
        <!-- modal for deleting tag -->
        <Modal v-model="showDeleteModal" width="360">
        <p slot="header" style="color:#f60;text-align:center">
            <Icon type="ios-information-circle"></Icon>
            <span>Delete confirmation</span>
        </p>
        <div style="text-align:center">
            <p>Are you sure you want to delete it?</p>
        </div>
        <div slot="footer">
            <Button type="error" size="large" long :loading="isDeleting" :disabled="isDeleting" @click="deleteTag">Delete</Button>
        </div>
    </Modal>
      </div>
    </div>
  </div>
</template>
<script>
export default {
  data() {
    return {
      data: {
        tagName: "",
      },
      addModal: false,
      editModal: false,
      showDeleteModal: false,
      isAdding: false,
      isEditing: false,
      isDeleting: false,
      tags: [],
      editData: {
        tagName: "",
      },
      deleteItem:{},
      index: -1,
    };
  },
  methods: {
    async addTag() {
      if (this.data.tagName.trim() == "") {
        return this.error("Tag Name is required!");
      }
      const res = await this.callApi("post", "app/create_tag", this.data);
      if (res.status === 201) {
        this.tags.unshift(res.data)
        this.success("Tag has been added successfully!");
        this.addModal = false;
        this.data.tagName = "";
      } else {
        if (res.status == 422) {
          if (res.data.errors.tagName) {
            this.info(res.data.errors.tagName[0]); //see in network part when we get error
          }
        } else {
          this.somethingWentWrong();
        }
      }
    },

    async editTag() {
      if (this.editData.tagName.trim() == "") {
        return this.error("Tag name is required!");
      }
      const res = await this.callApi("post", "app/edit_tag", this.editData);
      if (res.status === 200) {
        this.tags[this.index].tagName = this.editData.tagName
        this.success("Tag has been edited successfully!");
        this.editModal = false;
        this.editData.tagName = "";
      } else {
        if (res.status == 422) {
          if (res.data.errors.tagName) {
            this.info(res.data.errors.tagName[0]); //see in network part when we get error
          }
        } else {
          this.somethingWentWrong();
        }
      }
    },

    showEditModal(tag, i){
        //this is done to prevent real time changing in display while typing in input box
        let editObj = {
            id: tag.id,
            tagName: tag.tagName
        }
        this.editData = editObj
        this.editModal = true
        this.index = i
    },

    showDeletingModal(tag, i){
        this.showDeleteModal = true
        this.deleteItem = tag
        this.index = i
    },
    async deleteTag(){
        const res = await this.callApi('post', 'app/delete_tag', this.deleteItem)
        if (res.status == 200) {
            this.tags.splice(this.index, 1)
            this.success('Tag has been removed successfully!')
            this.showDeleteModal = false
        }
        else{
            this.somethingWentWrong()
        }
    }
  },
  async created() {
    const res = await this.callApi("get", "app/get_tags");
    if (res.status === 200) {
      this.tags = res.data;
    } else {
      this.somethingWentWrong();
    }
  },
};
</script>
