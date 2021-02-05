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
                    <render v-if="addButton" :data="addButton" :slot-props="{grid:grid}"></render>
                    <!--导出-->
                    <el-dropdown trigger="click">
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
                    <el-button plain size="small" icon="el-icon-delete" v-if="!hideDeleteSelection && selectionData.length > 0" @click="deleteSelect">删除选中</el-button>
                    <el-button type="danger" size="small" icon="el-icon-delete" v-if="!hideDeleteButton" @click="deleteAll()">清空数据</el-button>
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
                    <el-button v-if="filter" type="primary" size="small" icon="el-icon-zoom-in" @click="visibleFilter">筛选</el-button>
                    <render v-for="tool in tools" :data="tool" :ids="selectIds" :grid-params="params"></render>
                </el-col>
            </el-row>
        </div>
        <!--筛选-->
        <div class="filter" v-if="filter && filterShow">
            <render :data="filter" ></render>
        </div>
        <!--表格-->
        <a-table :columns="columns" :data-source="tableData" :pagination="false">
            <template  #default="{ text }">
                      <render :data="text" :slot-props="{grid:grid}"></render>
            </template>
        </a-table>
<!--        <el-table @selection-change="handleSelect" row-key='id' v-loading="loading" ref='dragTable' :data="tableData" v-bind="$attrs">-->
<!--            <el-table-column width="50" align="center" label="排序" v-if="sortDrag">-->
<!--                <template #default="scope">-->
<!--                    <div style="display: flex;flex-direction: column">-->
<!--                        <el-tooltip  effect="dark" content="置顶" placement="right-start"><i @click="sortTop(scope.$index,scope.row)" class="el-icon-caret-top" style="cursor: pointer"></i></el-tooltip>-->
<!--                        <el-tooltip effect="dark" content="拖动排序" placement="right-start"><i class="el-icon-rank sortHandel" style="font-weight:bold;cursor: grab"></i></el-tooltip>-->
<!--                        <el-tooltip  effect="dark" content="置底" placement="right-start"><i @click="sortBottom(scope.$index,scope.row)" class="el-icon-caret-bottom" style="cursor: pointer"></i></el-tooltip>-->
<!--                    </div>-->
<!--                </template>-->
<!--            </el-table-column>-->
<!--            <template v-for="column in columns">-->
<!--                <el-table-column v-if="checkboxColumn.indexOf(column.prop) > -1" :width="column.prop == 'EadminAction' ? eadminActionWidth:''" v-bind="column">-->
<!--                    <template #header>-->
<!--                        <render :data="column.header" :slot-props="{grid:grid}"></render>-->
<!--                    </template>-->
<!--                    <template v-if="!column.type" #default="scope">-->
<!--                        <render :data="scope.row[column.prop]" :slot-props="{grid:grid}"></render>-->
<!--                    </template>-->
<!--                    <template v-if="column.type === 'sortInput'" #default="scope">-->
<!--                        <el-input v-model="scope.row[column.prop].content.default[0]" @change="sortInput(scope.row.id,scope.row[column.prop].content.default[0])"></el-input>-->
<!--                    </template>-->
<!--                </el-table-column>-->
<!--            </template>-->
<!--        </el-table>-->
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
    import {defineComponent, ref, watch, inject,nextTick,triggerRef,computed} from "vue"
    import render from "/@/components/render.vue"
    import {useHttp} from '/@/hooks'
    import request from '/@/utils/axios'
    import {store} from '/@/store'
    import {ElMessageBox,ElMessage} from 'element-plus'
    import Sortable from 'sortablejs'
    import {useRoute} from 'vue-router'
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
            sortDrag: Boolean,
            sortInput: Boolean,
            tools:[Object,Array],
            hideDeleteButton: Boolean,
            hideDeleteSelection: Boolean,
            filter: [Object, Boolean],
            addButton: [Object, Boolean],
            filterField:String,
            params:Object,

        },
        inheritAttrs: false,
        emits: ['update:modelValue'],
        setup(props, ctx) {
            console.log(props.columns)
            const route = useRoute()
            const state = inject(store)
            const proxyData = state.proxyData
            const dragTable = ref('')
            const grid = ctx.attrs.eadmin_grid
            const {loading,http} = useHttp()
            const filterShow = ref(false)
            const quickSearch = ref('')
            const selectionData = ref([])
            const eadminActionWidth = ref(0)
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
            nextTick(()=>{
                //操作列宽度自适应
                document.getElementsByClassName('EadminAction').forEach(item=>{
                    if(eadminActionWidth.value < item.offsetWidth){
                        eadminActionWidth.value = item.offsetWidth
                    }
                })
                eadminActionWidth.value += 30
                dragSort()
            })
            //拖拽排序
            function dragSort(){
                const el = dragTable.value.$el.querySelectorAll('.el-table__body-wrapper > table > tbody')[0]
                Sortable.create(el, {
                    handle:'.sortHandel',
                    ghostClass: 'sortable-ghost', // Class name for the drop placeholder,
                    onEnd: evt => {
                        var newIndex = evt.newIndex;
                        var oldIndex = evt.oldIndex;
                        var oldItem = tableData.value[oldIndex]
                        var startPage = (page-1) * size
                        const targetRow = tableData.value.splice(evt.oldIndex, 1)[0]
                        tableData.value.splice(evt.newIndex, 0, targetRow)
                        if(evt.newIndex != evt.oldIndex){
                            sortRequest(oldItem.id,startPage +newIndex).catch(()=>{
                                const targetRow = tableData.value.splice(evt.newIndex, 1)[0]
                                tableData.value.splice(evt.oldIndex, 0, targetRow)
                            })
                        }
                    }
                })
            }
            function sortRequest(id,sort) {
                return new Promise((resolve, reject) =>{
                    request({
                        url: 'eadmin/batch.rest',
                        params:Object.assign(props.params,route.query),
                        method: 'put',
                        data:{
                            action:'eadmin_sort',
                            id:id,
                            sort: sort,
                            eadmin_ids:[]
                        }
                    }).then(res=>{
                        resolve(res)
                    }).catch(res=>{
                        reject(res)
                    })
                })

            }
            //排序置顶
            function sortTop(index,data){
                sortRequest(data.id,0).then(res=>{
                    if(page === 1){
                        const targetRow = tableData.value.splice(index, 1)[0]
                        tableData.value.unshift(targetRow)
                    }else{
                        tableData.value.splice(index,1)

                    }
                })

            }
            //排序置底
            function sortBottom(index,data){
                sortRequest(data.id,total.value-1).then(res=>{
                    if(page === 1){
                        const targetRow = tableData.value.splice(index, 1)[0]
                        tableData.value.push(targetRow)
                    }else{
                        tableData.value.splice(index,1)
                    }

                })
            }
            //输入框排序
            function sortInput(id,sort){
                sortRequest(id,sort)
            }
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
            //选中ids
            const selectIds = computed(()=>{
                let ids = []
                selectionData.value.forEach(item=>{
                    ids.push(item.id)
                })
                return ids
            })
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
                requestParams = Object.assign(requestParams, proxyData[props.filterField],{quickSearch:quickSearch.value},props.params,route.query)
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
            //删除全部
            function deleteAll(){
                deleteRequest('此操作将删除清空所有数据, 是否继续?',true)
            }
            //删除选中
            function deleteSelect() {
                deleteRequest('此操作将删除选中数据, 是否继续?',selectIds)
            }
            //删除请求
            function deleteRequest(message,ids) {
                ElMessageBox.confirm(message,'提示',{type: 'warning'}).then(()=>{
                    request({
                        url: props.loadDataUrl.replace('.rest','/delete.rest'),
                        data: Object.assign({ids: ids},props.params),
                        method:'delete',
                    }).then(res=>{
                        loadData()
                    })
                })
            }
            function visibleFilter() {
                filterShow.value = !filterShow.value
            }
            return {
                eadminActionWidth,
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
                handleSelect,
                visibleFilter,
                filterShow,
                deleteSelect,
                deleteAll,
                selectIds,
                dragTable,
                sortTop,
                sortBottom,
                sortInput
            }
        }
    })
</script>

<style scoped>
    .sortable-selecte{
        background-color: #EBEEF5 !important;

    }
    .sortable-ghost{
        opacity: .8;
        color: #fff!important;
        background: #2d8cf0!important;
    }
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
    .filter{
        border-top: 1px solid #ededed;
        background: #fff;
    }
</style>
