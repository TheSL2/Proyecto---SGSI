function mensajeError(err, fallback) {
    if (err.response?.status === 422) {
        return err.response.data?.message ?? 'No se pudo completar la operación.';
    }
    if (err.response?.status === 403) {
        return err.response.data?.message ?? 'No tienes permiso para modificar la aplicabilidad de requisitos ISO.';
    }
    if (err.response?.status === 401) {
        return 'Tu sesión expiró. Recarga la página e inicia sesión de nuevo.';
    }
    return fallback;
}

export function requisitosIsoIndex() {
    return {
        loading: true,
        error: null,
        items: [],
        filtroCategoria: '',
        cambiandoId: null,

        init() {
            this.cargar();
        },

        cargar() {
            this.loading = true;
            this.error = null;

            window.axios
                .get('/api/requisito-isos')
                .then((response) => {
                    this.items = response.data.data ?? response.data;
                })
                .catch((err) => {
                    this.error = mensajeError(err, 'No se pudieron cargar los requisitos ISO.');
                })
                .finally(() => {
                    this.loading = false;
                });
        },

        get itemsFiltrados() {
            if (!this.filtroCategoria) return this.items;
            return this.items.filter((i) => i.categoria === this.filtroCategoria);
        },

        get categorias() {
            return [...new Set(this.items.map((i) => i.categoria))];
        },

        toggleAplicable(item) {
            const valorAnterior = item.aplicable;
            const nuevoValor = !item.aplicable;

            item.aplicable = nuevoValor;
            this.cambiandoId = item.id;
            this.error = null;

            window.axios
                .patch(`/api/requisito-isos/${item.id}`, { aplicable: nuevoValor })
                .catch((err) => {
                    item.aplicable = valorAnterior;
                    this.error = mensajeError(err, 'No se pudo actualizar la aplicabilidad de este requisito.');
                })
                .finally(() => {
                    this.cambiandoId = null;
                });
        },
    };
}
