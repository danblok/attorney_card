export interface FormModel {
    representativeType: "private" | "legal"
    privateRepresentatives: PrivateRepresentative[]
    legalRepresentative: LegalRepresentative
    attorneyData: AttorneyData
    powersOfAttorney: PowersOfAttorney
}

export interface PrivateRepresentative {
    id: number
    reprId: number
    isGardiaEmployee: boolean
    firstName: string
    lastName: string
    secondName: string
    position: string
    passportIssuedBy: string
    passportIssuedDate: string
    divisionCode: string
    registrationAddress: string
    additionalInfo: string
    passportSeries: string
    passportNumber: string
}

export interface LegalRepresentative {
    id: number
    reprId: number
    fullName: string
    shortName: string
    ogrn: string
    inn: string
    legalAddress: string
    reprFirstName: string
    reprLastName: string
    reprSecondName: string
    reprPosition: string
    basePowersDoc: string
}

export interface AttorneyData {
    internalNumber: string
    externalNumber: string
    status: string
    initiator: string
    department: string
    reprId: number
    firstName: string
    lastName: string
    secondName: string
    position: string
    issueDate: string
    startDate: string
    endDate: string
    isNotarialPowerOfAttorney: boolean
    issueForm: number
    template: number
    file: string
    copiesCount: number
    releaseSystem: string
    justification: string
    activityArea: string
    additionalInfo: string
}

export interface PowersOfAttorney {
    powers: string[]
    powersFreeForm: string
    isPowersInFreeForm: boolean
}
