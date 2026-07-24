**Arquitectura** La arquitectura debe de ser modular, escalable y mantenible.
**Patron Arquitectonico** Debe de utilizar arquitectura onion.
**Inyeccion de Dependencias** Debe de utilizar inyeccion de dependencias.
**Estructura de carpetas** La estructura de carpetas debe de ser la siguiente:
```
src/
    Domain/
    Application/
    Infrastructure/
```
**Domain** Contiene las entidades, reglas de negocio y casos de uso.
**Application** Contiene las reglas de aplicacion.
**Infrastructure** Contiene las reglas de infraestructura.
**CQRS** Debe de utilizar CQRS para la separacion de comandos y queries.
