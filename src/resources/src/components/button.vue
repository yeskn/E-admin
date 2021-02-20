<template>
    <el-button @click="clickHandel"><slot></slot></el-button>
</template>

<script>
    import {defineComponent} from 'vue'
    import {useAjax} from '@/hooks'
    export default defineComponent({
        name: "EadminButton",
        emits: ['gridRefresh'],
        setup(props, ctx) {
            function clickHandel() {
                useAjax(ctx)
            }
            return {
                clickHandel
            }
        }
    })
</script>

<style scoped>

</style>
