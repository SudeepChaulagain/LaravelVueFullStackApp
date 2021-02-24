<template>
    <!-- modal for deleting tag -->
        <Modal :value="getDeleteModalObj.showDeleteModal"
        width="360"
        :mask-closable="false"
        :closable="false">
        <p slot="header" style="color:#f60;text-align:center">
            <Icon type="ios-information-circle"></Icon>
            <span>Delete confirmation</span>
        </p>
        <div style="text-align:center">
            <p>Are you sure you want to delete it?</p>
        </div>
        <div slot="footer">
            <Button type="default" size="large" @click="closeModal">Close</Button>
            <Button type="error" size="large" :loading="isDeleting" :disabled="isDeleting" @click="deleteItem">Delete</Button>
        </div>
    </Modal>
</template>

<script>
import {mapGetters} from 'vuex'
export default {
    data(){
        return{
              isDeleting: false,
        }

    },

    methods:{
        async deleteItem(){
        this.isDeleting = true
        const res = await this.callApi('post', this.getDeleteModalObj.deleteUrl, this.getDeleteModalObj.data)
        if (res.status === 200) {
            // this.tags.splice(this.index, 1)
            this.success('Item has been removed successfully!')
            this.$store.commit('showDeleteModal', true)
            // this.showDeleteModal = false
        }
        else{
            this.somethingWentWrong()
            this.$store.commit('showDeleteModal', false)
        }
        this.isDeleting = false
     },
     closeModal(){
          this.$store.commit('showDeleteModal', false)
     }
    },
    computed:{
        ...mapGetters(['getDeleteModalObj'])
    }
}
</script>

<style>

</style>
