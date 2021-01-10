<template>
    <!--工具栏-->
    <div class="tools" v-if="!hideTools">
        <el-row style="padding-top: 10px">
            <el-col :span="24">
                <!--快捷搜索-->
                <el-input class="hidden-md-and-down" v-model="quickSearch" clearable prefix-icon="el-icon-search" size="small" style="width: 200px;" placeholder="请输入关键字"></el-input>
                <!--导出-->
                <el-dropdown trigger="click" style="margin-left: 10px;">
                    <el-button type="primary" size="small" icon="el-icon-download">
                        导出<i class="el-icon-arrow-down el-icon--right"></i>
                    </el-button>
                    <template #dropdown>
                        <el-dropdown-menu >
                            <el-dropdown-item @click.native="exportData(1)">导出当前页</el-dropdown-item>
                            <el-dropdown-item @click.native="exportData(2)" v-show="selectionData.length > 0">导出选中行</el-dropdown-item>
                            <el-dropdown-item @click.native="exportData(0)">导出全部</el-dropdown-item>
                        </el-dropdown-menu>
                    </template>
                </el-dropdown>
                <!--筛选-->
                <render v-if="filter" :data="filter"></render>
                <!--刷新-->
                <div style="float: right;margin-right: 15px">
                    <el-button icon="el-icon-refresh" size="mini" circle style="margin-right: 10px"  @click="loading=true"></el-button>
                </div>
            </el-col>
        </el-row>
    </div>
    <!--表格-->
    <el-table @selection-change="handleSelect" v-loading="loading" :data="tableData" v-bind="$attrs">
        <el-table-column v-for="column in columns" v-bind="column">
                <template #header>
                    <render :data="column.header"></render>
                </template>
                <template v-if="!column.type" #default="scope">
                    <render :data="scope.row[column.prop]"></render>
                </template>
        </el-table-column>
    </el-table>
    <!--分页-->
    <el-pagination @size-change="handleSizeChange" @current-change="handleCurrentChange" v-if="pagination" class="pagination" v-bind="pagination" :total="total"></el-pagination>
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
            modelValue: Boolean,
            loadDataUrl: String,
            hideTools:Boolean,
            selection:Boolean,
            filter:[Object, Boolean],
            filterForm:Object
        },
        inheritAttrs: false,
        emits:['update:modelValue'],
        setup(props,ctx) {
            const loading = ref(false)
            const quickSearch = ref('')
            const selectionData = ref([])
            let tableData = ref(props.data)
            let page = 1
            let size = props.pagination.pageSize
            let total = ref(props.pagination.total || 0)
            watch(() => props.modelValue, (value) => {
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
            //当用户手动勾选数据行的 Checkbox 时触发的事件
            function handleSelect(selection){
                selectionData.value = selection
            }
            //请求获取数据
            function loadData() {
                let requestParams = {
                    build_request_type: 'page',
                    page: page,
                    size: size,
                }
                requestParams = Object.assign(requestParams,props.filterForm)
                request({
                    url: props.loadDataUrl,
                    params: requestParams
                }).then((res) => {
                    tableData.value = res.data
                    total.value= res.total
                }).finally(() => {
                    loading.value = false
                    ctx.emit('update:modelValue',false)
                })
            }

            return {
                total,
                handleSizeChange,
                handleCurrentChange,
                loading,
                tableData,
                quickSearch,
                selectionData,
                handleSelect
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
