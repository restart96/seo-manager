/**
 * @author SJ
 * @date   2019.12.03
 */

import 'babel-polyfill'
import '@mdi/font/css/materialdesignicons.css'
import Vue from 'vue'
import Vuetify from 'vuetify'
import axios from 'axios'
import VSeoManager from '../vue/SeoManager'
import {store} from '../vue/store/store'

Vue.use(Vuetify)
Vue.prototype.$axios = axios

new Vue({
    el: '#seo-manager',
    vuetify: new Vuetify({
        icons: {
            iconfont: 'mdi'
        }
    }),
    store,
    components: { VSeoManager }
})
