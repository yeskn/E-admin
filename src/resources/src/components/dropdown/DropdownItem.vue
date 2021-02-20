<template>
    <el-dropdown-item @click.native="clickHandel"><slot></slot></el-dropdown-item>
</template>

<script>
    import {defineComponent} from 'vue'
    import {useAjax} from '@/hooks'
    export default defineComponent({
        name: "EadminDropdownItem",
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
