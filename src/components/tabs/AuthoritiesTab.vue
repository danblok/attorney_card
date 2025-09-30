<script setup lang="ts">
import { useStore } from "@/stores"

const store = useStore()
const authorities = store.project.authorities
</script>
<template>
    <el-form-item
        label="Включить полномочия в свободной форме?"
        label-position="left"
        required
    >
        <el-checkbox v-model="authorities.isAuthoritiesInFreeForm" />
    </el-form-item>
    <el-form-item
        label="Выбор полномочий из справочника полномочий МЧД ФНС"
        required
        v-show="!authorities.isAuthoritiesInFreeForm"
    >
        <el-select v-model="authorities.values" multiple clearable filterable>
            <el-option
                v-for="item in store.authorities"
                :key="item.id"
                :label="item.value"
                :value="item.id"
            />
        </el-select>
    </el-form-item>
    <el-form-item
        label="Полномочия в свободной форме"
        required
        v-show="authorities.isAuthoritiesInFreeForm"
    >
        <el-input type="textarea" v-model="authorities.freeForm" />
    </el-form-item>
</template>
