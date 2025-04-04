<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import axios from 'axios';
import { useForm } from '@inertiajs/vue3';
import { computed, ref, onMounted, watch } from 'vue';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import { Switch } from '@/components/ui/switch';
import { Calendar } from '@/components/ui/calendar'
import { CalendarDate, DateFormatter, getLocalTimeZone, parseDate, today } from '@internationalized/date'

import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog'

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Vanzare',
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
    rate_date: string;
    current_date: string;
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

const isDialogOpen = ref(false); // Controls the dialog visibility
const closeDialog = () => {
    isDialogOpen.value = false;
};

// Computed property to calculate the amount in RON
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
            // Convert to RON if the input is in EUR
            amount = parseFloat((parseFloat(form.amount) * parseFloat(form.current_rate)).toFixed(2));
        } else {
            // Send the amount directly if it's in RON
            amount = parseFloat(form.amount);
        }

        if (isNaN(amount)) {
            console.error('Invalid amount provided.');
            return;
        }

        console.log('Sending request with amount:', amount);

        const response = await axios.post('/calculator', {
            amount: amount,                // Send the amount in RON
            is_eur: form.is_eur ? 1 : 0,         // Boolean to 1 or 0
            current_rate: form.current_rate, // Send the rate only if applicable
            is_pf: form.is_pf ? 1 : 0,           // Boolean to 1 or 0
            is_one_percent: form.is_one_percent ? 1 : 0,
            apply_impozit: form.apply_impozit ? 1 : 0,
        });

        result.value = response.data;  // Set response to result
    } catch (error) {
        console.error('Error fetching response:', error);
    }
};

onMounted(async () => {
    if (!form.is_eur) {
        try {
            const response = await axios.get('/exchange-rate');
            if (response.data) {

                exchangeRates.value = response.data;
                form.bnr_rate = exchangeRates.value[0].rate; // Assuming the first item in the array is the latest
                form.rate_date = exchangeRates.value[0].date; // Save the fetched date here
                form.current_rate = form.bnr_rate;
                form.current_date = form.rate_date;
            }
        } catch (error) {
            console.error('Error fetching exchange rate:', error);
        }
    }
});

// Watch the selected radio button and update the current rate accordingly
const updateRateAndDate = () => {
    // Check the selected rate option and update accordingly
    if (form.selectedRateOption === 'today') {
        // Use the BNR rate if "today" is selected
        form.current_rate = form.bnr_rate;
        form.rate_date = form.current_date; // Ensure the date is preserved
    }

    if (form.selectedRateOption === 'custom' && form.current_rate) {
        // Format the rate to have 4 zeros after the decimal
        const formattedValue = parseFloat(form.current_rate).toFixed(4);
        form.current_rate = formattedValue;
    }

    // Close the dialog after updating the rate and date
    closeDialog();
}




const isDatePickerDialogOpen = ref(false); // Controls if the date picker dialog is open
const selectedDate = computed({
    get: () => form.rate_date ? parseDate(form.rate_date) : undefined,
    set: (val) => {
        if (val) {
            form.rate_date = val.toString(); // Ensure you format it correctly if needed
        } else {
            form.rate_date = '';
        }
    }
});
const rateForSelectedDate = ref<string | null>(null);  // Holds the exchange rate for the selected date

interface ExchangeRate {
    rate: string;
    date: string;
}

const exchangeRates = ref<ExchangeRate[]>([]);

// Function to open the date picker dialog
function openDatePickerDialog() {
    isDatePickerDialogOpen.value = true;
}

// Function to close the date picker dialog
function closeDatePickerDialog() {
    isDatePickerDialogOpen.value = false;
}

// Handle the date selection from the calendar
function handleDateSelection(date: CalendarDate | undefined) {
    // Log the selected date for debugging
    console.log('Selected Date:', date);
    console.log('Selected Date:', selectedDate.value);
    // Check if the selected date is valid (not undefined or empty)
    if (date) {
        selectedDate.value = date;
        console.log('Formatted Selected Date:', selectedDate.value);
    } else {
        console.error('Invalid date selected');
    }
}

// Fetch the exchange rate for the selected date
function getRateForSelectedDate() {
    if (selectedDate.value) {
        console.log('Selected Date:', selectedDate.value);  // Log the selected date
        console.log('Exchange Rates:', exchangeRates.value);  // Log the array of exchange rates

        // Format selected date to ensure it matches the format in exchangeRates (e.g., 'YYYY-MM-DD')
        const formattedSelectedDate = formatDate(selectedDate.value.toString());

        // Check if exchangeRates.value is an array
        if (Array.isArray(exchangeRates.value)) {
            const selectedRate = exchangeRates.value.find(rate => formatDate(rate.date) === formattedSelectedDate);

            if (selectedRate) {
                rateForSelectedDate.value = selectedRate.rate;

                form.current_rate = selectedRate.rate; // Update the current rate with the selected rate
                form.rate_date = selectedRate.date; // Update the rate date

                console.log('Rate for selected date:', selectedRate.rate); // Log the rate found
                console.log(`Selected Date: ${formattedSelectedDate} - Rate: ${selectedRate.rate}`); // Log date and rate
            } else {
                console.error(`Rate for selected date (${formattedSelectedDate}) not found.`);
            }
        } else {
            console.error('exchangeRates.value is not an array:', exchangeRates.value);
        }
    }
}


