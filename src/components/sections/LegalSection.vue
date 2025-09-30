<script setup lang="ts">
import { Helper } from "@/lib/helper"
import { useStore } from "@/stores"
import type { LegalRepresentative, PrivateRepresentative } from "@/types"
import { watchEffect } from "vue"

const store = useStore()
const form = store.project

watchEffect(() => {
    console.log("selected legal:", form.legalRepresentative)
})
</script>

<template>
    <el-card>
        <h3>Организация</h3>
        <el-form-item>
            <el-select
                v-model="form.legalRepresentative.company"
                clearable
                filterable
                value-key="id"
                :value-on-clear="Helper.createCompany()"
            >
                <el-option
                    v-for="item in store.companies"
                    :key="item.id"
                    :label="item.shortName"
                    :value="item"
                />
            </el-select>
        </el-form-item>
        <el-form-item label="Полное наименование организации" required>
            <el-input v-model="form.legalRepresentative.company.fullName" />
        </el-form-item>
        <el-form-item label="Краткое наименование организации" required>
            <el-input v-model="form.legalRepresentative.company.shortName" />
        </el-form-item>
        <el-space direction="horizontal" :size="20">
            <el-form-item label="ОГРН" required>
                <el-input v-model="form.legalRepresentative.company.ogrn" />
            </el-form-item>
            <el-form-item label="ИНН" required>
                <el-input v-model="form.legalRepresentative.company.inn" />
            </el-form-item>
        </el-space>
        <el-form-item label="Юридический адрес организации" required>
            <el-input v-model="form.legalRepresentative.company.address" />
        </el-form-item>
        <h3>ФИО представителя по доверенности</h3>
        <el-form-item>
            <el-select
                v-model="form.legalRepresentative.representative"
                clearable
                filterable
                value-key="id"
                :value-on-clear="Helper.createPrivateRepresentative()"
            >
                <el-option
                    v-for="item in store.contacts"
                    :key="item.id"
                    :label="`${item.lastName} ${item.firstName} ${item.secondName}`"
                    :value="item"
                />
            </el-select>
        </el-form-item>
        <el-space class="horizontal-space" direction="horizontal">
            <el-form-item label="Фамилия">
                <el-input
                    v-model="form.legalRepresentative.representative.lastName"
                />
            </el-form-item>
            <el-form-item label="Имя">
                <el-input
                    v-model="form.legalRepresentative.representative.firstName"
                />
            </el-form-item>
            <el-form-item label="Отчество">
                <el-input
                    v-model="form.legalRepresentative.representative.secondName"
                />
            </el-form-item>
        </el-space>
        <el-form-item label="Должность представителя организации">
            <el-input
                v-model="form.legalRepresentative.representative.position"
            />
        </el-form-item>
        <el-form-item
            label="Документ-основание полномочий представителя
        организации"
        >
            <el-input v-model="form.legalRepresentative.basePowerDocument" />
        </el-form-item>
    </el-card>
</template>
