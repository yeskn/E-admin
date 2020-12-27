<template>
    <el-card>
        <el-input size="small" v-model="searchValue" @change="search" placeholder="搜索 按Enter确认" suffix-icon="el-icon-search"></el-input>
        <el-tabs v-model="tabVlue" @tab-click="handleClick">
            <el-tab-pane label="插件列表" name="all"><eadmin-component :url="requestUrl" v-if="this.tabVlue == 'all'"></eadmin-component></el-tab-pane>
            <el-tab-pane label="已安装" name="install"><eadmin-component :url="requestUrl" v-if="this.tabVlue == 'install'"></eadmin-component></el-tab-pane>
        </el-tabs>
    </el-card>
</template>
<script>
    export default {
        name: "",
        data() {
            return {
                searchValue:'',
                tabVlue:'all',
                requestUrl:'/plug/table',
                loading:false,
            }
        },
        methods:{
            handleClick(val){
                this.requestUrl = '/plug/table?type='+this.tabVlue+'&search='+this.searchValue
            },
            search(val){
                this.loading = true
                this.requestUrl = '/plug/table?type='+this.tabVlue+'&search='+val
                this.loading = false
            }
        }
    }
</script>

<style scoped>

</style>
