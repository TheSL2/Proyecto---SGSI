<template>
  <div style="padding: 20px;" v-if="reporte">
    <h2>Reporte Consolidado: {{ reporte.auditoria?.titulo || reporte.titulo }}</h2>
    <router-link to="/auditorias">← Volver a Auditorías</router-link>
    <hr>

    <p><strong>Estado:</strong> {{ reporte.auditoria?.estado || reporte.estado }}</p>

    <div v-if="reporte.resumen_cumplimiento" style="border: 1px solid #ccc; padding: 15px; border-radius: 6px; margin: 15px 0;">
      <h4>Cumplimiento ISO 27001</h4>
      <ul>
        <li><strong>Total Controles:</strong> {{ reporte.resumen_cumplimiento.total_controles }}</li>
        <li><strong>Conformes:</strong> {{ reporte.resumen_cumplimiento.conformes }}</li>
        <li><strong>Porcentaje:</strong> {{ reporte.resumen_cumplimiento.porcentaje_cumplimiento }}</li>
      </ul>
    </div>

    <h4>Hallazgos Registrados</h4>
    <ul v-if="reporte.auditoria?.hallazgos?.length">
      <li v-for="h in reporte.auditoria.hallazgos" :key="h.id">
        <strong>[{{ h.tipo_hallazgo }}]</strong> - {{ h.descripcion }} (Estado: {{ h.estado }})
      </li>
    </ul>
    <p v-else>No hay hallazgos registrados para esta auditoría.</p>
  </div>
  <div v-else style="padding: 20px;">Cargando reporte...</div>
</template>

<script>
import api from '../services/api';

export default {
  props: ['id'],
  data() {
    return {
      reporte: null
    };
  },
  async mounted() {
    try {
      const res = await api.get(`/auditorias/${this.id}/reporte`);
      this.reporte = res.data;
    } catch (err) {
      alert('Error al cargar el reporte');
      this.$router.push('/auditorias');
    }
  }
};
</script>