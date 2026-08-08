<template>
  <div style="padding: 20px;">
    <h2>Gestión de Auditorías SGSI</h2>
    <router-link to="/">← Volver al Dashboard</router-link>
    <button @click="$router.push('/auditorias/crear')" style="margin-left: 15px; background-color: #007bff; color: white; padding: 6px 12px; border: none; border-radius: 4px; cursor: pointer;">
      + Registrar Auditoría
    </button>
    <hr>

    <!-- Tabla de Auditorías -->
    <table border="1" cellpadding="8" style="border-collapse: collapse; width: 100%;">
      <thead>
        <tr style="background-color: #f2f2f2;">
          <th>ID</th>
          <th>Título</th>
          <th>Estado</th>
          <th>Fecha Inicio</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="auditoria in auditorias" :key="auditoria.id">
          <td>{{ auditoria.id }}</td>
          <td>{{ auditoria.titulo }}</td>
          <td><strong>{{ auditoria.estado }}</strong></td>
          <td>{{ auditoria.fecha_inicio }}</td>
          <td>
            <button @click="$router.push(`/auditorias/${auditoria.id}/reporte`)" style="margin-right: 5px;">Ver Reporte</button>
            <button @click="$router.push(`/auditorias/${auditoria.id}/editar`)" style="margin-right: 5px; background-color: #ffc107; border: none; padding: 4px 8px; cursor: pointer;">Editar</button>
            <button @click="eliminarAuditoria(auditoria.id)" style="background-color: #dc3545; color: white; border: none; padding: 4px 8px; cursor: pointer;">Eliminar</button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
import api from '../services/api';

export default {
  data() {
    return {
      auditorias: []
    };
  },
  async mounted() {
    await this.cargarAuditorias();
  },
  methods: {
    async cargarAuditorias() {
      try {
        const res = await api.get('/auditorias');
        this.auditorias = res.data.data || res.data;
      } catch (err) {
        console.error('Error al cargar auditorías:', err);
      }
    },
    async eliminarAuditoria(id) {
      if (confirm('¿Estás seguro de eliminar esta auditoría?')) {
        try {
          await api.delete(`/auditorias/${id}`);
          await this.cargarAuditorias();
        } catch (err) {
          alert(err.response?.data?.message || 'Error al eliminar la auditoría');
        }
      }
    }
  }
};
</script>