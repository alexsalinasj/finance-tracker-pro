<script setup lang="ts">
import { reactive, ref } from 'vue'
import { RouterLink, useRouter } from 'vue-router'
import { ArrowRight } from '@lucide/vue'
import ApiAlert from '../components/ApiAlert.vue'
import PrimaryButton from '../components/ui/PrimaryButton.vue'
import Input from '../components/ui/Input.vue'
import { apiErrorMessage } from '../services/api'
import { useAuthStore } from '../stores/auth'

const auth = useAuthStore()
const router = useRouter()
const error = ref('')
const form = reactive({
  email: 'demo@financepro.test',
  password: 'password',
})

async function submit() {
  error.value = ''
  try {
    await auth.login(form)
    await router.push({ name: 'dashboard' })
  } catch (err) {
    error.value = apiErrorMessage(err)
  }
}
</script>

<template>
  <section class="auth-card">
    <span class="auth-mark">
      <img class="brand-logo" src="/logo-sin-bg-app.png" alt="Finance Tracker Pro" />
    </span>
    <p class="eyebrow">Finanzas privadas</p>
    <h1 class="auth-title">Controla tu dinero con precisión.</h1>
    <p class="auth-subtitle">Visualiza ingresos, gastos, metas y presupuestos en una experiencia clara y segura.</p>

    <ApiAlert :message="error" />

    <form class="shell-grid" @submit.prevent="submit">
      <Input v-model="form.email" label="Correo" type="email" required />
      <Input v-model="form.password" label="Contraseña" type="password" required />
      <PrimaryButton type="submit" :loading="auth.loading">
        {{ auth.loading ? 'Entrando...' : 'Iniciar sesión' }}
        <ArrowRight :size="17" />
      </PrimaryButton>
    </form>

    <p class="auth-switch">
      ¿No tienes cuenta?
      <RouterLink to="/register">Crear cuenta</RouterLink>
    </p>
  </section>
</template>
