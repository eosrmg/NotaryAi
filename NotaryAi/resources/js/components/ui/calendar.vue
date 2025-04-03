<template>
    <div class="calendar">
        <header class="calendar-header">
            <button @click="prevMonth">Previous</button>
            <h2>{{ monthYear }}</h2>
            <button @click="nextMonth">Next</button>
        </header>
        <div class="calendar-grid">
            <div class="day" v-for="day in days" :key="day">{{ day }}</div>
            <div class="date" v-for="date in dates" :key="date" :class="{ 'today': isToday(date) }">
                {{ date.getDate() }}
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';

const currentDate = ref(new Date());
const days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];

const monthYear = computed(() => {
    return currentDate.value.toLocaleString('default', { month: 'long', year: 'numeric' });
});

const dates = computed(() => {
    const startOfMonth = new Date(currentDate.value.getFullYear(), currentDate.value.getMonth(), 1);
    const endOfMonth = new Date(currentDate.value.getFullYear(), currentDate.value.getMonth() + 1, 0);
    const dateArray = [];

    for (let i = 1; i <= endOfMonth.getDate(); i++) {
        dateArray.push(new Date(currentDate.value.getFullYear(), currentDate.value.getMonth(), i));
    }

    return dateArray;
});

const prevMonth = () => {
    currentDate.value.setMonth(currentDate.value.getMonth() - 1);
};

const nextMonth = () => {
    currentDate.value.setMonth(currentDate.value.getMonth() + 1);
};

const isToday = (date: Date) => {
    const today = new Date();
    return date.getDate() === today.getDate() && date.getMonth() === today.getMonth() && date.getFullYear() === today.getFullYear();
};
</script>

<style scoped>
.calendar {
    display: flex;
    flex-direction: column;
    align-items: center;
}

.calendar-header {
    display: flex;
    justify-content: space-between;
    width: 100%;
    margin-bottom: 10px;
}

.calendar-grid {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 5px;
}

.day, .date {
    padding: 10px;
    text-align: center;
}

.today {
    background-color: #f0f0f0;
    border-radius: 50%;
}
</style>