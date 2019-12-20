<template>
    <div>
        <v-card flat outlined class="elevation-2">
            <v-card-title class="pa-3">
                <span class="title">메타태그 - Image</span>
                <v-spacer></v-spacer>
                <v-btn text small color="primary" v-if="hasMapping" @click.stop.prevent="selectParam">SELECT</v-btn>
            </v-card-title>
            <v-divider class="mx-0"></v-divider>
            <v-card-text class="pa-3">
                <v-row>
                    <v-col cols="12">
                        <v-text-field v-if="route.translate.meta_image.mapped" v-model="value_mapping" dense persistent-hint readonly>
                            <v-icon slot="append" color="error" @click.stop.prevent="unsetParam">mdi-close</v-icon>
                        </v-text-field>
                        <div v-else class="d-flex">
                            <div class="image-type">
                                <v-select
                                    v-model="type"
                                    :items="types"
                                    persistent-hint
                                    dense
                                ></v-select>
                            </div>
                            <div class="ml-2 flex-grow-1 flex-shrink-1">
                                <v-text-field
                                    v-if="type === 'url'"
                                    v-model="value_url"
                                    persistent-hint
                                    dense
                                ></v-text-field>
                                <v-file-input
                                    v-else
                                    v-model="value_file"
                                    prepend-icon=""
                                    persistent-hint
                                    dense
                                ></v-file-input>
                            </div>
                        </div>
                    </v-col>
                </v-row>
            </v-card-text>
        </v-card>
        <v-select-param-dialog v-model="dialog" :route="route" @submit="setParam"></v-select-param-dialog>
    </div>
</template>

<script>
    import {EventBus} from '../../event_bus'
    import VSelectParamDialog from '../dialogs/SelectParam'

    export default {
        components: {
            VSelectParamDialog
        },
        props: {
            route: {
                type: Object,
                default() {
                    return {}
                }
            }
        },
        data: () => ({
            dialog: false,
            types: [
                {
                    value: 'file',
                    text: '파일'
                },
                {
                    value: 'url',
                    text: 'URL'
                }
            ],
            type: 'file',
            value_file: null,
            value_url: '',
            value_mapping: ''
        }),
        watch: {
            type(val) {
                this.route.translate.meta_image.value = (val === 'url') ? this.value_url : this.value_file
            },
            value_url(val) {
                this.route.translate.meta_image.value = val
            },
            value_file(val) {
                this.route.translate.meta_image.value = val
            },
            value_mapping(val) {
                if (val == '') {
                    this.route.translate.meta_image.value = (this.type === 'url') ? this.value_url : this.value_file
                } else {
                    this.route.translate.meta_image.value = val
                }
            }
        },
        computed: {
            hasMapping() {
                return this.route.translate.columns && this.route.translate.columns.length
            }
        },
        methods: {
            setImage() {
                if (!this.route.translate.meta_image) {
                    this.$set(this.route.translate, 'meta_image', {
                        mapped: false,
                        value: null
                    })
                }

                if (this.route.translate.meta_image.mapped) {
                    this.value_mapping = this.route.translate.meta_image.value
                } else if (this.route.translate.meta_image.value) {
                    this.type = 'url'
                    this.value_url = this.route.translate.meta_image.value
                } else {
                    this.type = 'file'
                    this.value_url = null
                }
            },
            selectParam() {
                this.dialog = true
            },
            setParam(val) {
                this.route.translate.meta_image.mapped = true
                this.value_mapping = val
            },
            unsetParam() {
                this.route.translate.meta_image.mapped = false
                this.value_mapping = ''
            }
        },
        created() {
            this.setImage()

            EventBus.$on('update-route', (route) => {
                this.route.translate.meta_image = route.translate.meta_image
                this.setImage()
            })

            EventBus.$on('delete-mapping-columns', (columns) => {
                if (this.route.translate.meta_image.mapped) {
                    let match = this.route.translate.meta_image.value.match(/^\{([^\}]+)\}$/)
                    if (match && columns.indexOf(match[1]) != -1) {
                        this.unsetParam()
                    }
                }
            })
        }
    }
</script>

<style scoped>
    .image-type {flex:0 0 80px;}
</style>