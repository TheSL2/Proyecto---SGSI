import { createRouter, createWebHistory } from 'vue-router';
import LoginView from '../views/LoginView.vue';
import DashboardView from '../views/DashboardView.vue';
import AuditoriasView from '../views/AuditoriasView.vue';
import AuditoriaFormView from '../views/AuditoriaFormView.vue';
import AuditoriaReporteView from '../views/AuditoriaReporteView.vue';

const routes = [
  { path: '/', name: 'dashboard', component: DashboardView },
  { path: '/login', name: 'login', component: LoginView },
  { path: '/auditorias', name: 'auditorias', component: AuditoriasView },
  { path: '/auditorias/crear', name: 'auditoria-crear', component: AuditoriaFormView },
  { path: '/auditorias/:id/editar', name: 'auditoria-editar', component: AuditoriaFormView, props: true },
  { path: '/auditorias/:id/reporte', name: 'auditoria-reporte', component: AuditoriaReporteView, props: true }
];

const router = createRouter({
  history: createWebHistory(),
  routes
});

export default router;