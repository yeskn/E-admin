<template>
    <render :data="mainComponent"></render>
</template>

<script>
    import { store } from '/@/store'
    import {defineComponent,inject} from 'vue'
    import render from '/@/components/render.vue'
    export default defineComponent({
        name: "login.vue",
        components: {
            render,
        },
        setup(){
            const state = inject(store)
            const mainComponent = state.mainComponent
            return {
                mainComponent
            }
        }
    })
</script>

<style scoped>

</style>