<template>
    <div class="app-wrapper">
        <sidebar v-if="sidebar.visible"></sidebar>
        <div class="main-container">
            <header-top></header-top>
            <div class="main-content">
                <el-backtop target=".main-content"></el-backtop>
                <transition name="el-fade-in">
                    <div v-show="!state.mainLoading">
                        <div class="header-title" v-if="proxyData.eadmin_title">
                            <div class="title" >{{proxyData.eadmin_title}}</div>
                            <breadcrumb></breadcrumb>
                        </div>
                        <render :data="mainComponent"></render>
                    </div>
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
    import breadcrumb from '/@/components/breadcrumb.vue'
    import { store } from '/@/store'
    export default defineComponent({
        name: "index",
        components: {
            Sidebar,
            headerTop,
            render,
            breadcrumb,
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
.header-title .title{
    font-size: 16px;
}
.header-title{
    display: flex;
    justify-content: space-between;
    height: 40px;
    padding:0 15px;
    align-items: center;
    background: #ffffff;
    border-bottom: 1px solid #ededed;

}
</style>
