<script setup lang="ts">
import { useFormStore } from "@/stores"
import { inject } from "vue"

const formStore = useFormStore()
const form = formStore.form
const formRef = inject("form-ref")

const representatives = [
    { label: "First Repr", value: 1 },
    { label: "Second Repr.", value: 2 },
]
</script>
<template>
    <el-card>
        <el-form-item label="Полное наименование организации" required>
            <el-input v-model="form.legalRepresentative.fullName" />
        </el-form-item>
        <el-form-item label="Краткое наименование организации" required>
            <el-input v-model="form.legalRepresentative.shortName" />
        </el-form-item>
        <el-space direction="horizontal" :size="20">
            <el-form-item label="ОГРН" required>
                <el-input v-model="form.legalRepresentative.ogrn" />
            </el-form-item>
            <el-form-item label="ИНН" required>
                <el-input v-model="form.legalRepresentative.inn" />
            </el-form-item>
        </el-space>
        <el-form-item label="Юридический адрес организации" required>
            <el-input v-model="form.legalRepresentative.legalAddress" />
        </el-form-item>
        <h3>ФИО представителя по доверенности</h3>
        <el-form-item>
            <el-select
                v-model="form.legalRepresentative.id"
                :options="representatives"
                clearable
                :empty-values="[0]"
                filterable
            ></el-select>
        </el-form-item>
        <el-space class="horizontal-space" direction="horizontal">
            <el-form-item label="Фамилия">
                <el-input v-model="form.legalRepresentative.reprLastName" />
            </el-form-item>
            <el-form-item label="Имя">
                <el-input v-model="form.legalRepresentative.reprFirstName" />
            </el-form-item>
            <el-form-item label="Отчество">
                <el-input v-model="form.legalRepresentative.reprSecondName" />
            </el-form-item>
        </el-space>
        <el-form-item label="Должность представителя организации">
            <el-input v-model="form.legalRepresentative.reprPosition" />
        </el-form-item>
        <el-form-item
            label="Документ-основание полномочий представителя
        организации"
        >
            <el-input v-model="form.legalRepresentative.reprPosition" />
        </el-form-item>
    </el-card>
</template>
