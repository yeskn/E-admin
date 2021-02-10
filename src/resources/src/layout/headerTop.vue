<template>
    <div class="header-container">
        <i v-if="sidebar.visible" :class="[sidebar.opend?'el-icon-s-unfold hamburger':'el-icon-s-fold hamburger']"
           style="font-size: 18px" @click="collapse"/>
        <el-menu :default-active="activeIndex" @select="selectMenu" class="el-menu-demo" mode="horizontal">
            <el-menu-item v-for="item in menus" :index="item.id+''">
                <i :class="item.icon" v-if="item.icon"></i>
                <span slot="title">{{item.name}}</span>
            </el-menu-item>
        </el-menu>
        <div class="right-menu">
            <el-tooltip effect="dark" content="全屏" placement="bottom">
                <screenfull id="screenfull" class="right-menu-item hover-effect" />
            </el-tooltip>
            <el-tooltip effect="dark" content="刷新" placement="bottom">
                <div class="right-menu-item hover-effect" @click="refresh">
                    <i class="el-icon-refresh-right refresh"/>
                </div>
            </el-tooltip>
            <el-dropdown trigger="click" class="avatar-container">
                <div class="avatar-wrapper">
                    <img :src="state.info.avatar" class="user-avatar ">
                    <span class="right-menu-item">
            <span style="color: #777777">{{ state.info.nickname }}</span>
            <div><el-badge is-dot type="success" style="top:4px"/> <span style="color: #999999">{{ state.info.username }}</span></div>
          </span>
                    <i class="el-icon-caret-bottom" style="line-height: 30px"/>
                </div>
                <template #dropdown>
                    <el-dropdown-menu class="user-dropdown">
                        <el-dropdown-item @click.native="info">
                            <span style="display:block;">个人信息</span>
                        </el-dropdown-item>
                        <el-dropdown-item @click.native="updatePwd">
                            <span style="display:block;">修改密码</span>
                        </el-dropdown-item>
                        <el-dropdown-item divided @click.native="logout">
                            <span style="display:block;">退出登陆</span>
                        </el-dropdown-item>
                    </el-dropdown-menu>
                </template>
            </el-dropdown>
        </div>
    </div>
</template>

<script>

    import {useRoute} from 'vue-router'
    import {link, findParent, findTree} from '/@/utils'
    import {defineComponent, watch, inject, computed} from 'vue'
    import {store, action} from '/@/store'
    import router from "../router";
    import screenfull from "/@/components/screenfull.vue";

    export default defineComponent({
        name: "headerTop",
        components:{
            screenfull
        },
        setup() {
            const route = useRoute()

            const state = inject(store)
            const sidebar = state.sidebar
            const menus = state.menus
            const activeIndex = computed(() => {
                let menu = findTree(state.menus, route.path.substr(1), 'url'), menuLevels = []
                if(route.path === '/' && menus.length > 0){
                    selectMenu(menus[0].id)
                    return state.menuModule + ''
                } else if (menu) {
                    menuLevels = findParent(state.menus, menu.pid)
                    let menuId
                    if (menuLevels.length > 0) {
                        menuId = menuLevels[0].id
                        action.selectMenuModule(menuId)
                    }
                    menuLevels.push(menu)
                    if (menu.pid === 0) {
                        action.selectMenuModule(menu.id)
                    }
                    action.setBreadcrumb(menuLevels)
                    return menuLevels[0].id + ''
                }else {
                    return state.menuModule + ''
                }
            })
            watch(() => state.menuModule, (val, oldVal) => {
                if(oldVal){
                    menus.forEach(item => {
                        if (item.id == val && item.children) {
                            action.sidebarVisible(true)
                            let url = defaultMenu(item.children)
                            if (url) {
                                link(url)
                            }
                        } else {
                            action.sidebarVisible(false)
                        }
                    })
                }
            })

            //侧边栏展开收缩
            function collapse() {
                action.sidebarOpen(!sidebar.opend)
            }

            //选择菜单
            function selectMenu(index, indexPath) {
                let menu = findTree(menus, index, 'id')
                action.selectMenuModule(index)
                if (!menu.children) {
                    link(menu.url)
                }
            }

            //查找当前第一个菜单
            function defaultMenu(menus) {
                for (let key in menus) {
                    if (menus[key].children) {
                        let item = defaultMenu(menus[key].children)
                        if (item) {
                            return item
                        }
                    } else {
                        return menus[key].url
                    }
                }
                return null
            }
            //退出登录
            function logout() {
                state.info.id = null
                localStorage.removeItem('eadmin_token')
                router.push(`/login?redirect=${route.fullPath}`)
            }
            //刷新
            function refresh() {
                router.push({path:'/refresh',replace:true})
            }
            return {
                activeIndex,
                state,
                selectMenu,
                collapse,
                sidebar,
                menus,
                logout,
                refresh
            }
        }
    })
</script>

<style lang="scss" scoped>
    .header-container {
        display: flex;
        align-items: center;
        background: #FFFFFF;
        height: 60px;
        width: 100%;
        /*box-shadow: 0 1px 4px rgba(0, 21, 41, .08);*/
    }

    .hamburger {
        padding: 0 10px;
        cursor: pointer;
    }

    .right-menu {
        margin-left: auto;
        height: 60px;
        display: flex;
        display: -webkit-flex;
        align-items: center;
        justify-content: center;
        color: #333;

        &:focus {
            outline: none;
        }

        .right-menu-item {
            display: inline-block;
            padding: 0 7px;
            vertical-align: text-bottom;

            &.hover-effect {
                padding: 20px 7px;
                cursor: pointer;
                transition: background .3s;

                &:hover {
                    background-color: #f9f9f9;
                }
            }
        }

        .avatar-container {

            .avatar-wrapper {
                height: 58px;
                display: flex;
                display: -webkit-flex;
                align-items: center;
                justify-content: center;
                color: #000011;
                position: relative;
                padding: 0 8px;

                &:hover {
                    cursor: pointer;
                    transition: background .3s;
                    background-color: #f9f9f9;
                }

                .user-avatar {
                    cursor: pointer;
                    width: 35px;
                    height: 35px;
                    border-radius: 50%;

                }

                .el-icon-caret-bottom {
                    cursor: pointer;
                    right: -20px;
                    font-size: 12px;
                }
            }
        }
    }
</style>
