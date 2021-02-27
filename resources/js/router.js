import Vue from 'vue'

import Router from 'vue-router'

//admin project pages
import Tags from './admin/pages/Tags.vue'
import Category from './admin/pages/Category.vue'
import AdminUsers from './admin/pages/AdminUsers.vue'
import Login from './admin/pages/Login.vue'
import Role from './admin/pages/Role.vue'
import AssignRole from './admin/pages/AssignRole.vue'
import Home from './components.vue/pages/Home.vue'




Vue.use(Router)

const routes = [
    {
    path: '/tags',
    component: Tags,
    name: 'tags'
    },
    {
    path: '/',
    component: Home,
    name: 'home'
    },
    {
        path: '/users',
        component: AdminUsers,
        name: 'users'
    },
    {
    path: '/category',
    component: Category,
    name: 'category'
    },
    {
    path: '/login',
    component: Login,
    name: 'login'
    },
    {
        path:'/role',
        component: Role,
        name: 'role'
    },
    {
        path: '/assignRole',
        component: AssignRole,
        name: 'assignRole'
    }

]

export default new Router({
    mode: 'history',
    routes
})
