-- =====================================================================
-- SISTEMA GESTOR DE SECRETOS (estilo Bitwarden)
-- Script de creación de base de datos - PostgreSQL 17
-- =====================================================================
--
-- ESTÁNDAR DE NOMBRADO
-- ---------------------------------------------------------------------
-- Base de datos : snake_case, minúsculas, singular  -> gestor_secretos
-- Esquema       : snake_case, minúsculas             -> secretos
-- Tablas        : snake_case, plural                 -> usuarios, logins
-- Columnas      : snake_case, singular                -> usuario_id
-- Llave primaria: siempre "id" (BIGINT/SMALLINT IDENTITY)
-- Llave foránea : "<entidad_singular>_id"             -> usuario_id
-- Booleanos     : prefijo "es_"                        -> es_activo
-- Fechas/horas  : sufijo "_en"                         -> creado_en
-- Tipos ENUM    : prefijo "estado_" / "metodo_"        -> estado_usuario
-- Constraints   : pk_<tabla>, fk_<tabla>_<referencia>,
--                 uq_<tabla>_<columna>, ck_<tabla>_<regla>
-- Índices       : idx_<tabla>_<columna(s)>
-- Funciones     : fn_<accion>          Procedimientos: sp_<accion>
-- Triggers      : tr_<tabla>_<evento>
-- =====================================================================

-- ============================
-- 0. BASE DE DATOS Y ESQUEMA
-- ============================
-- Ejecutar conectado como superusuario o rol con privilegios de creación.
-- (Descomentar si se ejecuta fuera de una base ya creada)
-- CREATE DATABASE gestor_secretos
--   WITH ENCODING = 'UTF8'
--   LC_COLLATE = 'es_GT.UTF-8'
--   LC_CTYPE   = 'es_GT.UTF-8'
--   TEMPLATE   = template0;
-- \c gestor_secretos

CREATE SCHEMA IF NOT EXISTS secretos;
COMMENT ON SCHEMA secretos IS 'Esquema principal del sistema gestor de secretos (bóvedas, archivos, MFA).';

SET search_path TO secretos, public;

-- Extensión para correos electrónicos case-insensitive sin duplicados
CREATE EXTENSION IF NOT EXISTS citext;
-- gen_random_uuid() es nativo desde PostgreSQL 13 (no requiere pgcrypto)

-- ============================
-- 1. TIPOS ENUMERADOS
-- ============================
CREATE TYPE secretos.estado_usuario AS ENUM ('activo', 'suspendido', 'eliminado');
COMMENT ON TYPE secretos.estado_usuario IS 'Estados posibles de una cuenta de usuario.';

CREATE TYPE secretos.estado_nota_compartida AS ENUM ('enviada', 'leida', 'revocada');
COMMENT ON TYPE secretos.estado_nota_compartida IS 'Ciclo de vida de una nota segura enviada entre cuentas.';

CREATE TYPE secretos.estado_interruptor AS ENUM ('activo', 'pausado', 'disparado');
COMMENT ON TYPE secretos.estado_interruptor IS 'Estado del interruptor de hombre muerto (dead man switch).';

CREATE TYPE secretos.metodo_envio_mfa AS ENUM ('correo', 'sms');
COMMENT ON TYPE secretos.metodo_envio_mfa IS 'Canal interno usado para entregar el código MFA (sin apps externas).';

-- ============================
-- 2. FUNCIÓN GENÉRICA DE AUDITORÍA
-- ============================
CREATE OR REPLACE FUNCTION secretos.fn_actualizar_timestamp()
RETURNS trigger
LANGUAGE plpgsql
AS $$
BEGIN
  NEW.actualizado_en := now();
  RETURN NEW;
END;
$$;
COMMENT ON FUNCTION secretos.fn_actualizar_timestamp() IS 'Actualiza automáticamente la columna actualizado_en en cada UPDATE.';

-- ============================
-- 3. TABLA: usuarios
-- ============================
DROP TABLE secretos.usuarios CASCADE;
CREATE TABLE secretos.usuarios (
  id                  BIGINT GENERATED ALWAYS AS IDENTITY,
  nombre_completo     VARCHAR(150)      NOT NULL,
  correo_electronico  CITEXT            NOT NULL,
  hash_contrasena     TEXT              NOT NULL,
  sal_contrasena      TEXT              NOT NULL,
  estado              secretos.estado_usuario NOT NULL DEFAULT 'activo',
  creado_en           TIMESTAMPTZ       NOT NULL DEFAULT now(),
  actualizado_en      TIMESTAMPTZ       NOT NULL DEFAULT now(),
  CONSTRAINT pk_usuarios PRIMARY KEY (id),
  CONSTRAINT uq_usuarios_correo_electronico UNIQUE (correo_electronico)
);

