import Vue from 'vue'

import Router from 'vue-router'

//admin project pages
import Tags from './admin/pages/Tags.vue'
import Category from './admin/pages/Category.vue'
import AdminUsers from './admin/pages/AdminUsers.vue'
import Login from './admin/pages/Login.vue'
import Role from './admin/pages/Role.vue'
import Home from './components.vue/pages/Home.vue'


Vue.use(Router)

const routes = [
    {
    path: '/tags',
    component: Tags
    },
    {
    path: '/',
    component: Home
    },
    {
        path: '/users',
        component: AdminUsers
    },
    {
    path: '/category',
    component: Category
    },
    {
    path: '/login',
    component: Login
    },
    {
        path:'/role',
        component: Role
    }

]

export default new Router({
    mode: 'history',
    routes
})
