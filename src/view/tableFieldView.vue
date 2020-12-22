<template>
    <el-dropdown @command="handleCommand" trigger="click" @visible-change="loadData" :hide-on-click="false">
        <el-button icon="el-icon-s-grid" size="mini"></el-button>
        <el-dropdown-menu slot="dropdown" >
            <div class="tools">
                <el-input v-model="searchText"  placeholder="请输入关键字搜索" style="width: 150px"></el-input>
                <el-popover
                        placement="left-start"
                        trigger="manual"
                        v-model="popoverVisible">
                    <el-form label-width="50px" :model="form" ref="tableViewForm">
                        <el-form-item  prop="fields" label="字段" :rules="[ { required: true, message: '请选择字段', trigger: 'blur' }]">
                            <el-card shadow="never" :body-style="{ padding: '10px',maxHeight:'350px',overflow:'auto' }">
                                <div style="border-bottom: 1px solid #ededed"><el-checkbox v-model="checkedAll">全选</el-checkbox></div>
                                <el-checkbox-group v-model="form.fields">
                                    <div v-for="(item,index) in checkOptions" class="flexFields">
                                        <el-checkbox :label="item.field + item.label">{{item.label}}</el-checkbox>
                                        <div style="display: flex;align-items: center"><i class="el-icon-bottom" @click="moveDown(checkboxFields,index)"></i><i class="el-icon-top"  @click="moveUp(checkboxFields,index)"></i></div>
                                    </div>
                                </el-checkbox-group>
                            </el-card>
                        </el-form-item>
                        <el-form-item prop="name" label="名称" :rules="[ { required: true, message: '请输入视图名称', trigger: 'blur' }]">
                            <el-input v-model="form.name" placeholder="请输入视图名称" ></el-input>
                        </el-form-item>
                        <el-form-item>
                            <el-button type="primary" @click="submitForm('tableViewForm')">保存</el-button>
                            <el-button @click="popoverVisible=false">取消</el-button>
                        </el-form-item>
                    </el-form>
                    <i class="el-icon-plus" style="margin-left:20px;font-size: 18px;cursor: pointer" slot="reference" @click="add"></i>
                </el-popover>
            </div>
            <div :style="{ maxHeight:'300px',overflow:'auto' }">
                <div v-if="gridList.length > 0">
                    <el-dropdown-item divided :command="''"><span :class="[select == -1?'defaultColor':'']">默认</span></el-dropdown-item>
                    <el-dropdown-item divided v-for="(item,index) in gridData" :command="item">
                        <div class="flex"><span v-if="select == item.id" class="defaultColor">{{item.name}}</span><span v-else>{{item.name}}</span>
                            <div>
                                <i class="el-icon-edit" @click.stop="edit(item.id)"></i>
                                <el-popconfirm title="确定删除吗？" @onConfirm="del(item.id,index)"><i class="el-icon-delete" slot="reference" @click.stop=""></i></el-popconfirm>
                            </div>
                        </div>
                    </el-dropdown-item>
                </div>
                <el-dropdown-item divided :command="''" v-else class="defaultColor">默认</el-dropdown-item>
            </div>
        </el-dropdown-menu>
    </el-dropdown>
</template>

