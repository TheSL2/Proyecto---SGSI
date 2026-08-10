function mensajeError(err, fallback) {
    if (err.response?.status === 422) {
        return err.response.data?.message ?? 'El archivo no cumple con el formato o tamaño permitido.';
    }
    if (err.response?.status === 403) {
        return err.response.data?.message ?? 'No tienes permiso para realizar esta acción.';
    }
    if (err.response?.status === 401) {
        return 'Tu sesión expiró. Recarga la página e inicia sesión de nuevo.';
    }
    if (err.response?.status === 404) {
        return 'La evidencia solicitada no existe.';
    }
    return fallback;
}

function paramActual(nombre) {
    return new URLSearchParams(window.location.search).get(nombre);
}

export function evidenciasIndex() {
    return {
        loading: true,
        error: null,
        items: [],
        checklistIdFiltro: paramActual('checklist_id'),
        evidenciaIdFiltro: paramActual('hallazgo_id'),

        init() {
            this.cargar();
        },

        cargar() {
            this.loading = true;
            this.error = null;

            window.axios
                .get('/api/evidencias')
                .then((response) => {
                    let data = response.data.data ?? response.data;

                    if (this.checklistIdFiltro) {
                        data = data.filter(
                            (item) => String(item.checklist_id) === String(this.checklistIdFiltro)
                        );
                    }
                    if (this.evidenciaIdFiltro) {
                        data = data.filter(
                            (item) => String(item.hallazgo_id) === String(this.evidenciaIdFiltro)
                        );
                    }

                    this.items = data;
                })
                .catch((err) => {
                    this.error = mensajeError(err, 'No se pudieron cargar las evidencias.');
                })
                .finally(() => {
                    this.loading = false;
                });
        },

        eliminar(id) {
            if (!window.confirm('¿Seguro que deseas eliminar esta evidencia?')) {
                return;
            }

            window.axios
                .delete(`/api/evidencias/${id}`)
                .then(() => {
                    this.cargar();
                })
                .catch((err) => {
                    this.error = mensajeError(err, 'No se pudo eliminar la evidencia.');
                });
        },

        urlCrear() {
            return this.evidenciaIdFiltro
                ? `/evidencias/create?hallazgo_id=${this.evidenciaIdFiltro}`
                : '/evidencias/create';
        },
    };
}

export function evidenciaShow(id) {
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
                .get(`/api/evidencias/${id}`)
                .then((response) => {
                    this.item = response.data.data ?? response.data;
                })
                .catch((err) => {
                    this.error = mensajeError(err, 'No se pudo cargar la evidencia.');
                })
                .finally(() => {
                    this.loading = false;
                });
        },

        eliminar() {
            if (!window.confirm('¿Seguro que deseas eliminar esta evidencia?')) {
                return;
            }

            this.eliminando = true;
            this.error = null;

            window.axios
                .delete(`/api/evidencias/${id}`)
                .then(() => {
                    window.location.href = '/evidencias';
                })
                .catch((err) => {
                    this.error = mensajeError(err, 'No se pudo eliminar la evidencia.');
                    this.eliminando = false;
                });
        }
    };
}

export function evidenciaForm() {
    return {
        guardando: false,
        error: null,
        exito: null,
        archivo: null,

        form: {
            checklist_id: paramActual('checklist_id') ?? '',
            hallazgo_id: paramActual('hallazgo_id') ?? '',
        },

        seleccionarArchivo(event) {
            this.archivo = event.target.files[0] ?? null;
        },

        subir() {
            if (!this.archivo) {
                this.error = 'Por favor selecciona un archivo.';
                return;
            }

            this.guardando = true;
            this.error = null;
            this.exito = null;

            const formData = new FormData();
            formData.append('archivo', this.archivo);

            if (this.form.checklist_id) {
                formData.append('checklist_id', this.form.checklist_id);
            }
            if (this.form.hallazgo_id) {
                formData.append('hallazgo_id', this.form.hallazgo_id);
            }

            window.axios
                .post('/api/evidencias', formData, {
                    headers: { 'Content-Type': 'multipart/form-data' },
                })
                .then(() => {
                    this.exito = 'Evidencia subida correctamente e integridad SHA-256 verificada.';
                    
                    // Redireccionar al origen adecuado después de 1 segundo
                    setTimeout(() => {
                        if (this.form.hallazgo_id) {
                            window.location.href = `/hallazgos/${this.form.hallazgo_id}`;
                        } else if (this.form.checklist_id) {
                            window.location.href = `/checklists/${this.form.checklist_id}`;
                        } else {
                            window.location.href = '/evidencias';
                        }
                    }, 1000);
                })
                .catch((err) => {
                    this.error = mensajeError(err, 'No se pudo subir la evidencia.');
                })
                .finally(() => {
                    this.guardando = false;
                });
        }
    };
}