/**
 * Utilitário para gerenciar JWT no frontend
 */

export const JWTUtils = {
    /**
     * Armazenar token JWT
     */
    setToken(token) {
        localStorage.setItem('jwt_token', token);
    },

    /**
     * Obter token JWT
     */
    getToken() {
        return localStorage.getItem('jwt_token');
    },

    /**
     * Remover token JWT
     */
    removeToken() {
        localStorage.removeItem('jwt_token');
        localStorage.removeItem('user_data');
    },

    /**
     * Verificar se token existe
     */
    hasToken() {
        return !!this.getToken();
    },

    /**
     * Obter headers para requisições autenticadas
     */
    getAuthHeaders() {
        const token = this.getToken();
        return {
            'Authorization': `Bearer ${token}`,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        };
    },

    /**
     * Fazer logout
     */
    logout() {
        this.removeToken();
        window.location.href = '/login';
    },

    /**
     * Verificar se token está expirado
     */
    isTokenExpired() {
        const token = this.getToken();
        if (!token) return true;

        try {
            const payload = JSON.parse(atob(token.split('.')[1]));
            return payload.exp * 1000 < Date.now();
        } catch (e) {
            return true;
        }
    }
};

export default JWTUtils; 