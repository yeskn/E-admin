<template>
    <div class="app-wrapper">
        <sidebar v-if="sidebar.visible"></sidebar>
        <div class="main-container">
            <header-top></header-top>
            <tags-view></tags-view>
            <div class="main-content" v-loading="state.mainLoading">
                <el-backtop target=".main-content"></el-backtop>
                <keep-alive v-for="item in state.mainComponent">
                    <render v-if="route.fullPath == item.url" :data="item.component"></render>
                </keep-alive>
            </div>
        </div>
    </div>
</template>

<script>
    import {useRoute,useRouter} from 'vue-router'
    import {defineComponent, inject} from 'vue'
    import headerTop from './headerTop.vue'
    import Sidebar from './sidebar/sidebar.vue'
    import render from '/@/components/render.vue'
    import breadcrumb from '/@/components/breadcrumb.vue'
    import tagsView from './tagsView.vue'
    import { store } from '/@/store'
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
            const router = useRouter()
            const state = inject(store)
            let proxyData = state.proxyData
            let sidebar = state.sidebar

            return {
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
        font-size: 18px;
    }
    .header-title{
        display: flex;
        justify-content: space-between;
        height: 40px;
        padding-left:15px;
        align-items: center;
        background: #ffffff;
        border-bottom: 1px solid #ededed;

    }


</style>
