<?php

/**
 * Modelo: Usuario.php
 * Propósito: Gestionar los perfiles de usuario, autenticación y administración.
 * Proyecto: GameSocial
 */

class Usuario{

	private $conexion;

	// Instanciamos la conexión a la base de datos
	public function __construct($conexion_db){
		$this->conexion = $conexion_db;
	}

	// Obtenemos información pública y privada de los usuarios por ID
	public function obtenerPorId($id_usuario){
		$sql = "SELECT id_usuario, nombre_usuario, email, password, foto_perfil, biografia, 
                       rol, estado, fecha_registro, token_recordarme
                FROM usuarios
                WHERE id_usuario = :id
                LIMIT 1";

		$stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id', $id_usuario, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);	
	}

	// Procesamos la información para la autenticación del usuario
	public function login($email, $password) {
        // Seleccionamos los campos necesarios para la sesión
        $sql = "SELECT id_usuario, nombre_usuario, password, rol, foto_perfil 
                FROM usuarios 
                WHERE email = :email 
                LIMIT 1";
        
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([':email' => $email]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario && password_verify($password, $usuario['password'])) {
            // Limpiamos el password del array antes de devolverlo por seguridad
            unset($usuario['password']);
            return $usuario;
        }
        
        return false;
    }

	// Actualizamos la biografía del usuario
	public function actualizarBiografia($id_usuario, $biografia) {
        $sql = "UPDATE usuarios SET biografia = :bio WHERE id_usuario = :id";
        $stmt = $this->conexion->prepare($sql);
        return $stmt->execute([
            ':bio' => $biografia,
            ':id'  => (int)$id_usuario
        ]);
    }

	// Actualizamos la foto de perfil
	public function actualizarFotoPerfil($id_usuario, $ruta) {
        $sql = "UPDATE usuarios SET foto_perfil = :foto WHERE id_usuario = :id";
        $stmt = $this->conexion->prepare($sql);
        return $stmt->execute([
            ':foto' => $ruta,
            ':id'   => (int)$id_usuario
        ]);
    }

	// Buscar por nombre
	public function buscarPorNombre($termino) {
        $sql = "SELECT id_usuario, nombre_usuario, foto_perfil, biografia
                FROM usuarios
                WHERE nombre_usuario LIKE :termino
                LIMIT 20";
        
        $stmt = $this->conexion->prepare($sql);
        $like = '%' . $termino . '%';
        $stmt->bindValue(':termino', $like, PDO::PARAM_STR);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

	// Obtenemos todos los usuarios
	public function obtenerTodos() {
        $sql = "SELECT id_usuario, nombre_usuario, email, rol, estado, fecha_registro FROM usuarios";
        return $this->conexion->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

	// Cambiar rol
	public function cambiarRol($id) {
        $sql = "UPDATE usuarios 
                SET rol = IF(rol = 'admin', 'usuario', 'admin') 
                WHERE id_usuario = :id";
        
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

	// Eliminar usuario
	public function eliminar($id) {
        $sql = "DELETE FROM usuarios WHERE id_usuario = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

	// Comprobamos si el email existe
	public function existeEmail($email) {
        $sql = "SELECT 1 FROM usuarios WHERE email = :email LIMIT 1";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([':email' => $email]);
        
        // Retornamos true si fetchColumn encuentra algo, false si no.
        return $stmt->fetchColumn() !== false;
    }

	// Comprobamos si el nombre de usuario existe
	public function existeNombreUsuario($nombre) {
	    $sql = "SELECT 1 FROM usuarios WHERE nombre_usuario = :nombre LIMIT 1";
	    $stmt = $this->conexion->prepare($sql);
	    $stmt->execute([':nombre' => $nombre]);
	
	    // Si fetchColumn devuelve algo, es que el nombre ya está en uso
	    return $stmt->fetchColumn() !== false;
	}

	// Registramos un usuario
	public function registrar($nombre, $email, $password) {
        // Encriptamos la contraseña antes de guardarla
        $hash = password_hash($password, PASSWORD_DEFAULT);
        
        $sql = "INSERT INTO usuarios (nombre_usuario, email, password, rol, estado, fecha_registro)
                VALUES (:nombre, :email, :password, 'usuario', 'activo', NOW())";
        
        $stmt = $this->conexion->prepare($sql);
        return $stmt->execute([
            ':nombre'   => $nombre,
            ':email'    => $email,
            ':password' => $hash
        ]);
    }

	// Guardamos el token de persistencia para la función "Recordarme".
	public function guardarTokenRecordarme($id_usuario, $token) {
	    $sql = "UPDATE usuarios SET token_recordarme = :token WHERE id_usuario = :id";
	    $stmt = $this->conexion->prepare($sql);
	    return $stmt->execute([
	        ':token' => $token,
	        ':id'    => $id_usuario
	    ]);
	}

}
