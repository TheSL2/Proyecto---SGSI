export const ESTADO_CHECKLIST_BADGE = {
    'Conforme': 'bg-green-100 text-green-700',
    'No Conforme Mayor': 'bg-red-100 text-red-700',
    'No Conforme Menor': 'bg-orange-100 text-orange-700',
    'Oportunidad de Mejora': 'bg-blue-100 text-blue-700',
    'No Aplicable': 'bg-gray-100 text-gray-500',
};

const OPCIONES_ESTADO = [
    'Conforme',
    'No Conforme Mayor',
    'No Conforme Menor',
    'Oportunidad de Mejora',
    'No Aplicable',
];

function mensajeError(err, fallback) {
    if (err.response?.status === 422) {
        return err.response.data?.message ?? 'No se pudo completar la operación (regla de negocio).';
    }
    if (err.response?.status === 401) {
        return 'Tu sesión expiró. Recarga la página e inicia sesión de nuevo.';
    }
    if (err.response?.status === 404) {
        return 'Ese ítem de checklist no existe.';
    }
    return fallback;
}

function paramActual(nombre) {
    return new URLSearchParams(window.location.search).get(nombre);
}

export function checklistsIndex() {
    return {
        loading: true,
        error: null,
        items: [],
        auditoriaIdFiltro: paramActual('auditoria_id'),

        init() {
            this.cargar();
        },

        cargar() {
            this.loading = true;
            this.error = null;

            window.axios
                .get('/api/checklists')
                .then((response) => {
                    let data = response.data.data ?? response.data;

                    if (this.auditoriaIdFiltro) {
                        data = data.filter(
                            (item) => String(item.auditoria?.id) === String(this.auditoriaIdFiltro)
                        );
                    }

                    this.items = data;
                })
                .catch((err) => {
                    this.error = mensajeError(err, 'No se pudieron cargar los ítems del checklist. Intenta de nuevo.');
                })
                .finally(() => {
                    this.loading = false;
                });
        },

        badgeClase(estado) {
            return ESTADO_CHECKLIST_BADGE[estado] ?? 'bg-gray-100 text-gray-700';
        },

        urlCrear() {
            return this.auditoriaIdFiltro
                ? `/checklists/create?auditoria_id=${this.auditoriaIdFiltro}`
                : '/checklists/create';
        },
    };
}

export function checklistShow(id) {
    return {
        loading: true,
        error: null,
        item: null,

        eliminando: false,

        get esNoConforme() {
            return ['No Conforme Mayor', 'No Conforme Menor'].includes(this.item?.estado_cumplimiento);
        },

        init() {
            this.cargar();
        },

        cargar() {
            this.loading = true;
            this.error = null;

            window.axios
                .get(`/api/checklists/${id}`)
                .then((response) => {
                    this.item = response.data.data ?? response.data;
                })
                .catch((err) => {
                    this.error = mensajeError(err, 'No se pudo cargar el ítem del checklist. Intenta de nuevo.');
                })
                .finally(() => {
                    this.loading = false;
                });
        },

        badgeClase(estado) {
            return ESTADO_CHECKLIST_BADGE[estado] ?? 'bg-gray-100 text-gray-700';
        },

        eliminar() {
            if (!window.confirm('¿Seguro que quieres eliminar este ítem del checklist? Esta acción no se puede deshacer.')) {
                return;
            }

            this.eliminando = true;
            this.error = null;

            window.axios
                .delete(`/api/checklists/${id}`)
                .then(() => {
                    const destino = this.item?.auditoria?.id
                        ? `/checklists?auditoria_id=${this.item.auditoria.id}`
                        : '/checklists';
                    window.location.href = destino;
                })
                .catch((err) => {
                    this.error = mensajeError(err, 'No se pudo eliminar el ítem del checklist.');
                    this.eliminando = false;
                });
        },
    };
}

export function checklistForm(modo, id = null) {
    return {
        modo,
        loading: modo === 'editar',
        guardando: false,
        error: null,

        opcionesEstado: OPCIONES_ESTADO,

        form: {
            auditoria_id: paramActual('auditoria_id') ?? '',
            requisito_iso_id: '',
            estado_cumplimiento: 'Conforme',
            observaciones: '',
            justificacion: '',
        },

        get requiereJustificacion() {
            return this.form.estado_cumplimiento === 'No Aplicable';
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
                .get(`/api/checklists/${id}`)
                .then((response) => {
                    const item = response.data.data ?? response.data;

                    this.form = {
                        auditoria_id: item.auditoria?.id ?? '',
                        requisito_iso_id: item.requisito_iso?.id ?? '',
                        estado_cumplimiento: item.estado_cumplimiento ?? 'Conforme',
                        observaciones: item.observaciones ?? '',
                        justificacion: item.justificacion ?? '',
                    };
                })
                .catch((err) => {
                    this.error = mensajeError(err, 'No se pudo cargar el ítem del checklist. Intenta de nuevo.');
                })
                .finally(() => {
                    this.loading = false;
                });
        },

        guardar() {
            this.guardando = true;
            this.error = null;

            const peticion = this.modo === 'crear'
                ? window.axios.post('/api/checklists', this.form)
                : window.axios.put(`/api/checklists/${id}`, this.form);

            peticion
                .then((response) => {
                    const item = response.data.data ?? response.data;
                    window.location.href = `/checklists/${item.id}`;
                })
                .catch((err) => {
                    this.error = mensajeError(err, 'No se pudo guardar el ítem del checklist. Revisa los datos e intenta de nuevo.');
                })
                .finally(() => {
                    this.guardando = false;
                });
        },
    };
}