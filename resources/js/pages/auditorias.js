export const ESTADO_BADGE = {
    'Borrador': 'bg-gray-100 text-gray-700',
    'Planificada': 'bg-blue-100 text-blue-700',
    'En Ejecución': 'bg-yellow-100 text-yellow-700',
    'En Revisión de Informe': 'bg-purple-100 text-purple-700',
    'Cerrada': 'bg-green-100 text-green-700',
};

const FLUJO_ESTADOS = [
    'Borrador',
    'Planificada',
    'En Ejecución',
    'En Revisión de Informe',
    'Cerrada',
];

function mensajeError(err, fallback) {
    if (err.response?.status === 422) {
        return err.response.data?.message ?? 'No se pudo completar la operación (regla de negocio).';
    }
    if (err.response?.status === 401) {
        return 'Tu sesión expiró. Recarga la página e inicia sesión de nuevo.';
    }
    if (err.response?.status === 404) {
        return 'Esa auditoría no existe.';
    }
    return fallback;
}

export function auditoriasIndex() {
    return {
        loading: true,
        error: null,
        auditorias: [],

        init() {
            this.cargar();
        },

        cargar() {
            this.loading = true;
            this.error = null;

            window.axios
                .get('/api/auditorias')
                .then((response) => {
                    this.auditorias = response.data.data ?? response.data;
                })
                .catch((err) => {
                    this.error = mensajeError(err, 'No se pudieron cargar las auditorías. Intenta de nuevo.');
                })
                .finally(() => {
                    this.loading = false;
                });
        },

        badgeClase(estado) {
            return ESTADO_BADGE[estado] ?? 'bg-gray-100 text-gray-700';
        },

        nombresAreas(auditoria) {
            return (auditoria.areas ?? []).map((a) => a.nombre).join(', ') || '—';
        },
    };
}

export function auditoriaShow(id) {
    return {
        loading: true,
        error: null,
        auditoria: null,

        descargandoInforme: false,
        errorInforme: null,

        eliminando: false,

        init() {
            this.cargar();
        },

        cargar() {
            this.loading = true;
            this.error = null;

            window.axios
                .get(`/api/auditorias/${id}`)
                .then((response) => {
                    this.auditoria = response.data.data ?? response.data;
                })
                .catch((err) => {
                    this.error = mensajeError(err, 'No se pudo cargar la auditoría. Intenta de nuevo.');
                })
                .finally(() => {
                    this.loading = false;
                });
        },

        badgeClase(estado) {
            return ESTADO_BADGE[estado] ?? 'bg-gray-100 text-gray-700';
        },

        descargarInforme() {
            this.descargandoInforme = true;
            this.errorInforme = null;

            window.axios
                .get(`/api/auditorias/${id}/informe`, { responseType: 'blob' })
                .then((response) => {
                    const url = window.URL.createObjectURL(new Blob([response.data], { type: 'application/pdf' }));
                    const enlace = document.createElement('a');
                    enlace.href = url;
                    enlace.download = `informe-auditoria-${id}.pdf`;
                    document.body.appendChild(enlace);
                    enlace.click();
                    document.body.removeChild(enlace);
                    window.URL.revokeObjectURL(url);
                })
                .catch(async (err) => {
                    if (err.response?.data instanceof Blob) {
                        try {
                            const texto = await err.response.data.text();
                            const json = JSON.parse(texto);
                            this.errorInforme = json.message ?? 'No se pudo generar el informe.';
                        } catch {
                            this.errorInforme = 'No se pudo generar el informe.';
                        }
                    } else {
                        this.errorInforme = mensajeError(err, 'No se pudo generar el informe.');
                    }
                })
                .finally(() => {
                    this.descargandoInforme = false;
                });
        },

        eliminar() {
            if (!window.confirm('¿Seguro que quieres eliminar esta auditoría? Esta acción no se puede deshacer.')) {
                return;
            }

            this.eliminando = true;
            this.error = null;

            window.axios
                .delete(`/api/auditorias/${id}`)
                .then(() => {
                    window.location.href = '/auditorias';
                })
                .catch((err) => {
                    this.error = mensajeError(err, 'No se pudo eliminar la auditoría.');
                    this.eliminando = false;
                });
        },
    };
}

export function auditoriaForm(modo, id = null) {
    return {
        modo,
        loading: modo === 'editar',
        guardando: false,
        error: null,

        opcionesEstado: FLUJO_ESTADOS,

        form: {
            titulo: '',
            fecha_inicio: '',
            fecha_fin: '',
            objetivo: '',
            alcance: '',
            estado: 'Borrador',
            auditor_lider_id: '',
            equipo_auditor: [],
            areas: [],
            conclusiones: '',
        },

        init() {
            if (this.modo === 'editar') {
                this.cargar();
            }
        },

        cargar() {
            this.loading = true;
            this.error = null;

            window.axios
                .get(`/api/auditorias/${id}`)
                .then((response) => {
                    const a = response.data.data ?? response.data;

                    this.form = {
                        titulo: a.titulo ?? '',
                        fecha_inicio: a.fecha_inicio ?? '',
                        fecha_fin: a.fecha_fin ?? '',
                        objetivo: a.objetivo ?? '',
                        alcance: a.alcance ?? '',
                        estado: a.estado ?? 'Borrador',
                        auditor_lider_id: a.auditor_lider?.id ?? '',
                        equipo_auditor: (a.equipo_auditor ?? []).map((u) => u.id),
                        areas: (a.areas ?? []).map((ar) => ar.id),
                        conclusiones: a.conclusiones ?? '',
                    };

                    const indiceActual = FLUJO_ESTADOS.indexOf(this.form.estado);
                    this.opcionesEstado = indiceActual === -1
                        ? FLUJO_ESTADOS
                        : FLUJO_ESTADOS.slice(indiceActual, indiceActual + 2);
                })
                .catch((err) => {
                    this.error = mensajeError(err, 'No se pudo cargar la auditoría. Intenta de nuevo.');
                })
                .finally(() => {
                    this.loading = false;
                });
        },

        guardar() {
            this.guardando = true;
            this.error = null;

            const payload = { ...this.form };
            if (payload.auditor_lider_id === '') {
                payload.auditor_lider_id = null;
            }

            const peticion = this.modo === 'crear'
                ? window.axios.post('/api/auditorias', payload)
                : window.axios.put(`/api/auditorias/${id}`, payload);

            peticion
                .then((response) => {
                    const a = response.data.data ?? response.data;
                    window.location.href = `/auditorias/${a.id}`;
                })
                .catch((err) => {
                    this.error = mensajeError(err, 'No se pudo guardar la auditoría. Revisa los datos e intenta de nuevo.');
                })
                .finally(() => {
                    this.guardando = false;
                });
        },
    };
}