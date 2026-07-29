# Especificación de Esquema de Base de Datos para Laravel (Gestor de Secretos)

Este documento contiene la estructura analizada del script SQL original, optimizada para que un modelo de IA (o desarrollador) construya las migraciones y modelos de **Laravel** de forma ultra-rápida. 

---

## 🛠 Directrices para Laravel (Convenciones)

1. **Esquema Personalizado**: El script utiliza el esquema `secretos`. En las migraciones de Laravel, se debe especificar `$table->schema('secretos')` o configurar el `search_path` en la conexión de base de datos.
2. **Timestamps Personalizados**: El sistema usa `creado_en` y `actualizado_en` en lugar de `created_at` y `updated_at`. 
   - *Migración*: `$table->timestampTz('creado_en')->useCurrent(); $table->timestampTz('actualizado_en')->useCurrent();`
   - *Modelos*: Definir `const CREATED_AT = 'creado_en';` y `const UPDATED_AT = 'actualizado_en';`.
3. **Llaves Primarias**: Usar `$table->id();` (equivale a `BIGINT GENERATED ALWAYS AS IDENTITY`).
4. **Tipos Enumerados (ENUM)**: Implementar mediante `$table->enum('columna', ['val1', 'val2'])` en migraciones, y usar **PHP Enums** nativos (PHP 8.1+) en los modelos con `$casts`.
5. **Soft Deletes**: Aunque hay un estado `eliminado` en `usuarios`, evaluar si se usa el estado lógico o si se implementará `$table->softDeletesTz()`.

---

## 📦 1. Tipos de Datos y Enums (Catálogos Internos)

En Laravel, estos se traducirán en arreglos de `$table->enum()` o tablas de catálogo:

| Nombre ENUM / Catálogo | Valores Permitidos | Modelo Sugerido |
| :--- | :--- | :--- |
| `estado_usuario` | `activo`, `suspendido`, `eliminado` | `UserStatus::class` (Enum) |
| `estado_nota_compartida` | `enviada`, `leida`, `revocada` | `SharedNoteStatus::class` (Enum) |
| `estado_interruptor` | `activo`, `pausado`, `disparado` | `SwitchStatus::class` (Enum) |
| `metodo_envio_mfa` | `correo`, `sms` | `MfaMethod::class` (Enum) |
| **Tabla:** `tipos_tarjeta` | `credito`, `debito` | `CardType` (Modelo de Catálogo) |

---

## 🗄 2. Entidades Principales (Modelos y Migraciones)

A continuación, la estructura de cada tabla desglosada para su traducción a `Blueprint`.

### 2.1 Usuarios (`usuarios`)
Entidad raíz del sistema.
* **Modelo:** `User`
* **Columnas:**
  * `id` (PK, BigInt)
  * `nombre_completo` (String, 150)
  * `correo_electronico` (String / CITEXT, Unique) - *Nota: Requiere extensión CITEXT*.
  * `hash_contrasena` (Text)
  * `sal_contrasena` (Text)
  * `estado` (Enum `estado_usuario`, default 'activo')
  * `creado_en`, `actualizado_en` (TimestampTz)

### 2.2 Tarjetas (`tarjetas`)
Tarjetas de pago encriptadas.
* **Modelo:** `Card` | **Relaciones:** `belongsTo(User::class)`, `belongsTo(CardType::class)`
* **Columnas:**
  * `id` (PK, BigInt)
  * `usuario_id` (FK -> `usuarios.id`, Cascade)
  * `tipo_tarjeta_id` (FK -> `tipos_tarjeta.id`, Restrict)
  * `alias` (String, 100)
  * `numero_encriptado` (Text)
  * `ultimos_4_digitos` (Char 4) - *Check constraint: `^[0-9]{4}$`*
  * `nombre_titular` (String, 150)
  * `fecha_expiracion` (Date)
  * `cvv_encriptado` (Text)
  * `banco_emisor` (String, 100, Nullable)
  * `creado_en` (TimestampTz)

### 2.3 Logins (`logins`)
Credenciales web de usuarios.
* **Modelo:** `Login` | **Relaciones:** `belongsTo(User::class)`
* **Columnas:**
  * `id` (PK, BigInt)
  * `usuario_id` (FK -> `usuarios.id`, Cascade)
  * `nombre_sitio` (String, 150)
  * `url` (Text, Nullable)
  * `usuario_login` (String, 150, Nullable)
  * `contrasena_encriptada` (Text)
  * `notas` (Text, Nullable)
  * `creado_en`, `actualizado_en` (TimestampTz)

### 2.4 Llaves SSH (`llaves_ssh`)
* **Modelo:** `SshKey` | **Relaciones:** `belongsTo(User::class)`
* **Columnas:**
  * `id` (PK, BigInt)
  * `usuario_id` (FK -> `usuarios.id`, Cascade)
  * `nombre` (String, 100)
  * `llave_privada_encriptada` (Text)
  * `llave_publica` (Text, Nullable)
  * `frase_paso_encriptada` (Text, Nullable)
  * `creado_en` (TimestampTz)

