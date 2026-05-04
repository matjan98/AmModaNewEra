<template>
  <div ref="rootEl" class="main-layout__hours-dropdown">
    <div
      v-for="row in rows"
      :key="row.label"
      class="main-layout__hours-row"
      :class="{ 'main-layout__hours-row--today': row.dayIndex === todayDayIndex }"
    >
      <span class="main-layout__hours-day">{{ row.label }}</span>
      <span class="main-layout__hours-time">{{ row.hours }}</span>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'

defineProps({
  rows: {
    type: Array,
    required: true,
  },
  todayDayIndex: {
    type: Number,
    required: true,
  },
})

const rootEl = ref(null)

defineExpose({ rootEl })
</script>
