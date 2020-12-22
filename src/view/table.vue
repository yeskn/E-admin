<template>
    <div>

        <!--{notempty name="$filter"}-->

        <el-drawer :with-header="false" size="25%" :append-to-body="true" :visible.sync="filterVisible" :modal="false">
            <div class="filter">

                <el-form label-width="100px" @submit.native.prevent size="small" ref="form" @submit.native.prevent :model="form">
                    <div>
                        <!-- PC端-->
                        <el-button size="small" class="hidden-md-and-down" type="primary" native-type="submit" icon="el-icon-search" :loading="loading" @click="handleFilter(false)">筛选</el-button>
                        <el-button size="small" class="hidden-md-and-down" icon="el-icon-refresh" @click="filterReset" style="box-shadow: 0px 2px 3px #cccccc">重置</el-button>
                        <!-- 移动端-->
                        <el-button size="mini" class="hidden-md-and-up" type="primary" native-type="submit" icon="el-icon-search" :loading="loading" @click="handleFilter(false)">筛选</el-button>
                        <el-button size="mini" class="hidden-md-and-up" icon="el-icon-refresh" @click="filterReset" style="box-shadow: 0px 2px 3px #cccccc">重置</el-button>
                    </div>
                    <el-divider></el-divider>
                    {$filter|raw|default=''}
                </el-form>
            </div>
        </el-drawer>

        <!--{/notempty}-->

        <!--{if isset($grid)}-->
        <div ref="gridHeader">
            <!--{notempty name="$header"}-->
            {$header|raw}
            <!--{/notempty}-->
            <div class="headContainer">
                <!--{if !isset($hideTools)}-->
                <el-row style="padding-top: 10px">
                    <el-col :span="24">
                        <!--{if isset($quickSearch)}-->
                        <!-- PC端-->
                        <el-input class="hidden-md-and-down" v-model="quickSearch" clearable prefix-icon="el-icon-search" size="small" style="width: 200px;" placeholder="请输入关键字"  @change="handleFilter(true)"></el-input>
                        <!-- 移动端-->
                        <el-input class="hidden-md-and-up" v-model="quickSearch" clearable prefix-icon="el-icon-search" size="mini" style="padding-right: 5px;margin-bottom: 5px" placeholder="请输入关键字"  @input="handleFilter(true)"></el-input>
                        <el-button class="hidden-md-and-down" type="primary" size="small" icon="el-icon-search" @click="handleFilter(true)">搜索</el-button>
                        <!--{/if}-->
                        <!--{if !isset($hideAddButton)}-->
                        <!-- PC端-->
                        <el-button class="hidden-md-and-down" type="primary" size="small" icon="el-icon-plus" @click="showDialog('添加',1)">添加</el-button>
                        <!-- 移动端-->
                        <el-button class="hidden-md-and-up" type="primary" size="mini" icon="el-icon-plus" @click="showDialog('添加',1)"></el-button>
                        <!--{/if}-->
                        <!--{if isset($exportOpen)}-->
                        <el-dropdown trigger="click" style="margin-left: 10px;">
                            <!-- PC端-->
                            <el-button class="hidden-md-and-down" type="primary" size="small" icon="el-icon-download">
                                导出<i class="el-icon-arrow-down el-icon--right"></i>
                            </el-button>
                            <!-- 移动端-->
                            <el-button class="hidden-md-and-up" type="primary" size="mini" icon="el-icon-download"></el-button>
                            <el-dropdown-menu slot="dropdown">
                                <el-dropdown-item @click.native="exportData(1)">导出当前页</el-dropdown-item>
                                <el-dropdown-item @click.native="exportData(2)" v-show="this.selectionData.length > 0">导出选中行</el-dropdown-item>
                                <el-dropdown-item @click.native="exportData(0)">导出全部</el-dropdown-item>
                            </el-dropdown-menu>
                        </el-dropdown>
                        <!--{/if}-->

                        <!-- PC端-->
                        <el-button class="hidden-md-and-down" plain size="small" icon="el-icon-circle-check" type="primary" v-show="selectButtonShow && iframeMode && iframeMultiple" @click="confirmSelect">确认选中</el-button>
                        <!-- 移动端-->
                        <el-button class="hidden-md-and-up" plain size="mini" icon="el-icon-circle-check" type="primary" v-show="selectButtonShow && iframeMode && iframeMultiple" @click="confirmSelect">确认选中</el-button>
                        <!-- PC端-->
                        <el-button class="hidden-md-and-down" plain type="primary" size="small" icon="el-icon-zoom-in" v-show="selectButtonShow && deleteColumnShow" @click="recoverySelect()">恢复选中</el-button>
                        <!-- 移动端-->
                        <el-button class="hidden-md-and-up" plain type="primary" size="mini" icon="el-icon-zoom-in" v-show="selectButtonShow && deleteColumnShow" @click="recoverySelect()"></el-button>

                        <!--{if !isset($hideDeletesButton)}-->
                        <!-- PC端-->
                        <el-button class="hidden-md-and-down" plain size="small" icon="el-icon-delete" v-show="selectButtonShow" @click="DeleteSelect">删除选中</el-button>
                        <el-button class="hidden-md-and-down" type="danger" size="small" icon="el-icon-delete" @click="deleteAll()">{{deleteButtonText}}</el-button>
                        <!-- 移动端-->
                        <el-button class="hidden-md-and-up" plain size="mini" icon="el-icon-delete" v-show="selectButtonShow" @click="DeleteSelect"></el-button>
                        <el-button class="hidden-md-and-up" type="danger" size="mini" icon="el-icon-delete" @click="deleteAll()"></el-button>
                        <!--{/if}-->
                        <!--{notempty name="$filter"}-->
                        <template v-if="filterMode == 'filter'">
                            <!-- PC端-->
                            <el-button class="hidden-md-and-down" size="small"  type="primary" icon="el-icon-zoom-in"  @click="filterVisible=true">高级筛选</el-button>
                            <!-- 移动端-->
                            <el-button class="hidden-md-and-up" size="mini"  type="primary" icon="el-icon-zoom-in"  @click="filterVisible=true"></el-button>
                        </template>
                        <!--{/notempty}-->
                        <!--{if isset($toolbar)}-->
                        {$toolbar|raw}
                        <!--{/if}-->

                        <div style="float: right;margin-right: 15px">
                            <el-button icon="el-icon-refresh" size="mini" circle style="margin-right: 10px"  @click="requestPageData"></el-button>
                            {if isset($onTableView)}
                            {$tableFieldView|raw}
                            {else/}
                            <el-dropdown trigger="click" :hide-on-click="false">
                                <el-button icon="el-icon-s-grid" size="mini"></el-button>
                                <el-dropdown-menu slot="dropdown">
                                    <el-checkbox-group v-model="checkboxColumn">
                                        <el-dropdown-item v-for="(item,index) in checkboxOptions">
                                            <el-checkbox  :label="item.field" v-if="item.label">{{item.label}}</el-checkbox>
                                        </el-dropdown-item>
                                    </el-checkbox-group>
                                </el-dropdown-menu>
                            </el-dropdown>
                            {/if}
                        </div>
                        <div>
                            <el-tag size="small" closable type="info" style="margin-top: 5px;margin-right: 5px" v-for="item in filterTags" @close="removeFilter(item.field)">{{item.label}}</el-tag>
                        </div>
                    </el-col>
                </el-row>
                <!--{/if}-->
            </div>
        </div>
        <!--{/if}-->
        <!--{if isset($trashed) && $trashed===true}-->
        <el-tabs v-model="activeTabsName" class="container" @tab-click="handleTabsClick"  ref="tabs">
            <el-tab-pane label="{$title|default='数据列表'}" name="data">
                {$tableHtml|raw}
            </el-tab-pane>
            <el-tab-pane label="回收站" name="trashed">
                {$tableHtml|raw}
            </el-tab-pane>
        </el-tabs>
        <!--{else/}-->
        <div style="position: relative;z-index: 10;flex: 1;">
            {$tableHtml|raw}
        </div>
        <!--{/if}-->
        <!--{if isset($grid)}-->
        <el-pagination class="hidden-md-and-down" style=" background: #fff; padding: 10px 16px;border-radius: 4px;"
                       @size-change="handleSizeChange"
                       @current-change="handleCurrentChange"
                       :page-sizes="pagesize"
                       :page-size="size"
                       :current-page="page"
                       background
                       :total="total"
                       :layout="pageHide ? 'total':'total, sizes, prev, pager, next, jumper'">
        </el-pagination>
        <el-pagination class="hidden-md-and-up" style=" background: #fff; padding: 10px 16px;border-radius: 4px;"
                       small
                       @size-change="handleSizeChange"
                       @current-change="handleCurrentChange"
                       :page-sizes="pagesize"
                       :page-size="size"
                       :current-page="page"
                       :total="total"
                       :layout="pageHide ? 'total':'total, sizes, prev, pager, next'">
        </el-pagination>
        <!--{/if}-->
        {$dialog|raw|default=''}
    </div>
