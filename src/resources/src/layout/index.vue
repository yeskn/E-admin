<template>
    <div class="app-wrapper">
        <sidebar v-if="sidebar.visible"></sidebar>
        <div class="main-container">
            <header-top></header-top>
            <div class="main-content">
                <el-backtop target=".main-content"></el-backtop>
                <transition name="el-fade-in">
                    <render :data="mainComponent"></render>
                </transition>
            </div>
        </div>
    </div>
</template>

<script>
    import {defineComponent, inject, computed, reactive, watch} from 'vue'
    import headerTop from './headerTop.vue'
    import Sidebar from './sidebar/sidebar.vue'
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
            let sidebar = state.sidebar
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
                state,
                sidebar,
                mainComponent,
                proxyData
            }
        }
    })
</script>

<style scoped>

</style>
