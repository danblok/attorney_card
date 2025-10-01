<script setup lang="ts">
import { onMounted, ref, watchEffect } from "vue"
import { type UploadUserFile } from "element-plus"
import { useStore } from "@/stores"

const store = useStore()
const form = store.project

const uploadedFiles = ref<UploadUserFile[]>([])

watchEffect(() => {
    console.log("initiator:", form.attorneyData.initiator)
    console.log("users:", store.users)
})

onMounted(async () => {
    if (form.attorneyData.file) {
        await store.fetchFileInfo(form.attorneyData.file)
        uploadedFiles.value = [store.file!]
    }
})

type FileUploadResponse = {
    data: string // ID of the uploaded file
}

function handleFileUpload(res: FileUploadResponse) {
    console.info("handleFileUpload:", res)
    form.attorneyData.file = res.data
}
</script>

<template>
    <el-space direction="vertical" class="w-full" alignment="flex-start">
        <el-space :size="50" direction="horizontal">
            <el-form-item label="Внутренний номер доверенности">
                <el-input v-model="form.attorneyData.internalNumber" disabled />
            </el-form-item>
            <el-form-item label="Внешний номер доверенности">
                <el-input v-model="form.attorneyData.externalNumber" />
            </el-form-item>
        </el-space>
        <el-space :size="20" direction="horizontal" alignment="flex-start">
            <el-form-item label="Статус доверенности" class="min-w-3xs">
                <el-select
                    v-model="form.attorneyData.status"
                    disabled
                    placeholder=""
                >
                    <el-option
                        v-for="item in store.statuses"
                        :key="item.id"
                        :label="item.value"
                        :value="item.id"
                    />
                </el-select>
            </el-form-item>
            <el-form-item label="Инициатор" class="min-w-3xs">
                <el-select
                    v-model="form.attorneyData.initiator"
                    placeholder=""
                    value-key="id"
                    disabled
                >
                    <el-option
                        v-for="item in store.users"
                        :key="item.id"
                        :label="`${item.lastName} ${item.firstName}
                    ${item.secondName}`"
                        :value="item"
                    />
                </el-select>
            </el-form-item>
            <el-form-item label="Подразделение">
                <el-input v-model="form.attorneyData.department" />
            </el-form-item>
        </el-space>
    </el-space>
    <h3>Единоличный исполнительный орган</h3>
    <el-form-item>
        <el-select
            v-model="form.attorneyData.executiveAuthority"
            clearable
            filterable
            value-key="id"
            :disabled="
                false // TODO: there is a condition to that
            "
            :value-on-clear="store.director"
        >
            <el-option
                v-for="item in store.users"
                :key="item.id"
                :label="`${item.lastName} ${item.firstName} ${item.secondName}`"
                :value="item"
            />
        </el-select>
    </el-form-item>
    <el-space :size="20" direction="horizontal">
        <el-form-item label="Фамилия">
            <el-input
                v-model="form.attorneyData.executiveAuthority.lastName"
                disabled
            />
        </el-form-item>
        <el-form-item label="Имя">
            <el-input
                v-model="form.attorneyData.executiveAuthority.firstName"
                disabled
            />
        </el-form-item>
        <el-form-item label="Отчество">
            <el-input
                v-model="form.attorneyData.executiveAuthority.secondName"
                disabled
            />
        </el-form-item>
    </el-space>
    <el-form-item label="Должность">
        <el-input
            v-model="form.attorneyData.executiveAuthority.position"
            disabled
        />
    </el-form-item>
    <el-form-item label="Дата выпуска доверенности">
        <el-date-picker
            type="date"
            v-model="form.attorneyData.issueDate"
            format="DD.MM.YYYY"
            value-format="DD.MM.YYYY"
        />
    </el-form-item>
    <el-space direction="horizontal" :size="50">
        <el-form-item label="Дата начала действия доверенности">
            <el-date-picker
                type="date"
                v-model="form.attorneyData.startDate"
                format="DD.MM.YYYY"
                value-format="DD.MM.YYYY"
            />
        </el-form-item>
        <el-form-item label="Дата окончания действия доверенности">
            <el-date-picker
                type="date"
                v-model="form.attorneyData.endDate"
                format="DD.MM.YYYY"
                value-format="DD.MM.YYYY"
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
                clearable
                :empty-values="['']"
                filterable
            >
                <el-option
                    v-for="item in store.forms"
                    :key="item.id"
                    :label="item.value"
                    :value="item.id"
                />
            </el-select>
        </el-form-item>
        <el-form-item label="Шаблон доверенности" required>
            <el-select
                v-model="form.attorneyData.template"
                clearable
                :empty-values="['']"
                filterable
            >
                <el-option
                    v-for="item in store.templates"
                    :key="item.id"
                    :label="item.value"
                    :value="item.id"
                />
            </el-select>
        </el-form-item>
    </el-space>
    <!-- TODO -->
    <el-form-item
        v-show="
            form.attorneyData.template && form.attorneyData.template !== '64'
        "
    >
        <el-upload
            action="/ac/api/file/upload"
            class="upload"
            :limit="1"
            :on-success="handleFileUpload"
            :file-list="uploadedFiles"
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
        v-show="
            form.attorneyData.issueForm && form.attorneyData.issueForm === '62'
        "
    >
        <el-select
            v-model="form.attorneyData.system"
            clearable
            :empty-values="['']"
            filterable
        >
            <el-option
                v-for="item in store.systems"
                :key="item.id"
                :label="item.value"
                :value="item.id"
            />
        </el-select>
    </el-form-item>
    <el-form-item label="Обоснование необходимости" required>
        <el-input type="textarea" v-model="form.attorneyData.justification" />
    </el-form-item>
    <el-form-item label="Область деятельности представителя по доверенности">
        <el-input type="textarea" v-model="form.attorneyData.activityArea" />
        <!-- PREVIOUSLY THERE WERE VALUES FROM ANOTHER SMART PROCESS -->
        <!-- <el-select -->
        <!--     v-model="form.attorneyData.activityArea" -->
        <!--     clearable -->
        <!--     :empty-values="['']" -->
        <!--     filterable -->
        <!-- > -->
        <!--     <el-option -->
        <!--         v-for="item in globalStore.activityAreas" -->
        <!--         :key="item.id" -->
        <!--         :label="item.value" -->
        <!--         :value="item.id" -->
        <!--     /> -->
        <!-- </el-select> -->
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
