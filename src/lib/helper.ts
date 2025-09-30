import type {
    Company,
    LegalRepresentative,
    PrivateRepresentative,
    Project,
    User,
} from "@/types"

export class Helper {
    public static createProject() {
        return {
            id: "185",
            subjectId: "60", // Физ лицо
            privateRepresentatives: [Helper.createPrivateRepresentative()],
            legalRepresentative: Helper.createLegalRepresentative(),
            attorneyData: {
                activityArea: "",
                additionalInfo: "",
                copiesCount: 1,
                department: "",
                externalNumber: "",
                file: "",
                executiveAuthority: Helper.createPrivateRepresentative(),
                initiator: Helper.createPrivateRepresentative(),
                internalNumber: "",
                issueDate: "",
                startDate: "",
                endDate: "",
                issueForm: "",
                template: "",
                justification: "",
                isNotarialPowerOfAttorney: false,
                system: "",
                status: "49",
            },
            authorities: {
                values: [],
                freeForm: "",
                isAuthoritiesInFreeForm: false,
            },
            status: "",
            isCancelled: false,
            isReadonly: false,
        } as Project
    }

    // Создаёт пустого представителя
    public static createPrivateRepresentative(
        isGardiaEmployee: boolean = false,
    ) {
        return {
            additionalInfo: "",
            divisionCode: "",
            externalId: "0",
            firstName: "",
            id: this.generateRandomString(),
            isGardiaEmployee: isGardiaEmployee,
            lastName: "",
            passportIssuedBy: "",
            passportIssuedDate: this.dateToString(new Date()),
            passportNumber: "",
            passportSeries: "",
            position: "",
            registrationAddress: "",
            secondName: "",
        } as PrivateRepresentative
    }

    public static createUser() {
        return {
            id: this.generateRandomString(),
            firstName: "",
            lastName: "",
            position: "",
            secondName: "",
        } as User
    }

    public static createCompany() {
        return {
            id: this.generateRandomString(),
            fullName: "",
            shortName: "",
            ogrn: "",
            inn: "",
            address: "",
        } as Company
    }

    // Создаёт пустого представителя
    public static createLegalRepresentative() {
        return {
            company: this.createCompany(),
            representative: this.createPrivateRepresentative(),
            basePowerDocument: "",
        } as LegalRepresentative
    }

    // Генерирует рандомную строку
    public static generateRandomString(length: number = 16) {
        const array = new Uint8Array(length / 2)
        crypto.getRandomValues(array)
        return (
            "UM:" +
            Array.from(array, (b) => b.toString(16).padStart(2, "0")).join("")
        )
    }

    public static dateToString(date: Date) {
        return date.toLocaleDateString("ru-RU")
    }
}
