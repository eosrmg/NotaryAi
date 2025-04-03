<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import axios from 'axios';
import { useForm } from '@inertiajs/vue3';
import { computed, ref, onMounted } from 'vue';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import { Switch } from '@/components/ui/switch';
import { Calendar } from '@/components/ui/calendar';
import {
    Dialog,
    DialogContent,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Calculator',
        href: '/',
    },
];

interface CalculationResult {
    onorariu: number;
    onorariuArhivare: number;
    tva: number;
    subtotal: number;
    taxaCF: number;
    impozit: number;
    total: number;
}

const form = useForm<{
    amount: string;
    is_eur: boolean;
    current_rate: string;
    selectedRateOption: string;
    bnr_rate: string;
    custom_rate: string;
    rate_date: '';
    current_date: '';
    is_pf: boolean;
    is_one_percent: boolean;
    apply_impozit: boolean;
}>({
    amount: '',
    is_eur: false,
    current_rate: '',
    selectedRateOption: 'today',
    bnr_rate: '',
    custom_rate: '',
    rate_date: '',
    current_date: '',
    is_pf: true,
    is_one_percent: true,
    apply_impozit: false,
});

const result = ref<CalculationResult | null>(null);
const isDialogOpen = ref(false);
const closeDialog = () => {
    isDialogOpen.value = false;
};

const amountInRON = computed(() => {
    if (form.is_eur && form.amount && form.current_rate) {
        return (parseFloat(form.amount) * parseFloat(form.current_rate)).toFixed(2);
    }
    return form.amount;
});

const calculateOnorariu = async () => {
    try {
        let amount: number;

        if (form.is_eur) {
            amount = parseFloat((parseFloat(form.amount) * parseFloat(form.current_rate)).toFixed(2));
        } else {
            amount = parseFloat(form.amount);
        }

        if (isNaN(amount)) {
            console.error('Invalid amount provided.');
            return;
        }

        const response = await axios.post('/calculator', {
            amount: amount,
            is_eur: form.is_eur ? 1 : 0,
            current_rate: form.current_rate,
            is_pf: form.is_pf ? 1 : 0,
            is_one_percent: form.is_one_percent ? 1 : 0,
            apply_impozit: form.apply_impozit ? 1 : 0,
        });

        result.value = response.data;
    } catch (error) {
        console.error('Error fetching response:', error);
    }
};

onMounted(async () => {
    if (!form.is_eur) {
        try {
            const response = await axios.get('/exchange-rate');
            if (response.data) {
                form.bnr_rate = response.data[0].rate;
                form.rate_date = response.data[0].date;
                form.current_rate = form.bnr_rate;
                form.current_date = form.rate_date;
            }
        } catch (error) {
            console.error('Error fetching exchange rate:', error);
        }
    }
});
</script>

