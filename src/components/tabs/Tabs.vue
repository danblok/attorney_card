<template>
    <el-form :model="form" :ref="formRef" label-position="top" class="form">
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
            <el-tab-pane label="Полномочия" name="PowersOfAttorney">
                <powers-of-attorney-tab></powers-of-attorney-tab>
            </el-tab-pane>
            <el-tab-pane label="Функции" name="Actions">
                <actions-tab></actions-tab>
            </el-tab-pane>
        </el-tabs>
    </el-form>
</template>

<script lang="ts" setup>
import { provide, ref, watch, watchEffect } from "vue"

import type { FormInstance } from "element-plus"
import { useFormStore } from "@/stores"

const activeTab = ref("AttorneyRepresentative")
const formRef = ref<FormInstance>()
const formStore = useFormStore()
const form = formStore.form

watchEffect(
    () => {
        console.log("form state:", form)
    },
    { flush: "post" },
)

provide("form-ref", formRef)
</script>

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