COMMENT ON TABLE secretos.usuarios IS 'Cuentas de usuario del gestor de secretos; entidad raíz de la que dependen todas las bóvedas.';
COMMENT ON COLUMN secretos.usuarios.id IS 'Identificador único de la cuenta.';
COMMENT ON COLUMN secretos.usuarios.nombre_completo IS 'Nombre completo del titular de la cuenta.';
COMMENT ON COLUMN secretos.usuarios.correo_electronico IS 'Correo electrónico único, usado como identificador de acceso.';
COMMENT ON COLUMN secretos.usuarios.hash_contrasena IS 'Hash de la contraseña maestra (argon2id/bcrypt), nunca texto plano.';
COMMENT ON COLUMN secretos.usuarios.sal_contrasena IS 'Sal criptográfica usada en el hash de la contraseña maestra.';
COMMENT ON COLUMN secretos.usuarios.estado IS 'Estado actual de la cuenta.';
COMMENT ON COLUMN secretos.usuarios.creado_en IS 'Fecha y hora de creación de la cuenta.';
COMMENT ON COLUMN secretos.usuarios.actualizado_en IS 'Fecha y hora de la última modificación del registro.';

CREATE TRIGGER tr_usuarios_actualizar_ts
  BEFORE UPDATE ON secretos.usuarios
  FOR EACH ROW EXECUTE FUNCTION secretos.fn_actualizar_timestamp();

-- ============================
-- 4. TABLA: tipos_tarjeta (catálogo)
-- ============================
CREATE TABLE secretos.tipos_tarjeta (
  id      SMALLINT GENERATED ALWAYS AS IDENTITY,
  nombre  VARCHAR(20) NOT NULL,
  CONSTRAINT pk_tipos_tarjeta PRIMARY KEY (id),
  CONSTRAINT uq_tipos_tarjeta_nombre UNIQUE (nombre)
);

COMMENT ON TABLE secretos.tipos_tarjeta IS 'Catálogo de tipos de tarjeta (crédito, débito).';
COMMENT ON COLUMN secretos.tipos_tarjeta.id IS 'Identificador del tipo de tarjeta.';
COMMENT ON COLUMN secretos.tipos_tarjeta.nombre IS 'Nombre del tipo de tarjeta (credito, debito).';

INSERT INTO secretos.tipos_tarjeta (nombre) VALUES ('credito'), ('debito');

-- ============================
-- 5. TABLA: tarjetas
-- ============================
CREATE TABLE secretos.tarjetas (
  id                  BIGINT GENERATED ALWAYS AS IDENTITY,
  usuario_id          BIGINT       NOT NULL,
  tipo_tarjeta_id     SMALLINT     NOT NULL,
  alias               VARCHAR(100) NOT NULL,
  numero_encriptado   TEXT         NOT NULL,
  ultimos_4_digitos   CHAR(4)      NOT NULL,
  nombre_titular      VARCHAR(150) NOT NULL,
  fecha_expiracion    DATE         NOT NULL,
  cvv_encriptado      TEXT         NOT NULL,
  banco_emisor        VARCHAR(100),
  creado_en           TIMESTAMPTZ  NOT NULL DEFAULT now(),
  CONSTRAINT pk_tarjetas PRIMARY KEY (id),
  CONSTRAINT fk_tarjetas_usuarios FOREIGN KEY (usuario_id)
    REFERENCES secretos.usuarios (id) ON DELETE CASCADE,
  CONSTRAINT fk_tarjetas_tipos_tarjeta FOREIGN KEY (tipo_tarjeta_id)
    REFERENCES secretos.tipos_tarjeta (id) ON DELETE RESTRICT,
  CONSTRAINT ck_tarjetas_ultimos_4_digitos CHECK (ultimos_4_digitos ~ '^[0-9]{4}$')
);

COMMENT ON TABLE secretos.tarjetas IS 'Tarjetas de crédito o débito almacenadas de forma cifrada por usuario.';
COMMENT ON COLUMN secretos.tarjetas.id IS 'Identificador de la tarjeta.';
COMMENT ON COLUMN secretos.tarjetas.usuario_id IS 'Usuario propietario de la tarjeta.';
COMMENT ON COLUMN secretos.tarjetas.tipo_tarjeta_id IS 'Referencia al catálogo de tipos de tarjeta.';
COMMENT ON COLUMN secretos.tarjetas.alias IS 'Nombre descriptivo asignado por el usuario.';
COMMENT ON COLUMN secretos.tarjetas.numero_encriptado IS 'Número completo de la tarjeta, cifrado en la capa de aplicación.';
COMMENT ON COLUMN secretos.tarjetas.ultimos_4_digitos IS 'Últimos 4 dígitos en claro, solo para referencia visual en la UI.';
COMMENT ON COLUMN secretos.tarjetas.nombre_titular IS 'Nombre del titular impreso en la tarjeta.';
COMMENT ON COLUMN secretos.tarjetas.fecha_expiracion IS 'Fecha de vencimiento de la tarjeta.';
COMMENT ON COLUMN secretos.tarjetas.cvv_encriptado IS 'Código de seguridad cifrado en la capa de aplicación.';
COMMENT ON COLUMN secretos.tarjetas.banco_emisor IS 'Nombre del banco o entidad emisora.';
COMMENT ON COLUMN secretos.tarjetas.creado_en IS 'Fecha y hora en que se guardó la tarjeta.';

