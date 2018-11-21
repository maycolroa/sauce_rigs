Se va a tener las estructura de tablas:

rol
--------
id PK   |
name    |

Permission
---------
id PK   |
name    |

team
-----------------------------
id PK                       |
name (company_id) Unico     |
descripcion (Nombre empresa)

rol_permission
---------------
rol_id          |
permission_id   |

team_rol
---------
tema_id |
rol_id  |

team_permission
---------------
team_id        |
permission_id  |


--------------------------------------------------------------------------

1. Creacion de rol

    - Nombre... Unico
    - Al buscar los permisos:
        * Buscar todos los modulos con licencia activa que tenga asociado el usuario en sesion
        * Buscar todos los permisos asociados a los modulos que pertenezcan al equipo del usuario en sesion
        * Solo extraer de la lista de permisos, los que tenga asociado el usuario en sesion para ese equipo
        * Mostrar esa lista

2. Al asociar el rol al usuario

    - Buscar todos los roles del equipo al que pertenece el usuario en sesion y solo los que el tenga asignado
    - Mostrar la lista de roles

