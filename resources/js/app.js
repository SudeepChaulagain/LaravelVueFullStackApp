require('./bootstrap');
import Vue from 'vue'
const MainApp = require('./components.vue/mainapp.vue')
import router from './router'
import ViewUI from 'view-design';
import 'view-design/dist/styles/iview.css';
import Common from './common'

Vue.use(ViewUI);
Vue.mixin(Common)

Vue.component('mainapp', MainApp.default)
const app = new Vue({
    el: '#app',
    router,
})
