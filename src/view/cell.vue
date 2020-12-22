<template><span>{$cell|raw}</span></template>
<script>
    export default {
        name:"cell",
        props:{
            data:Object,
            index:Number,
            tableData: Array,
            showEditId:Number,
            showDetailId:Number,
            page:Number,
            total:Number,
            size:Number,
            tableDataUpdate: Boolean,
            width:Number,
            globalRequestParams:[Object,Array]
        },
        mounted() {
            this.$nextTick(()=>{
                if(this.$refs.cellAction && this.$refs.cellAction.offsetWidth > 0){
                    this.$emit('update:width', this.$refs.cellAction.offsetWidth+30)
                }
            })
        },
        data(){
            return {
                form:{
                    switch:'{$switchValue|default="0"}',
                },
                requestUrl:'{$requestUrl}',
            }
        },
        watch: {
            tableDataUpdate(val) {
                this.$emit('update:tableDataUpdate', val)
            },
        },
        methods:{
            //输入框排序
            sortInput(row,field,sort){
                const param = {}
                param[field] = sort
                param.ids = [row.id]
                this.$request({
                    url: this.requestUrl +'/batch.rest',
                    method: 'put',
                    data: param
                })
            },
            //排序置顶
            sortTop(index,data){
                this.$request({
                    url: this.requestUrl +'/batch.rest',
                    params:this.globalRequestParams,
                    method: 'put',
                    data:{
                        action:'buldview_drag_sort',
                        sortable_data:{
                            id:data.id,
                            sort: 0
                        }
                    }
                }).then(res=>{
                    let title
                    if(this.page ==1){
                        const targetRow = this.tableData.splice(index, 1)[0]
                        this.tableData.splice(0, 0, targetRow)
                        title = '置顶完成'
                    }else{
                        this.tableData.splice(index,1)
                        title = '已置顶到第一页'
                    }
                    this.$emit('update:tableData', this.tableData)
                })

            },
            //排序置底
            sortBottom(index,data){
                this.$request({
                    url: this.requestUrl +'/batch.rest',
                    params:this.globalRequestParams,
                    method: 'put',
                    data:{
                        action:'buldview_drag_sort',
                        sortable_data:{
                            id:data.id,
                            sort: this.total-1
                        }
                    }
                }).then(res=>{
                    let title
                    if(this.page == Math.ceil(this.total / this.size)){
                        const targetRow = this.tableData.splice(index, 1)[0]
                        this.tableData.push(targetRow)
                        title = '置底完成'
                    }else{
                        this.tableData.splice(index,1)
                        title = '已置底到最后一页'
                    }
                    this.$emit('update:tableData', this.tableData)
                })
            },
            handleDetail(row,index){
                this.$emit('update:showDetailId', row.id)
            },
            handleEdit(row,index){
                this.$emit('update:showEditId', row.id)
            },
            handleDelete(row, index) {
                this.$confirm('此操作将删除该数据, 是否继续?', '提示', {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    type: 'warning'
                }).then(() => {
                    this.$request({
                        url: this.requestUrl+'/'+row.id+'.rest',
                        method: 'delete',
                    }).then(res=>{
                        this.deleteTreeData(this.tableData,row.id)
                        this.$emit('update:tableData', this.tableData)
                    })
                })
            },
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
            }
        },
    }
</script>

