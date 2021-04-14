<template>
    <div :class="['app-wrapper',state.device === 'mobile' ? 'mobile':'']">
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
                    <breadcrumb style="margin-right: 5px" v-if="state.device != 'mobile'"></breadcrumb>
                </div>
                <el-backtop target=".main-content"></el-backtop>
                <keep-alive v-for="item in state.mainComponent" :key="item.url">
                    <render v-if="route.fullPath == item.url" :data="item.component"></render>
                </keep-alive>
                <render :data="state.component"></render>
            </div>
        </div>
    </div>
</template>

<script>
    import {useRoute} from 'vue-router'
    import {defineComponent, inject} from 'vue'
    import headerTop from './headerTop.vue'
    import Sidebar from './sidebar/sidebar.vue'
    import render from '@/components/render.vue'
    import breadcrumb from '@/components/breadcrumb.vue'
    import tagsView from './tagsView.vue'
    import { store} from '@/store'
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
            let sidebar = state.sidebar
            return {
                route,
                state,
                sidebar,
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
