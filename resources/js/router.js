import Vue from 'vue'

import Router from 'vue-router'

//admin project pages
import Tags from './admin/pages/Tags.vue'
import Category from './admin/pages/Category.vue'

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
    path: '/category',
    component: Category
    },
]

export default new Router({
    mode: 'history',
    routes
})
