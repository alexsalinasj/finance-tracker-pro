<script setup lang="ts">
import { computed, watchEffect } from 'vue'
import { useRoute } from 'vue-router'
import AppNavbar from './components/AppNavbar.vue'
import { useAuthStore } from './stores/auth'

const route = useRoute()
const auth = useAuthStore()
const isAuthPage = computed(() => ['login', 'register'].includes(String(route.name)))

watchEffect(() => {
  if (auth.isAuthenticated && !auth.user) {
    void auth.fetchMe()
  }
})
</script>

<template>
  <AppNavbar v-if="auth.isAuthenticated && !isAuthPage" />
  <main :class="isAuthPage ? 'auth-shell' : 'app-shell'">
    <RouterView />
  </main>
</template>
