export const TIPO_HALLAZGO_BADGE = {
    'No Conforme Mayor': 'bg-red-100 text-red-700',
    'No Conforme Menor': 'bg-orange-100 text-orange-700',
    'Oportunidad de Mejora': 'bg-blue-100 text-blue-700',
    'Observacion': 'bg-gray-100 text-gray-600',
};

const OPCIONES_TIPO = [
    'No Conforme Mayor',
    'No Conforme Menor',
    'Oportunidad de Mejora',
    'Observacion',
];

const OPCIONES_ESTADO = ['Abierto', 'En Proceso', 'Cerrado'];
const OPCIONES_ESTADO_NOTIFICACION = ['Pendiente', 'Notificado', 'Aceptado'];

function mensajeError(err, fallback) {
    if (err.response?.status === 422) {
        return err.response.data?.message ?? 'No se pudo completar la operación (regla de negocio).';
    }
    if (err.response?.status === 401) {
        return 'Tu sesión expiró. Recarga la página e inicia sesión de nuevo.';
    }
    if (err.response?.status === 404) {
        return 'Ese hallazgo no existe.';
    }
    return fallback;
}

function paramActual(nombre) {
    return new URLSearchParams(window.location.search).get(nombre);
}

export function hallazgosIndex() {
    return {
        loading: true,
        error: null,
        items: [],
        checklistIdFiltro: paramActual('checklist_id'),

        init() {
            this.cargar();
        },

        cargar() {
            this.loading = true;
            this.error = null;

            window.axios
                .get('/api/hallazgos')
                .then((response) => {
                    let data = response.data.data ?? response.data;

                    if (this.checklistIdFiltro) {
                        data = data.filter(
                            (item) => String(item.checklist?.id) === String(this.checklistIdFiltro)
                        );
                    }

                    this.items = data;
                })
                .catch((err) => {
                    this.error = mensajeError(err, 'No se pudieron cargar los hallazgos. Intenta de nuevo.');
                })
                .finally(() => {
                    this.loading = false;
                });
        },

        badgeClase(tipo) {
            return TIPO_HALLAZGO_BADGE[tipo] ?? 'bg-gray-100 text-gray-700';
        },

        sinAcciones(item) {
            return !(item.acciones_correctivas ?? []).length;
        },

        urlCrear() {
            return this.checklistIdFiltro
                ? `/hallazgos/create?checklist_id=${this.checklistIdFiltro}`
                : '/hallazgos/create';
        },
    };
}

export function hallazgoShow(id) {
    return {
        loading: true,
        error: null,
        item: null,

        eliminando: false,

        init() {
            this.cargar();
        },

        cargar() {
            this.loading = true;
            this.error = null;

            window.axios
                .get(`/api/hallazgos/${id}`)
                .then((response) => {
                    this.item = response.data.data ?? response.data;
                })
                .catch((err) => {
                    this.error = mensajeError(err, 'No se pudo cargar el hallazgo. Intenta de nuevo.');
                })
                .finally(() => {
                    this.loading = false;
                });
        },

        badgeClase(tipo) {
            return TIPO_HALLAZGO_BADGE[tipo] ?? 'bg-gray-100 text-gray-700';
        },

        get sinAccionCorrectiva() {
            return !(this.item?.acciones_correctivas ?? []).length;
        },

        eliminar() {
            if (!window.confirm('¿Seguro que quieres eliminar este hallazgo? Esta acción no se puede deshacer.')) {
                return;
            }

            this.eliminando = true;
            this.error = null;

            window.axios
                .delete(`/api/hallazgos/${id}`)
                .then(() => {
                    window.location.href = '/hallazgos';
                })
                .catch((err) => {
                    this.error = mensajeError(err, 'No se pudo eliminar el hallazgo.');
                    this.eliminando = false;
                });
        },
    };
}

export function hallazgoForm(modo, id = null) {
    return {
        modo,
        loading: modo === 'editar',
        guardando: false,
        error: null,

        opcionesTipo: OPCIONES_TIPO,
        opcionesEstado: OPCIONES_ESTADO,
        opcionesEstadoNotificacion: OPCIONES_ESTADO_NOTIFICACION,

        form: {
            checklist_id: paramActual('checklist_id') ?? '',
            tipo_hallazgo: 'Observacion',
            clausula_o_control: '',
            descripcion: '',
            estado: 'Abierto',
            fecha_notificacion: '',
            estado_notificacion: 'Pendiente',
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
                .get(`/api/hallazgos/${id}`)
                .then((response) => {
                    const item = response.data.data ?? response.data;

                    this.form = {
                        checklist_id: item.checklist?.id ?? '',
                        tipo_hallazgo: item.tipo_hallazgo ?? 'Observacion',
                        clausula_o_control: item.clausula_o_control ?? '',
                        descripcion: item.descripcion ?? '',
                        estado: item.estado ?? 'Abierto',
                        fecha_notificacion: item.fecha_notificacion ?? '',
                        estado_notificacion: item.estado_notificacion ?? 'Pendiente',
                    };
                })
                .catch((err) => {
                    this.error = mensajeError(err, 'No se pudo cargar el hallazgo. Intenta de nuevo.');
                })
                .finally(() => {
                    this.loading = false;
                });
        },

        guardar() {
            this.guardando = true;
            this.error = null;

            const peticion = this.modo === 'crear'
                ? window.axios.post('/api/hallazgos', this.form)
                : window.axios.put(`/api/hallazgos/${id}`, this.form);

            peticion
                .then((response) => {
                    const item = response.data.data ?? response.data;
                    window.location.href = `/hallazgos/${item.id}`;
                })
                .catch((err) => {
                    this.error = mensajeError(err, 'No se pudo guardar el hallazgo. Revisa los datos e intenta de nuevo.');
                })
                .finally(() => {
                    this.guardando = false;
                });
        },
    };
}
