#HU-01: Registro de Cuenta de Usuario (Bóveda Maestra)
Descripción General:

Como nuevo usuario,
quiero registrarme en el sistema creando una cuenta con mi correo y una contraseña maestra,
para tener acceso a mi bóveda personal y comenzar a almacenar mis secretos de forma segura.

## Criterios de Aceptación
### 1. Formulario de Registro (Campos Obligatorios)

Dado que el usuario se encuentra en la pantalla de registro.

Cuando intente enviar el formulario.

Entonces el sistema debe exigir los siguientes campos obligatorios:

Nombre Completo (máximo 150 caracteres).

Correo Electrónico (formato válido de email).

Contraseña Maestra (cumpliendo políticas de seguridad: ej. mínimo 12 caracteres, mayúsculas, números y símbolos).

Confirmar Contraseña Maestra (debe coincidir exactamente con la contraseña ingresada).

## 2. Validación de Correo Único (Insensible a Mayúsculas)

Dado que un usuario ingresa un correo electrónico.

Cuando el correo ya existe en la base de datos (ignorando mayúsculas/minúsculas gracias al tipo CITEXT).

Entonces el sistema debe rechazar el registro y mostrar el mensaje: "Este correo electrónico ya está registrado." sin revelar detalles adicionales por seguridad.

## 3. Criptografía y Almacenamiento Seguro (Zero-Knowledge)

Dado que el usuario envía el formulario válido.

Cuando el backend procesa la solicitud.

Entonces la contraseña maestra en texto plano nunca debe guardarse ni enviarse sin protección.

El sistema debe generar una sal criptográfica única (sal_contrasena) y aplicar una función de derivación de claves (ej. Argon2id, PBKDF2 o Bcrypt) para generar el hash_contrasena. (Nota arquitectónica: idealmente, un hash primario se hace en el lado del cliente y el servidor le aplica un segundo hash).

## 4. Creación del Registro en Base de Datos

Dado que los datos son válidos y la criptografía se ejecutó con éxito.

Cuando se guarda el registro en la base de datos.

Entonces se debe crear una fila en la tabla secretos.usuarios con:

El estado inicial configurado automáticamente como 'activo'.

Los campos creado_en y actualizado_en asignados por defecto con el timestamp actual.

## 5. Retroalimentación al Usuario

Dado que el usuario completó el proceso exitosamente.

Cuando la base de datos confirma la transacción.

Entonces el sistema debe mostrar un mensaje de éxito ("Cuenta creada exitosamente") y redirigir al usuario a la pantalla de Inicio de Sesión (Login).

Tareas Técnicas (Sub-tareas para el equipo de desarrollo)
Frontend (UI/UX):

Crear la vista del formulario de registro.

Implementar validación en tiempo real (fortaleza de contraseña, coincidencia de contraseñas, formato de email).

Backend (API & Seguridad):

Crear endpoint POST /api/auth/registro.

Implementar el algoritmo de Hashing (Argon2id recomendado) para generar hash_contrasena y sal_contrasena.

Manejar la excepción de clave duplicada (uq_usuarios_correo_electronico) devolviendo un error 409 Conflict o 400 Bad Request.