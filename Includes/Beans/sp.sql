delimiter //
CREATE PROCEDURE generar_planilla5(IN mes varchar(2), IN my_year varchar(4))
BEGIN
    DECLARE ultimo_dia DATE;
    SET ultimo_dia = LAST_DAY(CONCAT(my_year, '-', mes, '-', '30'));
    SELECT  e.empleado_id,
    e.nombre, e.apellido,
    e.fecha_inicio_labores,
    b.bonificacion_num,
    i.isr_num,
    (select salario_ordinario from igss_salario where year = my_year) as 'salario_ordinario',
    (select igss from igss_salario where year = my_year) as igss,
    ((select salario_ordinario from igss_salario where year =  my_year) + b.bonificacion_num + isr_num)
    as 'total_sueldo',
    (((select salario_ordinario from igss_salario where year = my_year) + b.bonificacion_num + isr_num)  - i.isr_num - (select igss from igss_salario where year = my_year)) as salario_liquido
    from empleado e
    inner join bonificacion b on b.empleado_id = e.empleado_id
    inner join isr i on i.empleado_id = e.empleado_id
    and i.isr_fecha = (
	  select i.isr_fecha from isr i
    where i.isr_fecha <= ultimo_dia
    and i.empleado_id = e.empleado_id
    order by i.isr_fecha DESC limit 1
    )
    and b.bonificacion_fecha = (
    select b.bonificacion_fecha from bonificacion b
    where b.bonificacion_fecha <= ultimo_dia
    and b.empleado_id = e.empleado_id
    order by bonificacion_fecha DESC limit 1
    )
    and e.estado_id = 1;
END//
delimiter
