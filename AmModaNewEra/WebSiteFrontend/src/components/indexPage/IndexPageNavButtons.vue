<template>
  <div class="index-page-nav-buttons index-page-nav-buttons__fixed">
    <button
      v-if="activeTab === 'info'"
      type="button"
      class="index-page-nav-buttons__btn index-page-nav-buttons__btn--right text-primary"
      @click="$emit('goToGallery')"
    >
      <span>Galeria</span>
      <q-icon name="chevron_right" size="24px" class="index-page-nav-buttons__arrow" />
    </button>
    <button
      v-if="activeTab === 'gallery'"
      type="button"
      class="index-page-nav-buttons__btn index-page-nav-buttons__btn--left text-primary"
      @click="$emit('goToInfo')"
    >
      <q-icon name="chevron_left" size="24px" class="index-page-nav-buttons__arrow" />
      <span>Wróć</span>
    </button>
  </div>
</template>

<script setup>
defineProps({
  activeTab: {
    type: String,
    required: true,
  },
})

defineEmits(['goToGallery', 'goToInfo'])
</script>

<style scoped>
.index-page-nav-buttons__fixed {
  position: fixed;
  top: calc(var(--main-layout-header-height, 140px) + 20px);
  left: 0;
  right: 0;
  bottom: auto;
  z-index: 1000;
  pointer-events: none;
}

.index-page-nav-buttons__fixed .index-page-nav-buttons__btn {
  pointer-events: auto;
}

.index-page-nav-buttons__btn {
  position: fixed;
  top: calc(var(--main-layout-header-height, 140px) + 20px);
  font-weight: 500;
  display: inline-flex;
  align-items: center;
  gap: 3px;
  padding: 10px 12px;
  background: #ffffff;
  border: 1px solid rgba(0, 0, 0, 0.12);
  border-radius: 999px;
  box-shadow:
    0 10px 28px rgba(0, 0, 0, 0.28),
    0 2px 10px rgba(0, 0, 0, 0.18);
  cursor: pointer;
  font-family: 'Poppins', sans-serif;
  font-size: clamp(1.375rem, 0.7vw + 1.15rem, 1.625rem);
  letter-spacing: 0.02em;
  line-height: 1.2;
  text-transform: uppercase;
  transition: opacity 0.2s ease, transform 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
}

.index-page-nav-buttons__btn:hover {
  opacity: 0.9;
  transform: scale(1.02);
  box-shadow:
    0 12px 32px rgba(0, 0, 0, 0.32),
    0 3px 12px rgba(0, 0, 0, 0.22);
}

.index-page-nav-buttons__btn--right {
  right: 8px;
  left: auto;
  padding-right: 6px;
}

.index-page-nav-buttons__btn--left {
  left: 8px;
  right: auto;
  padding-left: 6px;
}

@media (min-width: 1000px) {
  .index-page-nav-buttons__btn--right {
    right: 16px;
  }

  .index-page-nav-buttons__btn--left {
    left: 16px;
  }
}

.index-page-nav-buttons__arrow {
  animation: index-page__arrow-move 2s ease-in-out infinite;
}

@keyframes index-page__arrow-move {
  0%, 100% { transform: translateX(0); }
  50% { transform: translateX(4px); }
}

.index-page-nav-buttons__btn--left .index-page-nav-buttons__arrow {
  animation-name: index-page__arrow-move-left;
}

@keyframes index-page__arrow-move-left {
  0%, 100% { transform: translateX(0); }
  50% { transform: translateX(-4px); }
}
</style>

