<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware appliquant des en-têtes HTTP de sécurité pour protéger l'application contre
 * le Clickjacking, les attaques XSS et l'exploration non autorisée du type MIME (MIME-sniffing).
 */
class SecurityHeaders
{
    /**
     * Intercepte la réponse et injecte les en-têtes de sécurité HTTP.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Bloque l'intégration du site dans des iframes (anti-Clickjacking)
        $response->headers->set('X-Frame-Options', 'DENY');

        // Empêche le navigateur de deviner le type MIME d'un fichier (anti-MIME sniffing)
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // Active le filtre XSS intégré du navigateur
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // Contrôle le comportement d'envoi du Referrer HTTP
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Désactive l'accès aux fonctionnalités matérielles sensibles
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');

        return $response;
    }
}
