<?php
$user_id_del = limpiar_cadena($_GET['user_id_del']);

# Verificando usuario #
$check_usuario = conexion();
$check_usuario = $check_usuario->query("SELECT id_usuario FROM usuario WHERE id_usuario = '$user_id_del'");

if ($check_usuario->rowCount() == 1) {

    # Verificando usuario #
    $check_productos = conexion();
    $check_productos = $check_productos->query("SELECT id_usuario FROM producto WHERE id_usuario = '$user_id_del' LIMIT 1");

    if ($check_productos->rowCount() <= 0) {

        $eliminar_usuario = conexion();
        $eliminar_usuario = $eliminar_usuario->prepare("DELETE FROM usuario WHERE id_usuario = :id");

        $eliminar_usuario->execute([":id" => $user_id_del]);

        if ($eliminar_usuario->rowCount() == 1) {
            echo '
            <div class="notification is-info is-light">
                <strong>¡Usuario eliminado!</strong><br>
                Los datos del usuario han sido eliminados
            </div>
        ';
        } else {
            echo '
            <div class="notification is-danger is-light">
                <strong>¡Opss!</strong><br>
                No se pudo eliminar el usuario, intenta nuevamente
            </div>
        ';
        }

        $eliminar_usuario = null;
    } else {
        echo '
            <div class="notification is-danger is-light">
                <strong>¡Opss!</strong><br>
                No se puede eliminar el usuario porque tiene productos registrados
            </div>
        ';
    }
    $check_productos = null;
} else {
    echo '
            <div class="notification is-danger is-light">
                <strong>¡Opss!</strong><br>
                El usuario que intenta eliminar no existe
            </div>
        ';
}

$check_usuario = null;
