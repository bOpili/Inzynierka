<script setup>
import { ref, computed } from 'vue';
import moment from 'moment';
import { route } from '../../../../vendor/tightenco/ziggy/src/js';

moment.updateLocale('en', {
    week: {
        dow: 1,
        doy: 4,
    },
});

const currentMonth = ref(moment());
const today = ref(moment());
const currentView = ref('month');
const selectedDay = ref(null);

const props = defineProps({
    events: {
        type: Array,
        required: true,
    },
});

const dayHeaders = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"];

const momentEvents = props.events.map(e => ({
    id: e.id,
    title: e.title,
    startDate: moment(e.startDate),
    endDate: moment(e.endDate),
}));

const daysInMonth = computed(() => {
    const start = currentMonth.value.clone().startOf('month').startOf('week');
    const end = currentMonth.value.clone().endOf('month').endOf('week');
    const days = [];
    let day = start.clone();

    while (day.isBefore(end)) {
        days.push(day.clone());
        day.add(1, 'day');
    }
    return days;
});

const daysInWeek = computed(() => {
    const start = selectedDay.value ? selectedDay.value.clone().startOf('week') : moment().startOf('week');
    const days = [];
    let day = start.clone();

    for (let i = 0; i < 7; i++) {
        days.push(day.clone());
        day.add(1, 'day');
    }
    return days;
});

const hoursInDay = computed(() => {
    const intervals = [];
    for (let i = 0; i < 24; i++) {
        intervals.push(`${String(i).padStart(2, '0')}:00`);
        intervals.push(`${String(i).padStart(2, '0')}:30`);
    }
    return intervals;
});

const currentViewTitle = computed(() => {
    if (currentView.value === 'month') return currentMonth.value.format('MMMM YYYY');
    if (currentView.value === 'week') return `${daysInWeek.value[0].format('MMM D')} - ${daysInWeek.value[6].format('MMM D, YYYY')}`;
    if (currentView.value === 'day') return selectedDay.value.format('MMMM D, YYYY');
});

const goToPrevious = () => {
    if (currentView.value === 'month') {
        currentMonth.value = currentMonth.value.clone().subtract(1, 'month');
    } else if (currentView.value === 'week') {
        selectedDay.value = selectedDay.value.clone().subtract(1, 'week');
    }
};

const goToNext = () => {
    if (currentView.value === 'month') {
        currentMonth.value = currentMonth.value.clone().add(1, 'month');
    } else if (currentView.value === 'week') {
        selectedDay.value = selectedDay.value.clone().add(1, 'week');
    }
};

const goToWeekView = (day) => {
    selectedDay.value = day;
    currentView.value = 'week';
};

const goToMonthView = () => {
    currentView.value = 'month';
};


const calculateTop = (event) => {
    const startHour = event.startDate.hour();
    const startMinute = event.startDate.minute();
    return startHour * 72 + (startMinute / 60) * 72;
};

const calculateHeight = (event) => {
    const duration = event.endDate.diff(event.startDate, 'minutes');
    return (duration / 60) * 72;
};

const filterEventsForDay = (day) => {
    return momentEvents.filter((event) => day.isSame(moment(event.startDate), 'day'));
};
</script>

<template>
    <div class="mb-7">
        <h2 class="text-lg mb-4">Events You partake in</h2>
        <div class="flex justify-around mb-4 col-span-7 space-x-5">
            <button @click="goToPrevious"><i class="fa-solid fa-angles-left"></i></button>
            <button @click="goToMonthView" class="">{{ currentViewTitle }}</button>
            <button @click="goToNext"><i class="fa-solid fa-angles-right"></i></button>
        </div>


        <div v-if="currentView === 'month'" class="grid grid-cols-7 place-items-center">
            <div class="text-md w-fit" v-for="header in dayHeaders" :key="header">
                {{ header }}
            </div>
            <button v-for="(day, index) in daysInMonth" :key="index"
                :class="['p-2 rounded-lg w-11  mb-1 text-center', day.isSame(currentMonth, 'month') ? 'hover:shadow-lg hover:dark:bg-orange-700 hover:bg-orange-500' : 'text-zinc-400 dark:text-black', (day.isSame(today, 'day') && day.isSame(currentMonth, 'month')) ? 'font-bold ring-1 ring-orange-500 bg-orange-500 bg-opacity-25' : '']"
                @click="day.isSame(currentMonth, 'month') ? goToWeekView(day) : ''">
                <div v-if="day.isSame(currentMonth, 'month')">{{ day.format('D') }}
                    <div class="text-xs mt-1 text-orange-600" v-if="filterEventsForDay(day).length > 0">
                        {{ filterEventsForDay(day).length }}
                    </div>
                </div>
            </button>
        </div>


        <div v-else-if="currentView === 'week'" class="grid grid-cols-7 place-items-center">
            <div v-for="day in daysInWeek" :key="day.format('YYYY-MM-DD')" class="p-2 w-full relative">
                <h3 class="text-center mb-4">{{ day.format('ddd, MMM D') }}</h3>
                <div class="relative">

                    <div v-for="hour in hoursInDay" :key="hour"
                        class="h-9 border-b border-b-orange-600 text-xs select-none">
                        {{ hour }}
                    </div>

                    <Link v-for="event in filterEventsForDay(day)" :key="event.start"
                        :href="route('event.show', event.id)" :style="{
                            top: `${calculateTop(event)}px`,
                            height: `${calculateHeight(event)}px`,
                        }"
                        class="absolute bg-orange-500 text-white rounded-md w-full select-none text-center shadow-lg">
                    <div class="flex flex-col content-around">
                        <div>{{ event.title }}</div>
                        <div>{{ moment(event.startDate).format('HH:mm') }} - {{ moment(event.endDate).format('HH:mm') }}
                        </div>
                    </div>
                    </Link>
                </div>
            </div>
        </div>
    </div>
</template>

<style>
.absolute {
    position: absolute;
    left: 0;
    right: 0;
}

.w-full {
    width: 100%;
}

.resize-handle {
    position: absolute;
    bottom: 0;
    width: 100%;
    height: 5px;
    background-color: rgb(194 65 12);
    cursor: ns-resize;
}
</style>