CREATE INDEX idx_tarjetas_usuario_id ON secretos.tarjetas (usuario_id);

-- ============================
-- 6. TABLA: logins
-- ============================
CREATE TABLE secretos.logins (
  id                      BIGINT GENERATED ALWAYS AS IDENTITY,
  usuario_id              BIGINT       NOT NULL,
  nombre_sitio            VARCHAR(150) NOT NULL,
  url                     TEXT,
  usuario_login           VARCHAR(150),
  contrasena_encriptada   TEXT         NOT NULL,
  notas                   TEXT,
  creado_en               TIMESTAMPTZ  NOT NULL DEFAULT now(),
  actualizado_en          TIMESTAMPTZ  NOT NULL DEFAULT now(),
  CONSTRAINT pk_logins PRIMARY KEY (id),
  CONSTRAINT fk_logins_usuarios FOREIGN KEY (usuario_id)
    REFERENCES secretos.usuarios (id) ON DELETE CASCADE
);

COMMENT ON TABLE secretos.logins IS 'Credenciales de acceso (usuario/contraseña) guardadas por cada usuario.';
COMMENT ON COLUMN secretos.logins.id IS 'Identificador del login.';
COMMENT ON COLUMN secretos.logins.usuario_id IS 'Usuario propietario del login.';
COMMENT ON COLUMN secretos.logins.nombre_sitio IS 'Nombre descriptivo del sitio o servicio.';
COMMENT ON COLUMN secretos.logins.url IS 'URL asociada al login.';
COMMENT ON COLUMN secretos.logins.usuario_login IS 'Usuario o correo utilizado para iniciar sesión en el sitio.';
COMMENT ON COLUMN secretos.logins.contrasena_encriptada IS 'Contraseña cifrada en la capa de aplicación.';
COMMENT ON COLUMN secretos.logins.notas IS 'Notas adicionales en texto libre.';
COMMENT ON COLUMN secretos.logins.creado_en IS 'Fecha y hora de creación del registro.';
COMMENT ON COLUMN secretos.logins.actualizado_en IS 'Fecha y hora de la última modificación.';

CREATE INDEX idx_logins_usuario_id ON secretos.logins (usuario_id);

CREATE TRIGGER tr_logins_actualizar_ts
  BEFORE UPDATE ON secretos.logins
  FOR EACH ROW EXECUTE FUNCTION secretos.fn_actualizar_timestamp();

-- ============================
-- 7. TABLA: llaves_ssh
-- ============================
CREATE TABLE secretos.llaves_ssh (
  id                          BIGINT GENERATED ALWAYS AS IDENTITY,
  usuario_id                  BIGINT       NOT NULL,
  nombre                      VARCHAR(100) NOT NULL,
  llave_privada_encriptada    TEXT         NOT NULL,
  llave_publica                TEXT,
  frase_paso_encriptada       TEXT,
  creado_en                   TIMESTAMPTZ  NOT NULL DEFAULT now(),
  CONSTRAINT pk_llaves_ssh PRIMARY KEY (id),
  CONSTRAINT fk_llaves_ssh_usuarios FOREIGN KEY (usuario_id)
    REFERENCES secretos.usuarios (id) ON DELETE CASCADE
);

COMMENT ON TABLE secretos.llaves_ssh IS 'Pares de llaves SSH almacenados de forma cifrada por usuario.';
COMMENT ON COLUMN secretos.llaves_ssh.id IS 'Identificador de la llave SSH.';
COMMENT ON COLUMN secretos.llaves_ssh.usuario_id IS 'Usuario propietario de la llave.';
COMMENT ON COLUMN secretos.llaves_ssh.nombre IS 'Nombre descriptivo de la llave.';
COMMENT ON COLUMN secretos.llaves_ssh.llave_privada_encriptada IS 'Llave privada cifrada en la capa de aplicación.';
COMMENT ON COLUMN secretos.llaves_ssh.llave_publica IS 'Llave pública asociada, almacenada en claro.';
COMMENT ON COLUMN secretos.llaves_ssh.frase_paso_encriptada IS 'Passphrase de la llave privada, cifrada.';
COMMENT ON COLUMN secretos.llaves_ssh.creado_en IS 'Fecha y hora de creación del registro.';

CREATE INDEX idx_llaves_ssh_usuario_id ON secretos.llaves_ssh (usuario_id);

