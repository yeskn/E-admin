<template>
    <div class="header-container">
        <i v-if="sidebar.visible" :class="[sidebar.opend?'el-icon-s-unfold hamburger':'el-icon-s-fold hamburger']" style="font-size: 18px" @click="collapse"/>
        <el-menu :default-active="activeIndex" @select="selectMenu" class="el-menu-demo" mode="horizontal" >
            <el-menu-item v-for="item in menus" :index="item.id+''">
                <i :class="item.icon" v-if="item.icon"></i>
                <span slot="title">{{item.name}}</span>
            </el-menu-item>
        </el-menu>
    </div>
</template>

<script>
    import { link } from '/@/utils'
    import {defineComponent, ref ,inject} from 'vue'
    import { store, action} from '/@/store'
    export default defineComponent({
        name: "headerTop",
        setup() {
            const state = inject(store)
            const sidebar = state.sidebar
            const menus = state.menus
            function collapse(){
                action.sidebarOpen(!sidebar.opend)
            }
            function selectMenu(index,indexPath) {
                let target = true
                let menu = null
                menus.forEach(item=>{
                    if(item.id == index){
                        if(item.children){
                            action.sidebarVisible(true)
                            target = false
                        }else{
                            action.sidebarVisible(false)
                        }
                        action.selectMenuModule(index)
                        menu = item
                    }
                })
                if(target){
                    link(menu.url)
                }
            }
            return {
                selectMenu,
                collapse,
                sidebar,
                menus,
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
