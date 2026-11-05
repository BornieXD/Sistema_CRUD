
USE sistema_crud;

ALTER TABLE usuarios
ADD CONSTRAINT uq_usuarios_email UNIQUE (email);
