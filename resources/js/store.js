import Vue from 'vue'
import Vuex from 'vuex'
Vue.use(Vuex)

export default new Vuex.Store({
    state:{
        deleteModalObj:{
            showDeleteModal: false,
            deleteUrl: '',
            data: null,
            index: -1,
            isDeleted: false
        },
        user: false,
        userPermission: null
    },

    getters:{
        getDeleteModalObj(state){
            return state.deleteModalObj
        },
        getUserPermission(state){
            return state.userPermission
        }
    },

    mutations:{
        showDeleteModal(state, data){
            const deleteModalObj = {
                showDeleteModal: false,
                deleteUrl : '',
                data : null,
                index: -1,
                isDeleted : data,
            }
            state.deleteModalObj = deleteModalObj
        },
        setDeleteModalObj(state, data){
            state.deleteModalObj = data
        },
        setUpdateUser(state, user){
            state.user = user
        },
        setUserPermission(state, userPermission){
            state.userPermission = userPermission
        }
    },

    actions:{}
})
