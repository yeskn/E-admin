<template>
    <div style="font-size: 14px;">
        <el-row :gutter="10">
        <!--{notempty name="title"}-->
            <el-col :span="24">
            <el-card class="box-card" :body-style="{padding: '0px 15px' }">
                {$html|raw}
            </el-card>
            </el-col>
        <!-- {else/}-->
        {$html|raw}
        <!--{/notempty}-->
        {$manyColumnHtml|raw|default=''}
        </el-row>

    </div>
</template>

<script>
    export default {
        name: "detail",
        data(){
            return {
                tableData: [],
                data:{$data|raw},
                cellComponent:{$cellComponent|raw|default='[]'},
                {$scriptVar|raw}
            }
        },
        created() {
            this.cellComponent.forEach((cmponent,index)=>{
                this.cellComponent[index] = () => new Promise(resolve => {
                    resolve(this.$splitCode(cmponent))
                })
            })
        }
    }
</script>

<style scoped>
    .el-col {
        margin-bottom: 10px;
    }
    .el-card__body .el-col{
        margin-bottom: 0px;
    }
    .el-card {
        margin-bottom: 10px;
    }
	img{
        max-width: 100%;
    }
</style>
