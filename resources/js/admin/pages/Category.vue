<template>
  <div>
    <div class="content">
      <div class="container-fluid">
        <!--~~~~~~~ TABLE ONE ~~~~~~~~~-->
        <div
          class="_1adminOverveiw_table_recent _box_shadow _border_radious _mar_b30 _p20"
        >
          <p class="_title0">
            Category
            <Button @click="addModal = true"
              v-if="isWritePermitted"><Icon type="md-add" />Add category</Button
            >
          </p>

          <div class="_overflow _table_div">
            <table class="_table">
              <!-- TABLE TITLE -->
              <tr>
                <th>ID</th>
                <th>Icon Image</th>
                <th>Category name</th>
                <th>Created at</th>
                <th>Action</th>
              </tr>
              <!-- TABLE TITLE -->

              <!-- ITEMS -->
              <tr v-for="(category, i) in categoryLists" :key="i" v-if="categoryLists.length">
                <td>{{ category.id }}</td>
                <td class="table_image">
                    <img :src="category.iconImage" />
                </td>
                <td class="_table_name">
                  {{ category.categoryName }}
                </td>
                <td>{{ category.created_at }}</td>
                <td>
                  <Button
                    type="info"
                    size="small"
                    @click="showEditModal(category, i)"
                    v-if="isUpdatePermitted">
                    Edit
                  </Button>
                  <Button
                    type="error"
                    size="small"
                    @click="showDeletingModal(category, i)"
                    v-if="isDeletePermitted">
                    Delete
                  </Button>
                </td>
              </tr>
              <!-- ITEMS -->
            </table>
          </div>
        </div>

        <!-- modal for adding category -->
        <Modal
          v-model="addModal"
          title="Add Category"
          :mask-closable="false"
          :closable="false"
        >
          <Input
            v-model="data.categoryName"
            placeholder="Enter category name..."
            style="width: 300px"
          />
          <div class="space"></div>
          <Upload
            ref="uploads"
            type="drag"
            :headers="{
              'x-csrf-token': token,
              'X-Requested-With': 'XMLHttpRequest',
            }"
            :on-success="handleSuccess"
            :on-error="handleError"
            :format="['jpg', 'jpeg', 'png']"
            :max-size="2048"
            :on-format-error="handleFormatError"
            :on-exceeded-size="handleMaxSize"
            action="/app/upload"
          >
            <div style="padding: 20px 0">
              <Icon
                type="ios-cloud-upload"
                size="52"
                style="color: #3399ff"
              ></Icon>
              <p>Click or drag files here to upload</p>
            </div>
          </Upload>
          <div class="demo-upload-list" v-if="data.iconImage">
            <img :src="`${data.iconImage}`" />
            <div class="demo-upload-list-cover">
              <Icon type="ios-trash-outline" @click="deleteImage"></Icon>
            </div>
          </div>
          <div slot="footer">
            <Button type="default" @click="addModal = false">Close</Button>
            <Button
              type="primary"
              @click="addCategory"
              :disabled="isAdding"
              :loading="isAdding"
              >{{ isAdding ? "Adding..." : "Add category" }}</Button
            >
          </div>
        </Modal>
        <!-- modal for editing category -->
        <Modal
          v-model="editModal"
          title="Edit Catgeory"
          :mask-closable="false"
          :closable="false"
        >
           <Input
            v-model="editData.categoryName"
            placeholder="Enter category name..."
            style="width: 300px"
          />
          <div class="space"></div>
          <Upload v-show="isIconImageNew"
            ref="editDataUploads"
            type="drag"
            :headers="{
              'x-csrf-token': token,
              'X-Requested-With': 'XMLHttpRequest',
            }"
            :on-success="handleSuccess"
            :on-error="handleError"
            :format="['jpg', 'jpeg', 'png']"
            :max-size="2048"
            :on-format-error="handleFormatError"
            :on-exceeded-size="handleMaxSize"
            action="/app/upload"
          >
            <div style="padding: 20px 0">
              <Icon
                type="ios-cloud-upload"
                size="52"
                style="color: #3399ff"
              ></Icon>
              <p>Click or drag files here to upload</p>
            </div>
          </Upload>
           <div class="demo-upload-list" v-if="editData.iconImage">
            <img :src="`${editData.iconImage}`" />
            <div class="demo-upload-list-cover">
              <Icon type="ios-trash-outline" @click="deleteImage(false)"></Icon>
            </div>
          </div>
          <div slot="footer">
            <Button type="default" @click="closeEditModal">Close</Button>
            <Button
              type="primary"
              @click="editCategory"
              :disabled="isEditing"
              :loading="isEditing"
              >{{ isEditing ? "Editing..." : "Edit category" }}</Button
            >
          </div>
        </Modal>
        <DeleteModal></DeleteModal>
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
        iconImage: "",
        categoryName: "",
      },
      addModal: false,
      editModal: false,
      showDeleteModal: false,
      isAdding: false,
      isEditing: false,
      isDeleting: false,
      categoryLists: [],
      editData: {
        categoryName: "",
        iconImage: "",
      },
      deleteItem: {},
      index: -1,
      token: "",
      isIconImageNew: false,
      isEditingItem: false
    };
  },
  methods: {
    async addCategory() {
      if (this.data.categoryName.trim() == "") {
        return this.error("Category Name is required!");
      }
      if (this.data.iconImage.trim() == "") {
        return this.error("Icon Image is required!");
      }
      //to add fullpath of image
      this.data.iconImage = `${this.data.iconImage}`
      const res = await this.callApi("post", "app/create_category", this.data)
      if (res.status === 201) {
        this.categoryLists.unshift(res.data);
        this.success("Category has been added successfully!")
        this.addModal = false
        this.data.categoryName = "";
        this.data.iconImage = ''
      } else {
        if (res.status == 422) {
          if (res.data.errors.categoryName) {
            this.info(res.data.errors.categoryName[0]); //see in network part when we get error
          }
          if (res.data.errors.iconImage) {
            this.info(res.data.errors.iconImage[0]); //see in network part when we get error
          }
        } else {
          this.somethingWentWrong();
        }
      }
    },

    async editCategory() {
      if (this.editData.categoryName.trim() == "") {
        return this.error("Category Name is required!");
      }
      if (this.editData.iconImage.trim() == "") {
        return this.error("Icon Image is required!");
      }
       //to add fullpath of image
      this.editData.iconImage = `${this.editData.iconImage}`
      const res = await this.callApi("post", "app/edit_category", this.editData);
      if (res.status === 200) {
        this.categoryLists[this.index].categoryName = this.editData.categoryName;
        this.success("Category has been edited successfully!");
        this.editModal = false;
      } else {
        if (res.status == 422) {
          if (res.data.errors.categoryName) {
            this.info(res.data.errors.categoryName[0]); //see in network part when we get error
          }
          if (res.data.errors.iconImage) {
            this.info(res.data.errors.iconImage[0]); //see in network part when we get error
          }
        } else {
          this.somethingWentWrong();
        }
      }
    },

    showEditModal(category, i) {
      this.editData = category;
      this.editModal = true;
      this.index = i;
      this.isEditingItem = true
    },
    closeEditModal(){
        this.isEditingItem = false
        this.editModal = false
    },

    showDeletingModal(category, i) {
           const deleteModalObj = {
            showDeleteModal: true,
            deleteUrl: 'app/delete_category',
            data: category,
            index: i,
            isDeleted: false
        }
        this.$store.commit('setDeleteModalObj',deleteModalObj )
    },
    handleSuccess(res, file) {
        res = `/uploads/${res}`
        if (this.isEditingItem) {
            return (this.editData.iconImage = res);
        }
        this.data.iconImage = res;
    },
    handleError(res, file) {
      this.$Notice.warning({
        title: "The file format is incorrect",
        desc: `${
          file.errors.file.length ? file.errors.file[0] : "Something went wrong"
        }`,
      });
    },
    handleFormatError(file) {
      this.$Notice.warning({
        title: "The file format is incorrect",
        desc:
          "File format of " +
          file.name +
          " is incorrect, please select jpg or png.",
      });
    },
    handleMaxSize(file) {
      this.$Notice.warning({
        title: "Exceeding file size limit",
        desc: "File  " + file.name + " is too large, no more than 2M.",
      });
    },
    async deleteImage(isAdd = true){
        let image
        //for editing
        if (!isAdd) {
            this.isIconImageNew = true
            image = this.editData.iconImage
            this.editData.iconImage = ''
            this.$refs.editDataUploads.clearFiles()
        }
        //for adding
        else{
            image = this.data.iconImage
            this.data.iconImage = ''
            this.$refs.uploads.clearFiles()
        }

      const res = await this.callApi('post', 'app/delete_image', {imageName: image})
      if (res.status!=200) {
          this.data.iconImage = image
          this.somethingWentWrong()
      }
    }
  },
  async created() {
    this.handleSpinCustom()
    this.token = window.Laravel.csrfToken
    const res = await this.callApi("get", "app/get_category")
    if (res.status === 200) {
      this.categoryLists = res.data
    } else {
      this.somethingWentWrong()
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
				this.categoryLists.splice(obj.index, 1)
			}
		}
	}

}
</script>
<style>
    .demo-spin-icon-load{
        animation: ani-demo-spin 1s linear infinite;
    }
</style>
