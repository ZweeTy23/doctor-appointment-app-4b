# 📋 Reporte de Avance — Segundo Mes
**Proyecto:** MediMatch — Sistema de Citas Médicas  
**Materia:** Desarrollo Backend (4.º Cuatrimestre)  
**Alumno:** José A.  
**Fecha:** Febrero 2026  
**Framework:** Laravel 12 · WireUI v2 · Livewire 3 · Alpine.js · Tailwind CSS

---

## 1. Descripción General del Proyecto

**MediMatch** es una aplicación web de gestión de citas médicas con un panel de administración. El proyecto utiliza una arquitectura MVC sobre Laravel 12, con autenticación provista por **Laravel Jetstream + Fortify**, tablas interactivas via **Livewire**, componentes UI de **WireUI v2** y reactividad del lado cliente con **Alpine.js**.

### Módulos implementados hasta la fecha

| Módulo | Estado | Descripción |
|---|---|---|
| Autenticación | ✅ Completo | Login, registro, 2FA (Jetstream) |
| Roles y Permisos | ✅ Completo | CRUD con Spatie Permission |
| Usuarios | ✅ Completo | CRUD completo + soft deletes |
| Pacientes | ✅ Completo | Expediente médico por paciente |
| Citas | 🔲 Pendiente | Módulo futuro |

---

## 2. Trabajo Realizado en el Segundo Mes

### 2.1 Módulo de Tipos de Sangre (`blood_types`)

Se creó la tabla `blood_types` con su seeder correspondiente, proporcionando un catálogo de los 8 tipos de sangre (A+, A−, B+, B−, AB+, AB−, O+, O−). Este catálogo se relaciona con el expediente del paciente.

### 2.2 Módulo de Pacientes — Base de datos

Se diseñó y ejecutó la migración `create_patients_table` que modela el expediente médico:

```
patients
├── id
├── user_id          (FK → users, cascade)
├── blood_type_id    (FK → blood_types, nullable, set null)
├── allergies        (text, nullable)
├── chronic_diseases (text, nullable)
├── surgery_history  (text, nullable)
├── family_history   (text, nullable)  ← agregado en migración adicional
├── observations     (text, nullable)
├── emergency_contact_name   (string, nullable)
├── emergency_contact_phone  (string, nullable)
├── emergency_relationship   (string, nullable)
└── timestamps
```

> **Decisión de diseño:** El paciente no se crea de forma independiente; se genera automáticamente al crear un Usuario con el rol de paciente. Por eso el `PatientController` solo expone `index`, `show`, `edit` y `update`.

### 2.3 Módulo de Pacientes — Capa de aplicación

Se implementaron los siguientes archivos:

| Archivo | Descripción |
|---|---|
| `app/Models/Patient.php` | Modelo Eloquent con `$fillable` y relaciones `belongsTo` con `User` y `BloodType` |
| `app/Http/Controllers/Admin/PatientController.php` | Controlador resource (solo index/show/edit/update) |
| `app/Livewire/Admin/DataTables/PatientTable.php` | Tabla interactiva con búsqueda y ordenamiento |
| `routes/admin.php` | Ruta resource restringida a 4 métodos |

### 2.4 Vista de Edición de Paciente con Tabs

Se construyó un formulario de edición dividido en **4 pestañas** para organizar la información del expediente médico:

| Pestaña | Campos |
|---|---|
| Datos Personales | Solo lectura (nombre, email, teléfono, dirección desde `User`) |
| Antecedentes | Alergias, enfermedades crónicas, antecedentes familiares y quirúrgicos |
| Información General | Tipo de sangre, observaciones |
| Contacto de Emergencia | Nombre, teléfono (con máscara), relación |

### 2.5 Refactorización con Principio DRY — Componentes Blade

Se crearon **3 componentes Blade reutilizables** bajo `resources/views/components/` para eliminar la repetición de ~100 líneas de HTML/Alpine.js:

#### `tabs.blade.php`
Contenedor principal del sistema de pestañas. Inicializa el estado de Alpine.js con la pestaña activa:
```html
<div x-data="{ tab: '{{ $active }}' }">
    <ul>{{ $header ?? '' }}</ul>  <!-- slot nombrado para los links -->
    <div>{{ $slot }}</div>        <!-- slot principal para el contenido -->
</div>
```

#### `tab-link.blade.php`
Botón de navegación individual con **4 estados visuales** controlados por Alpine.js (`:class`):
- Sin error + activo → borde azul
- Sin error + inactivo → hover azul
- Con error + activo → borde rojo + pulsante
- Con error + inactivo → borde rojo + pulsante

