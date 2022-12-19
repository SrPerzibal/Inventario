<?php

$inicio = ($pagina > 0) ? (($pagina * $registros) - $registros) : 0;
$tabla = "";

$campos = "producto.id_producto, producto.codigo_producto, producto.nombre_producto, producto.precio_producto, producto.stock_producto, 
producto.foto_producto, categoria.nombre_categoria, usuario.nombre_usuario, usuario.apellido_usuario";

if (isset($busqueda) && $busqueda != "") {

   $consulta_datos = "SELECT $campos FROM producto INNER JOIN categoria ON producto.id_categoria = categoria.id_categoria INNER JOIN usuario ON producto.id_usuario = usuario.id_usuario
   WHERE producto.codigo_producto LIKE '%$busqueda%' OR producto.nombre_producto LIKE '%$busqueda%' ORDER BY producto.nombre_producto ASC LIMIT $inicio,$registros";

   $consulta_total = "SELECT COUNT(id_producto) FROM producto WHERE codigo_producto LIKE '%$busqueda%' OR nombre_producto LIKE '%$busqueda%'";
} elseif ($id_categoria > 0) {

   $consulta_datos = "SELECT $campos FROM producto INNER JOIN categoria ON producto.id_categoria = categoria.id_categoria INNER JOIN usuario ON producto.id_usuario = usuario.id_usuario
   WHERE producto.id_categoria = '$id_categoria' ORDER BY producto.nombre_producto ASC LIMIT $inicio,$registros";

   $consulta_total = "SELECT COUNT(id_producto) FROM producto WHERE id_categoria = '$id_categoria'";
} else {

   $consulta_datos = "SELECT $campos FROM producto INNER JOIN categoria ON producto.id_categoria = categoria.id_categoria INNER JOIN usuario ON producto.id_usuario = usuario.id_usuario
   ORDER BY producto.nombre_producto ASC LIMIT $inicio,$registros";

   $consulta_total = "SELECT COUNT(id_producto) FROM producto";
}

$conexion = conexion();

$datos = $conexion->query($consulta_datos);
$datos = $datos->fetchAll();

$total = $conexion->query($consulta_total);
$total = (int) $total->fetchColumn();

$Npaginas = ceil($total / $registros);

if ($total >= 1 && $pagina <= $Npaginas) {
   $contador = $inicio + 1;
   $pag_inicio = $inicio + 1;
   foreach ($datos as $rows) {
      $tabla .= '
               <article class="media">
               <figure class="media-left">
                     <p class="image is-64x64">';
      if (is_file("./img/producto/" . $rows['foto_producto'])) {
         $tabla .= '<img src="./img/producto/' . $rows['foto_producto'] . '">';
      } else {
         $tabla .= '<img src="./img/producto.png">';
      }

      $tabla .= '</p>
               </figure>
               <div class="media-content">
                     <div class="content">
                        <p>
                           <strong>' . $contador . ' - ' . $rows['nombre_producto'] . '</strong><br>
                           <strong>CODIGO:</strong> ' . $rows['codigo_producto'] . ',
                           <strong>PRECIO:</strong> $' . $rows['precio_producto'] . ', 
                           <strong>STOCK:</strong> ' . $rows['stock_producto'] . ', 
                           <strong>CATEGORIA:</strong> ' . $rows['nombre_categoria'] . ', 
                           <strong>REGISTRADO POR:</strong> ' . $rows['nombre_usuario'] . ' ' . $rows['apellido_usuario'] . '
                        </p>
                     </div>
                     <div class="has-text-right">
                        <a href="index.php?vista=product_img&product_id_up=' . $rows['id_producto'] . '" class="button is-link is-rounded is-small">Imagen</a>
                        <a href="index.php?vista=product_update&product_id_up=' . $rows['id_producto'] . '" class="button is-success is-rounded is-small">Actualizar</a>
                        <a href="' . $url . $pagina . '&product_id_del=' . $rows['id_producto'] . '" class="button is-danger is-rounded is-small">Eliminar</a>
                     </div>
               </div>
            </article>
            <hr>
            ';
      $contador++;
   }
   $pag_final = $contador - 1;
} else {
   if ($total >= 1) {
      $tabla .= '
      <p class="has-text-centered">
				<a href="' . $url . '1" class="button is-link is-rounded is-small mt-4 mb-4">
					Haga clic acá para recargar el listado
				</a>
      </p>';
   } else {
      $tabla .= '<p class="has-text-centered">No hay registros en el sistema</p>';
   }
}


if ($total > 0 && $pagina <= $Npaginas) {
   $tabla .= '<p class="has-text-right">Mostrando productos <strong>' . $pag_inicio . '</strong> al <strong>' . $pag_final . '</strong> de un <strong>total de ' . $total . '</strong></p>';
}

$conexion = null;
echo $tabla;

if ($total >= 1 && $pagina <= $Npaginas) {
   echo paginador_tablas($pagina, $Npaginas, $url, 7);
}