-- ============================
-- 8. TABLA: notas_seguras
-- ============================
CREATE TABLE secretos.notas_seguras (
  id                      BIGINT GENERATED ALWAYS AS IDENTITY,
  usuario_id              BIGINT       NOT NULL,
  titulo                  VARCHAR(150) NOT NULL,
  contenido_encriptado    TEXT         NOT NULL,
  creado_en               TIMESTAMPTZ  NOT NULL DEFAULT now(),
  actualizado_en          TIMESTAMPTZ  NOT NULL DEFAULT now(),
  CONSTRAINT pk_notas_seguras PRIMARY KEY (id),
  CONSTRAINT fk_notas_seguras_usuarios FOREIGN KEY (usuario_id)
    REFERENCES secretos.usuarios (id) ON DELETE CASCADE
);

COMMENT ON TABLE secretos.notas_seguras IS 'Notas de texto cifradas guardadas por cada usuario.';
COMMENT ON COLUMN secretos.notas_seguras.id IS 'Identificador de la nota segura.';
COMMENT ON COLUMN secretos.notas_seguras.usuario_id IS 'Usuario propietario de la nota.';
COMMENT ON COLUMN secretos.notas_seguras.titulo IS 'Título descriptivo de la nota.';
COMMENT ON COLUMN secretos.notas_seguras.contenido_encriptado IS 'Contenido de la nota, cifrado en la capa de aplicación.';
COMMENT ON COLUMN secretos.notas_seguras.creado_en IS 'Fecha y hora de creación de la nota.';
COMMENT ON COLUMN secretos.notas_seguras.actualizado_en IS 'Fecha y hora de la última modificación.';

CREATE INDEX idx_notas_seguras_usuario_id ON secretos.notas_seguras (usuario_id);

CREATE TRIGGER tr_notas_seguras_actualizar_ts
  BEFORE UPDATE ON secretos.notas_seguras
  FOR EACH ROW EXECUTE FUNCTION secretos.fn_actualizar_timestamp();

-- ============================
-- 9. TABLA: archivos
-- ============================
CREATE TABLE secretos.archivos (
  id                      BIGINT GENERATED ALWAYS AS IDENTITY,
  usuario_id              BIGINT        NOT NULL,
  nombre_archivo          VARCHAR(255)  NOT NULL,
  ruta_almacenamiento     TEXT          NOT NULL,
  hash_sha256             CHAR(64)      NOT NULL,
  tamanio_bytes           BIGINT        NOT NULL,
  es_encriptado           BOOLEAN       NOT NULL DEFAULT TRUE,
  subido_en               TIMESTAMPTZ   NOT NULL DEFAULT now(),
  CONSTRAINT pk_archivos PRIMARY KEY (id),
  CONSTRAINT fk_archivos_usuarios FOREIGN KEY (usuario_id)
    REFERENCES secretos.usuarios (id) ON DELETE CASCADE,
  CONSTRAINT ck_archivos_tamanio_bytes CHECK (tamanio_bytes > 0)
);

COMMENT ON TABLE secretos.archivos IS 'Metadatos de archivos cifrados subidos por el usuario; el binario reside en almacenamiento externo.';
COMMENT ON COLUMN secretos.archivos.id IS 'Identificador del archivo.';
COMMENT ON COLUMN secretos.archivos.usuario_id IS 'Usuario propietario del archivo.';
COMMENT ON COLUMN secretos.archivos.nombre_archivo IS 'Nombre original del archivo.';
COMMENT ON COLUMN secretos.archivos.ruta_almacenamiento IS 'Ruta o clave del objeto en el almacenamiento (bucket/ruta).';
COMMENT ON COLUMN secretos.archivos.hash_sha256 IS 'Huella SHA-256 del archivo cifrado, para verificación de integridad.';
COMMENT ON COLUMN secretos.archivos.tamanio_bytes IS 'Tamaño del archivo en bytes.';
COMMENT ON COLUMN secretos.archivos.es_encriptado IS 'Indica si el archivo está cifrado en reposo.';
COMMENT ON COLUMN secretos.archivos.subido_en IS 'Fecha y hora de subida del archivo.';

CREATE INDEX idx_archivos_usuario_id ON secretos.archivos (usuario_id);


-- ============================
-- 10. TABLA: archivos_compartidos
-- ============================
CREATE TABLE secretos.archivos_compartidos (
  id                        BIGINT GENERATED ALWAYS AS IDENTITY,
  archivo_id                BIGINT       NOT NULL,
  usuario_origen_id         BIGINT       NOT NULL,
  usuario_destino_id        BIGINT,
  hash_contrasena_acceso    TEXT,
  token_acceso              UUID         NOT NULL DEFAULT gen_random_uuid(),
  max_descargas             INTEGER      NOT NULL DEFAULT 1,
  descargas_realizadas      INTEGER      NOT NULL DEFAULT 0,
  expira_en                 TIMESTAMPTZ  NOT NULL,
  creado_en                 TIMESTAMPTZ  NOT NULL DEFAULT now(),
  CONSTRAINT pk_archivos_compartidos PRIMARY KEY (id),
  CONSTRAINT fk_archivos_compartidos_archivos FOREIGN KEY (archivo_id)
    REFERENCES secretos.archivos (id) ON DELETE CASCADE,
  CONSTRAINT fk_archivos_compartidos_origen FOREIGN KEY (usuario_origen_id)
    REFERENCES secretos.usuarios (id) ON DELETE CASCADE,
  CONSTRAINT fk_archivos_compartidos_destino FOREIGN KEY (usuario_destino_id)
    REFERENCES secretos.usuarios (id) ON DELETE SET NULL,
  CONSTRAINT uq_archivos_compartidos_token UNIQUE (token_acceso),
  CONSTRAINT ck_archivos_compartidos_max_descargas CHECK (max_descargas > 0),
  CONSTRAINT ck_archivos_compartidos_descargas CHECK (descargas_realizadas BETWEEN 0 AND max_descargas)
);

