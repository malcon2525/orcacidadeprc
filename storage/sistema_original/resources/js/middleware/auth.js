/**
 * Middleware de autenticação para o frontend
 */

import JWTUtils from '../utils/jwt.js';

export const authMiddleware = {
    /**
     * Verificar se usuário está autenticado
     */
    checkAuth() {
        const token = JWTUtils.getToken();
        
        if (!token) {
            console.log('Usuário não autenticado, redirecionando para login');
            window.location.href = '/login';
            return false;
        }

        if (JWTUtils.isTokenExpired()) {
            console.log('Token expirado, redirecionando para login');
            JWTUtils.logout();
            return false;
        }

        return true;
    },

    /**
     * Aplicar middleware em rotas protegidas
     */
    requireAuth() {
        return this.checkAuth();
    },

    /**
     * Verificar se usuário está logado (para rotas de login)
     */
    guest() {
        const token = JWTUtils.getToken();
        
        if (token && !JWTUtils.isTokenExpired()) {
            console.log('Usuário já autenticado, redirecionando para home');
            window.location.href = '/home';
            return false;
        }

        return true;
    }
};

export default authMiddleware; 