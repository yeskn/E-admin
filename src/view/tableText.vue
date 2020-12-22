<template>
    <div>
        <el-table :data="tableData" @cell-click="cellClick" :row-class-name="tableRowClassName" @cell-mouse-leave="cellMouseLeave" @cell-mouse-enter="cellMouseEnter">
            <el-table-column
                    v-for="(label,field) in columns"
                    :prop="field"
                    :label="label">
                <template slot-scope="scope">
                    <el-input v-if="scope.$index == focusIndex" :ref="field" v-model='scope.row[field]'  @focus='focusInput(scope.$index)' @blur='blurInput' size='small' />
                    <template v-else><span v-if="scope.row[field] === null || scope.row[field] === '' || !scope.row[field]">--</span><span v-else>{{scope.row[field]}}</span></template>
                </template>
            </el-table-column>
            <el-table-column>
                <template slot-scope="scope">
                    <i class="el-icon-error" style="cursor: pointer;color: red" v-if="scope.$index == hoverIndex"  @click.stop="del(scope.$index)"></i>
                </template>
            </el-table-column>
        </el-table>
        <div style="margin-top: 10px">
            <el-button size="small" @click="add" type="primary">添加</el-button>
        </div>
    </div>
</template>

<script>
    export default {
        name: "",
        props:{
            value:Array,
            columns:[Array,Object],
        },
        data(){
            return {
                inputEditRow:null,
                tableData:[],
                hoverIndex:-1,
                focusIndex:-1,
                blurTimer:null,
            }
        },
        watch:{
            tableData(val){
                this.$emit('input', val)
            }
        },
        mounted() {
            this.tableData = this.value
        },
        methods:{
            //当单元格 hover 进入时会触发该事件
            cellMouseEnter(row, column, cell, event){
                this.hoverIndex = row.eadminIndex
            },
            //当单元格 hover 退出时会触发该事件
            cellMouseLeave(row, column, cell, event){
                this.hoverIndex = -1
            },
            //当某个单元格被点击时会触发该事件
            cellClick(row, column, cell, event){
                this.focusIndex = row.eadminIndex
                this.inputEditRow = row
                this.$set(this.tableData[row.eadminIndex],'eadmin_edit',true)
                this.$nextTick(()=>{
                    if(this.$refs[column.property]){
                        this.$refs[column.property][0].focus()
                    }
                })

            },
            //行的 className 的回调方法
            tableRowClassName({row, column, rowIndex, columnIndex}){
                row.eadminIndex = rowIndex;
            },
            getfirstField(obj){
                for(var key in obj){
                    return key;
                }
            },
            //添加
            add(){
                this.focusIndex = this.tableData.length
                const field = this.getfirstField(this.columns)
                this.inputEditField =  field
                this.inputEditRow = {eadmin_edit:true}
                this.tableData.push(this.inputEditRow)
                this.$nextTick(()=>{
                    if(this.$refs[field]){
                        this.$refs[field][0].focus()
                    }
                })
            },
            //删除
            del(index){
                this.tableData.splice(index,1)
            },
            focusInput(index){
                this.focusIndex = index
                clearTimeout(this.blurTimer)
            },
            blurInput(){
                this.blurTimer = setTimeout(()=>{
                    this.focusIndex = -1
                },300)
            },
        }
    }
</script>

<style scoped>

</style>
