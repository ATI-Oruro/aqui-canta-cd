# FASE 1 — Configuración del festival

> Estado: **COMPLETADA**
> Proyecto: ACB App — Gestión de calificaciones del XXXIII Festival Nacional de la Canción Boliviana "Aquí… Canta Bolivia"
> Fecha: 2026-09-11

---

## 1. Qué se implementó

La configuración oficial del festival derivada de la **convocatoria 2026** (documento
`docs/CONVOCATORIA oficial fondo blanco 2026.docx`):

1. Tabla `departamentos` (9 departamentos de Bolivia).
2. Tabla `fases` (9 preselecciones departamentales + 1 etapa final nacional).
3. Tabla `categorias` (13 categorías oficiales con `max_participantes` y `tiempo_presentacion`).
4. Tabla `parametros` (20 parámetros de evaluación).
5. Tabla pivote `categoria_parametro` con **PK compuesta** `(id_categoria, id_parametro)`,
   `valor_minimo`, `valor_maximo` y `orden`.
6. Modelos y relaciones de Eloquent en la arquitectura modular `app/Modules/*`.
7. Seeders oficiales idempotentes.
8. Vista admin "Configuración del Festival" para verificar la configuración cargada.
9. Reglas de negocio: suma de máximos = 100 por categoría, sin parámetros duplicados.

---

## 2. Fuente de datos (convocatoria oficial)

Los datos se extrajeron del documento oficial (`.docx`, convertido a texto vía su XML).
`max_participantes` = máximo de personas en escenario; `tiempo_presentacion` = minutos máximos.

| # | Categoría | Máx. participantes | Tiempo (min) |
|---|---|---|---|
| 1 | Ñusta | 1 | 10 |
| 2 | Solista vocal (con o sin acompañamiento) | 6 | 5 |
| 3 | Solista instrumental – Armónico | 1 | 5 |
| 4 | Solista instrumental – Melódico (con o sin acompañamiento) | 2 | 5 |
| 5 | Dúo vocal instrumental | 2 | 5 |
| 6 | Trío vocal instrumental | 3 | 5 |
| 7 | Grupo vocal instrumental | 8 | 5 |
| 8 | Composición | 6 | 5 |
| 9 | Estudiantina | 15 | 7 |
| 10 | Autóctono | 20 | 7 |
| 11 | Solista vocal infantil (con o sin acompañamiento) | 6 | 5 |
| 12 | Solista instrumental infantil (con o sin acompañamiento) | 2 | 5 |
| 13 | Especial | 10 | 5 |

> **Nota Ñusta:** la convocatoria indica 10 min en preselección y 15 min en la final.
> El sistema almacena un único `tiempo_presentacion` por categoría (el de preselección).
> En la FASE 3 el controlador de cronómetro puede ajustar `Presentacion.tiempo_maximo`
> por presentación si la etapa final lo requiere.

---

## 3. Migraciones

### `2026_09_11_000001_create_departamentos_table`
| Columna | Tipo |
|---|---|
| id | bigint PK |
| nombre | string |

### `2026_09_11_000002_create_fases_table`
| Columna | Tipo |
|---|---|
| id | bigint PK |
| fase | string (enum PRESELECCION \| FINAL) |
| departamento_id | FK nullable → departamentos (nullOnDelete) |

Índice en `fase`. `departamento_id` es `null` en la fase FINAL.

### `2026_09_11_000003_create_categorias_table`
| Columna | Tipo |
|---|---|
| id | bigint PK |
| categoria | string |
| max_participantes | uint (default 1) |
| tiempo_presentacion | uint (default 5) — minutos |
| timestamps | |

### `2026_09_11_000004_create_parametros_table`
| Columna | Tipo |
|---|---|
| id | bigint PK |
| parametro | string |
| timestamps | |

### `2026_09_11_000005_create_categoria_parametro_table`
| Columna | Tipo |
|---|---|
| id_categoria | FK → categorias (cascadeOnDelete) |
| id_parametro | FK → parametros (cascadeOnDelete) |
| valor_minimo | uint (default 1) |
| valor_maximo | uint |
| orden | tinyint |

**PK compuesta `(id_categoria, id_parametro)`** + índice en `id_parametro`.
La PK compuesta impide parámetros duplicados dentro de una categoría a nivel de base de datos.

---

## 4. Modelos y relaciones

