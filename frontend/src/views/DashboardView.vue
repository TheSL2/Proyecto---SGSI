<template>
  <div style="padding: 20px;">
    <h1>Dashboard de Métricas ISO 27001</h1>
    <button @click="logout" style="margin-bottom: 20px;">Cerrar Sesión</button>
    <router-link to="/auditorias" style="margin-left: 10px;">Ver Auditorías</router-link>

    <div v-if="metrics" style="display: flex; gap: 20px; margin-top: 20px;">
      <div style="border: 1px solid #ccc; padding: 15px; border-radius: 8px; flex: 1;">
        <h3>Cumplimiento Global ISO</h3>
        <p style="font-size: 24px; font-weight: bold; color: green;">
          {{ metrics.cumplimiento_global_iso?.porcentaje }}
        </p>
        <p>Evaluados: {{ metrics.cumplimiento_global_iso?.total_evaluados }} | Conformes: {{ metrics.cumplimiento_global_iso?.conformes }}</p>
      </div>

      <div style="border: 1px solid #ccc; padding: 15px; border-radius: 8px; flex: 1;">
        <h3>Auditorías por Estado</h3>
        <ul>
          <li v-for="(total, estado) in metrics.auditorias" :key="estado">
            <strong>{{ estado }}:</strong> {{ total }}
          </li>
        </ul>
      </div>

      <div style="border: 1px solid #ccc; padding: 15px; border-radius: 8px; flex: 1;">
        <h3>Hallazgos por Tipo</h3>
        <ul>
          <li v-for="(total, tipo) in metrics.hallazgos" :key="tipo">
            <strong>{{ tipo }}:</strong> {{ total }}
          </li>
        </ul>
      </div>
    </div>
    <div v-else>Cargando métricas...</div>
  </div>
</template>

<script>
import api from '../services/api';
import { useAuthStore } from '../stores/auth';

export default {
  data() {
    return {
      metrics: null
    };
  },
  async mounted() {
    try {
      const response = await api.get('/dashboard');
      this.metrics = response.data;
    } catch (error) {
      console.error('Error al cargar el dashboard', error);
    }
  },
  methods: {
    async logout() {
      const authStore = useAuthStore();
      await authStore.logout();
      this.$router.push('/login');
    }
  }
};
</script>