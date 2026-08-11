function mensajeError(err, fallback) {
    if (err.response?.status === 422) {
        return err.response.data?.message ?? 'No se pudo completar la operación.';
    }
    if (err.response?.status === 401) {
        return 'Tu sesión expiró. Recarga la página e inicia sesión de nuevo.';
    }
    return fallback;
}

export function areasIndex() {
    return {
        loading: true,
        error: null,
        items: [],

        creando: false,
        nuevaForm: { nombre: '', descripcion: '' },

        editandoId: null,
        editForm: { nombre: '', descripcion: '' },
        guardandoEdicion: false,

        init() {
            this.cargar();
        },

        cargar() {
            this.loading = true;
            this.error = null;

            window.axios
                .get('/api/areas')
                .then((response) => {
                    this.items = response.data.data ?? response.data;
                })
                .catch((err) => {
                    this.error = mensajeError(err, 'No se pudieron cargar las áreas.');
                })
                .finally(() => {
                    this.loading = false;
                });
        },

        crear() {
            if (!this.nuevaForm.nombre) return;

            this.creando = true;
            this.error = null;

            window.axios
                .post('/api/areas', this.nuevaForm)
                .then((response) => {
                    const item = response.data.data ?? response.data;
                    this.items.push(item);
                    this.nuevaForm = { nombre: '', descripcion: '' };
                })
                .catch((err) => {
                    this.error = mensajeError(err, 'No se pudo crear el área.');
                })
                .finally(() => {
                    this.creando = false;
                });
        },

        editar(item) {
            this.editandoId = item.id;
            this.editForm = { nombre: item.nombre, descripcion: item.descripcion ?? '' };
        },

        cancelarEdicion() {
            this.editandoId = null;
        },

        guardarEdicion(id) {
            this.guardandoEdicion = true;
            this.error = null;

            window.axios
                .patch(`/api/areas/${id}`, this.editForm)
                .then((response) => {
                    const actualizado = response.data.data ?? response.data;
                    const idx = this.items.findIndex((i) => i.id === id);
                    if (idx !== -1) this.items[idx] = actualizado;
                    this.editandoId = null;
                })
                .catch((err) => {
                    this.error = mensajeError(err, 'No se pudo guardar el área.');
                })
                .finally(() => {
                    this.guardandoEdicion = false;
                });
        },

        eliminar(id) {
            if (!window.confirm('¿Eliminar esta área?')) return;

            this.error = null;

            window.axios
                .delete(`/api/areas/${id}`)
                .then(() => {
                    this.items = this.items.filter((i) => i.id !== id);
                })
                .catch((err) => {
                    this.error = mensajeError(err, 'No se pudo eliminar el área.');
                });
        },
    };
}
