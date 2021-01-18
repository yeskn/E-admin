<template>
    <div :class="[sidebar.opend ? '':'collapse','sidebar-container']">
        <logo :collapse="sidebar.opend" ></logo>
        <el-menu :default-active="activeIndex"
                 text-color="#FFFFFF"
                 :collapse="!sidebar.opend"
                 :collapse-transition="true"
                 mode="vertical"
                 background-color="#000000"
                 @select="select"
                 >
            <menu-item v-for="item in menus" :menu="item"></menu-item>
        </el-menu>
    </div>
</template>

<script>
    import {useRoute} from 'vue-router'
    import { link,findParent,findTree } from '/@/utils'
    import Logo from '../logo.vue'
    import menuItem from './menuItem.vue'
    import { defineComponent,inject, ref ,computed ,watchEffect} from 'vue'
    import { store ,action} from '/@/store'
    export default defineComponent ({
        name: "sidebar",
        components: {
            Logo,
            menuItem,
        },
        setup() {
            const route = useRoute()
            const state = inject(store)
            const sidebar = state.sidebar
            const activeIndex = computed(()=>{
                let menu = findTree(state.menus,route.path,'url')
                if(menu){
                    let menuLevels = findParent(state.menus,menu.pid)
                    menuLevels.push(menu)
                    action.setBreadcrumb(menuLevels)
                    return menu.id+''
                }else{
                    return ''
                }
            })
            const menus = computed(()=>{
                let menus = []
                state.menus.forEach(res=>{
                    if(res.id == state.menuModule && res.children){
                        menus = res.children
                    }
                })
                return menus
            })
            function select(id,index) {
                let menu = findTree(state.menus,id,'id')
                link(menu.url)
            }
            return {
                select,
                menus,
                sidebar,
                activeIndex
            }
        }
    })
</script>

<style scoped>
.collapse{
    width: 64px !important;
}
</style>
