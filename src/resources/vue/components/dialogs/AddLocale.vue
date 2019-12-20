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
            <span class="title grey--text text--darken-2">지역 추가</span>
        </template>

        <v-text-field
            ref="name"
            class="locale-name"
            label="Locale"
            hint="영문으로 입력해 주세요"
            counter="4"
            v-model="name"
            :rules="rules.name"
            required
        ></v-text-field>
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
            }
        },
        data: () => ({
            result: {
                value: null,
                message: '',
                response: null
            },
            name: '',
            rules: {
                name: [
                    v => !!v || '지역을 입력해 주세요',
                    v => /^[A-Za-z]+$/.test(v) || '영문만 입력해 주세요',
                    v => v.length <= 4 || '최대 4자까지 입력 가능합니다.'
                ]
            },
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
                this.$store.commit('setLoading', true)
                this.$axios
                    .post(APP_URL + '/locales', {
                        name: this.name
                    })
                    .then(response => {
                        this.result.response = response
                        this.result.value = true
                        this.result.message = this.name.toUpperCase() + ' 언어가 추가 되었습니다.'
                    })
                    .catch(error => {
                        this.result.message = error.response.data.message
                        this.result.value = false
                    })
                    .finally(() => {
                        this.$store.commit('setLoading', false)
                    })
            },
            closed() {
                this.name = ''
                if (this.result.value) {
                    let locales = this.result.response.data.locales
                    EventBus.$emit('update-locales', locales)
                    EventBus.$emit('update-locale', locales.pop().name)
                }
            }
        }
    }
</script>

<style scoped>
    .locale-name >>> input {text-transform:uppercase;}
</style>