</template>
<script>

    export default {
        props:{
            iframeVisible:Boolean,
            iframeMode: {
                type: Boolean,
                default: false
            },
            iframeSelect:{
                type: [Array,String,Number,Object],
            },
            iframeMultiple:{
                type: Boolean,
                default: false
            },
            tableMaxHeight: {
                type: Number,
                default: 0
            },
        },
        data(){
            return {
                filterMode:'filter',
                quickSearch: '',
                form:{},
                filterTags:[],
                filterTmpTags:[],
                filterVisible:false,
                sortableParams:{},
                sortable:null,
                deleteButtonText:'清空数据',
                deleteColumnShow:false,
                inputEditRow:null,
                showDetailId:0,
                dialogVisible:false,
                tableDataUpdate:false,
                isDialog :false,
                newFormWindow :false,
                selectButtonShow:false,
                loading:false,
                plugDialog:null,
                inputEditField :'',
                inputEditId :0,
                showEditId :0,
                actionWidth:1,
                globalRequestParams:{},
                tableHeight: window.innerHeight ,
                activeTabsName:'data',
                cellComponent:{$cellComponent|raw|default='[]'},
                checkboxOptions:{$checkboxOptions|raw|default='[]'},
                checkboxColumn:{$checkboxColumn|raw|default='[]'},
                pageHide:{$pageHide|default='true'},
                page:1,
                pagesize:[],
                total:{$pageTotal|default=0},
                size:{$pageSize|default=20},
                selectionData:[],
                tableData:{$tableDataScriptVar|raw},
                {$tableScriptVar|raw}
        }
        },
        computed:{
            device() {
                return this.$store.state.app.device
            },
            actionFixed(){
                if(this.$store.state.app.device == 'mobile'){
                    return false;
                }else{
                    return 'right'
                }
            }
        },
        mounted() {
            this.$nextTick(()=>{
                if(this.tableMaxHeight > 0){
                    this.tableHeight = this.tableMaxHeight
                }else if(this.iframeMode){
                    this.tableHeight = window.innerHeight / 2
                }else{
                    let offsetTop = this.$el.parentElement.parentElement.parentElement.offsetTop + this.$el.parentElement.parentElement.offsetTop

                    if(this.$refs.gridHeader){
                        offsetTop += this.$refs.gridHeader.clientHeight
                    }
                    this.tableHeight = window.innerHeight -  offsetTop
                    if(this.$refs.tabs){
                        this.tableHeight -=  75
                    }
                    this.tableHeight -= 65
                }
            })
        },
        created(){
            /*{if isset($filterMode)}*/
            this.filterMode = '{$filterMode}'
            /*{/if}*/
            /*{if isset($dialogVar)}*/
            this.isDialog = true
            /*{/if}*/
            /*{if isset($newFormWindow)}*/
            this.newFormWindow = true
            /*{/if}*/
            let i = 10
            if(this.size < 10){
                i=this.size
            }
            for(i;i<=200;i+=10){
                this.pagesize.push(i)
            }
            if(this.$route.query.page != undefined){
                this.page = this.$route.query.page
            }
            if(this.$route.query.size != undefined){
                this.size = this.$route.query.size
            }
            this.cellComponent.forEach((cmponent,index)=>{
                this.cellComponent[index] = () => new Promise(resolve => {
                    resolve(this.$splitCode(cmponent))
                })
            })
            this.requestParams()
            if(sessionStorage.getItem('deleteColumnShow')){
                this.deleteColumnShow = true
                this.activeTabsName = 'trashed'
                this.requestPageData()
            }
            this.$nextTick(() => {
                this.setSort()
            })
        },
        inject:['reload'],
        watch:{
            deleteColumnShow(val){
                if(val){
                    this.deleteButtonText = '清空回收站'
                }else{
                    this.deleteButtonText = '清空数据'
                }
            },
            tableDataUpdate(val){
                if(val){
                    this.tableDataUpdate = false
                    this.requestPageData()
                }
            },
            dialogVisible(val){
                if(!val){
                    this.showEditId = 0
                    this.showDetailId = 0
                }
            },
            showEditId(val){
                if(val != 0){
                    this.showDialog('编辑',2)
                    this.showEditId = 0
                }
            },
            showDetailId(val){
                if(val != 0){
                    this.showDialog('详情',3)
                    this.showDetailId = 0
                }
            },
        },
        methods: {
            //移除筛选字段
            removeFilter(field){
                if(field.indexOf('__betweens') !== -1){
                    let formField = field.replace('__betweens','')
                    this.form[formField + '__between_start'] = ''
                    this.form[formField + '__between_end'] = ''
                }else{
                    if(this.form[field] instanceof Array){
                        this.form[field] = []
                    }else{
                        this.form[field] = ''
                    }
                }
                this.$delete(this.filterTags,field)
                this.handleFilter(false)
            },
            //列字段筛选
            filterColumnChange(val,field,label,itemType,usingData){
                if(field.indexOf('__between_start') !== -1 || field.indexOf('__between_end') !== -1){
                    field = field.replace('__between_start','')
                    field = field.replace('__between_end','')
                    if(this.form[field+'__between_start'] && this.form[field+'__between_end']){
                        val = this.form[field+'__between_start'] + ' ~ ' + this.form[field+'__between_end']
                        field = field + '__betweens'
                    }else if(!this.form[field+'__between_start'] && !this.form[field+'__between_end']){
                        val = ''
                        field = field + '__betweens'
                    }else{
                        return
                    }
                }
                //列筛选空值移除
                if(!val || (val instanceof Array && val.length  == 0)){
                    if(this.filterMode == 'column') {
                        this.removeFilter(field)
                    }
                    return
                }
                let showValue = val
                if(itemType == 'radio' || itemType == 'select' || itemType == 'checkbox'){
                    if(val instanceof Array){
                        let showValueArr = []
                        val.forEach(item=>{
                            showValueArr.push(usingData[item])
                        })
                        showValue = showValueArr.join(',')
                    }else{
                        showValue = usingData[val]
                    }
                }
                if(this.filterMode == 'filter') {
                    let filterTags = JSON.parse(JSON.stringify(this.filterTags));
                    this.filterTmpTags = Object.assign(filterTags,this.filterTmpTags)
                    this.filterTmpTags[field] = {
                        label:label + ': ' + showValue,
                        field:field
                    }
                }else if(this.filterMode == 'column'){
                    this.filterTags[field] = {
                        label:label + ': ' + showValue,
                        field:field
                    }
                }
                if(this.filterMode == 'column'){
                    this.handleFilter(false)
                }
            },
            // selectionChange(selection){
            //     if(selection.length > 2){
            //         selection.pop()
            //         this.$refs.dragTable.clearSelection()
            //         selection.forEach(row=>{
            //             console.log(row)
            //             this.$refs.dragTable.toggleRowExpansion(selection[0],true)
            //         })
            //     }
            // },
            //行的 className 的回调方法
            tableRowClassName({row, column, rowIndex, columnIndex}){
                row.eadminIndex = rowIndex;
            },
            //导出
            exportData(type){
                if(this.total == 0){
                    this.$message.warning('暂无数据')
                    return false
                }
                let param = ''
                for(field in this.globalRequestParams) {
                    param += '&'+field+'=' +this.globalRequestParams[field]
                }
                if(type == 0){
                    location.href = "{$exportUrl|raw|default=''}&build_request_type=export&export_type=all" + param
                }else if(type == 1){
                    location.href = "{$exportUrl|raw|default=''}&build_request_type=export&export_type=page&page=" + this.page + "&size=" + this.size + param
                }else if(type == 2){
                    let ids  =[]
                    this.selectionData.forEach((item)=>{
                        ids.push(item.id)
                    })
                    location.href = "{$exportUrl|raw|default=''}&build_request_type=export&export_type=select&ids=" + ids.join(',') + param
                }
            },
            //合计
            columnSumHandel(param) {
                const {columns, data} = param;
                const sums = [];
                columns.forEach((column, index) => {
                    data.map(item => {
                        if(item[column.property + 'isTotalRow']){
                            if(sums[index] == undefined){
                                sums[index] = 0
                            }
                            sums[index] += Number(item[column.property])
                        }
                    })
                    if(sums[index]){
                        sums[index] += data[0][column.property + 'totalText']
                    }
                })
                return sums;
            },
            //排序
            sortHandel({ column, prop, order }){
                if(order == null){
                    this.sortableParams = {}
                }else{
                    if(order === 'descending'){
                        order = 'desc'
                    }else if(order === 'ascending'){
                        order = 'asc'
                    }
                    this.sortableParams = {
                        sort_field:prop,
                        sort_by:order
                    }
                }
                this.requestPageData()
            },
            //拖拽排序
            setSort(){
                const el = this.$refs.dragTable.$el.querySelectorAll('.el-table__body-wrapper > table > tbody')[0]
                this.sortable = this.$sortable.create(el, {
                    handle:'.sortHandel',
                    ghostClass: 'sortable-ghost', // Class name for the drop placeholder,
                    onEnd: evt => {
                        var newIndex = evt.newIndex;
                        var oldIndex = evt.oldIndex;
                        var oldItem = this.tableData[oldIndex]
                        var startPage = (this.page-1) * this.size
                        const targetRow = this.tableData.splice(evt.oldIndex, 1)[0]
                        this.tableData.splice(evt.newIndex, 0, targetRow)


                        if(evt.newIndex != evt.oldIndex){
                            this.$request({
                                url: '{$submitUrl|default=""}/batch.rest',
                                params:this.globalRequestParams,
                                method: 'put',
                                data:{
                                    action:'buldview_drag_sort',
                                    sortable_data:{
                                        id:oldItem.id,
                                        sort: startPage +newIndex
                                    }
                                }
                            }).catch(res=>{
                                const targetRow = this.tableData.splice(evt.newIndex, 1)[0]
                                this.tableData.splice(evt.oldIndex, 0, targetRow)
                            })
                        }
                    }
                })
            },
            requestParams(){
                this.globalRequestParams = {}
                /*{if isset($submitParams)}*/
                /*{foreach $submitParams as $key=>$value}*/
                /*{php}if(is_array($value))continue;{/php}*/
                this.globalRequestParams['{$key}'] = '{$value}'
                /*{/foreach}*/
                /*{/if}*/
                this.globalRequestParams = Object.assign(this.globalRequestParams,this.$route.query)
            },
            //重置筛选表单
            filterReset(){
                this.$refs['form'].resetFields();
            },
            clearValidate(formName) {

            },
            //查询过滤
            handleFilter(quick){
                if(quick){
                    this.filterTags = []
                    for(field in this.form){
                        this.form[field] = ''
                    }
                    this.form.quickSearch = this.quickSearch
                }else{
                    this.filterTags = Object.assign({}, this.filterTags,this.filterTmpTags)
                    this.quickSearch = ''
                    this.form.quickSearch = ''
                    for(field in this.form){
                        if(!this.form[field] || (this.form[field] instanceof Array && this.form[field].length  == 0)){
                            this.$delete(this.filterTags,field)
                        }
                    }
                }
                this.page = 1
                this.requestPageData()
            },
            handleTabsClick(tab, event){
                this.page = 1
                if(this.activeTabsName == 'data'){
                    sessionStorage.removeItem('deleteColumnShow')
                    this.deleteColumnShow = false
                }else{
                    sessionStorage.setItem('deleteColumnShow',1)
                    this.deleteColumnShow = true

                }
                this.requestPageData()
            },
            //对话框表单 type=1添加，type=2编辑 ,type=3详情
            showDialog(title,type){
                let url  = '/{$submitUrl|default=""}'
                let params = {}
                /*{if isset($submitParams)}*/
                /*{foreach $submitParams as $key=>$value}*/
                /*{php}if(is_array($value))continue;{/php}*/
                params['{$key}'] = '{$value}'
                /*{/foreach}*/
                /*{/if}*/
                if(type == 1){
                    /*{if isset($addUrl) && $addRest}*/
                    url = '{$addUrl}/create.rest'
                    /*{elseif isset($addUrl)}*/
                    url = '{$addUrl}'
                    /*{else/}*/
                    url += '/create.rest'
                    /*{/if}*/

                    /*{if isset($addButtonParam)}*/
                    /*{foreach $addButtonParam as $key=>$value}*/
                    params['{$key}'] = '{$value}'
                    /*{/foreach}*/
                    /*{/if}*/
                }else if(type == 2){
                    /*{if isset($editUrl) && $editRest}*/
                    url = '{$editUrl}/'+this.showEditId + '/edit.rest'
                    /*{elseif isset($editUrl)}*/
                    url = '{$editUrl}?id='+this.showEditId
                    /*{else/}*/
                    url += '/'+this.showEditId + '/edit.rest'
                    /*{/if}*/
                }else if(type == 3){
                    /*{if isset($detailUrl) && $detailRest}*/
                    url = '{$detailUrl}/'+this.showDetailId + '.rest'
                    /*{elseif isset($detailUrl)}*/
                    url = '{$detailUrl}?id=' + this.showDetailId
                    /*{else/}*/
                    url += '/'+this.showDetailId+'.rest'
                    /*{/if}*/
                }
                if(this.newFormWindow){
                    params.windowUrl = url
                    params.windowTitle = title
                    let routeUrl = this.$router.resolve({
                             path: "/eadmin_window",
                             query: params
                    });
                    var winObj  = window.open(routeUrl.href, "", "fullscreen=yes")
                    winObj.onbeforeunload = (e)=>{
                        this.requestPageData()
                    }
                    return false
                }
                if(this.isDialog){
                    params.build_dialog = true
                    params.eadmin_component = true
                    this.$request({
                        url: url,
                        method: 'get',
                        params: params
                    }).then(response=>{
                        this.{$dialogTitleVar|default='isDialog'} = title
                        let cmponent = response.data
                        this.plugDialog = () => new Promise(resolve => {
                            resolve(this.$splitCode(cmponent))
                        })
                        this.dialogVisible = true
                    })
                }else{
                    this.$router.push({
                        path:url,
                        query:params
                    })
                }
            },
            //确认选中
            confirmSelect(){
                this.$emit('update:iframeSelect', this.selectionData)
                this.$emit('update:iframeVisible', false)

            },
            //当某一行被点击时会触发该事件
            rowClick(row, column, event){
                if(!this.iframeMultiple){
                    this.$emit('update:iframeSelect', row)
                    this.$emit('update:iframeVisible', false)
                }
            },
            rowDblclick(row, column, event){
                /*{if isset($dbclickEdit)}*/
                this.$nextTick(()=>{
                    this.showEditId = row.id
                })
                /*{/if}*/
                /*{if isset($dbclickDetail)}*/
                this.$nextTick(()=> {
                    this.showDetailId = row.id
                })
                /*{/if}*/
            },
            //当某个单元格被点击时会触发该事件
            cellClick(row, column, cell, event){
                this.inputEditRow = row
                this.inputEditField = column.property
                if(this.tableData[row.eadminIndex]){
                    this.$set( this.tableData[row.eadminIndex],'eadmin_edit',true)
                }
                this.$nextTick(()=>{
                    if(this.$refs[column.property+row.eadminIndex]){
                        this.$refs[column.property+row.eadminIndex].focus()
                    }
                })

            },
            //删除选中
            DeleteSelect(){
                let ids  =[]
                this.selectionData.forEach((item)=>{
                    ids.push(item.id)
                })
                this.deleteRequest('此操作将删除选中数据, 是否继续?',ids)
            },
            //恢复选中
            recoverySelect(){
                let ids  =[]
                this.selectionData.forEach((item)=>{
                    ids.push(item.id)
                })
                this.recoveryRequest('此操作将恢复选中数据, 是否继续?',ids)
            },
            //清空全部
            deleteAll(){
                this.deleteRequest('此操作将删除清空所有数据, 是否继续?','true')
            },
            //递归寻找删除ID索引并删除
            deleteTreeData(arr,id){
                for(var i = arr.length ; i > 0 ; i--){
                    if(arr[i-1].id == id){
                        arr.splice(i-1,1);
                    }else{
                        if(arr[i-1].children){
                            this.deleteTreeData(arr[i-1].children,id)
                        }
                    }
                }
            },
            blurInput(){
                this.$set( this.tableData[this.inputEditRow.eadminIndex],'eadmin_edit',false)
            },
            //行内编辑
            editInput(val){
                this.updateRequest(this.inputEditRow.id,this.inputEditField,val)
            },
            //更新请求
            updateRequest(id,field,value){
                let url  ='{$submitUrl|default=""}/batch.rest';
                const param = {}
                param[field] = value
                param.ids = [id]
                this.$request({
                    url: url,
                    method: 'put',
                    data: param
                }).then(response => {
                    if (response.code == 200) {
                        this.$set( this.tableData[this.inputEditRow.eadminIndex],'eadmin_edit',false)
                    } else {
                        this.$set( this.tableData[this.inputEditRow.eadminIndex],'eadmin_edit',true)
                    }
                }).catch(res => {
                    this.$set( this.tableData[this.inputEditRow.eadminIndex],'eadmin_edit',true)
                })
            },
            //恢复数据请求
            recoveryRequest(title,ids){
                this.$confirm(title, '提示', {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    type: 'info'
                }).then(() => {
                    let url  = '{$submitUrl|default=""}'
                    let requestParams = {}
                    /*{if isset($submitParams)}*/
                    /*{foreach $submitParams as $key=>$value}*/
                    /*{php}if(is_array($value))continue;{/php}*/
                    requestParams['{$key}'] = '{$value}'
                    /*{/foreach}*/
                    /*{/if}*/
                    this.$request({
                        url: url +'/batch.rest',
                        method: 'put',
                        data:{
                            ids:ids,
                            delete_time:null,
                        },
                        params:requestParams
                    }).then(res=>{
                        ids.forEach((id)=>{
                            this.deleteTreeData(this.tableData,id)
                        })
                        this.$notify({
                            title: '操作完成',
                            message: '数据恢复成功',
                            type: 'success',
                            duration: 1500
                        })

                    })
                })
            },
            //删除请求
            deleteRequest(title,deleteIds){
                this.$confirm(title, '提示', {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    type: 'warning'
                }).then(() => {
                    let url  = '{$submitUrl|default=""}'
                    let requestParams = {}
                    /*{if isset($submitParams)}*/
                    /*{foreach $submitParams as $key=>$value}*/
                    /*{php}if(is_array($value))continue;{/php}*/
                    requestParams['{$key}'] = '{$value}'
                    /*{/foreach}*/
                    /*{/if}*/
                    this.$request({
                        url: url+'/delete.rest',
                        method: 'delete',
                        data:{
                            ids:deleteIds,
                            trueDelete:this.deleteColumnShow
                        },
                        params:requestParams
                    }).then(res=>{
                        if(deleteIds == 'true'){
                            this.tableData= [];
                        }else{
                            deleteIds.forEach((delId)=>{
                                this.deleteTreeData(this.tableData,delId)
                            })
                        }
                    })
                })
            },
            //当用户手动勾选数据行的 Checkbox 时触发的事件
            handleSelect(selection){
                this.selectionData = selection
                if(selection.length > 0){
                    this.selectButtonShow=true
                }else{
                    this.selectButtonShow=false
                }
            },
            //分页大小改变
            handleSizeChange(val) {
                this.size = val
                this.requestPageData()

            },
            //分页改变
            handleCurrentChange(val) {
                this.page = val
                this.requestPageData()
            },
            requestPageData(){
                this.loading = true
                let url  = '{$submitUrl|default=""}'
                let requestParams = {
                    build_request_type:'page',
                    page:this.page,
                    size:this.size,
                }

                if(this.deleteColumnShow){
                    requestParams = Object.assign(requestParams,this.globalRequestParams,{'is_deleted':true})
                }
                requestParams = Object.assign(requestParams,this.globalRequestParams,this.form,this.sortableParams)
                requestParams.eadmin_component = true
                requestParams.eadmingrid = this.$route.path + JSON.stringify(this.$route.meta.params)
                this.$request({
                    url: url,
                    method: 'get',
                    params:requestParams
                }).then(res=>{
                    this.loading = false
                    this.tableData = res.data.data
                    if(res.data.total != undefined){
                        this.total = res.data.total
                    }
                    res.data.cellComponent.forEach((cmponent,index)=>{
                        this.cellComponent[index] = () => new Promise(resolve => {
                            resolve(this.$splitCode(cmponent))
                        })
                    })
                }).catch(res=>{
                    this.loading = false
                    this.filterVisible = false
                })
            }
        }
    }
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
    .filter {
        background: #fff;
        position: relative;
        padding: 20px 16px 0px;
        border-radius: 4px;
    }

    .headContainer {
        background: #fff;
        position: relative;
        border-radius: 4px;
        padding-left: 10px;
        padding-bottom: 10px;
    }
    .container {
        background: #fff;
        position: relative;
        padding: 10px 16px;
        border-radius: 4px;
        position: relative;
        z-index: 10;
    }
    :focus{
        outline:0;
    }
    .el-tabs__nav-wrap{
        z-index: 0;
    }
    .eadmin_popover_filter{
        height: auto!important;
        padding: 0!important;
    }
    .eadmin_form_box .el-form-item{
        margin-bottom: 0px;!important;
        border-bottom: 1px solid #ebebf0!important;
        padding: 5px 10px;
        width: 100%;

    }
    .eadmin_form_box .el-date-editor--daterange.el-input__inner{
        width: 100%;
    }
</style>
