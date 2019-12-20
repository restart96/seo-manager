<template>
    <div>
        <v-btn large color="success" outlined @click="confirmUpdateRoute">업데이트</v-btn>
        <v-btn large color="error" outlined @click="confirmDeleteRoute">삭제</v-btn>

        <v-form-dialog
            v-model="dialog.update"
            :result="result.update"
            color="success"
            max-width="400px"
            @submit="updateRoute"
        >
            <template v-slot:title>
                <v-icon class="flex-grow-1" color="success" size="96">mdi-checkbox-marked-circle-outline</v-icon>
            </template>

            <div class="text-center">
                <span class="body-1 font-weight-bold">이 Route를 업데이트하시겠습니까?</span>
            </div>
        </v-form-dialog>

        <v-form-dialog
            v-model="dialog.delete"
            :result="result.delete"
            color="error"
            max-width="400px"
            @submit="deleteRoute"
            @closed="closedDeleteRoute"
        >
            <template v-slot:title>
                <v-icon class="flex-grow-1" color="error" size="96">mdi-alert-circle-outline</v-icon>
            </template>

            <div class="text-center">
                <span class="body-1 font-weight-bold">이 Route를 삭제하시겠습니까?</span>
            </div>
        </v-form-dialog>
    </div>
</template>

<script>
    import {EventBus} from '../../event_bus'
    import VFormDialog from '../../layouts/FormDialog'

    export default {
        components: {
            VFormDialog
        },
        props: {
            route: {
                type: Object,
                default() {
                    return {}
                }
            },
            locale: {
                type: String,
                default: null
            }
        },
        data: () => ({
            dialog: {
                update: false,
                delete: false
            },
            result: {
                update: {
                    value: null,
                    message: ''
                },
                delete: {
                    value: null,
                    message: ''
                }
            }
        }),
        methods: {
            confirmUpdateRoute() {
                this.dialog.update = true
            },
            confirmDeleteRoute() {
                this.dialog.delete = true
            },
            updateRoute() {
                this.$store.commit('setLoading', true)

                let data = new FormData()
                data.append('_method', 'PATCH')
                data.append('locale', this.locale)
                data.append('mapping', JSON.stringify(this.route.mapping))
                data.append('translate', JSON.stringify(this.route.translate))
                if (!this.route.translate.meta_image.mapped &&
                    this.route.translate.meta_image.value) {
                    data.append('meta_image_file', this.route.translate.meta_image.value)
                }

                this.$axios
                    .post(APP_URL + '/routes/' + this.route.id, data, {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        }
                    })
                    .then(response => {
                        if (response.data.updated) {
                            EventBus.$emit('update-route', response.data.route)
                            this.result.update.message = '성공적으로 업데이트되었습니다.'
                            this.result.update.value = true
                        }
                    })
                    .catch(error => {
                        this.result.update.message = error.response.data.message
                        this.result.update.value = false
                    })
                    .finally(() => {
                        this.dialog.update = true
                        this.$store.commit('setLoading', false)
                    })
            },
            deleteRoute() {
                this.$store.commit('setLoading', true)

                this.$axios
                    .delete(APP_URL + '/routes/' + this.route.id)
                    .then(response => {
                        if (response.data.deleted) {
                            this.result.delete.message = 'Route를 삭제 완료했습니다.'
                            this.result.delete.value = true
                        }
                    })
                    .catch((error) => {
                        this.result.delete.message = error.response.data.message
                        this.result.delete.value = false
                    })
                    .finally(() => {
                        this.$store.commit('setLoading', false)
                    })
            },
            closedDeleteRoute() {
                if (this.result.delete.value) {
                    EventBus.$emit('delete-route', this.route)
                }
            }
        }
    }
</script>
