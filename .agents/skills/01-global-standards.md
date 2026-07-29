**Componentes Web** Todos los componentes web deben de ser rutilizables en cualquier parte de la aplicacion.
**Manejo de errores** Todos los errores deben de ir en un formato especifico para poder ser interpretados por la aplicacion.
**Idioma** Todo el codigo debe de estar en español.
**Convenciones** Todo el codigo debe de seguir las convenciones de PSR-12.
**Documentacion** El código debe de estar documentado con un estandar de documentacion en español. No debe de ser un comnentario de mas  de 100 characteres, debe de ir al inicio de cada funcion, metodo, clase, interfaz y archivo.

**Tecnologias** Es una aplicación laravel monilitica con blade como frontend, y tiene librerias como taildwind para decorar los componentes


**Colores** Los colores permitidos para utilizar son los siguientes para estadarizar la paleta de colores del sistema.

# Estándares de Paleta de Colores

Este proyecto utiliza el sistema de colores de Tailwind CSS. Hemos estandarizado las siguientes paletas para mantener la consistencia en toda la interfaz de usuario.

## Color Principal (Azul)
Se utiliza para acciones primarias, botones, enlaces y elementos destacados.

| Tono | Hexadecimal | Uso recomendado | Clase Tailwind |
| :--- | :--- | :--- | :--- |
| **50** | `#eff6ff` | Fondos muy claros, estados hover en menús | `bg-blue-50` |
| **100** | `#dbeafe` | Fondos secundarios de componentes | `bg-blue-100` |
| **500** | `#3b82f6` | **Color base.** Botones primarios, iconos | `bg-blue-500` |
| **700** | `#1d4ed8` | Estado hover de botones primarios, texto | `text-blue-700` |
| **900** | `#1e3a8a` | Títulos oscuros, fondos de contraste alto | `text-blue-900` |

## Neutrales (Gris / Slate)
Utilizamos la paleta *Slate* ya que su subtono frío/azulado armoniza mejor con nuestro color principal. Se usa para tipografía, bordes y fondos de layout.

| Tono | Hexadecimal | Uso recomendado | Clase Tailwind |
| :--- | :--- | :--- | :--- |
| **50** | `#f8fafc` | Fondo principal de la aplicación (App bg) | `bg-slate-50` |
| **100** | `#f1f5f9` | Fondos de tarjetas, inputs o contenedores | `bg-slate-100` |
| **300** | `#cbd5e1` | Bordes de inputs, divisores (`<hr>`) | `border-slate-300` |
| **500** | `#64748b` | Texto secundario, placeholders, iconos inactivos | `text-slate-500` |
| **700** | `#334155` | Texto principal (párrafos) | `text-slate-700` |
| **900** | `#0f172a` | Títulos principales (H1, H2, H3) | `text-slate-900` |

---

## Colores Semánticos (Estados)
Utilizados para comunicar retroalimentación al usuario. Los tonos `50` son ideales para el fondo de las alertas, mientras que los `700` se usan para el texto de las mismas.

### Éxito (Emerald)
Para confirmaciones, guardado exitoso y estados positivos.
*   **Fondo (50):** `#ecfdf5` (`bg-emerald-50`)
*   **Base (500):** `#10b981` (`bg-emerald-500`)
*   **Texto (700):** `#047857` (`text-emerald-700`)

### Alerta / Advertencia (Amber)
Para acciones que requieren atención, confirmaciones destructivas suaves.
*   **Fondo (50):** `#fffbeb` (`bg-amber-50`)
*   **Base (500):** `#f59e0b` (`bg-amber-500`)
*   **Texto (700):** `#b45309` (`text-amber-700`)

### Error (Red)
Para fallos, eliminaciones y mensajes de validación.
*   **Fondo (50):** `#fef2f2` (`bg-red-50`)
*   **Base (500):** `#ef4444` (`bg-red-500`)
*   **Texto (700):** `#b91c1c` (`text-red-700`)

## 📐 Estándares de Bordes (Border Radius)
Para mantener una apariencia moderna y consistente, evitamos los bordes completamente cuadrados (a menos que el diseño lo exija) y estandarizamos los siguientes niveles de redondeo.

| Nivel | Clase Tailwind | Uso recomendado |
| :--- | :--- | :--- |
| **Pequeño** | `rounded-sm` | Checkboxes, etiquetas pequeñas (tags), tooltips. |
| **Medio** | `rounded-md` | **Estándar base.** Botones primarios/secundarios, campos de texto (inputs), selectores. |
| **Grande** | `rounded-lg` | Tarjetas (Cards), modales pequeños, contenedores de alertas o banners. |
| **Extra Grande** | `rounded-xl` | Modales grandes, contenedores principales de la pantalla, imágenes de portadas. |
| **Píldora / Círculo** | `rounded-full` | Avatares de usuario, botones flotantes (FAB), badges numéricos de notificaciones. |

---

## Estándares de Tipografía (Tamaños y Pesos)
Utilizamos una escala tipográfica que garantiza la legibilidad y establece una clara jerarquía de la información. 

*Nota: Tailwind ajusta automáticamente el interlineado (`leading`) basado en el tamaño del texto, lo cual ayuda a mantener el ritmo vertical.*

### Títulos (Headings)
Los títulos siempre deben ir acompañados de los colores más oscuros de nuestra paleta neutral (ej. `text-slate-900` o `text-slate-800`).

| Jerarquía | Clase (Tamaño) | Peso (Font-Weight) | Uso recomendado |
| :--- | :--- | :--- | :--- |
| **Título 1 (H1)** | `text-3xl` / `text-4xl` | `font-bold` | Título principal de la página o módulo (Ej: "Mi Bóveda", "Configuración"). |
| **Título 2 (H2)** | `text-2xl` | `font-semibold` | Títulos de secciones importantes dentro de una página. |
| **Título 3 (H3)** | `text-xl` | `font-medium` | Títulos de tarjetas (Cards), modales, o sub-secciones. |
| **Título 4 (H4)** | `text-lg` | `font-medium` | Títulos menores, resaltados dentro de formularios o listas. |

### Cuerpo de Texto (Body)
El texto normal debe usar colores neutrales ligeramente más suaves para no fatigar la vista (ej. `text-slate-700` para texto principal y `text-slate-500` para secundario).

| Tipo | Clase Tailwind | Peso | Uso recomendado |
| :--- | :--- | :--- | :--- |
| **Párrafo Base** | `text-base` | `font-normal` | **Estándar base.** Texto de párrafos, contenido de notas seguras, texto dentro de modales. |
| **Secundario** | `text-sm` | `font-normal` | Fechas de creación, subtítulos descriptivos, placeholders de inputs, notas aclaratorias. |
| **Micro Texto** | `text-xs` | `font-medium` | Textos legales muy pequeños, etiquetas (badges), validaciones de error bajo un input. |
| **Botones / Enlaces** | `text-sm` o `text-base` | `font-medium` | Acciones de usuario. Siempre deben llevar `font-medium` para destacar sobre el texto normal. |