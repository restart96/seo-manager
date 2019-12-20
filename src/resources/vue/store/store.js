import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex)

export const store = new Vuex.Store({
    state: {
        locale: null,
        loading: false
    },
    mutations: {
        setLocale(state, locale) {
            state.locale = locale
        },
        setLoading(state, loading) {
            state.loading = loading
        }
    },
    getters: {
        locale: state => {
            return state.locale
        },
        loading: state => {
            return state.loading
        }
    }
})