#### `tab-content.blade.php`
Panel de contenido con `x-show` de Alpine.js y `style="display: none;"` condicional para **evitar el flash de contenido** al cargar la página:
```html
<div x-show="tab === '{{ $tab }}'"
     @if($active !== $tab) style="display: none;" @endif>
```

### 2.6 Detección Automática de Pestaña con Errores

Se implementó un bloque `@php` al inicio de la vista que calcula automáticamente cuál pestaña abrir en caso de errores de validación:

```php
$initialTab = 'datos-personales'; // default

foreach ($errorGroups as $tabName => $fields) {
    if ($errors->hasAny($fields)) {
        $initialTab = $tabName; // salta a la 1ª pestaña con error
        break;
    }
}
```

### 2.7 Detección de Cambios en el Controlador

Se añadió lógica para comparar los datos enviados contra los datos actuales del paciente, mostrando una alerta diferenciada con SweetAlert2:

- **Datos iguales** → Alerta `info`: "Sin cambios — No se detectaron cambios en el expediente."
- **Datos diferentes** → Alerta `success`: "Expediente actualizado."

---

## 3. Herramientas y Tecnologías Utilizadas

### Backend
| Herramienta | Versión | Uso |
|---|---|---|
| **Laravel** | 12.x | Framework principal MVC |
| **Eloquent ORM** | — | Modelos, relaciones y migraciones |
| **Laravel Jetstream** | — | Autenticación, 2FA, gestión de sesiones |
| **Laravel Fortify** | — | Pipeline de autenticación |
| **Spatie Laravel Permission** | — | Roles y permisos |
| **Livewire** | 3.x | Componentes reactivos del servidor (tablas) |
| **Rappasoft Livewire Tables** | — | DataTables con Livewire |

### Frontend
| Herramienta | Versión | Uso |
|---|---|---|
| **WireUI** | v2 (prefix `wire-`) | Componentes UI: cards, botones, inputs, modales, alerts |
| **Alpine.js** | — | Reactividad del lado cliente (tabs, show/hide) |
| **Tailwind CSS** | — | Utilidades de diseño |
| **Flowbite** | — | Estilos de tabs y componentes adicionales |
| **SweetAlert2** | 11.x | Alertas y confirmaciones |
| **Font Awesome** | 6.x | Iconografía |

### Herramientas de Desarrollo
| Herramienta | Uso |
|---|---|
| **Vite** | Bundler y hot-reload de assets (CSS/JS) |
| **npm** | Gestión de dependencias frontend |
| **Composer** | Gestión de dependencias PHP |
| **SQLite** | Base de datos (desarrollo local) |

---

## 4. Prácticas de Desarrollo Aplicadas

### 🔁 Principio DRY (Don't Repeat Yourself)
La refactorización del formulario de tabs demostró este principio en práctica: el código de la vista `edit.blade.php` pasó de ~280 líneas con lógica repetida por cada pestaña, a ~217 líneas limpias usando los 3 componentes reutilizables. Los componentes ahora pueden reutilizarse en cualquier otra vista del proyecto.

### 🏗️ Componentes Blade con Props y Slots
Se aplicó la API completa de componentes Blade de Laravel:
- **`@props`** para declarar propiedades tipadas con valores por defecto
- **Slots nombrados** (`<x-slot name="header">`) para zonas de contenido específicas
- **Expresiones de PHP en atributos** (`:active="$variable"`)

### ✅ Validación del Servidor
- Reglas `nullable` para campos opcionales del expediente
- Sanitización de entrada del teléfono con `preg_replace` antes de validar
- Mensajes de validación en español via `lang/es/validation.php`
- Los atributos de los campos están traducidos al español

### 🎨 Separación de responsabilidades (SoC)
- El **controlador** solo valida, compara y persiste datos
- La **vista** solo renderiza y detecta qué pestaña mostrar
- Los **componentes Blade** encapsulan la lógica visual de los tabs

### 🔒 Seguridad básica
- CSRF token en todos los formularios (`@csrf`)
- Spoofing de método HTTP (`@method('PUT')`)
- Validación siempre del lado servidor (nunca solo frontend)

---

## 5. Dificultades Encontradas y Cómo se Resolvieron

### ❌ Error: `tabs-link.blade.php` en lugar de `tab-link.blade.php`
**Problema:** Se creó el archivo con el nombre incorrecto (`tabs-link` con "s"), por lo que Laravel no podía resolver el componente `<x-tab-link>`.  
**Solución:** Se eliminó el archivo incorrecto y se creó `tab-link.blade.php` con el nombre correcto. Se verificó la convención de nombres de componentes Blade (guiones → CamelCase automático).