COMMENT ON TABLE secretos.archivos_compartidos IS 'Enlaces de acceso controlado usados para compartir un archivo, con límite de descargas, contraseña y expiración.';
COMMENT ON COLUMN secretos.archivos_compartidos.id IS 'Identificador del enlace compartido.';
COMMENT ON COLUMN secretos.archivos_compartidos.archivo_id IS 'Archivo que se comparte.';
COMMENT ON COLUMN secretos.archivos_compartidos.usuario_origen_id IS 'Usuario que comparte el archivo.';
COMMENT ON COLUMN secretos.archivos_compartidos.usuario_destino_id IS 'Usuario destinatario si el enlace es dirigido a una cuenta específica; nulo si es un enlace público.';
COMMENT ON COLUMN secretos.archivos_compartidos.hash_contrasena_acceso IS 'Hash de la contraseña requerida para acceder al enlace.';
COMMENT ON COLUMN secretos.archivos_compartidos.token_acceso IS 'Token único usado en la URL de acceso al archivo compartido.';
COMMENT ON COLUMN secretos.archivos_compartidos.max_descargas IS 'Número máximo de descargas permitidas.';
COMMENT ON COLUMN secretos.archivos_compartidos.descargas_realizadas IS 'Contador de descargas realizadas hasta el momento.';
COMMENT ON COLUMN secretos.archivos_compartidos.expira_en IS 'Fecha y hora en que el enlace deja de estar disponible.';
COMMENT ON COLUMN secretos.archivos_compartidos.creado_en IS 'Fecha y hora en que se creó el enlace compartido.';

CREATE INDEX idx_archivos_compartidos_archivo_id ON secretos.archivos_compartidos (archivo_id);
CREATE INDEX idx_archivos_compartidos_usuario_destino_id ON secretos.archivos_compartidos (usuario_destino_id);

-- ============================
-- 11. TABLA: notas_compartidas
-- ============================
CREATE TABLE secretos.notas_compartidas (
  id                    BIGINT GENERATED ALWAYS AS IDENTITY,
  nota_id               BIGINT       NOT NULL,
  usuario_origen_id     BIGINT       NOT NULL,
  usuario_destino_id    BIGINT       NOT NULL,
  estado                secretos.estado_nota_compartida NOT NULL DEFAULT 'enviada',
  enviado_en            TIMESTAMPTZ  NOT NULL DEFAULT now(),
  leido_en              TIMESTAMPTZ,
  CONSTRAINT pk_notas_compartidas PRIMARY KEY (id),
  CONSTRAINT fk_notas_compartidas_notas FOREIGN KEY (nota_id)
    REFERENCES secretos.notas_seguras (id) ON DELETE CASCADE,
  CONSTRAINT fk_notas_compartidas_origen FOREIGN KEY (usuario_origen_id)
    REFERENCES secretos.usuarios (id) ON DELETE CASCADE,
  CONSTRAINT fk_notas_compartidas_destino FOREIGN KEY (usuario_destino_id)
    REFERENCES secretos.usuarios (id) ON DELETE CASCADE
);

COMMENT ON TABLE secretos.notas_compartidas IS 'Registro de envío de notas seguras entre cuentas de usuario.';
COMMENT ON COLUMN secretos.notas_compartidas.id IS 'Identificador del envío.';
COMMENT ON COLUMN secretos.notas_compartidas.nota_id IS 'Nota segura que se comparte.';
COMMENT ON COLUMN secretos.notas_compartidas.usuario_origen_id IS 'Usuario que envía la nota.';
COMMENT ON COLUMN secretos.notas_compartidas.usuario_destino_id IS 'Usuario que recibe la nota.';
COMMENT ON COLUMN secretos.notas_compartidas.estado IS 'Estado actual del envío.';
COMMENT ON COLUMN secretos.notas_compartidas.enviado_en IS 'Fecha y hora de envío.';
COMMENT ON COLUMN secretos.notas_compartidas.leido_en IS 'Fecha y hora en que el destinatario leyó la nota.';

CREATE INDEX idx_notas_compartidas_usuario_destino_id ON secretos.notas_compartidas (usuario_destino_id);

