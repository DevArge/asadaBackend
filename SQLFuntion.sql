DELIMITER $$
CREATE FUNCTION `F_LecturaAnterior`(`idMedidorIN` INT, `periodoIN` VARCHAR(20)) RETURNS int(11)
    READS SQL DATA
    DETERMINISTIC
BEGIN
  DECLARE anterior int;
  SELECT lecturas.lectura INTO anterior from lecturas where periodo < periodoIN and idMedidor = idMedidorIN  order by periodo desc LIMIT 1;
  RETURN anterior;
END$$
DELIMITER ;
