import { ref, computed } from "vue"
import { defineStore } from "pinia"
import type { FormModel } from "@/types"

export const useFormStore = defineStore("form", () => {
    const form = ref<FormModel>({
        representativeType: "private",
        privateRepresentatives: [
            {
                id: 1,
                reprId: 1,
                isGardiaEmployee: true,
                firstName: "TNHOenuhonuh",
                lastName: "ontehunohunh",
                secondName: "onehunouh",
                position: "qwjkbqwmkbqwkb",
                passportIssuedBy: "qjbkwbk",
                passportIssuedDate: "2023-01-13",
                divisionCode: "12313213",
                registrationAddress: "nohneuhnouh ohuno heo",
                additionalInfo: "noheunohunohuneoh ",
                passportSeries: "9797",
                passportNumber: "08089080",
            },
            {
                id: 2,
                reprId: 2,
                isGardiaEmployee: false,
                firstName: "grgrgerogurcoug",
                lastName: "tndntdhdnhd",
                secondName: "mbxmbxmxmx",
                position: "l/r/r/lr/r",
                passportIssuedBy: "cfcfcgcffc",
                passportIssuedDate: "2025-08-25",
                divisionCode: "3453535",
                registrationAddress: "kafr,.cgodehtxunotdcg",
                additionalInfo: "mbqmjbv.cdu/',uco",
                passportSeries: "12313",
                passportNumber: "1234124",
            },
        ],
        legalRepresentative: {
            id: 0,
            reprId: 1,
            fullName: "Some Company Full Name",
            shortName: "SCFN",
            ogrn: "1231321314",
            inn: "12313-21313213-131",
            legalAddress: "aboba street 13",
            reprFirstName: "Kto-to",
            reprLastName: "honuehou",
            reprSecondName: "vwbqjkbqk",
            reprPosition: "ohnohentbotknh",
            basePowersDoc: "tqjbktkhbt qhbkt q kbnjtqkqt",
        },
        attorneyData: {
            activityArea: "",
            additionalInfo: "",
            copiesCount: 1,
            department: "",
            externalNumber: "",
            file: "",
            reprId: 0,
            firstName: "",
            initiator: "",
            internalNumber: "",
            issueDate: "",
            startDate: "",
            endDate: "",
            issueForm: 0,
            template: 0,
            justification: "",
            lastName: "",
            isNotarialPowerOfAttorney: false,
            position: "",
            releaseSystem: "",
            secondName: "",
            status: "",
        },
        powersOfAttorney: {
            powers: [],
            powersFreeForm: "",
            isPowersInFreeForm: false,
        },
    })

    async function save() {
        console.log("form:", form.value)
    }

    return { form, save }
})
