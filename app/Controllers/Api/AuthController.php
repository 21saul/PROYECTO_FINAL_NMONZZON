<?php
declare(strict_types=1);

/* NMZ-CABECERA-FICHERO-MAYUSCU */
/*
 * =============================================================================
 * AUTHCONTROLLER — CONTROLADOR HTTP (API REST, JSON)
 * =============================================================================
 * UBICACIÓN: APP/CONTROLLERS/API/AUTHCONTROLLER.PHP.
 * QUÉ HACE: RECIBE PETICIONES, USA MODELOS/LIBRARIES Y RESPONDE CON VISTA O JSON.
 * POR QUÉ ASÍ: SEPARA ENTRADA/SALIDA HTTP DE PERSISTENCIA Y DE SERVICIOS TRANSVERSALES.
 * =============================================================================
 */

// API DE AUTENTICACIÓN: REGISTRO, LOGIN, REFRESCO DE TOKEN, PERFIL Y RECUPERACIÓN DE CONTRASEÑA CON JWT.
namespace App\Controllers\Api;

use App\Libraries\JWTService;
use App\Models\AuthTokenModel;
use App\Models\UserModel;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

class AuthController extends BaseApiController
{
    protected UserModel $userModel;
    protected AuthTokenModel $authTokenModel;
    protected JWTService $jwtService;

