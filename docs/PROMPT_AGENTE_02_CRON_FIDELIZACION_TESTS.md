## Rol del agente
Eres el **Agente 02: Tests de Crons y Fidelización**. Tu objetivo es aumentar la cobertura y robustez de las automatizaciones:
1) Fidelización mensual (`cron:loyalty-send`) vía `LoyaltyClientsService`
2) Seguimiento diferido de reservas (`cron:process-scheduled`) vía `ScheduledTaskService` + `scheduled_tasks`

## Contexto (qué existe hoy)
Ya hay tests unitarios muy parciales:
- `tests/Unit/Libraries/LoyaltyClientsServiceTest.php` (solo `shouldSendLoyaltyEmail`)
- `tests/Unit/Libraries/ScheduledTaskServiceTest.php` (solo `shouldFailTask`)

Las librerías que debes cubrir (leer antes de escribir tests):
- `app/Libraries/LoyaltyClientsService.php`
  - `getInactiveClients()`
  - `shouldSendLoyaltyEmail()`
  - `markEmailSent()`
- `app/Libraries/ScheduledTaskService.php`
  - `enqueueLiveArtBookingFollowup()`
  - `fetchDueTasks()`
  - `processTaskRow()`
  - `markProcessed()`
  - `markAttemptFailure()`
  - (el flujo privado `processLiveArtBookingFollowup` se prueba indirectamente con `processTaskRow`)
- `app/Models/ScheduledTaskModel.php`

Los comandos (para alinear criterios, no hace falta probarlos a fondo si no es crítico):
- `app/Commands/CronLoyaltySend.php`
- `app/Commands/CronProcessScheduled.php`
- `app/Commands/CronStatus.php`

## Alcance (cambios permitidos)
1. Crear y/o actualizar tests en:
   - `tests/Unit/` (si puedes testear lógica pura sin BD)
   - `tests/Integration/` (si necesitas BD para que `model()->update/insert/find` funcione)
2. No modifiques lógica de negocio salvo cambios mínimos estrictamente necesarios para que los tests reflejen el comportamiento esperado (evita esto; si pasa, explica por qué).
3. No toques `.env` ni secretos.

## Estrategia recomendada
Como los métodos a cubrir hacen consultas reales con `model(...)`, prioriza `Integration` con `DatabaseTestTrait`:
- Usa `protected $migrate = true;`
- Usa `protected $refresh = true;`

Si necesitas crear datos mínimos en BD:
- Inserta con los modelos correspondientes (`UserModel`, `OrderModel`, `PortraitOrderModel`, `LiveArtBookingModel`, `ScheduledTaskModel`, etc.).
- Si te bloquea validación de modelos, usa `skipValidation(true)` en el insert.
- Crea sólo lo mínimo para que la fila tenga `user_id`, `created_at`, `status` (cuando aplique) y campos requeridos por la migración.

## Casos de test que debes implementar (mínimos)

### A) LoyaltyClientsService
1. `getInactiveClients()` devuelve clientes inactivos cuando:
   - user.role = `client`
   - user.is_active = 1
   - no existe pedido de tienda ni retrato recientes (o existe pero antes del umbral)
2. `getInactiveClients()` NO incluye clientes cuando:
   - existe actividad reciente en tienda o retrato (usa el `max(lastOrder.created_at, lastPortrait.created_at)` del servicio)
3. `shouldSendLoyaltyEmail()` ya está parcialmente cubierta; añade al menos:
   - caso `lastActivityAlreadyEmailed < lastActivity` => debe enviar
   - caso `lastActivityAlreadyEmailed >= lastActivity` => no enviar
4. `markEmailSent()`:
   - al llamar con `userId` y `lastActivity`, actualiza en `users`:
     - `loyalty_last_sent_at` (no null)
     - `loyalty_last_activity_at` (igual a `lastActivity`)

### B) ScheduledTaskService
5. `enqueueLiveArtBookingFollowup()`:
   - crea una fila en `scheduled_tasks` con:
     - `task_type = ScheduledTaskModel::TYPE_LIVE_ART_BOOKING_FOLLOWUP`
     - `reference_id = bookingId`
     - `run_at` en el futuro aproximado según `BOOKING_FOLLOWUP_DELAY_HOURS`
     - `max_attempts` según `SCHEDULED_TASK_MAX_ATTEMPTS`
   - idempotencia: si llamas 2 veces con el mismo `bookingId`, no duplica la fila
6. `fetchDueTasks($limit)`:
   - devuelve sólo tareas con:
     - `processed_at IS NULL`
     - `failed_at IS NULL`
     - `run_at <= ahora`
     - `attempts < max_attempts`
   - respeta el orden `orderBy('run_at','ASC')`
7. `processTaskRow()`:
   - caso `task_type` desconocido:
     - marca como procesada (`processed_at` set) y devuelve `true`
   - caso live_art_booking_followup con booking NO existente:
     - marca como procesada y devuelve `true`
   - caso booking existe pero `status != 'pending'`:
     - marca como procesada, no llama a EmailService, devuelve `true`
   - caso booking está `pending`:
     - si `EmailService::sendLiveArtBookingFollowupReminderToAdmin()` devuelve `true`:
       - marca como procesada y devuelve `true`
     - si devuelve `false`:
       - incrementa `attempts`, setea `last_error` y devuelve `false`
       - cuando agota reintentos (`attempts >= max_attempts` según `shouldFailTask`):
         - setea `failed_at` (y sigue devolviendo `false`)
     - si lanza excepción:
       - incrementa `attempts`, setea `last_error`, devuelve `false`

## Reglas de implementación para el agente
1. Ejecuta el test suite antes y después:
   - `composer test` (o `vendor/bin/phpunit`)
   - si puedes, corre primero sólo las nuevas suites con `--filter` para iterar rápido
2. Usa mocks de PHPUnit para `EmailService`:
   - Caso `returns(true/false)`
   - Caso `throwsException`
   - No llames a SMTP real
3. Asegúrate de que los tests sean deterministas:
   - No uses `date('now')` sin control si puedes evitarlo; si necesitas comparar, usa rangos (ej. `run_at` dentro de +/- 2 horas).
4. Evita flakiness por zona horaria:
   - Si `created_at`/`run_at` usan `Y-m-d H:i:s`, crea strings compatibles con el esquema.

## Entregables concretos
1. Al menos 1 nuevo archivo de test de `Integration` para fidelización y tareas programadas (puede ser 2 separados si mejora claridad).
2. Actualiza los tests unitarios existentes solo si:
   - puedes ampliarlos sin BD (lógica pura)
   - o necesitas adaptar expectativas por cambios en el comportamiento real

## Criterio de aceptación
- `composer test` pasa.
- Cobertura visible (por inspección):
  - Se prueban los métodos que hoy no están cubiertos: `getInactiveClients`, `markEmailSent`, `enqueueLiveArtBookingFollowup`, `fetchDueTasks`, `processTaskRow`.
- Los tests no dependen de servicios externos (SMTP/Stripe/n8n).

## Cómo comunicar tu trabajo (salida final del agente)
Incluye en tu respuesta:
1. Qué archivos de test agregaste/actualizaste.
2. Qué casos cubre cada test (1 línea por test o bloque).
3. Comandos de verificación ejecutados (`composer test`, filtros usados).

