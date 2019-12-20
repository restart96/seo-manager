<template>
    <v-form-dialog
        v-model="dialog"
        :result="result"
        color="success"
        max-width="400px"
        @submit="submit"
        @closed="closed"
    >
        <template v-slot:title>
            <span class="title grey--text text--darken-2">파라미터 선택</span>
        </template>

        <v-select
            label="Parameter"
            v-model="select"
            :items="route.translate.columns"
            :rules="rules.select"
            required
        ></v-select>
    </v-form-dialog>
</template>

<script>
    import {EventBus} from '../../event_bus'
    import VFormDialog from '../../layouts/FormDialog'

    export default {
        components: {
            VFormDialog
        },
        model: {
            prop: 'value',
            event: 'update:value'
        },
        props: {
            value: {
                type: Boolean,
                default: false
            },
            route: {
                type: Object,
                default() {
                    return {}
                }
            }
        },
        data: () => ({
            result: {
                value: null,
                message: '',
                response: null
            },
            rules: {
                select: [v => !!v || '파라미터를 선택해 주세요']
            },
            select: null
        }),
        computed: {
            dialog: {
                get() {
                    return this.value
                },
                set(val) {
                    this.$emit('update:value', val)
                }
            }
        },
        methods: {
            submit() {
                this.$emit('submit', '{' + this.select + '}')
                this.dialog = false
            },
            closed() {
                this.select = ''
            }
        }
    }
</script>