| Modelo | Módulo | Relaciones |
|---|---|---|
| `Departamento` | `App\Modules\Departamentos\Models` | `fases()` hasMany |
| `Fase` | `App\Modules\Fases\Models` | `departamento()` belongsTo; cast `fase` → `FaseEnum`; helpers `esPreseleccion()`/`esFinal()` |
| `Categoria` | `App\Modules\Categorias\Models` | `categoriaParametros()` hasMany; `parametros()` belongsToMany; `sumaValorMaximo()`; `sumaEsCien()` |
| `Parametro` | `App\Modules\Parametros\Models` | `categorias()` belongsToMany |
| `CategoriaParametro` | `App\Modules\Categorias\Models` | `categoria()` belongsTo; `parametro()` belongsTo; `$incrementing = false` (PK compuesta) |

### Enum nuevos
- `App\Support\Enums\FaseEnum`: `PRESELECCION` / `FINAL` + `label()`, `esPreseleccion()`, `esFinal()`.

---

## 5. Seeders

`DatabaseSeeder` llama en orden:
1. `DepartamentoSeeder` → 9 departamentos: Chuquisaca, La Paz, Cochabamba, Oruro, Potosí,
   Tarija, Santa Cruz, Beni, Pando.
2. `FaseSeeder` → una `PRESELECCION` por departamento + una `FINAL` (sin departamento).
3. `CategoriaParametrosSeeder` → 13 categorías + 20 parámetros + 47 relaciones, con
   `valor_minimo=1` y máximos oficiales.

Los tres seeders son **idempotentes** (`updateOrCreate`/`firstOrCreate`); los usuarios
también (el admin usa `updateOrCreate`).

`CategoriaParametrosSeeder` **lanzará una excepción** si la suma de `valor_maximo` de
cualquier categoría no es exactamente 100.

---

## 6. Rutas nuevas

| Método | URI | Nombre | Middleware | Controlador |
|---|---|---|---|---|
| GET | `/admin/configuracion` | `admin.configuracion` | admin | `ConfiguracionController@index` |

`ConfiguracionController` está en `App\Modules\Categorias\Controllers` y pasa a la vista
categorías (con params), departamentos (con conteo de fases) y fases.

---

## 7. Vistas

- `resources/views/admin/configuracion.blade.php`: tabla de categorías con sus parámetros
  (mín/máx por parámetro, tiempo y máx. participantes), lista de departamentos con nº de
  fases y lista de fases (etiqueta + departamento o "(nacional)").
- Cada categoría muestra `suma / 100` en verde/rojo según `sumaEsCien()`.
- El sidebar de admin ahora enlaza "Inicio" y "Configuración".

---

## 8. Reglas de negocio (validaciones)

1. **Suma de máximos = 100:** validada en seeder (excepción) y verificable en vista por
   categoría. En fases posteriores será prerequisito para habilitar la evaluación.
2. **Sin parámetros duplicados por categoría:** garantizado por PK compuesta
   `(id_categoria, id_parametro)`.
3. **`valor_minimo` ≤ `valor_maximo`:** por diseño (`valor_minimo` default 1).
4. Los seeders no deben re-introducirse manualmente; la vista de configuración es de
   verificación, no de carga.

---

## 9. Pruebas

Suite completa: **28 passed (140 assertions)**.

Test nuevo `tests/Feature/CategoriaParametrosConfigTest.php`:
- seed crea 13 categorías con suma 100 y 47 relaciones;
- seed idempotente (13/20/47 sin duplicados al repetir);
- PK compuesta impide duplicar un parámetro en una categoría (QueryException + 1 registro).

---

## 10. Cómo extender

- **Nueva categoría:** agregar el bloque en `CategoriaParametrosSeeder` y ejecutar
  `php artisan db:seed` (idempotente). No hacerlo por UI.
- **Relación directa con el resto del DER:** la cadena `Categoria → CategoriaParametro →
  Inscripcion → Presentacion →…` se construirá en FASE 2+ (los FKs se agregarán en las
  migraciones de las fases correspondientes).
- **Validación en escritura (futuro):** usar `Categoria::sumaEsCien()` antes de permitir
  calificaciones (FASE 4).

---

## 11. Próxima fase

FASE 2 — Importaciones y datos operativos: tablas de `Participante`, `Inscripcion`,
`InscripcionParticipante` y `Presentacion`, importación XLSX/CSV/JSON y carga de
participantes con su nombre/pseudónimo de presentación.