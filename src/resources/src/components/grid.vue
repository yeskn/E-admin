<template>
    <!--工具栏-->
    <div class="tools">
        <el-row style="padding-top: 10px">
            <el-col :span="24">
            <div style="float: right;margin-right: 15px">
                <el-button icon="el-icon-refresh" size="mini" circle style="margin-right: 10px"  @click="loading=true"></el-button>
            </div>
            </el-col>
        </el-row>
    </div>
    <!--表格-->
    <el-table v-loading="loading" :data="tableData" v-bind="$attrs">
        <el-table-column v-for="column in columns" v-bind="column">
            <template #header>
                <render :data="column.header"></render>
            </template>
            <template #default="scope">
                <render :data="scope.row[column.prop]"></render>
            </template>
        </el-table-column>
    </el-table>
    <!--分页-->
    <el-pagination @size-change="handleSizeChange" @current-change="handleCurrentChange" v-if="pagination" class="pagination" v-bind="pagination"></el-pagination>
</template>

<script>
    import {defineComponent, ref, watch,reactive} from "vue";
    import render from "/@/components/render.vue"
    import request from '/@/utils/axios'

    export default defineComponent({
        name: "EadminGrid",
        components: {
            render,
        },
        props: {
            data: Array,
            columns: Array,
            pagination: [Object, Boolean],
            loading: Boolean,
            loadDataUrl: String
        },
        inheritAttrs: false,
        setup(props) {
            const loading = ref(false)
            let tableData = ref(props.data)
            let page = 1
            let size = props.pagination.pageSize
            watch(() => props.loading, (value) => {
                loading.value = value
            })
            watch(loading, (value) => {
                if (value) {
                    loadData()
                }
            })
            //分页大小改变
            function handleSizeChange(val) {
                page = 1
                size = val
                loading.value = true

            }

            //分页改变
            function handleCurrentChange(val) {
                page = val
                loading.value = true
            }

            //请求获取数据
            function loadData() {
                let requestParams = {
                    build_request_type: 'page',
                    page: page,
                    size: size,
                }
                request({
                    url: props.loadDataUrl,
                    params: requestParams
                }).then((res) => {
                    tableData.value = res.data
                }).finally(() => {
                    loading.value = false
                })
            }

            return {
                handleSizeChange,
                handleCurrentChange,
                loading,
                tableData
            }
        }
    })
</script>

<style scoped>
    .pagination {
        background: #fff;
        padding: 10px 16px;
        border-radius: 4px;
    }
    .tools {
        background: #fff;
        position: relative;
        border-radius: 4px;
        padding-left: 10px;
        padding-bottom: 10px;
    }
</style>
