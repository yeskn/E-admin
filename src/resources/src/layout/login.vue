<template>
    <render :data="mainComponent"></render>
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
            const mainComponent = computed(()=>{
                const index = action.getComponentIndex(path)
                return state.mainComponent[index].component
            })
            onBeforeUnmount(()=>{
                action.clearComponent(path)
            })
            return {
                mainComponent,
                state
            }
        }
    })
</script>

<style scoped>

</style>
