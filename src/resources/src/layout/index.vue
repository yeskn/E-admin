<template>
    <div class="app-wrapper">
        <sidebar v-if="sidebar.visible"></sidebar>
        <div class="main-container">
            <header-top></header-top>
            <tags-view></tags-view>
            <div class="main-content" v-loading="state.mainLoading">
                <div class="header-title" v-if="state.mainTitle">
                    <div>
                        <span class="title">{{state.mainTitle}}</span>
                        <span class="desc" v-if="state.mainDescription">{{state.mainDescription}}</span>
                    </div>
                    <breadcrumb style="margin-right: 5px"></breadcrumb>
                </div>
                <el-backtop target=".main-content"></el-backtop>
                <keep-alive>
                    <render :data="mainComponent" :key="route.fullPath"></render>
                </keep-alive>
                <render :data="state.component"></render>
            </div>
        </div>
    </div>
</template>

<script>
    import {useRoute} from 'vue-router'
    import {defineComponent, inject,computed} from 'vue'
    import headerTop from './headerTop.vue'
    import Sidebar from './sidebar/sidebar.vue'
    import render from '@/components/render.vue'
    import breadcrumb from '@/components/breadcrumb.vue'
    import tagsView from './tagsView.vue'
    import { store ,action} from '@/store'
    export default defineComponent({
        name: "index",
        components: {
            Sidebar,
            headerTop,
            render,
            breadcrumb,
            tagsView
        },
        setup(){
            const route = useRoute()
            const state = inject(store)
            const mainComponent = computed(()=>{
                const index =  action.getComponentIndex(route.fullPath)
                if(state.mainComponent[index]){
                    return state.mainComponent[index].component
                }else{
                    return null
                }
            })
            let proxyData = state.proxyData
            let sidebar = state.sidebar
            return {
                mainComponent,
                route,
                state,
                sidebar,
                proxyData,
            }
        }
    })
</script>

<style scoped>
    .header-title .title{
        font-weight: 400;
        font-size: 20px;
        color: #414750;
    }
    .header-title .desc{
        font-size: 14px;
        display: inline-block;
        padding-left: 5px;
        color: #777;
        border-left: #dcdfe6 solid 1px;
        margin-left: 8px;
        text-indent: 3px;
    }

    .header-title{
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-weight: 400;
        font-size: 24px;
        font-family: Montserrat,Nunito,sans-serif;
        margin-bottom: 10px;
    }
</style>
