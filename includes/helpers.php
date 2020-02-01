<?php

require_once("constants.php");

function test_input($data)
{
    $data = removeSpaces($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

/**
 * Devuelve un cadena sin espacios
 */
function removeSpaces($str)
{
	return preg_replace('/\s+/', ' ', trim($str));
}

// función para encriptar contraseña
function encryptPassword($password)
{
    $passwordHash = password_hash($password . 'r8UN#uHVX5', PASSWORD_BCRYPT, ['cost' => 12]);
    return $passwordHash;
}

function verifyPassword($password, $passwordHash)
{
    $passwordMatch = ( password_verify($password . 'r8UN#uHVX5', $passwordHash) == $passwordHash );
    return $passwordMatch;
}

function redirect($destination)
{
    // handle URL - manejar la URL
    if (preg_match("/^https?:\/\//", $destination))
    {
        header("Location: " . $destination);
    }
    // handle absolute path - manejar ruta absoluta
    else if (preg_match("/^\//", $destination))
    {
        $protocol = (isset($_SERVER["HTTPS"])) ? "https" : "http";
        $host = $_SERVER["HTTP_HOST"];
        header("Location: $protocol://$host$destination");
    }
    // handle relative path - manejar ruta relativa
    else
    {
        // adapted from http://www.php.net/header
        $protocol = (isset($_SERVER["HTTPS"])) ? "https" : "http";
        $host = $_SERVER["HTTP_HOST"];
        $path = rtrim(dirname($_SERVER["PHP_SELF"]), "/\\");
        header("Location: $protocol://$host$path/$destination");
    }
    // exit immediately since we're redirecting anyway
    exit;
}

function checkFormat($regex, $value, $email = false)
{
    if ($email) {
        $value = filter_var($value, FILTER_SANITIZE_EMAIL);
    }
    return preg_match($regex, $value);
}

function checkLength($str, $minlength, $maxlength)
{
    $strlen = strlen($str);
    if ($strlen >= $minlength && $strlen <= $maxlength) {
        return true;
    }
    return false;
}

function pass_gen(int $length = 8) : string
{
    $pass = array();
    for ($i = 0; $i < $length; $i++) {
        $pass[] = chr(mt_rand(32, 126));
    }

    return implode($pass);
}

function validatePasswordStrength($password) // Caracteres se escribe sin acento
{
    // **REQUERIMIENTOS DE CONTRASEÑA DEL SITIO NETACAD.COM**
    // New password must include:
    // One upper case character => Un carácter en mayúscula
    // One lower case character => Un carácter en minúscula
    // One number character => Un carácter numérico
    // Please enter a minimum of 8 characters and maximum of 60 characters. => Por favor, ingrese un mínimo de 8 caracteres y un máximo de 60 caracteres.
    // No special characters, except these: @ . - _ ~ ! # $ % ^ & *
    // Password cannot contain all or part of your email address

    // Validate password strength
    $uppercase = preg_match('@[A-Z]@', $password);
    $lowercase = preg_match('@[a-z]@', $password);
    $number    = preg_match('@[0-9]@', $password);
    $specialChars = preg_match('@[^\w]@', $password);

    if(!$uppercase || !$lowercase || !$number || !$specialChars) {
        return false;
    }else{
        return true;
    }
}

function isPositiveInt($int) {
    /**
     * FILTER_VALIDATE_INT devuelve true aun cuando el número sea un string de la forma '33' y no 33
     * tampoco toma en cuenta el 0 como si lo hace la función is_int()
     */
    return filter_var($int, FILTER_VALIDATE_INT) && $int > 0;

    /**
     * Devuelve 1 si a es positivo, -1 si a es negativo, y 0 si a es cero.
     * no toma en cuenta el cero como válido
     */ 
    // if(gmp_sign($ID) == 1){
    //     echo 'Es valido';
    // } else {
    //     echo 'Es invalido';
    // }
    /**
     * 
     */
    filter_var($int, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);

    if (preg_match('/^-?\d+$/', $int))
        print "user input is an integer\n";
}

function dateToDb($date) {
    $df = explode('/', $date);
    return $df[2] . '-' . $df[1] . '-' . $df[0];
}

function dateToView($date) {
    $df = explode('-', $date);
    return $df[2] . '/' . $df[1] . '/' . $df[0];
}

/**
 * https://es.wikipedia.org/wiki/Clave_%C3%9Anica_de_Identificaci%C3%B3n_Tributaria
 */
function validarCuit($cuit){ // 27-27369830-2
    if ( !preg_match('/^\d{11}$/', $cuit) ) {
        return false;
    }
    /**
     * ^ niega la clase, pero sólo si se trata del primer carácter
     * reemplaza todos los caracteres que no son digitos (-, ., ' ').
     * $card_number = '7896-541-230'; $card_number = preg_replace('/\D+/', '', $card_number);
     */
	$cuit = preg_replace('/[^\d]/', '', (string) $cuit);
	$cuit_tipos = [20, 23, 24, 27, 30, 33, 34];

	if (strlen($cuit) != 11) {
		return false;
	}

	$tipo = (int) substr($cuit, 0, 2);

	if (!in_array($tipo, $cuit_tipos, true)) {
	    return false;
    }

	$acumulado = 0;
	$digitos = str_split($cuit); // Convertir en un array
	$digito = array_pop($digitos); // Extraer último elemento del array
	$contador = count($digitos);

	for ($i = 0; $i < $contador; $i++) {
		$acumulado += $digitos[ 9 - $i ] * (2 + ($i % 6));
	}

	$verif = 11 - ($acumulado % 11);

	// Si el resultado es 11, el dígito verificador será 0
	// Sino, será el dígito verificador
	$verif = $verif == 11 ? 0 : $verif;

	return $digito == $verif;
}

/**
 * Validación de fecha en el formato 03/02/2019 o 3/2/2019
 */
function validateDate($date){
    $matches = [];
    $pattern = '/^([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{4})$/';
    if (!preg_match($pattern, $date, $matches)) return false;
    
    //checkdate ( int $month , int $day , int $year ) : bool checkdate(12, 31, 2000)
    if (!checkdate($matches[2], $matches[1], $matches[3])) return false;
    // return $matches[3] . '-' . $matches[2] . '-' . $matches[1];
    return true;
}

/**
 * https://es.stackoverflow.com/questions/136325/validar-tel%C3%A9fonos-de-argentina-con-una-expresi%C3%B3n-regular
 */
function validar_tel($tel) {
    /**
     * sin espacios, puntos u otros símbolos
     */
    if ( !preg_match('/^\d+$/', $tel) ) {
        return false;
    }
    $re = '/^(?:((?P<p1>(?:\( ?)?+)(?:\+|00)?(54)(?<p2>(?: ?\))?+)(?P<sep>(?:[-.]| (?:[-.] )?)?+)(?:(?&p1)(9)(?&p2)(?&sep))?|(?&p1)(0)(?&p2)(?&sep))?+(?&p1)(11|([23]\d{2}(\d)??|(?(-10)(?(-5)(?!)|[68]\d{2})|(?!))))(?&p2)(?&sep)(?(-5)|(?&p1)(15)(?&p2)(?&sep))?(?:([3-6])(?&sep)|([12789]))(\d(?(-5)|\d(?(-6)|\d)))(?&sep)(\d{4})|(1\d{2}|911))$/D';
    if ( preg_match($re, $tel, $match) ) {
        return true;
    }
    return false;
}

/**
 * Obtener la edad recibe un string en el formato 23/04/1994
 */
function getAge($dateOfBirth)
{
    $today = date("Y-m-d");
    $diff = date_diff(date_create(str_replace('/', '-', $dateOfBirth)), date_create($today));
    return $diff->format('%y');
}

/**
 * REGEX
 */
/* Pluck the protocol, hostname, and port number from a URL */
// if (preg_match('{^(https?):// ([^/:]+) (?: :(\d+) )? }x', $_SERVER['REQUEST_URI'], $matches))
// {
//     $proto = $matches[1];
//     $host = $matches[2];
//     $port = $matches[3] ? $matches[3] : ($proto == "http" ? 80 : 443);
//     print "Protocol: $proto\n";
//     print "Host : $host\n";
//     print "Port : $port\n";
// }


// Funciones copiadas de TodoList para muestra no utilizar
function getUrlParam($name)
{
    if (!array_key_exists($name, $_GET)) {
        throw new NotFoundException('URL parameter "' . $name . '" not found.');
    }
    return $_GET[$name];
}

function getUserByGetId()
{
    $id = null;
    try {
        $id = getUrlParam('id');
    } catch (Exception $ex) {
        throw new NotFoundException('No User identifier provided.');
    }
    if (!is_numeric($id)) {
        throw new NotFoundException('Invalid User identifier provided.');
    }
    $userdao = new UserDao();
    $user = $userdao->findById($id);
    if ($user === null) {
        throw new NotFoundException('Unknown User identifier provided.');
    }
    return $user;
}
// End

function error_field ($title, array $errors) {
    if (array_key_exists ($title, $errors) ) {
        return 'is-invalid';
    }
    return '';
}


// método definido en asociado_registrar
function insertarTelAsociado($id_asociado, $nro_tel, $tipo = 'movil')
{
    $q = 'INSERT INTO telefono (nro_tel, tipo, id_asociado) VALUES (?, ?, ?); ';

    return Db::query($q, $nro_tel, $tipo, $id_asociado);
}

// Esta función esta definida en la página de registro.
function getUserByEmail($useremail)
{
    $q = 'SELECT * FROM user WHERE user_email = ? ;';

    return Db::query($q, $useremail);
}

/**
 * funcion repetida en asociado editar
 */
function obtenerTelAsociado($id_asociado, $tipo = 'movil')
{
    $q = 'SELECT
            nro_tel
          FROM
            telefono
          WHERE  tipo = ?
          AND id_asociado = ? ;';

    $stmt = Db::getInstance()->prepare($q);

    if($stmt === false) return false;

    $result = $stmt->execute([$tipo, $id_asociado]);

    if (!$result) return false;

    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getUserById($user_id)
{
    $q = 'SELECT * FROM user WHERE user_id = ? ;';

    return Db::query($q, $user_id);
}