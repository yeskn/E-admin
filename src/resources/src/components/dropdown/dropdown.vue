<template>
    <el-dropdown>
        <slot></slot>
        <template #dropdown>
            <slot name="dropdown"></slot>
        </template>
    </el-dropdown>
    <slot name="reference"></slot>
</template>

<script>
    export default {
        name: "EadminDropdown"
    }
</script>

<style scoped>

</style>