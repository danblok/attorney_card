<script setup lang="ts">
import { inject, watchEffect } from "vue"
import { Delete, Edit } from "@element-plus/icons-vue"
import { useFormStore } from "@/stores"
import type { PrivateRepresentative } from "@/types"

const formStore = useFormStore()
const form = formStore.form
const formRef = inject("form-ref")

interface Private {
    id: number
    firstName: string
    lastName: string
    secondName: string
    position: string
}

const model = defineModel<PrivateRepresentative>({ required: true })
const emits = defineEmits(["remove"])

const representatives = [
    { label: "First Repr", value: 1 },
    { label: "Second Repr.", value: 2 },
]
</script>
<template>
    <el-card class="card">
        <el-form-item label="Сотрудник Гардия" label-position="right">
            <el-checkbox v-model="model.isGardiaEmployee"></el-checkbox>
        </el-form-item>
        <h3>ФИО представителя по доверенности</h3>
        <el-form-item>
            <el-select
                v-model="model.reprId"
                :options="representatives"
                clearable
                :empty-values="[0]"
                filterable
            ></el-select>
        </el-form-item>
        <el-space class="horizontal-space" direction="horizontal">
            <el-form-item label="Фамилия" required>
                <el-input v-model="model.lastName" />
            </el-form-item>
            <el-form-item label="Имя" required>
                <el-input v-model="model.firstName" />
            </el-form-item>
            <el-form-item label="Отчество" required>
                <el-input v-model="model.secondName" />
            </el-form-item>
        </el-space>
        <el-form-item label="Должность представителя по доверенности" required>
            <el-input v-model="model.position" />
        </el-form-item>
        <el-space direction="horizontal" :size="20">
            <el-form-item label="Серия" required>
                <el-input v-model="model.passportSeries" />
            </el-form-item>
            <el-form-item label="Номер" required>
                <el-input v-model="model.passportNumber" />
            </el-form-item>
        </el-space>
        <el-form-item label="Паспорт выдан" required>
            <el-input type="textarea" v-model="model.passportIssuedBy" />
        </el-form-item>
        <el-space direction="horizontal" :size="20">
            <el-form-item label="Дата выдачи" required>
                <el-date-picker
                    type="date"
                    v-model="model.passportIssuedDate"
                    format="DD.MM.YYYY"
                    value-format="YYYY-MM-DD"
                />
            </el-form-item>
            <el-form-item label="Код подразделения" required>
                <el-input v-model="model.divisionCode" />
            </el-form-item>
        </el-space>
        <el-form-item label="Адрес регистрации">
            <el-input type="textarea" v-model="model.registrationAddress" />
        </el-form-item>
        <el-form-item label="Дополнительные сведения">
            <el-input type="textarea" v-model="model.additionalInfo" />
        </el-form-item>
        <el-space direction="horizontal" :size="20">
            <el-button type="primary" :icon="Edit">Сохранить</el-button>
            <el-button type="danger" :icon="Delete" @click="$emit('remove')" />
        </el-space>
    </el-card>
</template>

<style>
.horizontal-space {
    display: flex;
    justify-content: space-between;
    width: 100%;
}

.gap-space {
    display: flex;
    width: 100%;
    justify-content: flex-start;
}

h3 {
    margin-bottom: 1rem;
}

.card {
    margin-bottom: 1rem;
}
</style>
