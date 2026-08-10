import './bootstrap';

import Alpine from 'alpinejs';
import dashboardKpis from './pages/dashboard';
import { auditoriasIndex, auditoriaShow, auditoriaForm } from './pages/auditorias';
import { checklistsIndex, checklistShow, checklistForm } from './pages/checklists';
import { hallazgosIndex, hallazgoShow, hallazgoForm } from './pages/hallazgos';
import { accionesCorrectivasIndex, accionCorrectivaShow, accionCorrectivaForm } from './pages/acciones-correctivas';

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

Alpine.start();