    // INICIALIZA MODELOS, HELPER API Y SERVICIO JWT TRAS EL CONSTRUCTOR PADRE.
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger): void
    {
        parent::initController($request, $response, $logger);
        helper('api');
        $this->userModel      = model(UserModel::class);
        $this->authTokenModel = model(AuthTokenModel::class);
        $this->jwtService     = new JWTService();
    }

    // REGISTRA UN NUEVO USUARIO CLIENTE, CREA TOKENS Y GUARDA EL HASH DEL REFRESH EN BASE DE DATOS.
    public function register(): ResponseInterface
    {
        $input = $this->getRequestPayload();

        $rules = [
            'name'             => 'required|min_length[2]|max_length[100]',
            'email'            => 'required|valid_email|is_unique[users.email]',
            'password'         => 'required|min_length[8]',
            'password_confirm' => 'required|matches[password]',
        ];
        $messages = [
            'password' => [
                'min_length' => 'La contraseña debe tener al menos 8 caracteres.',
            ],
        ];

        if (!$this->validate($rules, $messages)) {
            return apiError('Error de validación', 422, $this->validator->getErrors());
        }

        $userId = $this->userModel->skipValidation(true)->insert([
            'name'     => $input['name'],
            'email'    => $input['email'],
            'password' => $input['password'],
            'role'     => 'client',
        ], true);

        if (!$userId) {
            return apiError('Error al registrar usuario', 500);
        }

        $user = $this->userModel->find($userId);
        $userData = $this->sanitizeUser($user);

        $accessToken  = $this->jwtService->generateAccessToken($user);
        $refreshToken = $this->jwtService->generateRefreshToken($user);

        $this->authTokenModel->insert([
            'user_id'    => (int) $userId,
            'token_hash' => hash('sha256', $refreshToken),
        ]);

        return apiResponse([
            'access_token'  => $accessToken,
            'refresh_token' => $refreshToken,
            'user'          => $userData,
        ], 201, 'Registro exitoso');
    }

    // AUTENTICA POR EMAIL Y CONTRASEÑA; APLICA BLOQUEO POR INTENTOS FALLIDOS Y EMITE NUEVOS TOKENS.
    public function login(): ResponseInterface
    {
        $input = $this->getRequestPayload();

        $rules = [
            'email'    => 'required|valid_email',
            'password' => 'required',
        ];

        if (!$this->validate($rules)) {
            return apiError('Error de validación', 422, $this->validator->getErrors());
        }

        $user = $this->userModel->where('email', $input['email'])->first();

        if (!$user) {
            return apiError('Credenciales inválidas', 401);
        }

        if (empty($user['is_active'])) {
            return apiError('Cuenta inactiva', 403);
        }

        if ($this->isUserLocked($user)) {
            return apiError('Cuenta bloqueada temporalmente. Intenta más tarde.', 423);
        }

        if (!password_verify($input['password'], $user['password'])) {
            $this->recordFailedLogin($user);
            return apiError('Credenciales inválidas', 401);
        }

        $this->userModel->skipValidation(true)->update($user['id'], [
            'failed_login_attempts' => 0,
            'locked_until'          => null,
            'last_login_at'         => date('Y-m-d H:i:s'),
            'last_login_ip'         => $this->request->getIPAddress(),
        ]);

        $accessToken  = $this->jwtService->generateAccessToken($user);
        $refreshToken = $this->jwtService->generateRefreshToken($user);

        $this->authTokenModel->insert([
            'user_id'    => (int) $user['id'],
            'token_hash' => hash('sha256', $refreshToken),
        ]);

        return apiResponse([
            'access_token'  => $accessToken,
            'refresh_token' => $refreshToken,
        ], 200, 'Login exitoso');
    }

    // RENUEVA EL ACCESS TOKEN SI EL REFRESH ES VÁLIDO, NO REVOCADO Y EL USUARIO EXISTE.
    public function refresh(): ResponseInterface
    {
        $input = $this->getRequestPayload();

        if (empty($input['refresh_token'])) {
            return apiError('refresh_token es requerido', 422);
        }

        // INICIO DE BLOQUE TRY
        try {
            $payload = $this->jwtService->validateToken($input['refresh_token']);
        } catch (\Exception $e) {
            return apiError('Token de refresco inválido o expirado', 401);
        }

        $tokenHash = hash('sha256', $input['refresh_token']);
        $stored = $this->authTokenModel
            ->where('token_hash', $tokenHash)
            ->where('revoked_at', null)
            ->first();

        if (!$stored) {
            return apiError('Token revocado o no encontrado', 401);
        }

        $data = (array) $payload->data;
        $userId = (int) ($data['id'] ?? $stored['user_id'] ?? 0);

        $user = $this->userModel->find($userId);
        if (!$user) {
            return apiError('Usuario no encontrado', 401);
        }

        $accessToken = $this->jwtService->generateAccessToken($user);

        return apiResponse(['access_token' => $accessToken], 200, 'Token renovado');
    }

    // REVOCA TODOS LOS TOKENES DE REFRESCO ACTIVOS DEL USUARIO AUTENTICADO (CIERRE DE SESIÓN).
    public function logout(): ResponseInterface
    {
        $userId = $this->getUserId();
        if (!$userId) {
            return apiError('No autorizado', 401);
        }

        $this->authTokenModel
            ->where('user_id', $userId)
            ->where('revoked_at', null)
            ->set(['revoked_at' => date('Y-m-d H:i:s')])
            ->update();

        return apiResponse(null, 200, 'Sesión cerrada');
    }

    // DEVUELVE EL PERFIL DEL USUARIO ACTUAL SIN DATOS SENSIBLES.
    public function profile(): ResponseInterface
    {
        $userId = $this->getUserId();
        if (!$userId) {
            return apiError('No autorizado', 401);
        }

        $user = $this->userModel->find($userId);
        if (!$user) {
            return apiError('Usuario no encontrado', 404);
        }

        return apiResponse($this->sanitizeUser($user));
    }

    // ACTUALIZA NOMBRE, TELÉFONO Y AVATAR DEL USUARIO AUTENTICADO TRAS VALIDAR ENTRADA.
    public function updateProfile(): ResponseInterface
    {
        $userId = $this->getUserId();
        if (!$userId) {
            return apiError('No autorizado', 401);
        }

        $input = $this->getRequestPayload();

        $rules = [
            'name'   => 'required|min_length[2]|max_length[100]',
            'phone'  => 'permit_empty|max_length[30]',
            'avatar' => 'permit_empty|max_length[500]',
        ];

        if (!$this->validate($rules)) {
            return apiError('Error de validación', 422, $this->validator->getErrors());
        }

        $update = ['name' => $input['name']];
        if (array_key_exists('phone', $input)) {
            $update['phone'] = $input['phone'];
        }
        if (array_key_exists('avatar', $input)) {
            $update['avatar'] = $input['avatar'];
        }

        $this->userModel->skipValidation(true)->update($userId, $update);
        $user = $this->userModel->find($userId);

        return apiResponse($this->sanitizeUser($user), 200, 'Perfil actualizado');
    }

    // INICIA FLUJO DE RECUPERACIÓN: SI EXISTE EL EMAIL, GUARDA TOKEN HASHEADO (RESPUESTA SIEMPRE GENÉRICA POR SEGURIDAD).
    public function forgotPassword(): ResponseInterface
    {
        $input = $this->getRequestPayload();

        $rules = ['email' => 'required|valid_email'];

        if (!$this->validate($rules)) {
            return apiError('Error de validación', 422, $this->validator->getErrors());
        }

        $user = $this->userModel->where('email', $input['email'])->first();

        if ($user) {
            $rawToken = bin2hex(random_bytes(32));
            $this->userModel->skipValidation(true)->update($user['id'], [
                'remember_token' => hash('sha256', $rawToken),
            ]);
        }

        return apiResponse(null, 200, 'Si existe una cuenta con ese email, se han enviado instrucciones de recuperación.');
    }

    // RESTABLECE LA CONTRASEÑA MEDIANTE TOKEN VÁLIDO ALMACENADO COMO HASH EN remember_token.
    public function resetPassword(): ResponseInterface
    {
        $input = $this->getRequestPayload();

        $rules = [
            'token'            => 'required',
            'password'         => 'required|min_length[8]',
            'password_confirm' => 'required|matches[password]',
        ];
        $messages = [
            'password' => [
                'min_length' => 'La contraseña debe tener al menos 8 caracteres.',
            ],
        ];

        if (!$this->validate($rules, $messages)) {
            return apiError('Error de validación', 422, $this->validator->getErrors());
        }

        $tokenHash = hash('sha256', $input['token']);
        $user = $this->userModel->where('remember_token', $tokenHash)->first();

        if (!$user) {
            return apiError('Token de reseteo inválido o expirado', 400);
        }

        $this->userModel->skipValidation(true)->update($user['id'], [
            'password'       => $input['password'],
            'remember_token' => null,
        ]);

        return apiResponse(null, 200, 'Contraseña actualizada correctamente');
    }

    // ELIMINA CAMPOS SENSIBLES DEL ARRAY DE USUARIO ANTES DE ENVIARLO AL CLIENTE.
    protected function sanitizeUser(?array $user): array
    {
        if ($user === null) {
            return [];
        }
        unset($user['password'], $user['remember_token']);
        return $user;
    }

    // COMPRUEBA SI LA CUENTA ESTÁ BLOQUEADA HASTA locked_until (FECHA FUTURA).
    protected function isUserLocked(array $user): bool
    {
        if (empty($user['locked_until'])) {
            return false;
        }
        return strtotime((string) $user['locked_until']) > time();
    }

    // INCREMENTA INTENTOS FALLIDOS Y BLOQUEA 15 MINUTOS TRAS 5 FALLOS.
    protected function recordFailedLogin(array $user): void
    {
        $attempts = (int) ($user['failed_login_attempts'] ?? 0) + 1;
        $update = ['failed_login_attempts' => $attempts];

        if ($attempts >= 5) {
            $update['locked_until'] = date('Y-m-d H:i:s', strtotime('+15 minutes'));
        }

        $this->userModel->skipValidation(true)->update($user['id'], $update);
    }

    // OBTIENE EL CUERPO DE LA PETICIÓN COMO ARRAY: PRIORIZA JSON Y SI NO, POST.
    protected function getRequestPayload(): array
    {
        $json = $this->request->getJSON(true);
        if (is_array($json) && !empty($json)) {
            return $json;
        }
        return $this->request->getPost() ?? [];
    }
}