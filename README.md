# Sistema de Trazabilidad para Control de Stock de Frutas

## 📋 Descripción
Sistema básico de control de stock con trazabilidad completa, desarrollado para la cátedra de Base de Datos. Permite registrar todas las operaciones (inserciones, actualizaciones y bajas lógicas) con su correspondiente trazabilidad.

## 🚀 Funcionalidades
- Control de stock de frutas (banana, manzana, pera)
- Registro de trazabilidad con:
  - Usuario operador
  - Fecha y hora
  - Tipo de acción (INSERCIÓN, UPDATE, SOFT-DELETE)
- Hash de integridad (MD5) para detectar alteraciones externas
- Interfaz simple y responsive

## 🛠️ Tecnologías Utilizadas
- PHP 7.4+
- MySQL 5.7+
- HTML5 + CSS3
- Git + GitHub

## 📦 Instalación

### 1. Clonar el repositorio
```bash
git clone https://github.com/tu-usuario/trazabilidad-frutas.git
cd trazabilidad-frutas