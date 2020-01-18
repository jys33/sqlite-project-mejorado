Cursos de base de datos 
https://lagunita.stanford.edu/courses/DB/2014/SelfPaced/about 
https://www.youtube.com/channel/UC5ZAemhQUQuNqW3c9Jkw8ug/videos 


https://es.khanacademy.org/computing/computer-programming/sql/modifying-databases-with-sql/a/using-sql-to-update-a-database
SQL normal
INSERT INTO diary_logs (id, food, activity)VALUES (123, "ice cream", "running");

Python con la biblioteca de SQLAlchemy:
diary_logs.insert().values(id=123, food="ice cream", activity='running')

Ejemplo de consulta

 function obtenerTelDeAsociado($id_asociado)
 {
    $q = 'SELECT nro_tel FROM telefono WHERE id_asociado = :id_asociado ;';

    $stmt = Db::getInstance()->prepare($q);

    if($stmt === false) return false;

    $data = [':id_asociado' => $id_asociado];

    $result = $stmt->execute($data);

    if (!$result) return false;

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
 }