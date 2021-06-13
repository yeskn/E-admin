<template>
    <a-dropdown>
        <slot></slot>
        <template #overlay>
            <slot name="dropdown"></slot>
        </template>
    </a-dropdown>
    <slot name="reference"></slot>
</template>

<script>
    export default {
        name: "EadminDropdown"
    }
</script>

<style scoped>

</style>
