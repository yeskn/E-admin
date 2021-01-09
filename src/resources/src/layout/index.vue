<template>
    <div class="app-wrapper">
        <sidebar></sidebar>
        <div class="main-container">
            <header-top></header-top>
            <div class="main-content">
                <render :data="mainComponent"></render>
            </div>
        </div>
    </div>
</template>

<script>
    import {defineComponent, inject, computed, reactive, watch} from 'vue'
    import headerTop from './headerTop.vue'
    import Sidebar from './sidebar.vue'
    import render from '/@/components/render.vue'
    import { store } from '/@/store'
    export default defineComponent({
        name: "index",
        components: {
            Sidebar,
            headerTop,
            render,
        },
        setup(){
            const state = inject(store)
            let proxyData = state.proxyData
            const mainComponent = computed(()=>{
               return  state.mainComponent
            })

            //重新加载赋值proxyData
            watch(()=>state.mainComponent,(newValue)=>{
               if(newValue){
                   for(let i in proxyData){
                       delete proxyData[i]
                   }
                }
            })
            return {
                mainComponent,
                proxyData
            }
        }
    })
</script>

<style scoped>

</style>