// Function to format the date as 'YYYY-MM-DD'
function formatDate(date: string) {
    // Ensure the date is in 'YYYY-MM-DD' format (adjust as needed based on your requirements)
    const parsedDate = new Date(date);
    const year = parsedDate.getFullYear();
    const month = String(parsedDate.getMonth() + 1).padStart(2, '0');  // Ensure 2 digits for month
    const day = String(parsedDate.getDate()).padStart(2, '0');  // Ensure 2 digits for day

    return `${year}-${month}-${day}`;
}

// Confirm the selected date and fetch the rate
async function confirmSelectedDate() {
    if (selectedDate.value) {
        getRateForSelectedDate();  // Fetch the rate for the selected date
        closeDatePickerDialog();
        closeDialog(); // Close the dialog
    }
}

// Function to cancel and close the dialog
function cancelDateSelection() {
    closeDatePickerDialog(); // Close the dialog without doing anything
}


const formattedAmount = computed({
    get() {
        if (!form.amount) return '';
        return new Intl.NumberFormat('en-US').format(Number(form.amount));
    },
    set(value: string) {
        // Remove all commas or spaces before converting back to a number
        const numericValue = Number(value.replace(/[\s,]/g, ''));
        if (!isNaN(numericValue)) {
            form.amount = numericValue.toString();
        }
    }
});

// Event handler to update amount when typing
function handleAmountInput(event: Event) {
    const input = event.target as HTMLInputElement;
    formattedAmount.value = input.value;
}

// Computed property to format the amount with commas
const formattedAmountInRON = computed(() => {
    if (!amountInRON.value) return '0';
    return new Intl.NumberFormat('en-US').format(Number(amountInRON.value));
});



