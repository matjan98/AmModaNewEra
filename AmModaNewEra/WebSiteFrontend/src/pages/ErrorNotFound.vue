<template>
  <div class="error-not-found">
    <div class="error-not-found__inner">
      <img
        :src="logoSrc"
        alt="A&M Moda Damska"
        class="error-not-found__logo"
        width="224"
        height="42"
        decoding="async"
      >

      <p class="error-not-found__code" aria-hidden="true">404</p>

      <h1 class="error-not-found__title">Nie znaleziono strony</h1>
      <p class="error-not-found__message">
        Ta podstrona nie istnieje. Wróć na stronę główną butiku A&amp;M Moda Damska.
      </p>

      <router-link to="/" class="error-not-found__home-btn">
        Strona główna
      </router-link>
    </div>
  </div>
</template>

<script setup>
import { useHead } from '@unhead/vue'
import logoSrc from '../assets/logo.webp'

useHead({
  title: 'Nie znaleziono strony - A&M Moda Damska',
  meta: [{ name: 'robots', content: 'noindex, nofollow' }],
})
</script>

<style scoped>
.error-not-found {
  min-height: 100vh;
  min-height: 100dvh;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 24px 16px;
  box-sizing: border-box;
  background: var(--am-page-bg, #bababa);
  color: rgba(255, 255, 255, 0.92);
  font-family: 'Poppins', sans-serif;
}

.error-not-found__inner {
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  width: 100%;
  max-width: 420px;
  padding: 32px 24px 28px;
  border-radius: 16px;
  background: rgba(14, 14, 18, 0.55);
  border: 1px solid rgba(255, 255, 255, 0.12);
  box-shadow:
    inset 0 1px 0 rgba(255, 255, 255, 0.08),
    0 14px 34px rgba(0, 0, 0, 0.35);
  backdrop-filter: blur(14px) saturate(1.1);
  -webkit-backdrop-filter: blur(14px) saturate(1.1);
}

.error-not-found__logo {
  display: block;
  width: auto;
  max-width: min(224px, 70vw);
  height: auto;
  max-height: 42px;
  object-fit: contain;
  margin-bottom: 28px;
}

.error-not-found__code {
  margin: 0 0 8px;
  font-size: clamp(3.5rem, 18vw, 5.5rem);
  font-weight: 600;
  line-height: 1;
  letter-spacing: 0.06em;
  color: rgba(255, 255, 255, 0.22);
  text-shadow: 0 2px 12px rgba(0, 0, 0, 0.35);
}

.error-not-found__title {
  margin: 0 0 12px;
  font-size: clamp(1.25rem, 4.5vw, 1.6rem);
  font-weight: 600;
  letter-spacing: 0.02em;
  color: rgba(255, 255, 255, 0.96);
  text-shadow:
    0 2px 8px rgba(0, 0, 0, 0.85),
    0 0 22px rgba(0, 0, 0, 0.55);
}

.error-not-found__message {
  margin: 0 0 24px;
  font-size: clamp(0.92rem, 2.8vw, 1rem);
  font-weight: 400;
  line-height: 1.45;
  color: rgba(255, 255, 255, 0.72);
}

.error-not-found__home-btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-width: 160px;
  padding: 12px 22px;
  border-radius: 999px;
  border: 1px solid rgba(0, 0, 0, 0.12);
  background: #ffffff;
  color: #141414;
  font-family: inherit;
  font-size: 1rem;
  font-weight: 600;
  letter-spacing: 0.02em;
  text-decoration: none;
  text-transform: uppercase;
  box-shadow:
    0 10px 28px rgba(0, 0, 0, 0.28),
    0 2px 10px rgba(0, 0, 0, 0.18);
  transition: opacity 0.2s ease, transform 0.2s ease, box-shadow 0.2s ease;
  -webkit-tap-highlight-color: transparent;
}

.error-not-found__home-btn:hover {
  opacity: 0.92;
  transform: scale(1.02);
  box-shadow:
    0 12px 32px rgba(0, 0, 0, 0.32),
    0 3px 12px rgba(0, 0, 0, 0.22);
}

.error-not-found__home-btn:focus-visible {
  outline: 2px solid rgba(230, 25, 113, 0.85);
  outline-offset: 3px;
}
</style>
