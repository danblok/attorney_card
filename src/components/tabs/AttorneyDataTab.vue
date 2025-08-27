<script setup lang="ts">
import { inject, ref } from "vue"
import {
    genFileId,
    type UploadInstance,
    type UploadProps,
    type UploadRawFile,
} from "element-plus"
import { useFormStore } from "@/stores"

const formStore = useFormStore()
const form = formStore.form
const formRef = inject("form-ref")

const upload = ref<UploadInstance>()
const handleExceed: UploadProps["onExceed"] = (files) => {
    upload.value!.clearFiles()
    const file = files[0] as UploadRawFile
    file.uid = genFileId()
    upload.value!.handleStart(file)
}

const representatives = [
    { label: "First Repr", value: 1 },
    { label: "Second Repr.", value: 2 },
]

const issueForms = [
    { label: "МЧД", value: 1 },
    { label: "На бумажном носителе", value: 2 },
]

const templates = [
    { label: "Стандартный", value: 1 },
    { label: "Произвольный", value: 2 },
]

const releaseSystems = [
    { label: "Контур.Доверенность", value: 1 },
    { label: "ПК ЦБ РФ", value: 2 },
]

const activityAreas = [{ label: "Страхования и перестрахования", value: 1 }]
</script>

<template>
    <el-space :size="50" direction="horizontal">
        <el-form-item label="Внутренний номер доверенности">
            <el-input v-model="form.attorneyData.internalNumber" disabled />
        </el-form-item>
        <el-form-item label="Внешний номер доверенности">
            <el-input v-model="form.attorneyData.externalNumber" />
        </el-form-item>
    </el-space>
    <el-space :size="20" direction="horizontal">
        <el-form-item label="Статус доверенности">
            <el-input v-model="form.attorneyData.internalNumber" disabled />
        </el-form-item>
        <el-form-item label="Инициатор">
            <el-input v-model="form.attorneyData.externalNumber" />
        </el-form-item>
        <el-form-item label="Подразделение">
            <el-input v-model="form.attorneyData.externalNumber" />
        </el-form-item>
    </el-space>
    <h3>Единоличный исполнительный орган</h3>
    <el-form-item>
        <el-select
            v-model="form.attorneyData.reprId"
            :options="representatives"
            clearable
            :empty-values="[0]"
            filterable
            disabled
        />
    </el-form-item>
    <el-space :size="20" direction="horizontal">
        <el-form-item label="Фамилия">
            <el-input v-model="form.attorneyData.lastName" disabled />
        </el-form-item>
        <el-form-item label="Имя">
            <el-input v-model="form.attorneyData.firstName" disabled />
        </el-form-item>
        <el-form-item label="Отчество">
            <el-input v-model="form.attorneyData.secondName" disabled />
        </el-form-item>
    </el-space>
    <el-form-item label="Должность">
        <el-input v-model="form.attorneyData.position" disabled />
    </el-form-item>
    <el-form-item label="Дата выпуска доверенности">
        <el-date-picker
            type="date"
            v-model="form.attorneyData.issueDate"
            format="DD.MM.YYYY"
            value-format="YYYY-MM-DD"
        />
    </el-form-item>
    <el-space direction="horizontal" :size="50">
        <el-form-item label="Дата начала действия доверенности">
            <el-date-picker
                type="date"
                v-model="form.attorneyData.startDate"
                format="DD.MM.YYYY"
                value-format="YYYY-MM-DD"
            />
        </el-form-item>
        <el-form-item label="Дата окончания действия доверенности">
            <el-date-picker
                type="date"
                v-model="form.attorneyData.endDate"
                format="DD.MM.YYYY"
                value-format="YYYY-MM-DD"
            />
        </el-form-item>
    </el-space>
    <el-form-item label="Нотариальная доверенность" label-position="left">
        <el-checkbox v-model="form.attorneyData.isNotarialPowerOfAttorney" />
    </el-form-item>
    <el-space :size="50" direction="horizontal">
        <el-form-item label="Форма выпуска доверенности" required>
            <el-select
                v-model="form.attorneyData.issueForm"
                :options="issueForms"
                clearable
                :empty-values="[0]"
                filterable
            />
        </el-form-item>
        <el-form-item label="Шаблон доверенности" required>
            <el-select
                v-model="form.attorneyData.template"
                :options="templates"
                clearable
                :empty-values="[0]"
                filterable
            />
        </el-form-item>
    </el-space>
    <el-form-item v-show="form.attorneyData.template > 1">
        <el-upload
            ref="upload"
            action="/ac/api/upload"
            class="upload"
            :limit="1"
            :on-exceed="handleExceed"
            :auto-upload="false"
        >
            <template #trigger>
                <el-button type="primary">Загрузить</el-button>
            </template>
        </el-upload>
    </el-form-item>
    <el-form-item label="Кол-во экземпляров" label-positon="left" required>
        <el-input-number :min="0" v-model="form.attorneyData.copiesCount" />
    </el-form-item>
    <el-form-item
        label="Система выпуска доверенности"
        required
        v-show="form.attorneyData.issueForm === 1"
    >
        <el-select
            v-model="form.attorneyData.releaseSystem"
            :options="releaseSystems"
            clearable
            :empty-values="['']"
            filterable
        />
    </el-form-item>
    <el-form-item label="Обоснование необходимости" required>
        <el-input type="textarea" v-model="form.attorneyData.justification" />
    </el-form-item>
    <el-form-item label="Область деятельности представителя по доверенности">
        <el-select
            v-model="form.attorneyData.activityArea"
            :options="activityAreas"
            clearable
            :empty-values="['']"
            filterable
        />
    </el-form-item>
    <el-form-item label="Иные данные">
        <el-input type="textarea" v-model="form.attorneyData.additionalInfo" />
    </el-form-item>
</template>

<style>
.upload {
    display: flex;
    gap: 20px;
}
</style>