</script>
<template>

    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">


        <!-- Sub-navigation links styled with Tailwind CSS -->
        <div class="navigation space-x-4 flex justify-start items-center pl-4 rounded-lg shadow-md">
            <Link :href="route('calculator')"
                class="btn btn-outline btn-primary rounded-lg pl-1 pr-1 hover:bg-blue-500 hover:text-white transition duration-200">
            Vanzare
            </Link>
            <Link :href="route('ipoteca')"
                class="btn btn-outline btn-primary rounded-lg pl-1 pr-1 hover:bg-blue-500 hover:text-white transition duration-200">
            Ipoteca
            </Link>
            <Link :href="route('succesiune')"
                class="btn btn-outline btn-primary rounded-lg pl-1 pr-1 hover:bg-blue-500 hover:text-white transition duration-200">
            Succesiune
            </Link>
        </div>

        <Dialog :open="isDialogOpen" @onOpenChange="(value: boolean) => isDialogOpen = value">


            <div class="flex h-full flex-1 flex-col gap-4 rounded-xl pl-4">
                <div class="p-4 w-full max-w-5xl justify-start h-fit">


                    <form @submit.prevent="calculateOnorariu" class="space-y-4">

                        <div class="flex h-full flex-1 flex-col bg-gray-800 gap-2 rounded-xl p-2">

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
                                        @click="form.is_eur = false; calculateOnorariu(); amountInRON">
                                        <span class="currency-text"
                                            :class="{ 'text-white': !form.is_eur, 'text-black': form.is_eur }">
                                            RON
                                        </span>
                                    </div>
                                </div>

                                <div class="items-center space-x-1 justify-s">
                                    <div
                                        class="border border-gray-300 bg-white rounded-lg px-3 py-2 flex items-center justify-center text-gray-700">
                                        <span>1 EUR = {{ form.current_rate }} </span>
                                        <span class="text-gray-700 text-md pl-4  pr-10">
                                            {{ form.selectedRateOption === 'custom' ? 'Curs modificat' : 'Curs din ' +
                                                form.rate_date }}
                                        </span>



                                        <Button variant="outline" class="pl-4 bg-blue-500 text-white hover:bg-blue-600"
                                            @click="isDialogOpen = true">
                                            Modifica Curs
                                        </Button>


                                    </div>
                                </div>

                            </div>



                            <div class="relative flex items-center w-full ">
                                <Input v-model="formattedAmount" @input="handleAmountInput" type="text"
                                    class="w-full bg-white text-gray-800 outline-none text-left pr-14" />
                                <span class="absolute right-4 text-gray-800">{{ form.is_eur ? 'EUR' : 'RON' }}</span>
                            </div>


                            <div v-if="form.is_eur" class="flex items-center justify-s">
                                <div
                                    class="border border-gray-300 bg-white rounded-lg px-3 py-2 w-full flex items-center justify-between text-gray-700">
                                    <span class="text-right text-gray-800 text-sm">{{ formattedAmountInRON }}</span>
                                    <span class="ml-2 text-gray-800">RON</span>
                                </div>
                            </div>

                            <div class="flex justify-center items-center space-x-2">
                                <Button type="submit" class="bg-orange-500 w-60 hover:bg-orange-600 text-white">
                                    Calculeaza
                                </Button>
                            </div>
                        </div>



                        <div v-if="result" class="flex h-full flex-1 flex-col gap-4 rounded-xl t">


                            <div class="grid auto-rows-min gap-4 md:grid-cols-2 h-fit ">

                                <!-- SubTotal section -->

                                <div
                                    class="flex flex-col bg-gray-800 text-white p-3 rounded-lg shadow-md space-y-4 h-fit">

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

                                    <!-- Tarif CF section is implemented below -->

                                    <div
                                        class="flex flex-col bg-gray-800 text-white p-3 rounded-lg shadow-md space-y-4">
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


                                    <!-- Impozit section (Moved to the right side) -->
                                    <div
                                        class="flex flex-col space-y-2  bg-gray-800 text-white p-3 rounded-lg shadow-md">

                                        <div class="flex items-center space-x-2 justify-start bg-gray-800"
                                            @click="calculateOnorariu()">
                                            <Switch id="impozit" v-model="form.apply_impozit" />
                                            <label for="impozit" class="text-sm font-medium leading-none">
                                                Se percepe impozit?
                                            </label>
                                        </div>

                                        <div v-if="form.apply_impozit"
                                            class="flex items-center space-x-2 justify-between">

                                            <div class="currency-selector">
                                                <div class="currency-option"
                                                    :class="{ 'selected': form.is_one_percent }"
                                                    @click="form.is_one_percent = true; calculateOnorariu()">
                                                    <span class="currency-text"
                                                        :class="{ 'text-white': form.is_one_percent, 'text-black': !form.is_one_percent }">
                                                        1%
                                                    </span>
                                                </div>
                                                <div class="currency-option"
                                                    :class="{ 'selected': !form.is_one_percent }"
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
                                    <!-- Result section is implemented below -->

                                    <div
                                        class="flex w-full justify-between bg-gray-600 text-white p-3 rounded-lg shadow-md">
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

                        <!-- Radio Buttons Group -->
                        <div class="flex flex-col space-y-4 col-span-3">
                            <!-- First Radio Button: Editable Input -->
                            <label class="flex items-center space-x-2">
                                <input type="radio" v-model="form.selectedRateOption" value="custom"
                                    class="form-radio" />
                                <span>Curs valutar: 1 EUR = </span>
                                <Input id="custom_rate" type="text" v-model="form.current_rate"
                                    :disabled="form.selectedRateOption !== 'custom'" class="w-32" />
                            </label>

                            <!-- Second Radio Button: Current Rate Display -->
                            <label class="flex items-center space-x-2">
                                <input type="radio" v-model="form.selectedRateOption" value="today"
                                    class="form-radio" />
                                <span>Curs valutar BNR Azi: 1 EUR = {{ form.bnr_rate }} RON</span>
                            </label>
                        </div>
                    </div>
                    <!-- Buttons -->
                    <div class="flex flex-col space-y-4 mt-6 justify-center">
                        <Button class="bg-orange-500 hover:bg-orange-600 text-white" @click="updateRateAndDate">Modifica
                            Curs</Button>

                        <Button class="bg-blue-500 hover:bg-blue-600 text-white" @click="openDatePickerDialog">
                            Alege Data Curs Valutar
                        </Button>

                        <Button class="bg-gray-500 hover:bg-gray-600 text-white" @click="closeDialog">
                            Anuleaza
                        </Button>
                    </div>
                </div>


                <Dialog :open="isDatePickerDialogOpen" @onClose="closeDatePickerDialog">
                    <DialogContent>
                        <DialogHeader>
                            <DialogTitle>Alege Data Curs Valutar</DialogTitle>
                        </DialogHeader>
                        <div class="flex flex-col items-center justify-center gap-4 py-4">
                            <div class="mb-4">
                                <!-- Shadcn UI Calendar (Date Picker) -->
                                <Calendar v-model="selectedDate" :max-value="today(getLocalTimeZone())"
                                    @update="handleDateSelection" />
                            </div>
                            <!-- Buttons -->
                            <div class="flex space-x-4 mt-6 justify-center">
                                <!-- Confirm Button -->
                                <Button class="bg-blue-500 hover:bg-blue-600 text-white" @click="confirmSelectedDate">
                                    Alege Data
                                </Button>
                                <!-- Cancel Button -->
                                <Button class="bg-gray-500 hover:bg-gray-600 text-white" @click="cancelDateSelection">
                                    Anuleaza
                                </Button>
                            </div>
                        </div>
                    </DialogContent>
                </Dialog>






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