<template>
    <div>
        <el-input size="small" v-model="searchValue" @change="search" placeholder="搜索 按Enter确认" suffix-icon="el-icon-search"></el-input>
    </div>
</template>

<script>
    export default {
        name: "eadminSearch",
        data() {
           return {
               searchValue:''
           }
        },
        created() {
            if(this.$route.query.search){
                this.searchValue = this.$route.query.search
            }
        },
        methods:{
            search(val){
                this.$router.push({
                    path:this.$route.path,
                    query:{
                        search:val
                    }
                })
            }
        }
    }
</script>

<style scoped>

</style>
