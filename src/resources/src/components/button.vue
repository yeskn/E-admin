<template>
    <el-button @click="clickHandel" :loading="loading"><slot></slot></el-button>
</template>

<script>
    import {defineComponent} from 'vue'
    import {useAjax} from '@/hooks'
    export default defineComponent({
        name: "EadminButton",
        emits: ['gridRefresh'],
        setup(props, ctx) {
            const {loading,http} = useAjax()
            function clickHandel() {
                http(ctx)
            }
            return {
                loading,
                clickHandel
            }
        }
    })
</script>

<style scoped>

</style>
