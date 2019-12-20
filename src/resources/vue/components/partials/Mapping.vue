<template>
    <v-card flat outlined class="elevation-2">
        <v-card-title class="pa-3">
            <span class="title">Mapping</span>
            <v-spacer></v-spacer>
            <v-btn icon @click="show = !show">
                <v-icon>{{ show ? 'mdi-chevron-up' : 'mdi-chevron-down' }}</v-icon>
            </v-btn>
        </v-card-title>
        <v-expand-transition>
            <div v-show="show">
                <v-divider class="mx-0"></v-divider>
                <v-card-text class="pa-3">
                    <v-row v-for="(param, key, index) in route.mapping" :key="key" :class="index > 0 ? 'mt-2' : ''">
                        <v-col cols="6" sm="4" md="2">
                            <v-text-field
                                label="Param"
                                :value="key"
                                class="static-text"
                                readonly
                                hide-details
                                dense
                            ></v-text-field>
                        </v-col>
                        <v-col cols="6" sm="4" md="2">
                            <v-select
                                label="Model"
                                v-model="param.model"
                                :items="getModels"
                                @input="updateModel(key)"
                                hide-details
                                dense
                                no-data-text="등록된 모델이 없습니다"
                            ></v-select>
                        </v-col>
                        <v-col cols="4" sm="4" md="2">
                            <v-select
                                label="Find By"
                                v-model="param.find_by"
                                :items="columns[key]"
                                hide-details
                                dense
                                no-data-text="설정 가능한 컬럼이 없습니다"
                            ></v-select>
                        </v-col>
                        <v-col cols="8" sm="12" md="6">
                            <v-select
                                ref="columns"
                                label="Use Columns (for Mapping)"
                                v-model="param.columns"
                                :items="columns[key]"
                                multiple
                                hide-details
                                deletable-chips
                                dense
                                no-data-text="설정 가능한 컬럼이 없습니다"
                                @change="updateColumns"
                            >
                                <template v-slot:selection="{ item, parent }">
                                    <v-chip
                                        class="px-2"
                                        color="grey darken-1"
                                        label
                                        small
                                        dark
                                        close
                                        outlined
                                        @click:close="parent.selectItem(item)"
                                    >
                                        {{ item }}
                                    </v-chip>
                                </template>
                            </v-select>
                        </v-col>
                    </v-row>
                </v-card-text>
            </div>
        </v-expand-transition>
    </v-card>
</template>

<script>
    import {EventBus} from '../../event_bus'

    export default {
        props: {
            route: {
                type: Object,
                default() {
                    return {}
                }
            },
            models: {
                type: Array,
                default() {
                    return []
                }
            },
        },
        data: () => ({
            show: false,
            columns: {}
        }),
        watch: {
            columnsProp: {
                handler(newVal, oldVal) {
                    let add_items = this.diffArray(newVal, oldVal)
                    let del_items = this.diffArray(oldVal, newVal)
                    if (add_items.length) {
                        EventBus.$emit('add-mapping-columns', add_items)
                    }
                    if (del_items.length) {
                        EventBus.$emit('delete-mapping-columns', del_items)
                    }
                },
                deep: true
            }
        },
        computed: {
            getModels() {
                let models = []
                for (let item of this.models) {
                    models.push({
                        value: item.model,
                        text: item.model.split('\\').pop()
                    })
                }
                return models
            },
            columnsProp() {
                return this.route.translate.columns
            }
        },
        methods: {
            diffArray(a, b) {
                if ((a instanceof Array) && (b instanceof Array)) {
                    return a.filter(item => {
                        return b.indexOf(item) == -1
                    })
                }
                return []
            },
            updateModel(param) {
                let mapping = this.route.mapping[param]

                mapping.find_by = null
                mapping.columns = []

                this.setColumns(param)
            },
            setMapping() {
                let mapping = this.route.mapping
                if (!mapping) {
                    mapping = {}
                }

                this.route.mapping = {}
                for (let val of this.route.params) {
                    if (!mapping[val]) {
                        mapping[val] = {
                            model: null,
                            find_by: null,
                            columns: []
                        }
                    }
                    this.route.mapping[val] = mapping[val]
                    this.setColumns(val)
                }
                
            },
            setColumns(param) {
                let mapping = this.route.mapping[param]
                if (mapping.model) {
                    for (let model of this.models) {
                        if (model.model == mapping.model) {
                            this.$set(this.columns, param, model.columns)
                            break
                        }
                    }
                }
            },
            updateColumns() {
                this.$set(this.route.translate, 'columns', [])
                for (let param in this.route.mapping) {
                    for (let col of this.route.mapping[param].columns) {
                        this.route.translate.columns.push(param.toUpperCase() + '-' + col.toUpperCase())
                    }
                }
            }
        },
        created() {
            this.setMapping()
            this.updateColumns()
        }
    }
</script>

<style scoped>
    .v-chip + .v-chip {margin-left:0 !important;}
</style>