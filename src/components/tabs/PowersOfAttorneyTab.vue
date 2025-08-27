<script setup lang="ts">
import { useFormStore } from "@/stores"
import { inject, ref } from "vue"

const formStore = useFormStore()
const form = formStore.form
const formRef = inject("form-ref")
const powersOfAttorney = [
    { label: "Полномочия 1", value: 1 },
    { label: "Полномочия 2", value: 2 },
]
</script>
<template>
    <el-form-item
        label="Включить полномочия в свободной форме?"
        label-position="left"
        required
    >
        <el-checkbox v-model="form.powersOfAttorney.isPowersInFreeForm" />
    </el-form-item>
    <el-form-item
        label="Выбор полномочий из справочника полномочий МЧД ФНС"
        required
        v-show="!form.powersOfAttorney.isPowersInFreeForm"
    >
        <el-select
            v-model="form.powersOfAttorney.powers"
            :options="powersOfAttorney"
            multiple
            clearable
            :empty-values="['']"
            filterable
        />
    </el-form-item>
    <el-form-item
        label="Полномочия в свободной форме"
        required
        v-show="form.powersOfAttorney.isPowersInFreeForm"
    >
        <el-input
            type="textarea"
            v-model="form.powersOfAttorney.powersFreeForm"
        />
    </el-form-item>
</template>
