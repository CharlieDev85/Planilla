select empleado_id, nombre, apellido, fecha_inicio_labores
from empleado
where fecha_inicio_labores <= last_day('2016/06/01')
and estado_id = 1


--------------


SELECT  e.empleado_id,
e.nombre, e.apellido,
e.fecha_inicio_labores,
b.bonificacion_num,
i.isr_num,
(select salario_ordinario from igss_salario where year = 2016) as 'salario_ordinario',
(select igss from igss_salario where year = 2016) as igss,
((select salario_ordinario from igss_salario where year = 2016) + b.bonificacion_num + isr_num)
as 'total_sueldo',
(((select salario_ordinario from igss_salario where year = 2016) + b.bonificacion_num + isr_num)  - i.isr_num - (select igss from igss_salario where year = 2016)) as salario_liquido
from empleado e
inner join bonificacion b on b.empleado_id = e.empleado_id
inner join isr i on i.empleado_id = e.empleado_id
and i.isr_fecha = (
	select i.isr_fecha from isr i
    where i.isr_fecha <= '2016-06-30'
    and i.empleado_id = e.empleado_id
    order by i.isr_fecha DESC limit 1
)
and b.bonificacion_fecha = (
    select b.bonificacion_fecha from bonificacion b
    where b.bonificacion_fecha <= '2016-06-30'
    and b.empleado_id = e.empleado_id
    order by bonificacion_fecha DESC limit 1
)
and e.estado_id = 1;

-- la misma fecha.-si se cambia la fecha_inicio_labores al pasasdo, se debe isertar isr y bono por defecto con la misma fecha
-- se se cambia la fecha_inicio_labores al futuro, se deben eliminar previous bonos e isr e insertar nuevos default con