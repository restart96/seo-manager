<template>
    <v-app v-cloak>
        <v-header></v-header>
        <v-content>
            <v-container fluid>
                <v-routes></v-routes>
            </v-container>
        </v-content>

        <v-overlay :value="loading" z-index="1000">
            <v-progress-circular indeterminate size="96" width="6"></v-progress-circular>
        </v-overlay>
    </v-app>
</template>

<script>
    import VHeader from './layouts/Header'
    import VRoutes from './components/Routes'

    export default {
        components: {
            VHeader,
            VRoutes
        },
        data: () => ({
            loading: false
        }),
        created() {
            this.$store.watch(
                (state, getters) => getters.loading,
                (val) => {
                    this.loading = val
                }
            )
        }
    }
</script>
