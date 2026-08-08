<template>
  <div style="padding: 20px; max-width: 600px; margin: 0 auto;">
    <h2>{{ esEdicion ? 'Editar Auditoría #' + id : 'Registrar Nueva Auditoría' }}</h2>
    <router-link to="/auditorias">← Volver a Auditorías</router-link>
    <hr>

    <form @submit.prevent="guardar">
      <div style="margin-bottom: 15px;">
        <label>Título:</label><br>
        <input type="text" v-model="form.titulo" required style="width: 100%; padding: 8px;" />
      </div>

      <div style="margin-bottom: 15px;">
        <label>Objetivo:</label><br>
        <textarea v-model="form.objetivo" required style="width: 100%; padding: 8px;"></textarea>
      </div>

      <div style="margin-bottom: 15px;">
        <label>Alcance:</label><br>
        <textarea v-model="form.alcance" required style="width: 100%; padding: 8px;"></textarea>
      </div>

      <div style="margin-bottom: 15px;">
        <label>Fecha de Inicio:</label><br>
        <input type="date" v-model="form.fecha_inicio" required style="width: 100%; padding: 8px;" />
      </div>

      <div style="margin-bottom: 15px;">
        <label>Fecha de Fin:</label><br>
        <input type="date" v-model="form.fecha_fin" required style="width: 100%; padding: 8px;" />
      </div>

      <div style="margin-bottom: 15px;">
        <label>Estado:</label><br>
        <select v-model="form.estado" style="width: 100%; padding: 8px;">
          <option value="Planificada">Planificada</option>
          <option value="En Proceso">En Proceso</option>
          <option value="Completada">Completada</option>
          <option value="Cancelada" v-if="esEdicion">Cancelada</option>
        </select>
      </div>

      <div v-if="!esEdicion" style="margin-bottom: 15px;">
        <label>ID Auditor Líder:</label><br>
        <input type="number" v-model="form.auditor_lider_id" required style="width: 100%; padding: 8px;" />
      </div>

      <button type="submit" style="background-color: #28a745; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer;">
        {{ esEdicion ? 'Actualizar' : 'Guardar' }}
      </button>
    </form>
  </div>
</template>

<script>
import api from '../services/api';

export default {
  props: ['id'],
  data() {
    const hoy = new Date().toISOString().split('T')[0];
    return {
      esEdicion: !!this.id,
      form: {
        titulo: '',
        objetivo: 'Evaluar los controles de seguridad de la información.',
        alcance: 'Sistemas centrales e infraestructura cloud.',
        fecha_inicio: hoy,
        fecha_fin: hoy,
        estado: 'Planificada',
        auditor_lider_id: 1
      }
    };
  },
  async mounted() {
    if (this.esEdicion) {
      try {
        const res = await api.get(`/auditorias/${this.id}`);
        this.form = res.data.data || res.data;
      } catch (err) {
        alert('Error al cargar la auditoría');
        this.$router.push('/auditorias');
      }
    }
  },
  methods: {
    async guardar() {
      try {
        if (this.esEdicion) {
          await api.put(`/auditorias/${this.id}`, this.form);
        } else {
          await api.post('/auditorias', this.form);
        }
        this.$router.push('/auditorias');
      } catch (err) {
        alert(err.response?.data?.message || 'Error al guardar');
      }
    }
  }
};
</script>