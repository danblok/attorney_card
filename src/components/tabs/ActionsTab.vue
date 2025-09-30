<script setup lang="ts">
import { useStore } from "@/stores"

const store = useStore()
</script>
<template>
    <el-space direction="vertical" style="align-items: stretch" :size="20">
        <el-button type="primary" @click="store.save">
            Сохранить изменения в карточке
        </el-button>
        <el-button type="primary">Скачать</el-button>
        <el-button type="primary">Копировать</el-button>
        <el-button type="primary">Просмотр доверенности</el-button>
        <el-button type="primary">Отменить доверенность</el-button>
        <el-button type="danger">Отправить на согласование</el-button>
        <el-button type="primary">Протокол согласования</el-button>
        <el-button
            type="primary"
            tag="a"
            :href="`/crm/type/1112/list/category/28/?UF_CRM_21_1750312821=${store.project.attorneyData.internalNumber}&apply_filter=Y`"
        >
            Журнал изменений
        </el-button>
    </el-space>
</template>
