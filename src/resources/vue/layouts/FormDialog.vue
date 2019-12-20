<template>
    <v-dialog v-model="dialog" v-bind="$attrs" scrollable>
        <template v-if="result.value === null">
            <v-form ref="form" v-model="valid" lazy-validation @submit.stop.prevent="submit">
                <v-card>
                    <v-card-title>
                        <slot name="title"></slot>
                    </v-card-title>
                    <v-card-text>
                        <v-container>
                            <slot></slot>
                        </v-container>
                    </v-card-text>
                    <v-card-actions class="justify-center pa-6 pt-2">
                        <v-btn type="submit" :color="color">확인</v-btn>
                        <v-btn :color="color" outlined @click="cancel">취소</v-btn>
                    </v-card-actions>
                </v-card>
            </v-form>
        </template>

        <template v-if="result.value !== null">
            <v-card>
                <v-card-title class="justify-center">
                    <v-icon :color="getColor" size="96">{{ getIcon }}</v-icon>
                </v-card-title>
                <v-card-text class="text-center pt-5">
                    <span class="subtitle-1 font-weight-medium">{{ result.message }}</span>
                </v-card-text>
                <v-card-actions class="justify-center pa-6 pt-2">
                    <v-btn :color="getColor" outlined @click="complete">확인</v-btn>
                </v-card-actions>
            </v-card>
        </template>
    </v-dialog>
</template>

<script>
    export default {
        model: {
            prop: 'value',
            event: 'update:value'
        },
        props: {
            value: {
                type: Boolean,
                default: false
            },
            color: {
                type: String,
                default: ''
            },
            result: {
                value: {
                    type: Boolean,
                    default: null
                },
                message: {
                    type: String,
                    default: ''
                }
            }
        },
        data: () => ({
            valid: true
        }),
        computed: {
            dialog: {
                get() {
                    return this.value
                },
                set(val) {
                    this.$emit('update:value', val)
                }
            },
            getColor() {
                return this.result.value ? 'success' : 'error'
            },
            getIcon() {
                return this.result.value ? 'mdi-checkbox-marked-circle-outline' : 'mdi-alert-circle-outline'
            }
        },
        watch: {
            dialog(val) {
                if (!val) {
                    setTimeout(() => {
                        this.closed()
                    }, 200)
                }
            }
        },
        methods: {
            submit() {
                if (!this.$refs.form.validate()) {
                    return false
                }

                this.$emit('submit')

                return false
            },
            cancel() {
                this.$emit('cancel')
                this.close()
            },
            complete() {
                this.$emit('complete', this.result.value)

                if (this.result.value) {
                    this.close()
                } else {
                    this.result.value = null
                    this.result.message = ''
                }
            },
            close() {
                this.dialog = false
            },
            closed() {
                this.$nextTick(() => {
                    this.$emit('closed')

                    this.result.value = null
                    this.result.message = ''

                    this.valid = true
                    if (this.$refs.form) {
                        this.$refs.form.resetValidation()
                    }
                })
            }
        }
    }
</script>
