// filepath: /NotaryAi/NotaryAi/resources/js/types/index.ts

export interface BreadcrumbItem {
    title: string;
    href: string;
}

export interface CalculationResult {
    onorariu: number;
    onorariuArhivare: number;
    tva: number;
    subtotal: number;
    taxaCF: number;
    impozit: number;
    total: number;
}

export interface FormData {
    amount: string;
    is_eur: boolean;
    current_rate: string;
    selectedRateOption: string;
    bnr_rate: string;
    custom_rate: string;
    rate_date: string;
    current_date: string;
    is_pf: boolean;
    is_one_percent: boolean;
    apply_impozit: boolean;
}