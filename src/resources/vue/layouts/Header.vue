<template>
    <div>
        <v-app-bar color="white" app>
            <v-toolbar-title class="align-center">
                <h6 class="overline">SEO MANAGER</h6>
                <h3 class="title">SEO 관리</h3>
            </v-toolbar-title>
            <v-spacer></v-spacer>
            <v-btn class="d-none d-sm-inline-flex" color="success" outlined @click="dialog = true">지역 추가</v-btn>
            <v-select class="flex-grow-0 ml-5"
                v-model="locale"
                :items="locales"
                label="지역"
                single-line
                hide-details
            ></v-select>
            <v-btn class="ml-10 d-none d-sm-inline-flex" color="primary" @click="syncRoutes">URL 업데이트</v-btn>
        </v-app-bar>

        <v-add-locale-dialog v-model="dialog"></v-add-locale-dialog>
    </div>
</template>

<script>
    import {EventBus} from '../event_bus'
    import VAddLocaleDialog from '../components/dialogs/AddLocale'

    export default {
        components: {
            VAddLocaleDialog
        },
        data: () => ({
            dialog: false,
            locales: []
        }),
        computed: {
            locale: {
                get() {
                    return this.$store.getters.locale
                },
                set(locale) {
                    this.setLocale(locale)
                }
            }
        },
        methods: {
            getLocale() {
                let locale = localStorage.getItem('locale')
                if (this.locales.length) {
                    for (let val of this.locales) {
                        if (val.value == locale) {
                            return locale
                        }
                    }
                    return this.locales[0].value
                }
                return ''
            },
            setLocale(locale) {
                localStorage.setItem('locale', locale)
                this.$store.commit('setLocale', locale)
                EventBus.$emit('get-routes', locale)
            },
            getLocales() {
                return new Promise((resolve, reject) => {
                    this.$axios
                        .get(APP_URL + '/locales')
                        .then(response => {
                            resolve(response.data.locales)
                        })
                })
            },
            setLocales(locales) {
                this.locales = []
                for (let locale of locales) {
                    this.locales.push({
                        value: locale.name,
                        text: locale.name.toUpperCase()
                    })
                }
            },
            syncRoutes() {
                EventBus.$emit('sync-routes', this.getLocale())
            }
        },
        created() {
            EventBus.$on('update-locales', (locales) => {
                this.setLocales(locales)
            })
            EventBus.$on('update-locale', (locale) => {
                this.setLocale(locale)
            })
        },
        mounted() {
            this.getLocales()
                .then(locales => {
                    this.setLocales(locales)
                    this.setLocale(this.getLocale())
                })
        }
    }
</script>

<style scoped>
    .v-select {width:120px;}
</style>
