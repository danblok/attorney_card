<script setup lang="ts">
import { useFormStore } from "@/stores"

const formStore = useFormStore()
</script>
<template>
    <el-space direction="vertical" style="align-items: stretch" :size="20">
        <el-button type="primary" @click="formStore.save"
            >Сохранить изменения в карточке</el-button
        >
        <el-button type="primary">Скачать</el-button>
        <el-button type="primary">Копировать</el-button>
        <el-button type="primary">Просмотр доверенности</el-button>
        <el-button type="primary">Отменить доверенность</el-button>
        <el-button type="danger">Отправить на согласование</el-button>
        <el-button type="primary">Протокол согласования</el-button>
        <el-button type="primary">Журнал изменений</el-button>
    </el-space>
</template>
