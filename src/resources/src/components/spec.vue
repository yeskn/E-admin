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
                prop="name"
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
                                id:2,
                                name:'尺寸',
                                spec:['L','M','S']
                            }
                        ]
                    },
                ],
                specGroup:'',
                selectSpec:[]
            })
            //规格分组
            const specs = computed(()=>{
                console.log(123)
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
                const data = []
                let num = 0
                state.selectSpec.forEach(item=>{
                    if(num === 0){
                        num = item.selected.length
                    }else{
                        if(item.selected.length > 0){
                            num = item.selected.length * num
                        }

                    }
                })
            })
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