<script>
    export default {
        props: {
            fields: Array,
        },
        inject:['reload'],
        data() {
            return {
                searchText: '',
                form: {
                    fields:[],
                },
                popoverVisible:false,
                checkboxFields:[],
                checkedAll:false,
                gridList:[],
                select:-1,
            }
        },

        computed:{
            gridData(){
                return this.gridList.filter(item=>{
                    return item.name.indexOf(this.searchText) >= 0
                })
            },
            checkOptions(){
                if(this.form.all_fields){
                    this.checkboxFields = this.form.all_fields
                }
                return this.checkboxFields
            }
        },
        watch:{
            checkedAll(val){
                this.form.fields = []
                if(val){
                    this.checkboxFields.forEach(item=>{
                        this.form.fields.push(item.field + item.label)
                    })
                }
            }
        },
        methods:{
            handleCommand(command) {
                this.$request({
                    url:'/eadmin/tableView/select',
                    method:'post',
                    data:{
                        grid:this.grid(),
                        data:command
                    }
                }).then(res=>{
                    this.reload()
                })
            },
            add(){
                this.form =  {
                    fields:[],
                }
                this.popoverVisible=true
                this.checkboxFields = this.fields.filter(item=>{
                    if(item.label){
                        return true
                    }else{
                        return false
                    }
                })
            },
            //编辑
            edit(id){
                this.popoverVisible = true
                this.$request({
                    url:'/eadmin/tableView/'+id,
                }).then(res=>{
                    this.form = res.data
                    if(this.form.fields.length == this.checkboxFields.length){
                        this.checkedAll = true
                    }else{
                        this.checkedAll = false
                    }
                })
            },
            grid(){
                if(this.$route.meta.params.length > 0){
                    return this.$route.path + JSON.stringify(this.$route.meta.params)
                }else{
                    return this.$route.path
                }
            },
            //删除
            del(id,index){
                this.$request({
                    url:'/eadmin/tableView',
                    method:'delete',
                    data:{
                        id:id
                    }
                }).then(res=>{
                    this.gridList.splice(index,1)
                    this.$notify({
                        title: '删除完成',
                        message: res.message,
                        type: 'success',
                        duration: 1500
                    })
                })
            },
            //加载列表
            loadData(bool){
                if(bool){
                    this.$request({
                        url:'/eadmin/tableView',
                        params:{
                            grid:this.grid()
                        }
                    }).then(res=>{
                        this.gridList = res.data.list
                        this.select = res.data.select
                    })
                }
            },
            //提交
            submitForm(formName){
                this.$refs[formName].validate((valid) => {
                    if (valid) {
                        this.form.all_fields = this.checkboxFields
                        let all_fields = this.form.all_fields.map(item=>{
                            return item.field + item.label
                        })
                        this.form.fields.sort((a,b)=>{
                            return all_fields.indexOf(a) - all_fields.indexOf(b)
                        })
                        this.form.grid = this.grid()
                        this.$request({
                            url:'/eadmin/tableView',
                            method:'post',
                            data:this.form
                        }).then(res=>{
                            this.popoverVisible = false
                            this.$notify({
                                title: '操作完成',
                                message: res.message,
                                type: 'success',
                                duration: 1500
                            })
                            this.reload()
                            this.form = {
                                fields:[],
                            }
                        })
                    }
                });
            },
            swapArray(arr, index1, index2) {
                arr[index1] = arr.splice(index2, 1, arr[index1])[0];
                return arr;
            },
            // 上移 将当前数组index索引与后面一个元素互换位置，向数组后面移动一位
            moveUp(arr, index) {
                if(index - 1 >= 0) {
                    this.swapArray(arr, index, index - 1);
                }
            },
            // 下移 将当前数组index索引与前面一个元素互换位置，向数组前面移动一位
            moveDown(arr, index) {
                if(arr.length > index + 1){
                    this.swapArray(arr, index, index + 1);
                }
            },
        }
    }
</script>

<style scoped>
    .flexFields {
        display: flex;
        align-items: center;
        justify-content: space-between
    }

    .flexFields i {
        margin-left: 5px;
        font-size: 16px;
        display: none;
    }

    .flexFields:hover i{
        display:block;
        cursor: pointer;

    }
    .flex {
        display: flex;
        align-items: center;
        justify-content: space-between
    }

    .flex i {
        margin-left: 5px;
        font-size: 16px;
    }

    .tools {
        list-style: none;
        line-height: 36px;
        padding: 0 20px;
        margin-bottom: 10px;
        font-size: 14px;
        color: #606266;
        outline: none;
        display: flex;
        align-items: center;
        justify-content: space-between
    }
    .defaultColor{
        color: #409EFF;
    }
</style>
