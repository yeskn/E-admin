<template>
    <div>
        <!--工具栏-->
        <div class="tools" v-if="!hideTools">
            <el-row style="padding-top: 10px">
                <el-col :span="24">
                    <!--快捷搜索-->
                    <el-input class="hidden-md-and-down" v-model="quickSearch" clearable prefix-icon="el-icon-search"
                              size="small" style="margin-right: 10px;width: 200px;" placeholder="请输入关键字" @change="handleFilter" v-if="quickSearchOn"></el-input>
                    <el-button class="hidden-md-and-down" type="primary" size="small" icon="el-icon-search" @click="handleFilter" v-if="quickSearchOn">搜索</el-button>
                    <!--添加-->
                    <render v-if="addButton" :data="addButton"></render>
                    <!--导出-->
                    <el-dropdown trigger="click" style="margin-left: 10px;">
                        <el-button type="primary" size="small" icon="el-icon-download">
                            导出<i class="el-icon-arrow-down el-icon--right"></i>
                        </el-button>
                        <template #dropdown>
                            <el-dropdown-menu>
                                <el-dropdown-item @click.native="exportData(1)">导出当前页</el-dropdown-item>
                                <el-dropdown-item @click.native="exportData(2)" v-show="selectionData.length > 0">导出选中行
                                </el-dropdown-item>
                                <el-dropdown-item @click.native="exportData(0)">导出全部</el-dropdown-item>
                            </el-dropdown-menu>
                        </template>
                    </el-dropdown>
                    <!--筛选-->
                    <render v-if="filter" :data="filter"></render>

                    <div style="float: right;margin-right: 15px">
                        <!--刷新-->
                        <el-button icon="el-icon-refresh" size="mini" circle style="margin-right: 10px"
                                   @click="loading=true"></el-button>
                        <!--列过滤器-->
                        <el-dropdown trigger="click" :hide-on-click="false">
                            <el-button icon="el-icon-s-grid" size="mini"></el-button>
                            <template #dropdown>
                                <el-dropdown-menu>
                                    <el-checkbox-group v-model="checkboxColumn">
                                        <el-dropdown-item v-for="item in columns">
                                            <el-checkbox :label="item.prop" v-if="item.label">{{item.label}}</el-checkbox>
                                        </el-dropdown-item>
                                    </el-checkbox-group>
                                </el-dropdown-menu>
                            </template>
                        </el-dropdown>
                    </div>

                </el-col>
            </el-row>
        </div>
        <!--表格-->
        <el-table @selection-change="handleSelect" v-loading="loading" :data="tableData" v-bind="$attrs">
            <template v-for="column in columns">
                <el-table-column v-if="checkboxColumn.indexOf(column.prop) > -1" v-bind="column">
                    <template #header>
                        <render :data="column.header" :slot-props="{grid:grid}"></render>
                    </template>
                    <template v-if="!column.type" #default="scope">
                        <render :data="scope.row[column.prop]" :slot-props="{grid:grid}"></render>
                    </template>
                </el-table-column>
            </template>
        </el-table>
        <!--分页-->
        <el-pagination class="pagination"
                       @size-change="handleSizeChange"
                       @current-change="handleCurrentChange"
                       v-if="pagination"
                       v-bind="pagination"
                       :total="total"
                       :page-size="size"
                       :current-page="page">
        </el-pagination>
    </div>
</template>

<script>
    import {defineComponent, ref, watch, inject,reactive,triggerRef} from "vue";
    import render from "/@/components/render.vue"
    import {useHttp} from '/@/hooks'
    import {store} from '/@/store'
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
            hideTools: Boolean,
            filter: [Object, Boolean],
            addButton: [Object, Boolean],
            filterField:String,
            params:Object,
        },
        inheritAttrs: false,
        emits: ['update:modelValue'],
        setup(props, ctx) {
            const state = inject(store)
            const proxyData = state.proxyData
            const grid = ctx.attrs.eadmin_grid
            const {loading,http} = useHttp()
            const quickSearch = ref('')
            const selectionData = ref([])
            const quickSearchOn = ctx.attrs.quickSearch
            let checkboxColumn = ref([])
            props.columns.forEach(item => {
                checkboxColumn.value.push(item.prop)
            })
            let tableData = ref(props.data)
            let page = 1
            let size = props.pagination.pageSize
            let total = ref(props.pagination.total || 0)
            watch(() => props.modelValue, (value) => {
                quickSearch.value = ''
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
            function handleSelect(selection) {
                selectionData.value = selection
            }
            //快捷搜索
            function handleFilter() {
                page = 1
                loading.value = true
                //清空筛选条件
                proxyData[props.filterField] = {}
            }
            //请求获取数据
            function loadData() {
                let requestParams = {
                    ajax_request_data: 'page',
                    page: page,
                    size: size,
                }
                requestParams = Object.assign(requestParams, proxyData[props.filterField],{quickSearch:quickSearch.value},props.params)
                http({
                    url: props.loadDataUrl,
                    params: requestParams
                }).then((res) => {
                    tableData.value = res.data
                    triggerRef(tableData)
                    total.value = res.total
                }).finally(() => {
                    ctx.emit('update:modelValue', false)
                })
            }
            function success() {
console.log(23)
            }
            return {
                success,
                grid,
                quickSearchOn,
                page,
                size,
                total,
                handleFilter,
                checkboxColumn,
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
