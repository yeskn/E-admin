<template>
    <div :class="[sidebar.opend ? '':'collapse','sidebar-container']">
        <logo :collapse="sidebar.opend" ></logo>
        <el-menu :default-active="activeIndex"
                 text-color="#FFFFFF"
                 :collapse="!sidebar.opend"
                 :unique-opened="true"
                 :collapse-transition="true"
                 mode="vertical"
                 background-color="#000000"
                 >
            <el-menu-item index="1"><i class="el-icon-location"></i><span>我的工作台</span></el-menu-item>
            <el-submenu index="2">
                <template #title><i class="el-icon-location"></i><span>我的工作台</span></template>
                <el-menu-item index="2-1"><i class="el-icon-location"></i><span>我的工作台</span></el-menu-item>
                <el-menu-item index="2-2">选项2</el-menu-item>
                <el-menu-item index="2-3">选项3</el-menu-item>
                <el-submenu index="2-4">
                    <template #title>选项4</template>
                    <el-menu-item index="2-4-1">选项1</el-menu-item>
                    <el-menu-item index="2-4-2">选项2</el-menu-item>
                    <el-menu-item index="2-4-3">选项3</el-menu-item>
                </el-submenu>
            </el-submenu>
        </el-menu>
    </div>
</template>

<script lang="ts">
    import Logo from './logo.vue'
    import { defineComponent,inject, ref } from 'vue'
    import { store } from '/@/store'
    export default defineComponent ({
        name: "sidebar",
        components: {
            Logo
        },
        setup() {
            const state = inject(store)
            const sidebar = state.sidebar
            return {
                sidebar,
                activeIndex: ref('')
            }
        }
    })
</script>

<style scoped>
.collapse{
    width: 64px !important;
}
</style>