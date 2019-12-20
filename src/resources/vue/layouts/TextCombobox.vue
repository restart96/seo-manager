<template>
    <div>
        <div v-show="combobox">
            <v-combobox
                ref="text"
                v-model="text"
                :search-input.sync="search"
                hint="* 직접 텍스트를 입력하거나 파라미터를 클릭하세요."
                persistent-hint
                no-filter
                dense
                multiple
                deletable-chips
            >
                <template v-slot:selection="{ item, parent }">
                    <v-chip
                        class="px-2 item"
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
                <template v-slot:no-data>
                    <v-list-item v-if="!!search">
                        <v-list-item-content>
                            <v-list-item-title>
                                <v-btn color="success" text small @click.stop="addText">엔터를 누르거나 클릭하여 항목을 추가합니다</v-btn>
                            </v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>
                </template>
            </v-combobox>
        </div>
        <div v-show="!combobox">
            <slot name="text-field" v-bind:item="text[0]">
                <v-text-field v-model="text[0]" dense persistent-hint></v-text-field>
            </slot>
        </div>
    </div>
</template>

<script>
    import Sortable from 'sortablejs'

    export default {
        model: {
            prop: 'value',
            event: 'update:value'
        },
        props: {
            value: {
                type: Array,
                default() {
                    return []
                }
            },
            combobox: {
                default: false
            }
        },
        data: () => ({
            search: ''
        }),
        watch: {
            combobox(val) {
                if (!val && this.text.length) {
                    this.$emit('update:value', [this.text.join(' ')])
                }
            }
        },
        computed: {
            text: {
                get() {
                    return this.value
                },
                set(val) {
                    this.$emit('update:value', val)
                }
            }
        },
        methods: {
            addText() {
                this.text.push(this.search.trim())
                this.search = ''
                this.$refs.text.focus()
            },
            setSortable() {
                let that = this
                let element = this.$refs.text.$el.querySelector('[role=combobox] .v-select__selections')
                Sortable.create(element, {
                    animation: 100,
                    draggable: '.item',
                    forceFallback: true,
                    onUpdate({newIndex, oldIndex}) {
                        let items = that.text.slice()
                        const item = items.splice(oldIndex, 1)[0]
                        items.splice(newIndex, 0, item)

                        that.text = []
                        that.$nextTick(() => {
                            that.text = items.slice()
                        })
                    }
                })
            }
        },
        mounted() {
            this.setSortable()
        }
    }
</script>

<style scoped>
    .v-chip.item {cursor:move;}
    .v-chip.item.sortable-fallback {opacity:0.25 !important;}
    .v-chip.item.sortable-ghost {background:#ffffe2 !important;}
</style>
