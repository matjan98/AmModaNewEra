<template>
  <component
    :is="expandable ? 'button' : 'div'"
    :type="expandable ? 'button' : undefined"
    class="main-layout__open-status"
    :class="{
      'main-layout__open-status--expanded': expandable && expanded,
      'main-layout__open-status--open-today': isOpenToday,
      'main-layout__open-status--static': !expandable,
    }"
    :role="expandable ? undefined : 'status'"
    @click="onClick"
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
      v-if="expandable"
      :name="expanded ? 'expand_less' : 'expand_more'"
      size="20px"
      class="main-layout__hours-chevron"
    />
  </component>
</template>

<script setup>
const props = defineProps({
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
    default: false,
  },
  expandable: {
    type: Boolean,
    default: true,
  },
})

const emit = defineEmits(['update:expanded'])

function onClick() {
  if (!props.expandable) return
  emit('update:expanded', !props.expanded)
}
</script>
