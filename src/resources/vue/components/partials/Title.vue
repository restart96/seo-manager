<template>
    <v-card flat outlined class="elevation-2">
        <v-card-title class="pa-3">
            <span class="title">Title</span>
        </v-card-title>
        <v-divider class="mx-0"></v-divider>
        <v-card-text class="pa-3">
            <v-row>
                <v-col cols="12" v-if="route.translate.columns && route.translate.columns.length">
                    <v-card outlined>
                        <v-card-title class="pa-3">
                            <span class="subtitle-1">Mapped Params</span>
                        </v-card-title>
                        <v-divider class="mx-0"></v-divider>
                        <v-card-text class="pa-2 text-left">
                            <v-chip
                                v-for="(param, index) in route.translate.columns"
                                :key="index"
                                class="ma-1"
                                color="yellow darken-3"
                                label
                                small
                                dark
                                @click="addMappedParam(param)"
                            >
                                {{ param }}
                            </v-chip>
                        </v-card-text>
                    </v-card>
                    <span class="d-block mt-1 caption text-left">* 파라미터를 클릭 시 하단 제목 입력창에 추가됩니다.</span>
                </v-col>
                <v-col cols="12">
                    <v-text-combobox v-model="route.translate.title" :combobox="hasMapping"></v-text-combobox>
                </v-col>
            </v-row>
        </v-card-text>
    </v-card>
</template>

<script>
    import {EventBus} from '../../event_bus'
    import VTextCombobox from '../../layouts/TextCombobox'

    export default {
        components: {
            VTextCombobox
        },
        props: {
            route: {
                type: Object,
                default() {
                    return {}
                }
            },
        },
        computed: {
            hasMapping() {
                return this.route.translate.columns && this.route.translate.columns.length
            }
        },
        methods: {
            setTitle() {
                if (!this.route.translate.title) {
                    this.$set(this.route.translate, 'title', [])
                }
            },
            addMappedParam(param) {
                param = '{' + param + '}'
                let index = this.route.translate.title.indexOf(param)
                if (index !== -1) {
                    this.route.translate.title.splice(index, 1)
                }
                this.route.translate.title.push(param)
            }
        },
        created() {
            this.setTitle()

            EventBus.$on('delete-mapping-columns', (columns) => {
                this.route.translate.title = this.route.translate.title.filter(item => {
                    let match = item.match(/^\{([^\}]+)\}$/)
                    return !match || columns.indexOf(match[1]) == -1
                })
            })
        }
    }
</script>
