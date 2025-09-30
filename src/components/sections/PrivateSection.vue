<script setup lang="ts">
import { computed, ref, watchEffect } from "vue"
import { Delete } from "@element-plus/icons-vue"
import { useStore } from "@/stores"
import type { PrivateRepresentative } from "@/types"
import { Helper } from "@/lib/helper"

interface Props {
    value: PrivateRepresentative
}
const props = defineProps<Props>()
const emit = defineEmits(["remove", "update"])
const selected = ref<PrivateRepresentative>(props.value)

const globalStore = useStore()
const isGardiaEmployee = ref<boolean>(false)

const representatives = computed(() =>
    globalStore.representatives.filter(
        (repr) => repr.isGardiaEmployee === isGardiaEmployee.value,
    ),
)

function handleClear() {
    isGardiaEmployee.value = false
}

watchEffect(() => {
    emit("update", selected.value)
    isGardiaEmployee.value = selected.value.isGardiaEmployee
})
</script>
<template>
    <el-card class="card">
        <el-form-item label="Сотрудник Гардия" label-position="right">
            <el-checkbox
                v-model="isGardiaEmployee"
                @change="
                    (isEmployee: boolean) => {
                        if (globalStore.currentUser && isEmployee) {
                            selected = globalStore.representatives.find(
                                (repr) =>
                                    repr.externalId ===
                                        globalStore.currentUser?.id &&
                                    repr.isGardiaEmployee,
                            )! // Не может быть null, потому что данные берутся
                            // из одного источника
                        } else {
                            selected = Helper.createPrivateRepresentative()
                        }
                    }
                "
            ></el-checkbox>
        </el-form-item>
        <h3>ФИО представителя по доверенности</h3>
        <el-form-item>
            <el-select
                v-model="selected"
                value-key="id"
                clearable
                filterable
                :value-on-clear="Helper.createPrivateRepresentative()"
                @clear="handleClear"
            >
                <el-option
                    v-for="item in representatives"
                    :key="item.id"
                    :label="`${item.lastName} ${item.firstName} ${item.secondName}`"
                    :value="item"
                />
            </el-select>
        </el-form-item>
        <el-space class="horizontal-space" direction="horizontal" :size="20">
            <el-form-item label="Фамилия" required>
                <el-input v-model="selected.lastName" />
            </el-form-item>
            <el-form-item label="Имя" required>
                <el-input v-model="selected.firstName" />
            </el-form-item>
            <el-form-item label="Отчество" required>
                <el-input v-model="selected.secondName" />
            </el-form-item>
        </el-space>
        <el-form-item label="Должность представителя по доверенности" required>
            <el-input v-model="selected.position" />
        </el-form-item>
        <el-space direction="horizontal" :size="20">
            <el-form-item label="Серия" required>
                <el-input v-model="selected.passportSeries" />
            </el-form-item>
            <el-form-item label="Номер" required>
                <el-input v-model="selected.passportNumber" />
            </el-form-item>
        </el-space>
        <el-form-item label="Паспорт выдан" required>
            <el-input type="textarea" v-model="selected.passportIssuedBy" />
        </el-form-item>
        <el-space direction="horizontal" :size="20">
            <el-form-item label="Дата выдачи" required>
                <el-date-picker
                    type="date"
                    v-model="selected.passportIssuedDate"
                    format="DD.MM.YYYY"
                    value-format="DD.MM.YYYY"
                />
            </el-form-item>
            <el-form-item label="Код подразделения" required>
                <el-input v-model="selected.divisionCode" />
            </el-form-item>
        </el-space>
        <el-form-item label="Адрес регистрации">
            <el-input type="textarea" v-model="selected.registrationAddress" />
        </el-form-item>
        <el-form-item label="Дополнительные сведения">
            <el-input type="textarea" v-model="selected.additionalInfo" />
        </el-form-item>
        <el-space direction="horizontal" :size="20">
            <el-button type="danger" :icon="Delete" @click="$emit('remove')" />
        </el-space>
    </el-card>
</template>

<style>
.horizontal-space {
    display: flex;
    justify-content: flex-start;
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
