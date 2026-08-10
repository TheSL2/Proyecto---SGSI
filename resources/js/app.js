import './bootstrap';

import Alpine from 'alpinejs';
import dashboardKpis from './pages/dashboard';
import { auditoriasIndex, auditoriaShow, auditoriaForm } from './pages/auditorias';
import { checklistsIndex, checklistShow, checklistForm } from './pages/checklists';
import { hallazgosIndex, hallazgoShow, hallazgoForm } from './pages/hallazgos';
import { accionesCorrectivasIndex, accionCorrectivaShow, accionCorrectivaForm } from './pages/acciones-correctivas';
import { evidenciasIndex, evidenciaShow, evidenciaForm } from './pages/evidencias';
import { areasIndex } from './pages/areas';
import { usuariosIndex } from './pages/usuarios';
import { requisitosIsoIndex } from './pages/requisitos-iso';

window.Alpine = Alpine;

Alpine.data('dashboardKpis', dashboardKpis);
Alpine.data('auditoriasIndex', auditoriasIndex);
Alpine.data('auditoriaShow', auditoriaShow);
Alpine.data('auditoriaForm', auditoriaForm);
Alpine.data('checklistsIndex', checklistsIndex);
Alpine.data('checklistShow', checklistShow);
Alpine.data('checklistForm', checklistForm);
Alpine.data('hallazgosIndex', hallazgosIndex);
Alpine.data('hallazgoShow', hallazgoShow);
Alpine.data('hallazgoForm', hallazgoForm);
Alpine.data('accionesCorrectivasIndex', accionesCorrectivasIndex);
Alpine.data('accionCorrectivaShow', accionCorrectivaShow);
Alpine.data('accionCorrectivaForm', accionCorrectivaForm);
Alpine.data('evidenciasIndex', evidenciasIndex);
Alpine.data('evidenciaShow', evidenciaShow);
Alpine.data('evidenciaForm', evidenciaForm);
Alpine.data('areasIndex', areasIndex);
Alpine.data('usuariosIndex', usuariosIndex);
Alpine.data('requisitosIsoIndex', requisitosIsoIndex);

Alpine.start();