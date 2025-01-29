<script setup>

import { ref, computed } from 'vue';
import moment from 'moment';
moment.updateLocale('en', {
    week: {
        dow: 1,
        doy: 4,
    }
});
const currentMonth = ref(moment());
const today = ref(moment());
var currentView = ref('month');
var selectedDay = ref(null);
let eventDuration = ref(30);
const events = ref([]);
const isResizing = ref(false);
const resizingEvent = ref(null);
const resizeStartY = ref(null);
const isDragging = ref(false);
const draggingEvent = ref(null);
const dragStartY = ref(null);
const dragStartX = ref(null);
const dragStartTime = ref(null);

const emit = defineEmits(['selectedEventTimeframe']);

const dayHeaders = ["Monday", "Tuesday", "Wednesday", "Thursday", "Firday", "Saturday", "Sunday"];

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
    const start = selectedDay.value.clone().startOf('week');
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
        currentMonth.value = selectedDay.value.clone();
    } else if (currentView.value === 'day') {
        selectedDay.value = selectedDay.value.clone().subtract(1, 'day');
    }
};

const goToNext = () => {
    if (currentView.value === 'month') {
        currentMonth.value = currentMonth.value.clone().add(1, 'month');
    } else if (currentView.value === 'week') {
        selectedDay.value = selectedDay.value.clone().add(1, 'week');
        currentMonth.value = selectedDay.value.clone();
    } else if (currentView.value === 'day') {
        selectedDay.value = selectedDay.value.clone().add(1, 'day');
    }
};

const goToWeekView = (day) => {
    if (!day.isSame(currentMonth.value, 'month')) {
        currentMonth.value = day.clone();
    } else {
        selectedDay.value = day;
        currentView.value = 'week';
    }
};

const goToMonthView = () => {
    currentView.value = 'month';
};

const goUpCalendar = () => {
    switch (currentView.value) {
        case 'week':
            goToMonthView()
            break;
        case 'day':
            goToWeekView(selectedDay.value)
            break;
        default:
            break;
    }
};

const createEvent = (day, hour) => {
    const [hourPart, minutePart] = hour.split(':').map(Number);
    var start = day.clone().hour(hourPart).minute(minutePart);
    var end = start.clone().add(eventDuration.value, 'minutes');

    if(end.day() != start.day()){
        end.day(end.day()-1);
        end.hour(23);
        end.minutes(59);
        start = end.clone().subtract(eventDuration.value - 1,'minutes');
    }

    const newEvent = {
        start,
        end,
        day: day.format('YYYY-MM-DD'),
    };

    events.value.pop();
    events.value.push(newEvent);
    emit('selectedEventTimeframe', { start: start.format('YYYY-MM-DD HH:mm:ss'), end: end.format('YYYY-MM-DD HH:mm:ss') });
};


const calculateTop = (event) => {
    const startHour = event.start.hour();
    const startMinute = event.start.minute();
    return startHour * 72 + (startMinute / 60) * 72;
};

const calculateHeight = (event) => {
    const duration = event.end.diff(event.start, 'minutes');
    return (duration / 60) * 72;
};

const startResizing = (event, e) => {
    isResizing.value = true;
    resizingEvent.value = event;
    resizeStartY.value = e.clientY;
    document.addEventListener('mousemove', handleResize);
    document.addEventListener('mouseup', endResizing);
};

const handleResize = (e) => {
    if (!isResizing.value || !resizingEvent.value) return;

    const deltaY = e.clientY - resizeStartY.value;
    const minutesPerPixel = 30 / 72;
    let extraMinutes = deltaY * minutesPerPixel;

    extraMinutes = Math.round(extraMinutes / 15) * 15;

    if (extraMinutes !== 0) {
        const newEnd = resizingEvent.value.end.clone().add(extraMinutes, 'minutes');
        if (newEnd.diff(resizingEvent.value.start, 'minutes') >= 15) {
            if (newEnd.day() != resizingEvent.value.start.day()) {
                newEnd.day(newEnd.day() - 1)
                newEnd.hour(23)
                newEnd.minute(59)
            }

            resizingEvent.value.end = newEnd;
            resizeStartY.value = e.clientY;
            eventDuration.value = resizingEvent.value.end.diff(resizingEvent.value.start, 'minutes')
        }
    }
};

const endResizing = () => {
    if (isResizing.value) {
        emit('selectedEventTimeframe', { start: resizingEvent.value.start.format('YYYY-MM-DD HH:mm:ss'), end: resizingEvent.value.end.format('YYYY-MM-DD HH:mm:ss') });
        isResizing.value = false;
        resizingEvent.value = null;
        resizeStartY.value = null;
        document.removeEventListener('mousemove', handleResize);
        document.removeEventListener('mouseup', endResizing);
    }
};

const startDragging = (event, e) => {
    isDragging.value = true;
    draggingEvent.value = event;
    dragStartY.value = e.clientY;
    dragStartX.value = e.clientX;
    dragStartTime.value = event.start.clone();
    document.addEventListener('mousemove', handleDragging);
    document.addEventListener('mouseup', endDragging);
};

