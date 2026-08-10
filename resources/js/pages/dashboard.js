export default function dashboardKpis() {
    return {
        loading: true,
        error: null,
        anio: new Date().getFullYear(),
        data: {
            auditorias: { programadas: 0, ejecutadas: 0, en_ejecucion: 0 },
            hallazgos_por_tipo: {},
            tasa_cumplimiento_anexo_a: { total_evaluados: 0, conformes: 0, tasa: 0 },
            acciones_correctivas: { a_tiempo: 0, vencidas: 0, cerradas: 0, rechazadas: 0 },
        },

        init() {
            this.cargar();
        },

        cargar() {
            this.loading = true;
            this.error = null;

            window.axios
                .get('/api/dashboard/resumen', { params: { anio: this.anio } })
                .then((response) => {
                    this.data = response.data;
                })
                .catch((err) => {
                    this.error =
                        err.response?.status === 401
                            ? 'Tu sesión expiró. Recarga la página e inicia sesión de nuevo.'
                            : 'No se pudo cargar el dashboard. Intenta de nuevo.';
                })
                .finally(() => {
                    this.loading = false;
                });
        },

        // Suma total de hallazgos, usada para las barras proporcionales.
        get totalHallazgos() {
            return Object.values(this.data.hallazgos_por_tipo).reduce((a, b) => a + b, 0);
        },

        porcentajeHallazgo(total) {
            return this.totalHallazgos > 0 ? Math.round((total / this.totalHallazgos) * 100) : 0;
        },
    };
}
