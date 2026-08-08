<template>
  <div style="max-width: 400px; margin: 50px auto; padding: 20px; border: 1px solid #ccc; border-radius: 8px;">
    <h2>Iniciar Sesión - SGSI</h2>
    <form @submit.prevent="handleLogin">
      <div style="margin-bottom: 15px;">
        <label>Correo Electrónico:</label><br>
        <input type="email" v-model="email" required style="width: 100%; padding: 8px;" />
      </div>
      <div style="margin-bottom: 15px;">
        <label>Contraseña:</label><br>
        <input type="password" v-model="password" required style="width: 100%; padding: 8px;" />
      </div>
      <p v-if="errorMessage" style="color: red;">{{ errorMessage }}</p>
      <button type="submit" style="width: 100%; padding: 10px; background-color: #007bff; color: white; border: none; border-radius: 4px;">Ingresar</button>
    </form>
  </div>
</template>

<script>
import { useAuthStore } from '../stores/auth';

export default {
  data() {
    return {
      email: '',
      password: '',
      errorMessage: ''
    };
  },
  methods: {
    async handleLogin() {
      const authStore = useAuthStore();
      try {
        await authStore.login({ email: this.email, password: this.password });
        this.$router.push('/');
      } catch (error) {
        this.errorMessage = error.response?.data?.message || 'Error al iniciar sesión';
      }
    }
  }
};
</script>