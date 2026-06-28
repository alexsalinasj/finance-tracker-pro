<script setup lang="ts">
import { reactive, ref } from 'vue'
import { RouterLink, useRouter } from 'vue-router'
import { ArrowRight } from '@lucide/vue'
import ApiAlert from '../components/ApiAlert.vue'
import Input from '../components/ui/Input.vue'
import PrimaryButton from '../components/ui/PrimaryButton.vue'
import { apiErrorMessage } from '../services/api'
import { useAuthStore } from '../stores/auth'

const auth = useAuthStore()
const router = useRouter()
const error = ref('')
const form = reactive({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
})

async function submit() {
  error.value = ''
  try {
    await auth.register(form)
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
    <p class="eyebrow">Nuevo espacio financiero</p>
    <h1 class="auth-title">Construye una relación más inteligente con tu dinero.</h1>
    <p class="auth-subtitle">Cada cuenta tiene datos privados, categorías propias y reportes independientes.</p>

    <ApiAlert :message="error" />

    <form class="shell-grid" @submit.prevent="submit">
      <Input v-model="form.name" label="Nombre" required />
      <Input v-model="form.email" label="Correo" type="email" required />
      <Input v-model="form.password" label="Contraseña" type="password" min="8" required />
      <Input v-model="form.password_confirmation" label="Confirmar contraseña" type="password" min="8" required />
      <PrimaryButton type="submit" :loading="auth.loading">
        {{ auth.loading ? 'Creando...' : 'Crear cuenta' }}
        <ArrowRight :size="17" />
      </PrimaryButton>
    </form>

    <p class="auth-switch">
      ¿Ya tienes cuenta?
      <RouterLink to="/login">Inicia sesión</RouterLink>
    </p>
  </section>
</template>
