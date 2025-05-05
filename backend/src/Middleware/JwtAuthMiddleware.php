<?php

namespace App\Middleware;

use Cake\Core\Configure;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Cake\Http\Exception\UnauthorizedException;

class JwtAuthMiddleware
{
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next)
    {
        $authHeader = $request->getHeaderLine('Authorization');
        $secretKey = Configure::read('App.secretKey');

        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            throw new UnauthorizedException('Token não enviado.');
        }

        $token = str_replace('Bearer ', '', $authHeader);

        try {
            $decoded = JWT::decode($token, new Key($secretKey, 'HS256'));
            $request = $request->withAttribute('jwtPayload', $decoded);
        } catch (\Exception $e) {
            throw new UnauthorizedException('Token inválido ou expirado.');
        }

        return $next($request, $response);
    }
}