### ❌ Error: `ñ` suelto en `edit.blade.php`
**Problema:** Un carácter `ñ` extraño quedó antes de `<x-admin-layout>` en la línea 18, causando que el HTML de la página comenzara con un carácter no válido.  
**Solución:** Se identificó y eliminó el carácter en la revisión del archivo.

### ❌ Campo `family_history` no se guardaba
**Problema:** La vista tenía el campo `family_history` pero el controlador no lo incluía en las reglas de validación, por lo que `$request->validate()` nunca lo retornaba en `$data` y nunca se persistía en la base de datos.  
**Solución:** Se añadió `'family_history' => 'nullable|string|max:1000'` a las reglas de validación del método `update`.

### ❌ Flash de contenido al cargar la página (FOUC)
**Problema:** Al usar solo `x-show` de Alpine.js sin `style="display: none;"`, los paneles de todas las pestañas eran visibles por un instante al cargar la página antes de que Alpine.js inicializara.  
**Solución:** En `tab-content.blade.php` se añade `style="display: none;"` condicionalmente, solo cuando `$active !== $tab` (es decir, cuando la pestaña no es la activa por defecto). Esto permite que el contenido activo sea visible de inmediato sin flash.

### ❌ Alpine.js y el uso incorrecto de `onclick` en tabs
**Problema:** Una implementación inicial intentó usar `onclick="tab = '...'"`  en lugar de la directiva de Alpine.js `@click.prevent`, lo que no funciona porque `onclick` no tiene acceso al contexto de `x-data`.  
**Solución:** Se corrigió a `@click.prevent="tab = '{{ $tab }}'"` que es la sintaxis correcta de Alpine.js para manipular estado reactivo.

### ❌ `Swal` no definido en el `<head>`
**Problema:** El layout `admin.blade.php` tenía el script de SweetAlert antes de cargar el CDN, lo que causaría un `ReferenceError: Swal is not defined`.  
**Solución identificada:** El layout tiene dos instancias del script; la del `<head>` falla silenciosamente, pero la del final del `<body>` (después de que el CDN carga) funciona correctamente. El comportamiento visible es correcto aunque el código tiene redundancia.

### ❌ CSS aparentemente "sin estilos" tras añadir componentes nuevos
**Problema:** Después de crear los 3 nuevos archivos de componentes, los estilos de Tailwind no se aplicaban correctamente en el navegador.  
**Causa real:** Vite en modo `dev` no recompiló el CSS porque los archivos de componentes eran *nuevos* (no modificaciones de existentes), por lo que el navegador tenía caché del bundle anterior.  
**Solución:** Se ejecutó `npm run build` para forzar una recompilación completa de los assets, y se hizo un hard refresh (`Ctrl + Shift + R`) en el navegador.

---

## 6. Aprendizajes Obtenidos

1. **Componentes Blade como unidad de reutilización:** Los componentes con `@props`, slots y slots nombrados son la forma idiomática de aplicar DRY en vistas Laravel, más expresivos que los `@include` tradicionales.

2. **Alpine.js como alternativa ligera a Vue/React:** Para interacciones simples como tabs, toggles o modales, Alpine.js es suficiente y no requiere compilación ni bundler adicional; convive naturalmente con Blade.

3. **El orden de los scripts importa:** Un CDN o script referenciado antes de que se cargue su dependencia fallará. Los scripts que usan librerías externas deben ir después del CDN correspondiente.

4. **`nullable` en validación Laravel y los middlewares de sanitización:** Laravel aplica `TrimStrings` y `ConvertEmptyStringsToNull` automáticamente en el pipeline HTTP. Esto significa que valores como `"   "` (solo espacios) llegan al controlador como `null`, y con reglas `nullable`, pasan validación sin errores. Es importante entender este comportamiento para diseñar validaciones correctas.

5. **Detección de cambios sin dirty tracking:** Comparar el array validado con los atributos actuales del modelo (`$patient->{$key} != $value`) es un patrón simple y efectivo para evitar actualizaciones innecesarias y mejorar la experiencia del usuario.

6. **Convención de nombres en componentes Blade:** Laravel convierte automáticamente `<x-tab-link>` al archivo `tab-link.blade.php` usando guiones como separadores. Cualquier desviación (como `tabs-link.blade.php`) hace que el componente no sea encontrado.

---

*Reporte generado el 20 de febrero de 2026*
