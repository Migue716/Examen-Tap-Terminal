<?php

namespace App\Swagger;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    title: 'Tap Terminal API',
    description: 'API REST - Examen de Admisión Área de Desarrollo. Autenticación Bearer (Sanctum).'
)]
#[OA\Server(url: 'http://localhost:8000/api', description: 'Local / Docker')]
#[OA\SecurityScheme(
    securityScheme: 'bearerAuth',
    type: 'http',
    name: 'Authorization',
    in: 'header',
    scheme: 'bearer',
    bearerFormat: 'Sanctum'
)]
#[OA\Tag(name: 'Auth', description: 'Login, logout y recuperación de contraseña')]
#[OA\Tag(name: 'Products', description: 'Catálogo de productos')]
#[OA\Tag(name: 'Users', description: 'Usuarios del sistema')]
#[OA\Tag(name: 'Profiles', description: 'Perfiles y permisos por sección')]
#[OA\Tag(name: 'Sections', description: 'Secciones del sistema')]
#[OA\Tag(name: 'Exports', description: 'Exportación PDF y Excel')]
class OpenApiDefinitions
{
    #[OA\Post(
        path: '/auth/login',
        tags: ['Auth'],
        summary: 'Iniciar sesión',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['username', 'password'],
                properties: [
                    new OA\Property(property: 'username', type: 'string', format: 'email', example: 'admin@tapterminal.com'),
                    new OA\Property(property: 'password', type: 'string', example: 'Admin123!'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Token y datos del usuario'),
            new OA\Response(response: 401, description: 'Credenciales inválidas'),
        ]
    )]
    public function login(): void
    {
    }

    #[OA\Post(
        path: '/auth/forgot-password',
        tags: ['Auth'],
        summary: 'Recuperar contraseña',
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                properties: [new OA\Property(property: 'username', type: 'string', format: 'email')]
            )
        ),
        responses: [new OA\Response(response: 200, description: 'Credenciales enviadas al correo')]
    )]
    public function forgotPassword(): void
    {
    }

    #[OA\Post(path: '/auth/logout', tags: ['Auth'], summary: 'Cerrar sesión', security: [['bearerAuth' => []]], responses: [new OA\Response(response: 200, description: 'OK')])]
    public function logout(): void
    {
    }

    #[OA\Get(path: '/auth/me', tags: ['Auth'], summary: 'Usuario autenticado', security: [['bearerAuth' => []]], responses: [new OA\Response(response: 200, description: 'OK')])]
    public function me(): void
    {
    }

    #[OA\Get(path: '/sections', tags: ['Sections'], summary: 'Listar secciones', security: [['bearerAuth' => []]], responses: [new OA\Response(response: 200, description: 'OK')])]
    public function sectionsIndex(): void
    {
    }

    #[OA\Get(path: '/products', tags: ['Products'], summary: 'Listar productos', security: [['bearerAuth' => []]], responses: [new OA\Response(response: 200, description: 'OK')])]
    public function productsIndex(): void
    {
    }

    #[OA\Get(path: '/products/{id}', tags: ['Products'], summary: 'Detalle producto', security: [['bearerAuth' => []]], parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))], responses: [new OA\Response(response: 200, description: 'OK')])]
    public function productsShow(): void
    {
    }

    #[OA\Post(
        path: '/products',
        tags: ['Products'],
        summary: 'Alta de producto',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(content: new OA\JsonContent(required: ['name', 'brand', 'price'], properties: [
            new OA\Property(property: 'name', type: 'string'),
            new OA\Property(property: 'brand', type: 'string'),
            new OA\Property(property: 'price', type: 'integer', maximum: 999),
        ])),
        responses: [new OA\Response(response: 201, description: 'Creado')]
    )]
    public function productsStore(): void
    {
    }

    #[OA\Put(path: '/products/{id}', tags: ['Products'], summary: 'Editar producto', security: [['bearerAuth' => []]], parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))], responses: [new OA\Response(response: 200, description: 'OK')])]
    public function productsUpdate(): void
    {
    }

    #[OA\Delete(path: '/products/{id}', tags: ['Products'], summary: 'Eliminar producto', security: [['bearerAuth' => []]], parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))], responses: [new OA\Response(response: 200, description: 'OK')])]
    public function productsDestroy(): void
    {
    }

    #[OA\Get(path: '/products-export/{format}', tags: ['Exports'], summary: 'Exportar productos', security: [['bearerAuth' => []]], parameters: [new OA\Parameter(name: 'format', in: 'path', required: true, schema: new OA\Schema(enum: ['pdf', 'excel']))], responses: [new OA\Response(response: 200, description: 'Archivo binario')])]
    public function productsExport(): void
    {
    }

    #[OA\Get(path: '/users', tags: ['Users'], summary: 'Listar usuarios', security: [['bearerAuth' => []]], responses: [new OA\Response(response: 200, description: 'OK')])]
    public function usersIndex(): void
    {
    }

    #[OA\Get(path: '/users/{id}', tags: ['Users'], summary: 'Detalle usuario', security: [['bearerAuth' => []]], parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))], responses: [new OA\Response(response: 200, description: 'OK')])]
    public function usersShow(): void
    {
    }

    #[OA\Post(path: '/users', tags: ['Users'], summary: 'Alta de usuario', security: [['bearerAuth' => []]], responses: [new OA\Response(response: 201, description: 'Creado')])]
    public function usersStore(): void
    {
    }

    #[OA\Put(path: '/users/{id}', tags: ['Users'], summary: 'Editar usuario', security: [['bearerAuth' => []]], parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))], responses: [new OA\Response(response: 200, description: 'OK')])]
    public function usersUpdate(): void
    {
    }

    #[OA\Delete(path: '/users/{id}', tags: ['Users'], summary: 'Eliminar usuario', security: [['bearerAuth' => []]], parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))], responses: [new OA\Response(response: 200, description: 'OK')])]
    public function usersDestroy(): void
    {
    }

    #[OA\Get(path: '/users-export/{format}', tags: ['Exports'], summary: 'Exportar usuarios', security: [['bearerAuth' => []]], parameters: [new OA\Parameter(name: 'format', in: 'path', required: true, schema: new OA\Schema(enum: ['pdf', 'excel']))], responses: [new OA\Response(response: 200, description: 'Archivo binario')])]
    public function usersExport(): void
    {
    }

    #[OA\Get(path: '/profiles', tags: ['Profiles'], summary: 'Listar perfiles', security: [['bearerAuth' => []]], responses: [new OA\Response(response: 200, description: 'OK')])]
    public function profilesIndex(): void
    {
    }

    #[OA\Get(path: '/profiles/{id}', tags: ['Profiles'], summary: 'Detalle perfil', security: [['bearerAuth' => []]], parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))], responses: [new OA\Response(response: 200, description: 'OK')])]
    public function profilesShow(): void
    {
    }

    #[OA\Post(path: '/profiles', tags: ['Profiles'], summary: 'Alta de perfil', security: [['bearerAuth' => []]], responses: [new OA\Response(response: 201, description: 'Creado')])]
    public function profilesStore(): void
    {
    }

    #[OA\Put(path: '/profiles/{id}', tags: ['Profiles'], summary: 'Editar perfil', security: [['bearerAuth' => []]], parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))], responses: [new OA\Response(response: 200, description: 'OK')])]
    public function profilesUpdate(): void
    {
    }

    #[OA\Delete(path: '/profiles/{id}', tags: ['Profiles'], summary: 'Eliminar perfil', security: [['bearerAuth' => []]], parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))], responses: [new OA\Response(response: 200, description: 'OK')])]
    public function profilesDestroy(): void
    {
    }

    #[OA\Get(path: '/profiles-export/{format}', tags: ['Exports'], summary: 'Exportar perfiles', security: [['bearerAuth' => []]], parameters: [new OA\Parameter(name: 'format', in: 'path', required: true, schema: new OA\Schema(enum: ['pdf', 'excel']))], responses: [new OA\Response(response: 200, description: 'Archivo binario')])]
    public function profilesExport(): void
    {
    }
}
