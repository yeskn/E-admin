<template>
    <render :data="state.component"></render>
</template>

<script>
    import { store } from '@/store'
    import {defineComponent,inject} from 'vue'
    import render from '@/components/render.vue'
    export default defineComponent({
        name: "login.vue",
        components: {
            render,
        },
        setup(){
            const state = inject(store)
            return {
                state
            }
        }
    })
</script>

<style scoped>

</style>
