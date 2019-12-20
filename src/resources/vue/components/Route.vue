<template>
    <div>
        <v-dialog
            v-model="dialog"
            persistent
            no-click-animation
            fullscreen
            hide-overlay
            scrollable
            transition="dialog-bottom-transition"
        >
            <v-card>
                <v-toolbar dark color="success" class="flex-grow-0">
                    <v-toolbar-title>{{ route.uri }}</v-toolbar-title>
                    <v-spacer></v-spacer>
                    <v-btn icon dark @click="close">
                        <v-icon>mdi-close</v-icon>
                    </v-btn>
                </v-toolbar>
                <v-card-text v-if="loading" class="d-flex justify-center align-center">
                    <v-progress-circular indeterminate color="grey lighten-2"/>
                </v-card-text>
                <v-card-text v-else class="text-center pa-4">
                    <v-row class="px-2">
                        <v-col cols="12" v-if="route.params.length">
                            <v-route-mapping :route="route" :models="models"></v-route-mapping>
                        </v-col>
                        <v-col cols="12" lg="6">
                            <v-route-title :route="route"></v-route-title>
                        </v-col>
                        <v-col cols="12" lg="6">
                            <v-route-meta-title :route="route"></v-route-meta-title>
                        </v-col>
                        <v-col cols="12" lg="6">
                            <v-route-meta-description :route="route"></v-route-meta-description>
                        </v-col>
                        <v-col cols="12" lg="6">
                            <v-route-meta-keywords :route="route"></v-route-meta-keywords>
                        </v-col>
                        <v-col cols="12" lg="6">
                            <v-route-meta-image :route="route"></v-route-meta-image>
                        </v-col>
                        <v-col cols="12" lg="6">
                            <v-route-meta-url :route="route"></v-route-meta-url>
                        </v-col>
                    </v-row>
                </v-card-text>
                <v-card-actions v-if="!loading" class="justify-center pa-6 pt-2">
                    <v-route-actions :route="route" :locale="locale"></v-route-actions>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </div>
</template>

<script>
    import {EventBus} from '../event_bus'
    import VRouteMapping from './partials/Mapping'
    import VRouteTitle from './partials/Title'
    import VRouteMetaTitle from './partials/MetaTitle'
    import VRouteMetaDescription from './partials/MetaDescription'
    import VRouteMetaKeywords from './partials/MetaKeywords'
    import VRouteMetaUrl from './partials/MetaUrl'
    import VRouteMetaImage from './partials/MetaImage'
    import VRouteActions from './partials/Actions'

    const pause = ms => new Promise(resolve => setTimeout(resolve, ms))

    export default {
        components: {
            VRouteMapping,
            VRouteTitle,
            VRouteMetaTitle,
            VRouteMetaDescription,
            VRouteMetaKeywords,
            VRouteMetaUrl,
            VRouteMetaImage,
            VRouteActions
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
            locale: {
                type: String,
                default: null
            },
            id: {
                type: Number,
                default: null
            },
            models: {
                type: Array,
                default() {
                    return []
                }
            }
        },
        data: () => ({
            loading: true,
            sync: false,
            dialog: false,
            route: {}
        }),
        watch: {
            value(val) {
                if (this.sync) {
                    this.sync = false
                } else if (val != this.dialog) {
                    this.sync = true
                    this.dialog = val
                }
            },
            dialog(val) {
                if (this.sync) {
                    this.sync = false
                } else if (val != this.value) {
                    this.sync = true
                    this.$emit('update:value', val)
                }

                setTimeout(() => {
                    val ? this.opened() : this.closed()
                }, 200)
            }
        },
        methods: {
            async getRoute() {
                this.loading = true

                await pause(200)

                this.$axios.source = this.$axios.CancelToken.source()

                return new Promise((resolve, reject) => {
                    this.$axios
                        .get(APP_URL + '/routes/' + this.id + '/edit', {
                            cancelToken: this.$axios.source.token,
                            params: {
                                locale: this.locale
                            }
                        })
                        .then(response => {
                            this.route = response.data.route
                        })
                        .finally(() => {
                            this.loading = false
                        })
                })
            },
            reset() {
                this.loading = true
                this.route = {}
            },
            opened() {
                this.getRoute()
            },
            close() {
                this.dialog = false
            },
            closed() {
                this.$nextTick(() => {
                    if (this.$axios.source) {
                        this.$axios.source.cancel();
                        this.$axios.source = null
                    }

                    this.$emit('closed')
                    this.reset()
                })
            }
        },
        mounted() {
            EventBus.$on('delete-route', (route) => {
                this.close()
            })
        }
    }
</script>

<style scoped>
    .v-card__actions .v-btn {min-width:100px;}
</style>
