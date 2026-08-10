export const ESTADO_BADGE = {
    Pendiente: 'bg-gray-100 text-gray-600',
    'En Proceso': 'bg-blue-100 text-blue-700',
    Verificada: 'bg-green-100 text-green-700',
    Rechazada: 'bg-red-100 text-red-700',
    Vencida: 'bg-orange-100 text-orange-700',
};

const OPCIONES_ESTADO = ['Pendiente', 'En Proceso', 'Verificada', 'Rechazada'];

function mensajeError(err, fallback) {
    if (err.response?.status === 422) {
        return err.response.data?.message ?? 'No se pudo completar la operación (regla de negocio).';
    }
    if (err.response?.status === 403) {
        return err.response.data?.message ?? 'No tienes permiso para realizar esta acción.';
    }
    if (err.response?.status === 401) {
        return 'Tu sesión expiró. Recarga la página e inicia sesión de nuevo.';
    }
    if (err.response?.status === 404) {
        return 'Esa acción correctiva no existe.';
    }
    return fallback;
}

function paramActual(nombre) {
    return new URLSearchParams(window.location.search).get(nombre);
}

export function accionesCorrectivasIndex() {
    return {
        loading: true,
        error: null,
        items: [],
        hallazgoIdFiltro: paramActual('hallazgo_id'),

        init() {
            this.cargar();
        },

        cargar() {
            this.loading = true;
            this.error = null;

            window.axios
                .get('/api/acciones-correctivas')
                .then((response) => {
                    let data = response.data.data ?? response.data;

                    if (this.hallazgoIdFiltro) {
                        data = data.filter(
                            (item) => String(item.hallazgo_id ?? item.hallazgo?.id) === String(this.hallazgoIdFiltro)
                        );
                    }

                    this.items = data;
                })
                .catch((err) => {
                    this.error = mensajeError(err, 'No se pudieron cargar las acciones correctivas.');
                })
                .finally(() => {
                    this.loading = false;
                });
        },

        badgeClase(estado) {
            return ESTADO_BADGE[estado] ?? 'bg-gray-100 text-gray-700';
        },

        urlCrear() {
            return this.hallazgoIdFiltro
                ? `/acciones-correctivas/create?hallazgo_id=${this.hallazgoIdFiltro}`
                : '/acciones-correctivas/create';
        },
    };
}

export function accionCorrectivaShow(id) {
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
                .get(`/api/acciones-correctivas/${id}`)
                .then((response) => {
                    this.item = response.data.data ?? response.data;
                })
                .catch((err) => {
                    this.error = mensajeError(err, 'No se pudo cargar la acción correctiva.');
                })
                .finally(() => {
                    this.loading = false;
                });
        },

        badgeClase(estado) {
            return ESTADO_BADGE[estado] ?? 'bg-gray-100 text-gray-700';
        },

        eliminar() {
            if (!window.confirm('¿Seguro que quieres eliminar esta acción correctiva?')) {
                return;
            }

            this.eliminando = true;
            this.error = null;

            window.axios
                .delete(`/api/acciones-correctivas/${id}`)
                .then(() => {
                    window.location.href = '/acciones-correctivas';
                })
                .catch((err) => {
                    this.error = mensajeError(err, 'No se pudo eliminar la acción correctiva.');
                    this.eliminando = false;
                });
        },
    };
}

export function accionCorrectivaForm(modo, id = null) {
    return {
        modo,
        loading: modo === 'editar',
        guardando: false,
        error: null,

        opcionesEstado: OPCIONES_ESTADO,
        evidenciasDelHallazgo: [],
        hallazgoInfo: null,

        form: {
            hallazgo_id: paramActual('hallazgo_id') ?? '',
            causa_raiz: '',
            descripcion_accion: '',
            responsable_id: '',
            fecha_limite: '',
            estado: 'Pendiente',
            evidencia_cierre_id: '',
        },

        init() {
            if (this.modo === 'editar') {
                this.cargar();
            } else if (this.form.hallazgo_id) {
                this.cargarEvidenciasDelHallazgo(this.form.hallazgo_id);
            }
        },

        cargar() {
            this.loading = true;
            this.error = null;

            window.axios
                .get(`/api/acciones-correctivas/${id}`)
                .then((response) => {
                    const item = response.data.data ?? response.data;

    
                    const targetHallazgoId = item.hallazgo_id ?? item.hallazgo?.id ?? '';

                    this.form = {
                        hallazgo_id: targetHallazgoId,
                        causa_raiz: item.causa_raiz ?? '',
                        descripcion_accion: item.descripcion_accion ?? '',
                        responsable_id: item.responsable_id ?? item.responsable?.id ?? '',
                        fecha_limite: item.fecha_limite ?? '',
                        estado: item.estado ?? 'Pendiente',
                        evidencia_cierre_id: item.evidencia_cierre_id ?? item.evidencia_cierre?.id ?? '',
                    };

                    return this.cargarEvidenciasDelHallazgo(targetHallazgoId);
                })
                .catch((err) => {
                    this.error = mensajeError(err, 'No se pudo cargar la acción correctiva.');
                })
                .finally(() => {
                    this.loading = false;
                });
        },

        cargarEvidenciasDelHallazgo(hallazgoId) {
            if (!hallazgoId) {
                this.evidenciasDelHallazgo = [];
                return Promise.resolve();
            }

            return window.axios
                .get('/api/evidencias')
                .then((response) => {
                    const data = response.data.data ?? response.data;

                    this.evidenciasDelHallazgo = data.filter((ev) => {
                        if (!ev.hallazgo_id) return false;
                        return String(ev.hallazgo_id) === String(hallazgoId);
                    });
                })
                .catch(() => {
                    this.evidenciasDelHallazgo = [];
                });
        },

        guardar() {
            this.guardando = true;
            this.error = null;

            const payload = { ...this.form };
            if (!payload.evidencia_cierre_id) {
                delete payload.evidencia_cierre_id;
            }
            if (!payload.causa_raiz) {
                delete payload.causa_raiz;
            }

            const peticion = this.modo === 'crear'
                ? window.axios.post('/api/acciones-correctivas', payload)
                : window.axios.put(`/api/acciones-correctivas/${id}`, payload);

            peticion
                .then((response) => {
                    const item = response.data.data ?? response.data;
                    window.location.href = `/acciones-correctivas/${item.id}`;
                })
                .catch((err) => {
                    this.error = mensajeError(err, 'No se pudo guardar la acción correctiva. Revisa los datos.');
                })
                .finally(() => {
                    this.guardando = false;
                });
        },
    };
}