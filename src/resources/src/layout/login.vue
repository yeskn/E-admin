<template>
    <render :data="state.component"></render>
</template>

<script>
    import { store,action } from '@/store'
    import {defineComponent,inject,computed,onBeforeUnmount} from 'vue'
    import render from '@/components/render.vue'
    import {useRoute} from 'vue-router'
    export default defineComponent({
        name: "login.vue",
        components: {
            render,
        },
        setup(){
            const route = useRoute()
            const state = inject(store)
            const path = route.fullPath
            onBeforeUnmount(()=>{
                action.clearComponent(path)
            })
            return {
                state
            }
        }
    })
</script>

<style scoped>

</style>
