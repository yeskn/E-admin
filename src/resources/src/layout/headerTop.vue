<template>
    <div class="header-container">
        <i :class="[sidebar.opend?'el-icon-s-unfold hamburger':'el-icon-s-fold hamburger']" style="font-size: 18px" @click="collapse"/>
        <el-menu :default-active="activeIndex" class="el-menu-demo" mode="horizontal" >
            <el-menu-item index="1">处理中心</el-menu-item>
            <el-submenu index="2">
                <template #title>我的工作台</template>
                <el-menu-item index="2-1">选项1</el-menu-item>
                <el-menu-item index="2-2">选项2</el-menu-item>
                <el-menu-item index="2-3">选项3</el-menu-item>
                <el-submenu index="2-4">
                    <template #title>选项4</template>
                    <el-menu-item index="2-4-1">选项1</el-menu-item>
                    <el-menu-item index="2-4-2">选项2</el-menu-item>
                    <el-menu-item index="2-4-3">选项3</el-menu-item>
                </el-submenu>
            </el-submenu>
            <el-menu-item index="3" disabled>消息中心</el-menu-item>
            <el-menu-item index="4"><a href="https://www.ele.me" target="_blank">订单管理</a></el-menu-item>
        </el-menu>
    </div>
</template>

<script lang="ts">
    import {defineComponent, ref ,inject} from 'vue'
    import { store, action} from '/@/store'
    export default defineComponent({
        name: "headerTop",
        setup() {
            const state = inject(store)
            const sidebar = state.sidebar
            function collapse(){
                action.sidebarOpen(!sidebar.opend)
            }
            return {
                collapse,
                sidebar,
                activeIndex: ref('')
            }
        }
    })
</script>

<style scoped>
    .header-container {
        display: flex;
        align-items: center;
        background: #FFFFFF;
        height: 60px;
        width: 100%;
        border-bottom: 1px solid #dee4ec;
        box-shadow: 0 4px 10px -2px rgba(0, 0, 0, 0.05);

    }

    .hamburger {
        padding: 0 10px;
        cursor: pointer;
    }
</style>