<template>
    <Head title="Calculator" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <Dialog :open="isDialogOpen" @onOpenChange="(value: boolean) => isDialogOpen = value">
            <div class="flex h-full flex-1 flex-col gap-4 rounded-xl pl-4">
                <div class="p-4 w-full max-w-5xl justify-start h-fit">
                    <h1 class="text-xl font-bold mb-4 text-center">Calculator</h1>
                    <form @submit.prevent="calculateOnorariu" class="space-y-4">
                        <div class="flex h-full flex-1 flex-col bg-gray-800 gap-4 rounded-xl p-4">
                            <div class="grid auto-rows-min gap-1 md:grid-cols-2">
                                <div class="currency-selector">
                                    <div class="currency-option" :class="{ 'selected': form.is_eur }"
                                        @click="form.is_eur = true; calculateOnorariu()">
                                        <span class="currency-text"
                                            :class="{ 'text-white': form.is_eur, 'text-black': !form.is_eur }">
                                            Euro
                                        </span>
                                    </div>
                                    <div class="currency-option" :class="{ 'selected': !form.is_eur }"
                                        @click="form.is_eur = false; calculateOnorariu()">
                                        <span class="currency-text"
                                            :class="{ 'text-white': !form.is_eur, 'text-black': form.is_eur }">
                                            RON
                                        </span>
                                    </div>
                                </div>
                                <div class="items-center space-x-1 justify-s">
                                    <div class="border border-gray-300 bg-white rounded-lg px-3 py-2 flex items-center justify-center text-gray-700">
                                        <span>1 EUR = {{ form.current_rate }} </span>
                                        <span class="text-gray-700 text-md pl-4 pr-10">
                                            {{ form.selectedRateOption === 'custom' ? 'Curs modificat' : 'Curs din ' + form.rate_date }}
                                        </span>
                                        <Button variant="outline" class="pl-4 bg-blue-500 text-white hover:bg-blue-600"
                                            @click="isDialogOpen = true">
                                            Modifica Curs
                                        </Button>
                                    </div>
                                </div>
                            </div>
                            <div class="relative flex items-center w-full ">
                                <Input v-model="form.amount" type="number"
                                    class="w-full bg-white text-gray-800 outline-none text-left pr-14" />
                                <span class="absolute right-4 text-gray-800">{{ form.is_eur ? 'EUR' : 'RON' }}</span>
                            </div>
                            <div v-if="form.is_eur" class="flex items-center justify-s">
                                <div class="border border-gray-300 bg-white rounded-lg px-3 py-2 w-full flex items-center justify-between text-gray-700">
                                    <span class="text-right text-gray-800 text-sm">{{ amountInRON }}</span>
                                    <span class="ml-2 text-gray-800">RON</span>
                                </div>
                            </div>
                            <div class="flex justify-center items-center space-x-2">
                                <Button type="submit" class="bg-orange-500 w-60 hover:bg-orange-600 text-white">
                                    Calculeaza
                                </Button>
                            </div>
                        </div>
                        <div v-if="result" class="flex h-full flex-1 flex-col gap-4 rounded-xl">
                            <div class="grid auto-rows-min gap-4 md:grid-cols-2 h-fit ">
                                <div class="flex flex-col bg-gray-800 text-white p-3 rounded-lg shadow-md space-y-4 h-fit">
                                    <div class="flex justify-between">
                                        <p class="text-left">Onorariu:</p>
                                        <p class="text-right">{{ result.onorariu }}</p>
                                    </div>
                                    <div class="flex justify-between">
                                        <p class="text-left">Onorariu arhivare:</p>
                                        <p class="text-right">{{ result.onorariuArhivare }}</p>
                                    </div>
                                    <div class="flex justify-between">
                                        <p class="text-left">TVA:</p>
                                        <p class="text-right">{{ result.tva }}</p>
                                    </div>
                                    <div class="flex justify-between pt-9">
                                        <p class="text-left">Subtotal:</p>
                                        <p class="text-right">{{ result.subtotal }}</p>
                                    </div>
                                </div>
                                <div class="relative aspect-video overflow-hidden rounded-xl space-y-1">
                                    <div class="flex flex-col bg-gray-800 text-white p-3 rounded-lg shadow-md space-y-4">
                                        <div class="currency-selector">
                                            <div class="currency-option" :class="{ 'selected': form.is_pf }"
                                                @click="form.is_pf = true; calculateOnorariu()">
                                                <span class="currency-text"
                                                    :class="{ 'text-white': form.is_pf, 'text-black': !form.is_pf }">
                                                    PF
                                                </span>
                                            </div>
                                            <div class="currency-option" :class="{ 'selected': !form.is_pf }"
                                                @click="form.is_pf = false; calculateOnorariu()">
                                                <span class="currency-text"
                                                    :class="{ 'text-white': !form.is_pf, 'text-black': form.is_pf }">
                                                    PJ
                                                </span>
                                            </div>
                                        </div>
                                        <div class="flex justify-between">
                                            <p class="text-left">Tarif CF:</p>
                                            <p class="text-right">{{ result.taxaCF }}</p>
                                        </div>
                                    </div>
                                    <div class="flex flex-col space-y-2 bg-gray-800 text-white p-3 rounded-lg shadow-md">
                                        <div class="flex items-center space-x-2 justify-start bg-gray-800"
                                            @click="calculateOnorariu()">
                                            <Switch id="impozit" v-model="form.apply_impozit" />
                                            <label for="impozit" class="text-sm font-medium leading-none">
                                                Se percepe impozit?
                                            </label>
                                        </div>
                                        <div v-if="form.apply_impozit" class="flex items-center space-x-2 justify-between">
                                            <div class="currency-selector">
                                                <div class="currency-option" :class="{ 'selected': form.is_one_percent }"
                                                    @click="form.is_one_percent = true; calculateOnorariu()">
                                                    <span class="currency-text"
                                                        :class="{ 'text-white': form.is_one_percent, 'text-black': !form.is_one_percent }">
                                                        1%
                                                    </span>
                                                </div>
                                                <div class="currency-option" :class="{ 'selected': !form.is_one_percent }"
                                                    @click="form.is_one_percent = false; calculateOnorariu()">
                                                    <span class="currency-text"
                                                        :class="{ 'text-white': !form.is_one_percent, 'text-black': form.is_one_percent }">
                                                        3%
                                                    </span>
                                                </div>
                                            </div>
                                            <p class="mb-1">{{ result.impozit }}</p>
                                        </div>
                                    </div>
                                    <div class="flex w-full justify-between bg-gray-600 text-white p-3 rounded-lg shadow-md">
                                        <p class="text-left text-lg">Total:</p>
                                        <p class="text-right text-lg">{{ result.total }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <DialogContent open="isDialogOpen">
                <DialogHeader>
                    <DialogTitle>Modifica curs valutar</DialogTitle>
                </DialogHeader>
                <div class="grid gap-4 py-4">
                    <div class="grid grid-cols-4 gap-4 items-start">
                        <div class="flex flex-col space-y-4 col-span-3">
                            <label class="flex items-center space-x-2">
                                <input type="radio" v-model="form.selectedRateOption" value="custom" class="form-radio" />
                                <span>Curs valutar: 1 EUR = </span>
                                <Input id="custom_rate" type="text" v-model="form.current_rate"
                                    :disabled="form.selectedRateOption !== 'custom'" class="w-32" />
                            </label>
                            <label class="flex items-center space-x-2">
                                <input type="radio" v-model="form.selectedRateOption" value="today" class="form-radio" />
                                <span>Curs valutar BNR Azi: 1 EUR = {{ form.bnr_rate }} RON</span>
                            </label>
                        </div>
                    </div>
                    <div class="flex flex-col space-y-4 mt-6 justify-center">
                        <Button class="bg-orange-500 hover:bg-orange-600 text-white" @click="updateRateAndDate">Modifica Curs</Button>
                        <Button class="bg-blue-500 hover:bg-blue-600 text-white" @click="openDatePickerDialog">Alege Data Curs Valutar</Button>
                        <Button class="bg-gray-500 hover:bg-gray-600 text-white" @click="closeDialog">Anuleaza</Button>
                    </div>
                </div>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>

<style scoped>
.currency-selector {
    display: flex;
    border: 2px solid #ccc;
    border-radius: 4px;
    width: 200px;
    height: 40px;
    overflow: hidden;
    cursor: pointer;
}

.currency-option {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: #f5f5f5;
    transition: background-color 0.3s;
}

.currency-option.selected {
    background-color: #dc8b34;
}

.currency-text {
    font-weight: bold;
    transition: color 0.3s;
}

.text-white {
    color: white;
}

.text-black {
    color: black;
}
</style>