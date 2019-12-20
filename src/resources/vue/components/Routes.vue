<template>
    <div>
        <v-data-table
            :headers="headers"
            :items="items"
            class="elevation-1"
            :mobile-breakpoint="0"
            disable-sort
            disable-pagination
            hide-default-footer
        >
            <template v-slot:item="{ item, index }">
                <tr class="route">
                    <td class="py-1">
                        <v-icon class="handle" @click="">mdi-reorder-horizontal</v-icon>
                    </td>
                    <td class="py-1 text-left">
                        <template v-if="item.default_url">
                            <a :href="item.default_url" target="_blank" :title="item.default_url">
                                <div class="text-truncate">{{ item.uri }}</div>
                            </a>
                        </template>
                        <template v-else>
                            <div class="text-truncate grey--text text--darken-3">{{ item.uri }}</div>
                        </template>
                    </td>
                    <td class="py-1 text-left d-none d-sm-table-cell">
                        <table class="meta-tags">
                            <colgroup>
                                <col width="100"/>
                                <col width="*"/>
                            </colgroup>
                            <tbody>
                                <tr>
                                    <td class="pa-1 grey--text text-left">Title</td>
                                    <td class="pa-1 grey--text text--darken-3 text-left">
                                        <div class="text-truncate">
                                            {{ item.translate.meta_title ? item.translate.meta_title.join(' ') : '' }}
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="pa-1 grey--text text-left">Description</td>
                                    <td class="pa-1 grey--text text--darken-3 text-left">
                                        <div class="text-truncate">{{ item.translate.meta_description ? item.translate.meta_description.value : '' }}</div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="pa-1 grey--text text-left">Keywords</td>
                                    <td class="pa-1 grey--text text--darken-3 text-left">
                                        <div class="text-truncate">
                                            {{ item.translate.meta_keywords ? (item.translate.meta_keywords.mapped ? item.translate.meta_keywords.value : item.translate.meta_keywords.value.join(', ')) : '' }}
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                    <td class="py-1 d-none d-md-table-cell">
                        <template v-if="item.translate.meta_image">
                            <div v-if="item.translate.meta_image.mapped" class="text-center">{{ item.translate.meta_image.value }}</div>
                            <div v-else class="image py-1 px-4">
                                <img v-if="item.translate.meta_image.value" :src="item.translate.meta_image.value"/>
                            </div>
                        </template>
                    </td>
                    <td class="py-1 text-center">
                        <v-btn outlined fab x-small color="success" @click="editItem(item)">
                            <v-icon>mdi-pencil</v-icon>
                        </v-btn>
                    </td>
                </tr>
            </template>

            <template v-slot:no-data>
                <div class="text-xs-center my-12 py-12">
                    <v-progress-circular v-if="loading" indeterminate color="grey lighten-2"/>
                    <div v-else class="no-data-text">
                        <p class="title">설정된 경로가 없습니다.</p>
                        <p>아래 버튼을 클릭하여 모든 경로를 불러오세요.</p>
                        <v-btn color="primary" @click="syncItems">경로 생성</v-btn>
                    </div>
                </div>
            </template>
        </v-data-table>

        <v-route-dialog v-model="editing" :locale="locale" :id="route" :models="models"></v-route-dialog>
    </div>
</template>

