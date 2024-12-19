DROP TRIGGER IF EXISTS actualizar_total_ticket_update;

DELIMITER //

-- Actualización al crear, actualizar o eliminar un registro
CREATE TRIGGER actualizar_total_ticket_update
AFTER UPDATE
ON emisores_tickets_detalles
FOR EACH ROW
BEGIN
    -- Declarar variables para almacenar resultados intermedios
    DECLARE nuevo_total DECIMAL(10, 2);
    DECLARE total_descuento DECIMAL(10, 2);

    -- Calcular el total de importes y descuentos en una sola consulta
    SELECT 
        COALESCE(SUM(importe), 0) AS total_importe,
        COALESCE(SUM(descuento), 0) AS total_descuento
    INTO 
        nuevo_total, total_descuento
    FROM 
        emisores_tickets_detalles
    WHERE 
        id_emisor = COALESCE(OLD.id_emisor, NEW.id_emisor)
        AND id_documento = COALESCE(OLD.id_documento, NEW.id_documento)
        AND folio_ticket = COALESCE(OLD.folio_ticket, NEW.folio_ticket);

    -- Ajustar el total restando el descuento
    SET nuevo_total = GREATEST(nuevo_total - total_descuento, 0);

    -- Actualizar el total en la tabla emisores_tickets
    UPDATE emisores_tickets
    SET total = nuevo_total
    WHERE id_emisor = COALESCE(OLD.id_emisor, NEW.id_emisor)
      AND id_documento = COALESCE(OLD.id_documento, NEW.id_documento)
      AND folio_ticket = COALESCE(OLD.folio_ticket, NEW.folio_ticket);

    -- Opcional: Insertar registro si no existe (manejo de consistencia)
    IF ROW_COUNT() = 0 THEN
        INSERT INTO emisores_tickets (id_emisor, id_documento, folio_ticket, total)
        VALUES (COALESCE(OLD.id_emisor, NEW.id_emisor), COALESCE(OLD.id_documento, NEW.id_documento), COALESCE(OLD.folio_ticket, NEW.folio_ticket), nuevo_total);
    END IF;
END;
//

DELIMITER ;

--  ---------------------------------------------------------------------------------------------

DROP TRIGGER IF EXISTS actualizar_total_ticket_insert;

DELIMITER //

-- Actualización al crear, actualizar o eliminar un registro
CREATE TRIGGER actualizar_total_ticket_insert
AFTER INSERT
ON emisores_tickets_detalles
FOR EACH ROW
BEGIN
    -- Declarar variables para almacenar resultados intermedios
    DECLARE nuevo_total DECIMAL(10, 2);
    DECLARE total_descuento DECIMAL(10, 2);

    -- Calcular el total de importes y descuentos en una sola consulta
    SELECT 
        COALESCE(SUM(importe), 0) AS total_importe,
        COALESCE(SUM(descuento), 0) AS total_descuento
    INTO 
        nuevo_total, total_descuento
    FROM 
        emisores_tickets_detalles
    WHERE 
        id_emisor = NEW.id_emisor
        AND id_documento = NEW.id_documento
        AND folio_ticket = NEW.folio_ticket;

    -- Ajustar el total restando el descuento
    SET nuevo_total = GREATEST(nuevo_total - total_descuento, 0);

    -- Actualizar el total en la tabla emisores_tickets
    UPDATE emisores_tickets
    SET total = nuevo_total
    WHERE id_emisor = NEW.id_emisor
      AND id_documento = NEW.id_documento
      AND folio_ticket = NEW.folio_ticket;

    -- Opcional: Insertar registro si no existe (manejo de consistencia)
    IF ROW_COUNT() = 0 THEN
        INSERT INTO emisores_tickets (id_emisor, id_documento, folio_ticket, total)
        VALUES (NEW.id_emisor, NEW.id_documento, NEW.folio_ticket, nuevo_total);
    END IF;
END;
//

DELIMITER ;

-- -----------------------------------------------------------------------------

DROP TRIGGER IF EXISTS actualizar_total_ticket_delete;

DELIMITER //

CREATE TRIGGER actualizar_total_ticket_delete
AFTER DELETE
ON emisores_tickets_detalles
FOR EACH ROW
BEGIN
    -- Declarar variables para almacenar resultados intermedios
    DECLARE nuevo_total DECIMAL(10, 2);
    DECLARE total_descuento DECIMAL(10, 2);

    -- Calcular el total de importes y descuentos en una sola consulta
    SELECT 
        COALESCE(SUM(importe), 0) AS total_importe,
        COALESCE(SUM(descuento), 0) AS total_descuento
    INTO 
        nuevo_total, total_descuento
    FROM 
        emisores_tickets_detalles
    WHERE 
        id_emisor = OLD.id_emisor
        AND id_documento = OLD.id_documento
        AND folio_ticket = OLD.folio_ticket;

    -- Ajustar el total restando el descuento
    SET nuevo_total = GREATEST(nuevo_total - total_descuento, 0);

    -- Actualizar el total en la tabla emisores_tickets
    UPDATE emisores_tickets
    SET total = nuevo_total
    WHERE id_emisor = OLD.id_emisor
      AND id_documento = OLD.id_documento
      AND folio_ticket = OLD.folio_ticket;

    -- Opcional: Eliminar registro si el nuevo total es 0 y no hay más registros relacionados
    IF ROW_COUNT() = 0 AND nuevo_total = 0 THEN
        DELETE FROM emisores_tickets
        WHERE id_emisor = OLD.id_emisor
          AND id_documento = OLD.id_documento
          AND folio_ticket = OLD.folio_ticket;
    END IF;
END;
//
DELIMITER ;