### 2.5 Notas Seguras (`notas_seguras`)
* **Modelo:** `SecureNote` | **Relaciones:** `belongsTo(User::class)`
* **Columnas:**
  * `id` (PK, BigInt)
  * `usuario_id` (FK -> `usuarios.id`, Cascade)
  * `titulo` (String, 150)
  * `contenido_encriptado` (Text)
  * `creado_en`, `actualizado_en` (TimestampTz)

### 2.6 Archivos (`archivos`)
* **Modelo:** `File` | **Relaciones:** `belongsTo(User::class)`
* **Columnas:**
  * `id` (PK, BigInt)
  * `usuario_id` (FK -> `usuarios.id`, Cascade)
  * `nombre_archivo` (String, 255)
  * `ruta_almacenamiento` (Text)
  * `hash_sha256` (Char 64)
  * `tamanio_bytes` (BigInt) - *Check constraint: `> 0`*
  * `es_encriptado` (Boolean, default true)
  * `subido_en` (TimestampTz)

---

## 🤝 3. Componentes de Compartición y N:M

### 3.1 Archivos Compartidos (`archivos_compartidos`)
Enlaces de acceso controlado.
* **Modelo:** `SharedFile`
* **Relaciones:** `belongsTo(File::class)`, `belongsTo(User::class, 'usuario_origen_id')`, `belongsTo(User::class, 'usuario_destino_id')`
* **Columnas Notables:**
  * `usuario_destino_id` (Nullable - para enlaces públicos)
  * `token_acceso` (UUID, default gen_random_uuid(), Unique)
  * `max_descargas` / `descargas_realizadas` (Integer, Lógica de límite aplicada en BD con Check constraints).

### 3.2 Notas Compartidas (`notas_compartidas`)
* **Modelo:** `SharedNote`
* **Relaciones:** Origen (User), Destino (User), Nota (SecureNote).
* **Columnas Notables:** `estado` (Enum `estado_nota_compartida`), `leido_en` (TimestampTz, Nullable).

### 3.3 Interruptor de Emergencia (Dead Man Switch)
Módulo compuesto por 3 tablas:
1. `interruptores_emergencia` (Modelo: `EmergencySwitch`)
   - Relacionado a usuario. Almacena lógica de tiempo (`intervalo_confirmacion` INTERVAL, `vence_en` TIMESTAMPTZ, `token_confirmacion` UUID).
2. `interruptor_destinatarios` (Modelo: `SwitchRecipient`)
   - Destinatarios por interruptor (1:N).
3. `interruptor_archivos` (Tabla Pivote)
   - Relación N:M entre `EmergencySwitch` y `File`. En Laravel, se define mediante `belongsToMany` en el modelo `EmergencySwitch` apuntando a `File::class`.

---

## 🛡 4. MFA y Logs (Tablas Particionadas de Alto Volumen)

El script implementa particionamiento (`PARTITION BY RANGE`). En Laravel, la migración principal debe usar sentencias `DB::statement()` para crear la tabla maestra de forma particionada, ya que Schema Builder no soporta particiones nativas por defecto.

### 4.1 Configuraciones MFA (`configuraciones_mfa`)
* Relación 1:1 estricta con `usuarios` (`uq_configuraciones_mfa_usuario_id`).

### 4.2 Log de Descargas (`log_descargas`) - **PARTICIONADA**
* **Modelo:** `DownloadLog`
* **Llave Primaria Compuesta:** `id` y `descargado_en`.
* **Columnas:** `archivo_compartido_id`, `ip_origen` (INET -> `$table->ipAddress('ip_origen')`), `descargado_en`.

### 4.3 Códigos MFA (`codigos_mfa`) - **PARTICIONADA**
* **Modelo:** `MfaCode`
* **Llave Primaria Compuesta:** `id` y `generado_en`.

*Nota sobre Migraciones de Particiones:* Para implementar las tablas 4.2 y 4.3 en Laravel, crea la tabla particionada con `DB::statement()` y luego corre un Seeder o un Command de Artisan programado para disparar el SP `secretos.sp_crear_particion_bimestral`.

---

## 🚀 Resumen Rápido para Prompting del Generador de Código

Copia y pega esto al AI que generará tu código Laravel:

> **Contexto:** Construye modelos, migraciones y fábricas (factories) para un Gestor de Secretos en Laravel 11.
> **Reglas:** 
> - Usa `$table->schema('secretos')` si es posible, o asume esquema por defecto si la base de datos ya lo maneja.
> - Usa Enums de PHP 8.1 en `$casts`.
> - Reemplaza `created_at` / `updated_at` por `creado_en` y `actualizado_en` usando constantes en los Modelos.
> - Usa `$table->uuid()` para tokens y `$table->ipAddress()` para IPs.
> - Crea relaciones correctas (`belongsTo`, `hasMany`, `belongsToMany` para `interruptor_archivos`).
> - Usa `DB::statement()` en migraciones para las restricciones CHECK complejas (`chk_tarjetas_ultimos_4_digitos`, `chk_archivos_tamanio_bytes`).