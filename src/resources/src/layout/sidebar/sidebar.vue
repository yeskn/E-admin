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
            <menu-item v-for="item in menus" :menu="item" :key="item.id"></menu-item>
        </el-menu>
    </div>
</template>

<script>
    import {useRoute} from 'vue-router'
    import { link,findTree } from '@/utils'
    import Logo from '../logo.vue'
    import menuItem from './menuItem.vue'
    import { defineComponent,inject, computed} from 'vue'
    import { store ,action} from '@/store'
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
            //当前激活菜单
            const activeIndex = computed(()=>{
                let menu = findTree(state.menus,route.fullPath.substr(1),'url')
                if(menu){
                    return menu.id+''
                }else{
                    return ''
                }
            })
            //侧边栏菜单渲染
            const menus = computed(()=>{
                let menu= null
                state.menus.forEach(res=>{
                    if(res.id == state.menuModule && res.children){
                        menu = res.children
                    }
                })

                return menu
            })
            //选择菜单
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
