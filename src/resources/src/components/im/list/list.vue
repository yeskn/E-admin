<template>
    <div class="container">
        <div class="searchBox">
            <el-input class="searchInput" prefix-icon="el-icon-search" v-model="searchKeyWord" clearable
                      placeholder="搜索"></el-input>
        </div>
        <div class="list">
            <recent-list v-show="leftTool == 'message'" :search="searchKeyWord"></recent-list>
            <friend-list v-show="leftTool == 'friend'" :search="searchKeyWord"></friend-list>
        </div>
    </div>
</template>

<script>
    import {defineComponent, reactive, toRefs,watch} from "vue";
    import recentList from './recentList.vue'
    import friendList from './friendList.vue'
    import im from '../websocket/websocket'
    export default defineComponent({
        name: "ImList",
        components: {
            recentList,
            friendList
        },
        setup() {
            const state = reactive({
                //搜索关键字
                searchKeyWord: '',
            })
            return {
                ...toRefs(state),
                ...toRefs(im.state)
            }
        }
    })
</script>

<style scoped>
    .container {
        width: 250px;
        height: 700px;
        display: flex;
        flex-direction: column;
        background: #f9fafb;
        border-right: #f3f4f6 solid 1px;
    }

    .searchBox {
        padding: 5px;
        height: 50px;
        border-bottom: rgba(0, 0, 0, 0.02) solid 1px;
    }

    .list {
        flex: 1;
    }
</style>
