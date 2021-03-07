<template>
    <el-select v-model="specGroup">
        <el-option v-for="item in group" :key="item.id"  :value="item.id">{{item.name}}</el-option>
    </el-select>
    <el-table :data="specs" border size="mini">
        <el-table-column
                prop="name"
                label="可选规格">
        </el-table-column>
        <el-table-column
                label="规格内容">
            <template #default="scope">
                <el-checkbox-group v-model="scope.row.selected">
                    <el-checkbox v-for="item in scope.row.spec" :label="item"></el-checkbox>
                </el-checkbox-group>
            </template>
        </el-table-column>
    </el-table>
    <el-table :data="specData">
        <el-table-column
                prop="name"
                label="商品规格">
        </el-table-column>
        <el-table-column
                prop="value"
                label="市场价">
        </el-table-column>
    </el-table>
<!--    <eadmin-many-item v-model="group" :many-data="many">-->
<!--        <template #default="{row}">-->
<!--            <el-row>-->
<!--                <el-col :span="6">-->
<!--                    分组-->
<!--                    <el-select v-model="row.id">-->
<!--                        <el-option></el-option>-->
<!--                    </el-select>-->
<!--                </el-col>-->
<!--                <el-col :span="6">-->
<!--                    分组-->
<!--                    <el-select v-model="row.id">-->
<!--                        <el-option></el-option>-->
<!--                    </el-select>-->
<!--                </el-col>-->
<!--            </el-row>-->
<!--        </template>-->
<!--    </eadmin-many-item>-->
</template>

<script>
    import {defineComponent,reactive,toRefs,computed} from "vue";
    import {findTree} from "@/utils";
    export default defineComponent({
        name: "EadminSpec",
        inheritAttrs:false,
        setup(props){
            const state =reactive({
                group:[
                    {
                        id:1,
                        name:'衣服',
                        specs:[
                            {
                                id:2,
                                name:'颜色',
                                spec:['红色','绿色']
                            },
                            {
                                id:3,
                                name:'尺寸',
                                spec:['L','M','S']
                            },
                            {
                                id:3,
                                name:'类型',
                                spec:['正常','超大','迷你']
                            },
                            {
                                id:5,
                                name:'评价',
                                spec:['好吧','很好','嗯嗯']
                            }
                        ]
                    },
                ],
                specGroup:'',
                selectSpec:[]
            })
            //规格分组
            const specs = computed(()=>{
                const spec = findTree(state.group,state.specGroup,'id')
                if(spec){
                    state.selectSpec =  spec.specs.map(item=>{
                        item.selected = []
                        return item
                    })
                    return state.selectSpec
                }else{
                    return []
                }
            })

            //已选择规格
            const specData = computed(()=>{
                let data = []
                let num = 0
                let selectedArr = []
                state.selectSpec.forEach(item=>{
                    let arr = []
                    item.selected.forEach(selected=>{
                        arr.push(selected)
                    })
                    selectedArr.push(arr)
                })
                if(selectedArr.length > 0){
                    data = specParse(selectedArr,data)
                    if(data.length > 0){
                        data = data.shift()
                    }

                }
                data =  data.map(item=>{
                    return {
                        name:item,
                        value:item,
                    }
                })
                return data
            })
            function specParse(arr1,arr3) {

                if(arr1[0] && arr1[0].length === 0){
                    arr1.shift()
                    if(arr1.length > 1) {
                        return specParse(arr1,arr3)
                    }else if(arr1.length == 1){
                        return arr1
                    }
                    return arr3
                }
                arr1[0].forEach(item1 => {
                    if(arr1[1].length > 0){
                        arr1[1].forEach(item2 => {
                            arr3.push(`${item1} - ${item2}`)
                        });
                    }else{
                        arr3.push(item1)
                    }
                });
                arr1 = arr1.slice(2)
                arr1.unshift(arr3)
                arr3 = []
                if(arr1.length > 1) {
                    return specParse(arr1,arr3)
                }else{
                   return arr1
                }
            }
            return {
                specData,
                specs,
                ...toRefs(state)
            }
        }
    })
</script>

<style scoped>

</style>