-- ============================
-- 12. TABLA: interruptores_emergencia (dead man switch)
-- ============================
CREATE TABLE secretos.interruptores_emergencia (
  id                        BIGINT GENERATED ALWAYS AS IDENTITY,
  usuario_id                BIGINT       NOT NULL,
  nombre                    VARCHAR(100) NOT NULL,
  intervalo_confirmacion    INTERVAL     NOT NULL DEFAULT '24 hours',
  token_confirmacion        UUID         NOT NULL DEFAULT gen_random_uuid(),
  confirmado_en             TIMESTAMPTZ  NOT NULL DEFAULT now(),
  vence_en                  TIMESTAMPTZ  NOT NULL,
  estado                    secretos.estado_interruptor NOT NULL DEFAULT 'activo',
  creado_en                 TIMESTAMPTZ  NOT NULL DEFAULT now(),
  CONSTRAINT pk_interruptores_emergencia PRIMARY KEY (id),
  CONSTRAINT fk_interruptores_emergencia_usuarios FOREIGN KEY (usuario_id)
    REFERENCES secretos.usuarios (id) ON DELETE CASCADE,
  CONSTRAINT uq_interruptores_emergencia_token UNIQUE (token_confirmacion)
);

COMMENT ON TABLE secretos.interruptores_emergencia IS 'Interruptor de hombre muerto: si no se confirma dentro del intervalo definido, se envía el contenido protegido a los destinatarios configurados.';
COMMENT ON COLUMN secretos.interruptores_emergencia.id IS 'Identificador del interruptor.';
COMMENT ON COLUMN secretos.interruptores_emergencia.usuario_id IS 'Usuario propietario del interruptor.';
COMMENT ON COLUMN secretos.interruptores_emergencia.nombre IS 'Nombre descriptivo del interruptor.';
COMMENT ON COLUMN secretos.interruptores_emergencia.intervalo_confirmacion IS 'Frecuencia requerida de confirmación (por defecto 24 horas).';
COMMENT ON COLUMN secretos.interruptores_emergencia.token_confirmacion IS 'Token secreto usado por el usuario para confirmar que sigue activo.';
COMMENT ON COLUMN secretos.interruptores_emergencia.confirmado_en IS 'Fecha y hora de la última confirmación recibida.';
COMMENT ON COLUMN secretos.interruptores_emergencia.vence_en IS 'Fecha y hora límite para la próxima confirmación, antes de disparar el envío.';
COMMENT ON COLUMN secretos.interruptores_emergencia.estado IS 'Estado actual del interruptor.';
COMMENT ON COLUMN secretos.interruptores_emergencia.creado_en IS 'Fecha y hora de creación del interruptor.';

CREATE INDEX idx_interruptores_emergencia_usuario_id ON secretos.interruptores_emergencia (usuario_id);
CREATE INDEX idx_interruptores_emergencia_vence_en ON secretos.interruptores_emergencia (vence_en)
  WHERE estado = 'activo';

-- ============================
-- 13. TABLA: interruptor_archivos (puente N:M)
-- ============================
CREATE TABLE secretos.interruptor_archivos (
  interruptor_id  BIGINT NOT NULL,
  archivo_id      BIGINT NOT NULL,
  CONSTRAINT pk_interruptor_archivos PRIMARY KEY (interruptor_id, archivo_id),
  CONSTRAINT fk_interruptor_archivos_interruptor FOREIGN KEY (interruptor_id)
    REFERENCES secretos.interruptores_emergencia (id) ON DELETE CASCADE,
  CONSTRAINT fk_interruptor_archivos_archivo FOREIGN KEY (archivo_id)
    REFERENCES secretos.archivos (id) ON DELETE CASCADE
);

COMMENT ON TABLE secretos.interruptor_archivos IS 'Relación N:M entre interruptores de emergencia y los archivos que protegen.';
COMMENT ON COLUMN secretos.interruptor_archivos.interruptor_id IS 'Interruptor de emergencia asociado.';
COMMENT ON COLUMN secretos.interruptor_archivos.archivo_id IS 'Archivo protegido por el interruptor.';

-- ============================
-- 14. TABLA: interruptor_destinatarios
-- ============================
CREATE TABLE secretos.interruptor_destinatarios (
  id                      BIGINT GENERATED ALWAYS AS IDENTITY,
  interruptor_id          BIGINT       NOT NULL,
  correo_destinatario     CITEXT       NOT NULL,
  nombre_destinatario     VARCHAR(150),
  CONSTRAINT pk_interruptor_destinatarios PRIMARY KEY (id),
  CONSTRAINT fk_interruptor_destinatarios_interruptor FOREIGN KEY (interruptor_id)
    REFERENCES secretos.interruptores_emergencia (id) ON DELETE CASCADE
);

