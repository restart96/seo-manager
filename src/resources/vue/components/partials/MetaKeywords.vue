<template>
    <div>
        <v-card flat outlined class="elevation-2">
            <v-card-title class="pa-3">
                <span class="title">메타태그 - Keywords</span>
                <v-spacer></v-spacer>
                <v-btn text small color="primary" v-if="hasMapping" @click.stop.prevent="selectParam">SELECT</v-btn>
            </v-card-title>
            <v-divider class="mx-0"></v-divider>
            <v-card-text class="pa-3">
                <v-row>
                    <v-col cols="12">
                        <v-text-field v-if="route.translate.meta_keywords.mapped" v-model="value_mapping" dense persistent-hint readonly>
                            <v-icon slot="append" color="error" @click.stop.prevent="unsetParam">mdi-close</v-icon>
                        </v-text-field>
                        <v-text-combobox v-else v-model="value" :combobox="true"></v-text-combobox>
                    </v-col>
                </v-row>
            </v-card-text>
        </v-card>
        <v-select-param-dialog v-model="dialog" :route="route" @submit="setParam"></v-select-param-dialog>
    </div>
</template>

<script>
    import {EventBus} from '../../event_bus'
    import VTextCombobox from '../../layouts/TextCombobox'
    import VSelectParamDialog from '../dialogs/SelectParam'

    export default {
        components: {
            VTextCombobox,
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
            value: [],
            value_mapping: ''
        }),
        watch: {
            value(val) {
                this.route.translate.meta_keywords.value = val
            },
            value_mapping(val) {
                if (val == '') {
                    this.route.translate.meta_keywords.value = this.value
                } else {
                    this.route.translate.meta_keywords.value = val
                }
            }
        },
        computed: {
            hasMapping() {
                return this.route.translate.columns && this.route.translate.columns.length
            }
        },
        methods: {
            setKeywords() {
                if (!this.route.translate.meta_keywords) {
                    this.$set(this.route.translate, 'meta_keywords', {
                        mapped: false,
                        value: []
                    })
                }

                if (this.route.translate.meta_keywords.mapped) {
                    this.value_mapping = this.route.translate.meta_keywords.value
                } else {
                    this.value = this.route.translate.meta_keywords.value
                }
            },
            selectParam() {
                this.dialog = true
            },
            setParam(val) {
                this.route.translate.meta_keywords.mapped = true
                this.value_mapping = val
            },
            unsetParam() {
                this.route.translate.meta_keywords.mapped = false
                this.value_mapping = ''
            }
        },
        created() {
            this.setKeywords()

            EventBus.$on('delete-mapping-columns', (columns) => {
                if (this.route.translate.meta_keywords.mapped) {
                    let match = this.route.translate.meta_keywords.value.match(/^\{([^\}]+)\}$/)
                    if (match && columns.indexOf(match[1]) != -1) {
                        this.unsetParam()
                    }
                }
            })
        }
    }
</script>
