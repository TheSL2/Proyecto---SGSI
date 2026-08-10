function mensajeError(err, fallback) {
    if (err.response?.status === 422) {
        return err.response.data?.message ?? 'No se pudo completar la operación.';
    }
    if (err.response?.status === 401) {
        return 'Tu sesión expiró. Recarga la página e inicia sesión de nuevo.';
    }
    if (err.response?.status === 403) {
        return 'Solo un Administrador puede realizar esta acción.';
    }
    return fallback;
}

const ROLES = ['Administrador', 'Consultor', 'Auditor', 'Auditado', 'Alta Dirección'];

export function usuariosIndex() {
    return {
        loading: true,
        error: null,
        items: [],
        areas: [],
        roles: ROLES,

        guardandoId: null,

        init() {
            Promise.all([
                window.axios.get('/api/users'),
                window.axios.get('/api/areas'),
            ])
                .then(([resUsuarios, resAreas]) => {
                    this.items = (resUsuarios.data.data ?? resUsuarios.data).map((u) => ({
                        ...u,
                        _area_id: u.area?.id ? String(u.area.id) : '',
                    }));
                    this.areas = resAreas.data.data ?? resAreas.data;
                })
                .catch((err) => {
                    this.error = mensajeError(err, 'No se pudieron cargar los usuarios.');
                })
                .finally(() => {
                    this.loading = false;
                });
        },

        guardar(item) {
            this.guardandoId = item.id;
            this.error = null;

            window.axios
                .patch(`/api/users/${item.id}`, {
                    rol: item.rol,
                    area_id: item._area_id ? Number(item._area_id) : null,
                    activo: item.activo,
                })
                .then((res) => {
                    const usuarioActualizado = res.data.data ?? res.data;

                    item.rol = usuarioActualizado.rol;
                    item.activo = usuarioActualizado.activo;
                    item.area = usuarioActualizado.area;
                    item._area_id = usuarioActualizado.area?.id ? String(usuarioActualizado.area.id) : '';
                })
                .catch((err) => {
                    this.error = mensajeError(err, 'No se pudo guardar el usuario.');
                })
                .finally(() => {
                    this.guardandoId = null;
                });
        },
    };
}