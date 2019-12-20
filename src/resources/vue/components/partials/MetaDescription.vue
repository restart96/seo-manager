<template>
    <div>
        <v-card flat outlined class="elevation-2">
            <v-card-title class="pa-3">
                <span class="title">메타태그 - Description</span>
                <v-spacer></v-spacer>
                <v-btn text small color="primary" v-if="hasMapping" @click.stop.prevent="selectParam">SELECT</v-btn>
            </v-card-title>
            <v-divider class="mx-0"></v-divider>
            <v-card-text class="pa-3">
                <v-row>
                    <v-col cols="12">
                        <v-text-field v-if="route.translate.meta_description.mapped" v-model="value_mapping" dense persistent-hint readonly>
                            <v-icon slot="append" color="error" @click.stop.prevent="unsetParam">mdi-close</v-icon>
                        </v-text-field>
                        <v-textarea
                            v-else
                            v-model="value"
                            hint="* 직접 텍스트를 입력하거나 파라미터를 선택하세요."
                            persistent-hint
                            dense
                            no-resize
                            auto-grow
                            rows="1"
                        ></v-textarea>
                        
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
            value: '',
            value_mapping: ''
        }),
        watch: {
            value(val) {
                this.route.translate.meta_description.value = val
            },
            value_mapping(val) {
                if (val == '') {
                    this.route.translate.meta_description.value = this.value
                } else {
                    this.route.translate.meta_description.value = val
                }
            }
        },
        computed: {
            hasMapping() {
                return this.route.translate.columns && this.route.translate.columns.length
            }
        },
        methods: {
            setDescription() {
                if (!this.route.translate.meta_description) {
                    this.$set(this.route.translate, 'meta_description', {
                        mapped: false,
                        value: ''
                    })
                }

                if (this.route.translate.meta_description.mapped) {
                    this.value_mapping = this.route.translate.meta_description.value
                } else {
                    this.value = this.route.translate.meta_description.value
                }
            },
            selectParam() {
                this.dialog = true
            },
            setParam(val) {
                this.route.translate.meta_description.mapped = true
                this.value_mapping = val
            },
            unsetParam() {
                this.route.translate.meta_description.mapped = false
                this.value_mapping = ''
            }
        },
        created() {
            this.setDescription()

            EventBus.$on('delete-mapping-columns', (columns) => {
                if (this.route.translate.meta_description.mapped) {
                    let match = this.route.translate.meta_description.value.match(/^\{([^\}]+)\}$/)
                    if (match && columns.indexOf(match[1]) != -1) {
                        this.unsetParam()
                    }
                }
            })
        }
    }
</script>