COMMENT ON TABLE secretos.interruptor_destinatarios IS 'Destinatarios que recibirán el contenido protegido si el interruptor de emergencia se dispara.';
COMMENT ON COLUMN secretos.interruptor_destinatarios.id IS 'Identificador del destinatario.';
COMMENT ON COLUMN secretos.interruptor_destinatarios.interruptor_id IS 'Interruptor de emergencia asociado.';
COMMENT ON COLUMN secretos.interruptor_destinatarios.correo_destinatario IS 'Correo electrónico al que se enviará el contenido.';
COMMENT ON COLUMN secretos.interruptor_destinatarios.nombre_destinatario IS 'Nombre de referencia del destinatario.';

CREATE INDEX idx_interruptor_destinatarios_interruptor_id ON secretos.interruptor_destinatarios (interruptor_id);

-- ============================
-- 15. TABLA: configuraciones_mfa
-- ============================
CREATE TABLE secretos.configuraciones_mfa (
  id                    BIGINT GENERATED ALWAYS AS IDENTITY,
  usuario_id            BIGINT       NOT NULL,
  secreto_encriptado    TEXT         NOT NULL,
  metodo_envio          secretos.metodo_envio_mfa NOT NULL DEFAULT 'correo',
  es_activo             BOOLEAN      NOT NULL DEFAULT TRUE,
  activado_en           TIMESTAMPTZ  NOT NULL DEFAULT now(),
  CONSTRAINT pk_configuraciones_mfa PRIMARY KEY (id),
  CONSTRAINT fk_configuraciones_mfa_usuarios FOREIGN KEY (usuario_id)
    REFERENCES secretos.usuarios (id) ON DELETE CASCADE,
  CONSTRAINT uq_configuraciones_mfa_usuario_id UNIQUE (usuario_id)
);

COMMENT ON TABLE secretos.configuraciones_mfa IS 'Configuración de doble factor de autenticación propio del sistema (sin app externa), uno por usuario.';
COMMENT ON COLUMN secretos.configuraciones_mfa.id IS 'Identificador de la configuración MFA.';
COMMENT ON COLUMN secretos.configuraciones_mfa.usuario_id IS 'Usuario dueño de la configuración (relación 1 a 1).';
COMMENT ON COLUMN secretos.configuraciones_mfa.secreto_encriptado IS 'Secreto usado para generar códigos, cifrado en la capa de aplicación.';
COMMENT ON COLUMN secretos.configuraciones_mfa.metodo_envio IS 'Canal por el que se entrega el código MFA.';
COMMENT ON COLUMN secretos.configuraciones_mfa.es_activo IS 'Indica si el MFA está habilitado actualmente.';
COMMENT ON COLUMN secretos.configuraciones_mfa.activado_en IS 'Fecha y hora en que se activó el MFA.';

-- =====================================================================
-- 16. TABLAS DE ALTO VOLUMEN — PARTICIONADAS BIMESTRALMENTE
-- =====================================================================
-- Estas tablas registran eventos (descargas, generación de códigos MFA)
-- y crecen de forma continua. Se particionan por RANGE cada 2 meses
-- sobre su columna de fecha, con una partición DEFAULT de resguardo.

-- ---- 16.1 log_descargas ----
CREATE TABLE secretos.log_descargas (
  id                          BIGINT      GENERATED ALWAYS AS IDENTITY,
  archivo_compartido_id       BIGINT      NOT NULL,
  ip_origen                   INET,
  descargado_en               TIMESTAMPTZ NOT NULL DEFAULT now(),
  CONSTRAINT pk_log_descargas PRIMARY KEY (id, descargado_en),
  CONSTRAINT fk_log_descargas_archivos_compartidos FOREIGN KEY (archivo_compartido_id)
    REFERENCES secretos.archivos_compartidos (id) ON DELETE CASCADE
) PARTITION BY RANGE (descargado_en);

COMMENT ON TABLE secretos.log_descargas IS 'Bitácora de cada descarga realizada sobre un enlace compartido; particionada bimestralmente por descargado_en.';
COMMENT ON COLUMN secretos.log_descargas.id IS 'Identificador del evento de descarga.';
COMMENT ON COLUMN secretos.log_descargas.archivo_compartido_id IS 'Enlace compartido que fue descargado.';
COMMENT ON COLUMN secretos.log_descargas.ip_origen IS 'Dirección IP desde la que se realizó la descarga.';
COMMENT ON COLUMN secretos.log_descargas.descargado_en IS 'Fecha y hora de la descarga; también es la llave de partición.';

CREATE INDEX idx_log_descargas_archivo_compartido_id ON secretos.log_descargas (archivo_compartido_id);

-- ---- 16.2 codigos_mfa ----
CREATE TABLE secretos.codigos_mfa (
  id                        BIGINT      GENERATED ALWAYS AS IDENTITY,
  configuracion_mfa_id      BIGINT      NOT NULL,
  codigo                    CHAR(6)     NOT NULL,
  generado_en               TIMESTAMPTZ NOT NULL DEFAULT now(),
  expira_en                 TIMESTAMPTZ NOT NULL,
  es_usado                  BOOLEAN     NOT NULL DEFAULT FALSE,
  CONSTRAINT pk_codigos_mfa PRIMARY KEY (id, generado_en),
  CONSTRAINT fk_codigos_mfa_configuraciones_mfa FOREIGN KEY (configuracion_mfa_id)
    REFERENCES secretos.configuraciones_mfa (id) ON DELETE CASCADE,
  CONSTRAINT ck_codigos_mfa_codigo CHECK (codigo ~ '^[0-9]{6}$')
) PARTITION BY RANGE (generado_en);

