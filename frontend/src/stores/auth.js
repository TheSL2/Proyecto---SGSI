import { defineStore } from 'pinia';
import api from '../services/api';

export const useAuthStore = defineStore('auth', {
  state: () => ({
    token: localStorage.getItem('token') || null,
    user: JSON.parse(localStorage.getItem('user')) || null,
  }),
  getters: {
    isAuthenticated: (state) => !!state.token,
  },
  actions: {
    async login(credentials) {
      const response = await api.post('/login', credentials);
      
      // Mapear token según el formato que devuelve la API
      const tokenReceived = response.data.access_token || response.data.token;
      
      if (!tokenReceived) {
        throw new Error('No se recibió un token válido del backend.');
      }

      this.token = tokenReceived;
      this.user = response.data.user || null;

      localStorage.setItem('token', this.token);
      if (this.user) {
        localStorage.setItem('user', JSON.stringify(this.user));
      }
    },
    async logout() {
      try {
        await api.post('/logout');
      } catch (e) {
        console.error(e);
      } finally {
        this.token = null;
        this.user = null;
        localStorage.removeItem('token');
        localStorage.removeItem('user');
      }
    }
  }
});