const handleDragging = (e) => {
    if (!isDragging.value || !draggingEvent.value || isResizing.value) return;

    if (draggingEvent.value.end.minute() % 15 != 0) {
        draggingEvent.value.end.minute(draggingEvent.value.end.minute() + 1)
    }

    const deltaY = e.clientY - dragStartY.value;
    const minutesPerPixel = 30 / 36;
    let minutesDelta = Math.round((deltaY * minutesPerPixel) / 15) * 15;


    if (minutesDelta !== 0) {
        const newStart = dragStartTime.value.clone().add(minutesDelta, 'minutes');
        const duration = draggingEvent.value.end.diff(draggingEvent.value.start, 'minutes');
        const newEnd = newStart.clone().add(duration, 'minutes');

        if (newEnd.day() != draggingEvent.value.start.day()) {
            newEnd.day(newEnd.day() - 1)
            newEnd.hour(23)
            newEnd.minute(59)
        }

        if (newStart.day() == draggingEvent.value.start.day() && (duration-newEnd.diff(newStart, 'minutes') == 1 || (duration-newEnd.diff(newStart, 'minutes') == 0))) {
            draggingEvent.value.start = newStart;
            draggingEvent.value.end = newEnd;
        }
    }

};

const endDragging = () => {
    if (isDragging.value) {
        if(draggingEvent.value.end.day() != draggingEvent.value.start.day()){
            draggingEvent.value.end.subtract(1,'minute');
        }
        emit('selectedEventTimeframe', { start: draggingEvent.value.start.format('YYYY-MM-DD HH:mm:ss'), end: draggingEvent.value.end.format('YYYY-MM-DD HH:mm:ss') });
        isDragging.value = false;
        draggingEvent.value = null;
        dragStartY.value = null;
        dragStartTime.value = null;
        document.removeEventListener('mousemove', handleDragging);
        document.removeEventListener('mouseup', endDragging);
    }
};

</script>

<template>
    <div class="mb-7">
        <h2 class="text-lg mb-4">Select when event is taking place</h2>
        <div class="flex justify-around mb-4 col-span-7 space-x-5">
            <button @click.prevent="goToPrevious"><i class="fa-solid fa-angles-left"></i></button>
            <button @click.prevent="goUpCalendar" class="">{{ currentViewTitle }}</button>
            <button @click.prevent="goToNext"><i class="fa-solid fa-angles-right"></i></button>
        </div>

        <div v-if="currentView === 'month'" class="grid grid-cols-7 place-items-center">
            <div class="text-md w-fit" v-for="header in dayHeaders">
                {{ header }}
            </div>
            <button v-for="(day, index) in daysInMonth" :key="index"
                :class="['p-2 rounded-lg w-11  mb-1 text-center', day.isSame(currentMonth, 'month') ? 'hover:shadow-lg hover:dark:bg-orange-700 hover:bg-orange-500' : 'text-zinc-400 dark:text-black', (day.isSame(today, 'day') && day.isSame(currentMonth, 'month')) ? 'font-bold ring-1 ring-orange-500 bg-orange-500 bg-opacity-25' : '']"
                @click="day.isSame(currentMonth, 'month') ? goToWeekView(day) : ''">
                <span v-if="day.isSame(currentMonth, 'month')">{{ day.format('D') }}</span>
            </button>
        </div>

        <div v-else-if="currentView === 'week'" class="grid grid-cols-7 place-items-center overflow-auto scrollbar-hide overscroll-contain scrollable">
            <div v-for="day in daysInWeek" :key="day.format('YYYY-MM-DD')"
            :class="['p-2 w-full relative', day.isSame(today, 'day') ? 'bg-orange-500 bg-opacity-25' : '']">

        <h3 class="text-center mb-4">{{ day.format('ddd, MMM D') }}</h3>
        </div>
            <div v-for="day in daysInWeek" :key="day.format('YYYY-MM-DD')"
                :class="['p-2 w-full relative', day.isSame(today, 'day') ? 'bg-orange-500 bg-opacity-25' : '']">
                <div class="relative">
                    <div v-for="hour in hoursInDay" :key="hour"
                        :class="['h-9 border-b border-b-orange-600 text-xs select-none']"
                        @click="createEvent(day, hour)">
                        {{ hour }}
                    </div>
                    <div v-for="event in events.filter(e => e.day === day.format('YYYY-MM-DD'))"
                        :key="event.start.format()" :style="{
                            top: `${calculateTop(event)}px`,
                            height: `${calculateHeight(event)}px`,
                        }"
                        class="absolute bg-orange-500 text-white rounded-md cursor-move w-full select-none content-center text-center"
                        @mousedown="(e) => startDragging(event, e)">
                        {{ event.start.format('HH:mm') }} - {{ event.end.format('HH:mm') }}
                        <div class="resize-handle" @mousedown="(e) => startResizing(event, e)"></div>
                    </div>
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

.cursor-move {
    cursor: move;
}

.resize-handle {
    position: absolute;
    bottom: 0;
    width: 100%;
    height: 5px;
    background-color: rgb(194 65 12);
    cursor: ns-resize;
}

.scrollable {
    height: 60rem;
}
</style>