COMMENT ON TABLE secretos.codigos_mfa IS 'Historial de códigos MFA generados y su estado de uso; particionada bimestralmente por generado_en.';
COMMENT ON COLUMN secretos.codigos_mfa.id IS 'Identificador del código MFA.';
COMMENT ON COLUMN secretos.codigos_mfa.configuracion_mfa_id IS 'Configuración MFA que generó el código.';
COMMENT ON COLUMN secretos.codigos_mfa.codigo IS 'Código numérico de 6 dígitos enviado al usuario.';
COMMENT ON COLUMN secretos.codigos_mfa.generado_en IS 'Fecha y hora de generación; también es la llave de partición.';
COMMENT ON COLUMN secretos.codigos_mfa.expira_en IS 'Fecha y hora en que el código deja de ser válido.';
COMMENT ON COLUMN secretos.codigos_mfa.es_usado IS 'Indica si el código ya fue consumido.';

CREATE INDEX idx_codigos_mfa_configuracion_mfa_id ON secretos.codigos_mfa (configuracion_mfa_id);

-- ---- 16.3 Procedimiento genérico para crear particiones bimestrales ----
CREATE OR REPLACE PROCEDURE secretos.sp_crear_particion_bimestral(
  p_tabla_base   TEXT,
  p_fecha_inicio DATE
)
LANGUAGE plpgsql
AS $$
DECLARE
  v_inicio_bimestre DATE;
  v_fin_bimestre     DATE;
  v_sufijo           TEXT;
  v_nombre_particion TEXT;
BEGIN
  -- Redondea la fecha de inicio al primer día del bimestre (ene-feb, mar-abr, ...)
  v_inicio_bimestre := make_date(
    EXTRACT(YEAR FROM p_fecha_inicio)::INT,
    (((EXTRACT(MONTH FROM p_fecha_inicio)::INT - 1) / 2) * 2) + 1,
    1
  );
  v_fin_bimestre := v_inicio_bimestre + INTERVAL '2 months';
  v_sufijo := to_char(v_inicio_bimestre, '"y"YYYY"_m"MM');
  v_nombre_particion := format('%s_%s', p_tabla_base, v_sufijo);

  EXECUTE format(
    'CREATE TABLE IF NOT EXISTS secretos.%I PARTITION OF secretos.%I
       FOR VALUES FROM (%L) TO (%L)',
    v_nombre_particion, p_tabla_base, v_inicio_bimestre, v_fin_bimestre
  );

  EXECUTE format(
    'COMMENT ON TABLE secretos.%I IS %L',
    v_nombre_particion,
    format('Partición bimestral de %s: %s a %s', p_tabla_base, v_inicio_bimestre, v_fin_bimestre - INTERVAL '1 day')
  );
END;
$$;

COMMENT ON PROCEDURE secretos.sp_crear_particion_bimestral(TEXT, DATE) IS 'Crea (si no existe) la partición bimestral correspondiente a una fecha, para log_descargas o codigos_mfa.';

-- ---- 16.4 Particiones iniciales (año en curso) + partición DEFAULT ----
DO $$
DECLARE
  v_tabla TEXT;
  v_mes   DATE;
BEGIN
  FOREACH v_tabla IN ARRAY ARRAY['log_descargas', 'codigos_mfa']
  LOOP
    -- Genera 6 particiones bimestrales cubriendo el año en curso
    FOR v_mes IN
      SELECT generate_series(date_trunc('year', now())::date, date_trunc('year', now())::date + INTERVAL '10 months', INTERVAL '2 months')::date
    LOOP
      CALL secretos.sp_crear_particion_bimestral(v_tabla, v_mes);
    END LOOP;

    -- Partición DEFAULT para filas fuera de rango (p.ej. datos históricos o futuros no anticipados)
    EXECUTE format(
      'CREATE TABLE IF NOT EXISTS secretos.%I PARTITION OF secretos.%I DEFAULT',
      v_tabla || '_default', v_tabla
    );
  END LOOP;
END;
$$;

-- NOTA OPERATIVA: programar secretos.sp_crear_particion_bimestral(...) vía pg_cron
-- (o un job de la aplicación) para crear el bimestre siguiente con antelación, por ejemplo:
--   CALL secretos.sp_crear_particion_bimestral('log_descargas', (now() + INTERVAL '2 months')::date);
--   CALL secretos.sp_crear_particion_bimestral('codigos_mfa',   (now() + INTERVAL '2 months')::date);

-- =====================================================================
-- FIN DEL SCRIPT
-- =====================================================================
