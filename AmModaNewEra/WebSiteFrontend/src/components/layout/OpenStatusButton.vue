<template>
  <button
    type="button"
    class="main-layout__open-status"
    :class="{ 'main-layout__open-status--expanded': expanded }"
    @click="$emit('update:expanded', !expanded)"
  >
    <span>
      <span v-if="isOpenToday">
        <span class="main-layout__hours-label">Dzisiaj otwarte:</span>
        <span class="main-layout__hours-value">{{ todayHours }}</span>
      </span>
      <span v-else>
        Dzisiaj zamknięte
      </span>
    </span>
    <q-icon
      :name="expanded ? 'expand_less' : 'expand_more'"
      size="20px"
      class="main-layout__hours-chevron"
    />
  </button>
</template>

<script setup>
defineProps({
  isOpenToday: {
    type: Boolean,
    required: true,
  },
  todayHours: {
    type: String,
    required: true,
  },
  expanded: {
    type: Boolean,
    required: true,
  },
})

defineEmits(['update:expanded'])
</script>