<script>
    import {EventBus} from '../event_bus'
    import VRouteDialog from './Route'
    import Sortable from 'sortablejs'

    export default {
        components: {
            VRouteDialog
        },
        data: () => ({
            loading: true,
            locale: null,
            headers: [
                {text: 'SORT', align: 'center', width: '70px'},
                {text: 'URI', align: 'center', width: '100%'},
                {text: 'META', align: 'center', width: '70%', class: ['d-none', 'd-sm-table-cell']},
                {text: 'IMAGE', align: 'center', width: '210px', class: ['d-none', 'd-md-table-cell']},
                {text: 'ACTIONS', align: 'center', width: '90px'}
            ],
            items: [],
            items_clone: [],
            editing: false,
            route: null,
            models: []
        }),
        methods: {
            getItems(loading) {
                if (loading !== false) {
                    this.loading = true
                }
                return new Promise((resolve, reject) => {
                    this.$axios
                        .get(APP_URL + '/routes', {
                            params: {
                                locale: this.locale
                            }
                        })
                        .then(response => {
                            this.items = []
                            this.$nextTick(() => {
                                this.items = response.data.routes
                                this.items_clone = this.items.slice()
                            })
                            resolve(response)
                        })
                        .finally(() => {
                            if (loading !== false) {
                                this.loading = false
                            }
                        })
                })
            },
            syncItems() {
                this.$store.commit('setLoading', true)
                return new Promise((resolve, reject) => {
                    this.$axios
                        .post(APP_URL + '/routes', {
                            locale: this.locale
                        })
                        .then(response => {
                            this.items = []
                            this.$nextTick(() => {
                                this.items = response.data.routes
                                this.items_clone = this.items.slice()
                            })
                            resolve(response)
                        })
                        .finally(() => {
                            this.$store.commit('setLoading', false)
                        })
                })
            },
            editItem(item) {
                this.editing = true
                this.route = item.id
            },
            getModels() {
                this.$axios
                        .get(APP_URL + '/models')
                        .then(response => {
                            this.models = response.data.models
                        })
            },
            addRouteEvents() {
                EventBus.$on('get-routes', (locale) => {
                    this.locale = locale
                    if (this.items.length) {
                        this.$store.commit('setLoading', true)
                        this.getItems(false)
                            .finally(() => {
                                setTimeout(() => {
                                    this.$store.commit('setLoading', false)
                                }, 200)
                            })
                    } else {
                        this.getItems()
                    }
                })

                EventBus.$on('sync-routes', (locale) => {
                    this.locale = locale
                    this.syncItems()
                })

                EventBus.$on('update-route', (route) => {
                    for (let i in this.items) {
                        if (this.items[i].id === route.id) {
                            this.$set(this.items, i, route)
                            break
                        }
                    }
                })

                EventBus.$on('delete-route', (route) => {
                    this.$store.commit('setLoading', true)
                    this.getItems(false)
                        .finally(() => {
                            setTimeout(() => {
                                this.$store.commit('setLoading', false)
                            }, 200)
                        })
                })
            },
            setSortable() {
                let that = this
                let table = document.querySelector('.v-data-table tbody')
                Sortable.create(table, {
                    animation: 100,
                    handle: '.handle',
                    onUpdate({newIndex, oldIndex}) {
                        const item = that.items_clone.splice(oldIndex, 1)[0]
                        that.items_clone.splice(newIndex, 0, item)

                        that.$axios
                            .patch(APP_URL + '/routes/reorder', {
                                items: that.items_clone
                            })
                    }
                })
            }
        },
        created() {
            this.addRouteEvents()
        },
        mounted() {
            this.getModels()
            this.setSortable()
        }
    }
</script>

<style scoped>
    .no-data-text {line-height:2rem;}
    .v-data-table >>> table {table-layout:fixed;}
    .v-data-table >>> table tbody tr.sortable-ghost {background:#ffffe2;}
    .v-data-table >>> table tbody tr td {position:relative;}
    .v-data-table >>> table tbody tr td a {text-decoration:none;}
    .v-data-table >>> table tbody tr td .image {position:absolute;top:0;bottom:0;left:0;right:0;}
    .v-data-table >>> table tbody tr td .image img {width:100%;height:100%;object-fit:contain;}
    .v-data-table >>> table tbody tr td .handle {cursor:move;}
    .v-data-table >>> .v-data-table__empty-wrapper:hover {background:#fff !important;}
    .v-data-table >>> table.meta-tags tr td {height:auto;border-bottom:1px solid #f0f0f0 !important;}
    .v-data-table >>> table.meta-tags tr td:first-child {max-width:max-content;}
    .v-data-table >>> table.meta-tags tr:last-child td {border-bottom:0 !important;}

    @media screen and (min-width: 600px) {
        .v-data-table >>> table .v-data-table-header th:nth-child(2) {width:30% !important;}
    }
</style>
