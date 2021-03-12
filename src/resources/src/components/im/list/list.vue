<template>
    <div class="container">
        <div class="searchBox">
            <el-input class="searchInput" prefix-icon="el-icon-search" v-model="searchKeyWord" clearable
                      placeholder="搜索"></el-input>
        </div>
        <div class="list">
            <el-scrollbar style="height:100%;">
                <recent-list :list="list" v-if="selectTool === 'mesage'"></recent-list>
                <friend-list :list="list" v-if="selectTool === 'friend'"></friend-list>
            </el-scrollbar>
        </div>
    </div>
</template>

<script>
    import {defineComponent, reactive, toRefs,watch} from "vue";
    import recentList from './recentList.vue'
    import friendList from './friendList.vue'

    export default defineComponent({
        name: "ImList",
        components: {
            recentList,
            friendList
        },
        props: {
            selectTool: String,
            list: Array,
        },
        setup() {
            const state = reactive({
                //搜索关键字
                searchKeyWord: '',
            })
            // watch(()=>props.selectTool,val=>{
            //
            // })
            return {
                ...toRefs(state)
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
