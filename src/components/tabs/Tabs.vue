<script lang="ts" setup>
import { ref, watchEffect } from "vue"

import { useStore } from "@/stores"

const store = useStore()
const activeTab = ref("AttorneyRepresentative")
const form = store.project

watchEffect(
    () => {
        console.log("form state:", form)
    },
    { flush: "post" },
)
</script>

<template>
    <el-form :model="form" label-position="top" class="form">
        <el-tabs v-model="activeTab" type="border-card" class="tabs">
            <el-tab-pane
                label="Данные представителя по доверенности"
                name="AttorneyRepresentative"
            >
                <attorney-representative-tab></attorney-representative-tab>
            </el-tab-pane>
            <el-tab-pane label="Данные доверенности" name="AttorneyData">
                <attorney-data-tab></attorney-data-tab>
            </el-tab-pane>
            <el-tab-pane label="Полномочия" name="Authorities">
                <authorities-tab></authorities-tab>
            </el-tab-pane>
            <el-tab-pane label="Функции" name="Actions">
                <actions-tab></actions-tab>
            </el-tab-pane>
        </el-tabs>
    </el-form>
</template>

<style>
.form {
    width: 100%;
}
.el-tabs__item.is-active {
    background: var(--el-color-primary) !important;
    color: white !important;
}

.el-tabs__nav {
    border-bottom: 4px solid var(--el-color-primary);
}

.el-tabs__nav-scroll {
    background: white;
}
</style>
