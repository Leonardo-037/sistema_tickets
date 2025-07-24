<?php

require_once dirname(__DIR__, 3) . "/vendor/autoload.php";

// Cargar variables de entorno antes de iniciar la sesión
$dotenv = DotenvVault\DotenvVault::createImmutable(dirname(__DIR__, 3));
$dotenv->safeLoad();

@session_name($_ENV["APP_NAME"]);
@session_start();
/*enable this for development purpose */
//ini_set('display_startup_errors', 1);
//ini_set('display_errors', 1);
//error_reporting(-1);
date_default_timezone_set(@date_default_timezone_get());
define('ArtifyABSPATH', dirname(__FILE__) . '/');
require_once ArtifyABSPATH . "config/config.php";
spl_autoload_register('artifyAutoLoad');

function artifyAutoLoad($class) {
    if (file_exists(ArtifyABSPATH . "classes/" . $class . ".php"))
        require_once ArtifyABSPATH . "classes/" . $class . ".php";
}

if (isset($_REQUEST["artify_instance"])) {
    $fomplusajax = new ArtifyAjaxCtrl();
    $fomplusajax->handleRequest();
}

function buscador_tabla($data, $obj, $columnDB = array()) {
    $queryfy = $obj->getQueryfyObj();
    $tabla = $obj->getLangData("tabla");

    $columnNames = $queryfy->columnNames($tabla);
 
    $whereClause = "";
 
    if(isset($data["action"]) && $data["action"] == "search"){
        if (isset($data['search_col']) && isset($data['search_text'])) {
                $search_col = $data['search_col'];
                $search_text = $data['search_text'];
             
                // Sanitize inputs to prevent SQL injection
                $search_col = preg_replace('/[^a-zA-Z0-9_]/', '', $search_col);
                $search_text = htmlspecialchars($search_text, ENT_QUOTES, 'UTF-8');
             
            if ($search_text !== '') { 
                if ($search_col !== 'all') {
                    $whereClause = "WHERE $search_col LIKE '%$search_text%'";
                } else {
                    $whereConditions = [];
                    foreach ($columnNames as $columnName) {
                        $whereConditions[] = "$columnName LIKE '%$search_text%'";
                    }
                    $whereClause = "WHERE " . implode(" OR ", $whereConditions);
                }
            }
 
            $query = "SELECT id as ID, name as Name 
            FROM $tabla
            $whereClause";
 
            $obj->setQuery($query);
        }
    }
 
    return $data;
}

function format_sql_col_tabla($data, $obj, $columnDB = array()) {
    $queryfy = $obj->getQueryfyObj();
    $tabla = $obj->getLangData("tabla");
 
    $columnNames = $queryfy->columnNames($tabla);
 
    $template = array(
        'colname' => '',
        'tooltip' => '',
        'attr' => '',
        'sort' => '',
        'col' => '',
        'type' => ''
    );
 
    $default_cols = array();
    foreach ($columnDB as $column) {
        // Aplicar la plantilla y ajustar los valores específicos de la columna
        $details = $template;
        $details['colname'] = ucfirst(str_replace('_', ' ', $column));
        $details['col'] = $column;
 
        // Verificar si la columna está en la base de datos
        if (in_array($column, $columnNames)) {
            // Columna existente en la base de datos
            $default_cols[$column] = $details;
        } else {
            // Columna concatenada o que no está en la base de datos
            $default_cols[$column] = $details;
        }
    }

     // Convertir las claves de $data a minúsculas
    $data = array_change_key_case($data, CASE_LOWER);

    // Evitar duplicados y combinar datos de manera controlada
    foreach ($default_cols as $key => $value) {
        if (!array_key_exists($key, $data)) {
            $data[$key] = $value;
        }
    }
 
    return $data;
}

function eliminacion_masiva_tabla($data, $obj){
    $tabla = $obj->getLangData("tabla");
    $pk = $obj->getLangData("pk");
    $queryfy = $obj->getQueryfyObj();
 
    // Obtener los IDs seleccionados del array
    $selected_ids = $data["selected_ids"];
 
    // Asegurarse de que $selected_ids no esté vacío
    if (!empty($selected_ids)) {
        // Recorrer cada ID y eliminar el producto correspondiente
        foreach ($selected_ids as $id) {
            $queryfy->where($pk, $id);
            $queryfy->delete($tabla);
        }
    }
 
    return $data;
}

function eliminar_tabla($data, $obj){
    $tabla = $obj->langData["tabla"];
    $pk = $obj->langData["pk"];
    $queryfy = $obj->getQueryfyObj();
 
    $id = $data["id"];
 
    if (!empty($id)) {
        $queryfy->where($pk, $id);
        $queryfy->delete($tabla);
    }
 
    return $data;
}

function carga_masiva_nmedicos_insertar($data, $obj){
    $archivo = basename($data["carga_masiva_nmedicos"]["archivo"]);
    $extension = pathinfo($archivo, PATHINFO_EXTENSION);

    $queryfy = $obj->getQueryfyObj();
   
    $rutInvalidos = [];

    if (empty($archivo)) { 
        $error_msg = array("message" => "", "error" => "No se ha subido ningún Archivo", "redirectionurl" => "");
        die(json_encode($error_msg));
    } else {
        if ($extension != "xlsx") { /* comprobamos si la extensión del archivo es diferente de excel */
            //unlink(__DIR__ . "/uploads/".$archivo); /* eliminamos el archivo que se subió */
            $error_msg = array("message" => "", "error" => "El Archivo Subido no es un Archivo Excel Válido", "redirectionurl" => "");
            die(json_encode($error_msg));
        } else {

            $records = $queryfy->excelToArray("uploads/".$archivo); /* Acá capturamos el nombre del archivo excel a importar */

            $sql = array();
            foreach ($records as $Excelval) {

                $rut_completo = $Excelval['Rut'] . '-' . $Excelval['Dv'];

                if (!App\Controllers\HomeController::validaRut($rut_completo)) {
                    $rutInvalidos[] = $rut_completo;
                } else {

                    $existingMedico = $queryfy->DBQuery("SELECT * FROM nmedico WHERE rutmedico = :rut", ['rut' => $rut_completo]);

                    if (!$existingMedico) {
                        $sql = array(
                            'nmedico' => $Excelval['Nombre'],
                            'especialidad' => $Excelval['Especialidad'],
                            'rutmedico' => $rut_completo
                        );

                        $queryfy->insertBatch("nmedico", array($sql));
                    } else {
                        $error_msg = array("message" => "", "error" => "Lo Siguientes Médicos ingresados ya existen: ". implode(", ", $Excelval["Nombre"]), "redirectionurl" => "");
                        die(json_encode($error_msg));
                    }
                }
            }

            if (!empty($rutInvalidos)) {
                $error_msg = array("message" => "", "error" => "Los siguientes Rut inválidos no han sido cargados: " . implode(", ", $rutInvalidos), "redirectionurl" => "");
                die(json_encode($error_msg));
            }
            $data["carga_masiva_nmedicos"]["archivo"] = basename($data["carga_masiva_nmedicos"]["archivo"]);
        }
    }
    return $data;
}


function actualizar_criticosapa($data, $obj){
    $Idsolicitud = $data["criticosapa"]["Idsolicitud"];
    $fecharesultado = $data["criticosapa"]["fecharesultado"];
    $notificado = $data["criticosapa"]["notificado"];

    if($notificado == "si"){
        $queryfy = $obj->getQueryfyObj();
        $queryfy->insert("historico_caso", array(
            "tipo" => "6",
            "fecha_y_hora" => $fecharesultado,
            "Id_solicitud" => $Idsolicitud
        ));
    }

    $obj->setLangData("success", "Datos Actualizados con éxito");
    return $data;
}

function actualizar_notificar_paciente($data, $obj){
    $Idsolicitud = $data["criticosapa"]["Idsolicitud"];
    $fecha = $data["criticosapa"]["fecha"];
    $hora = $data["criticosapa"]["hora"];
    $nombre_funcionario = $data["criticosapa"]["nombre_funcionario"];
    $texto_libre = $data["criticosapa"]["texto_libre"];

    $queryfy = $obj->getQueryfyObj();
    $queryfy->insert("historico_caso", array(
        "tipo" => "5",
        "fecha_y_hora" => $fecha . ' ' . $hora,
        "Id_solicitud" => $Idsolicitud
    ));

    return $data;
}

function formatTableColCallBack($data, $obj){
    // Definir la nueva columna y su valor
    $newColumns = [
        'Imprimir' => [
            'colname' => 'Imprimir', // Nombre visible de la columna
            'tooltip' => '', // Tooltip, si es necesario
            'attr' => '', // Atributos adicionales, si es necesario
            'sort' => '', // Indicar si la columna es ordenable
            'col' => 'imprimir', // Nombre interno de la columna
            'type' => 'text', // Tipo de columna
        ],
        'resultados' => [
            'colname' => 'Resultados', // Nombre visible de la columna
            'tooltip' => '', // Tooltip, si es necesario
            'attr' => '', // Atributos adicionales, si es necesario
            'sort' => '', // Indicar si la columna es ordenable
            'col' => 'resultados', // Nombre interno de la columna
            'type' => 'text', // Tipo de columna
        ],
        'traza' => [
            'colname' => 'Traza', // Nombre visible de la columna
            'tooltip' => '', // Tooltip, si es necesario
            'attr' => '', // Atributos adicionales, si es necesario
            'sort' => '', // Indicar si la columna es ordenable
            'col' => 'traza', // Nombre interno de la columna
            'type' => 'text', // Tipo de columna
        ],
        'edicion' => [
            'colname' => 'Edición', // Nombre visible de la columna
            'tooltip' => '', // Tooltip, si es necesario
            'attr' => '', // Atributos adicionales, si es necesario
            'sort' => '', // Indicar si la columna es ordenable
            'col' => 'edicion', // Nombre interno de la columna
            'type' => 'text', // Tipo de columna
        ]
    ];

    // Agregar las nuevas columnas al array $data
    foreach ($newColumns as $key => $column) {
        $data[$key] = $column;
    }

    return $data;
}

function actualizar_solicitudesapa($data, $obj){
    $obj->setLangData("success", "Datos Actualizados con éxito");
    return $data;
}  

function formatTableDataCallBack($data, $obj){
        // Definir los nombres y valores de las nuevas columnas
        $newColumns = [
        'Imprimir' => function($row){
            return '<a class="btn btn-light btn-sm ver_solicitudes" href="javascript:;" title="Imprimir" data-id="'.$row['Idsolicitud'].'">
                        <i class="fa fa-print"></i>
                    </a>
                    <a class="btn btn-light btn-sm ver_etiquetas" href="javascript:;" title="Etiqueta" data-id="'.$row['Idsolicitud'].'">
                        <i class="fa fa-barcode"></i>
                    </a>';
        },        
        'resultados' => function($row) {
            return '<a class="btn btn-light btn-sm ver_resultado" href="javascript:;" title="Resultado" data-id="'.$row['Idsolicitud'].'">
                        <i class="fa fa-upload"></i>
                    </a>
                    <a class="btn btn-light btn-sm ver_pdf" href="javascript:;" title="PDF" data-id="'.$row['Idsolicitud'].'">
                        <i class="fa fa-file-pdf-o"></i>
                    </a>';
        },
        'traza' => function($row) {
            return '<a class="btn btn-light btn-sm ver_traza" href="javascript:;" title="Traza" data-id="'.$row['Idsolicitud'].'">
                        <i class="fa fa-info-circle"></i>
                    </a>';
        },
        'edicion' => function($row) {
            return '<a class="artify-actions btn btn-warning btn-sm artify-button artify-button-edit" href="javascript:;" title="Editar" data-id="'.$row['Idsolicitud'].'" data-action="edit">
                        <i class="fa fa-pencil-square-o"></i>
                    </a>
                    <a class="btn btn-danger btn-sm eliminar_solicitudes" href="javascript:;" title="Eliminar" data-id="'.$row['Idsolicitud'].'">
                        <i class="fa fa-trash"></i>
                    </a>';
        }
    ];

    // Iterar sobre cada fila de datos y agregar las nuevas columnas
    foreach ($data as &$row) {
        foreach ($newColumns as $colName => $value) {
            if (is_callable($value)) {
                // Si el valor es una función, llámala con la fila actual
                $row[$colName] = $value($row);
            } else {
                // Si el valor no es una función, asígnalo directamente
                $row[$colName] = $value;
            }
        }
    }

    return $data;
}

function actualizar_resultados($data, $obj){
    $Idsolicitud = $data["solicitudesapa"]["Idsolicitud"];
    $estado = $data["solicitudesapa"]["estado"];
    $fecharesultado = $data["solicitudesapa"]["fecharesultado"];
    $nmedico = $data["solicitudesapa"]["nmedico"];
    $critico = $data["solicitudesapa"]["critico"];
    $rut =  $data["solicitudesapa"]["rut"];
    $nombres = $data["solicitudesapa"]["nombres"];
    $apaterno = $data["solicitudesapa"]["apaterno"];
    $amaterno = $data["solicitudesapa"]["amaterno"];
    $resultado = basename($data["solicitudesapa"]["resultado"]);

    $explode = explode('.', $resultado);
    $extension = array_pop($explode);

    if ($extension != "pdf") {
        $error_msg = array("message" => "", "error" => "El Archivo Subido no es un Archivo PDF Válido", "redirectionurl" => "");
        die(json_encode($error_msg));
    }

    $fecharesultadoDateTime = DateTime::createFromFormat('Y-m-d\TH:i', $fecharesultado);
    if ($fecharesultadoDateTime) {
        $fecharesultadoMysql = $fecharesultadoDateTime->format('Y-m-d H:i:s');
    } else {
        // Manejar el error si la fecha no tiene el formato esperado
        $fecharesultadoMysql = null; // O puedes elegir otro valor por defecto
    }

    if ($critico == 'si') {
        $queryfy = $obj->getQueryfyObj();
       
        $queryfy->insert("criticosapa", array(
            "Id_solicitud" => $Idsolicitud, 
            "rut" => $rut,
            "nombres" => $nombres,
            "apaterno" => $apaterno,
            "amaterno" => $amaterno, 
            "fecharesultado" => $fecharesultado,
            "nmedico" => $nmedico
        ));    
    }

    $queryfy = $obj->getQueryfyObj();
    $queryfy->where("Idsolicitud", $Idsolicitud);
    $solicitudes = $queryfy->select("solicitudesapa");

    $queryfy->insert("historico_caso", array(
        "tipo" => $solicitudes[0]["estado"],
        "fecha_y_hora" => $fecharesultadoMysql,
        "Id_solicitud" => $Idsolicitud
    ));

    $data["solicitudesapa"]["resultado"] = basename($data["solicitudesapa"]["resultado"]);
    $data["solicitudesapa"]["fecharesultado"] = $fecharesultadoMysql;
    $obj->setLangData("success", "Resultados Actualizados con éxito");
    return $data;
}


function search_table($data, $obj) {
    if (isset($data["action"]) && $data["action"] == "search") {
        if (isset($data['search_col']) && isset($data['search_text'])) {
            $search_col = $data['search_col'];
            $search_text = $data['search_text'];

            // Limpiar condiciones previas
            $obj->clearWhereConditions();

            // Si se busca por 'all', aplicar condiciones a todas las columnas relevantes
            if ($search_col == 'all') {
                $obj->where("Idsolicitud", "%$search_text%", "LIKE", "OR")
                    ->where("fechatoma", "%$search_text%", "LIKE", "OR")
                    ->where("rut", "%$search_text%", "LIKE", "OR")
                    ->where("CONCAT(nombres, ' ', apaterno, ' ', amaterno)", "%$search_text%", "LIKE", "OR")
                    ->where("tipomuestra", "%$search_text%", "LIKE", "OR")
                    ->where("servicio", "%$search_text%", "LIKE", "OR")
                    ->where("dgclinico", "%$search_text%", "LIKE", "OR")
                    ->where("organo", "%$search_text%", "LIKE", "OR")
                    ->where("nmedico", "%$search_text%", "LIKE", "OR")
                    ->where("centroderivacion", "%$search_text%", "LIKE", "OR")
                    ->where("estado", "%$search_text%", "LIKE");
                    
            } else {
                // Aplicar condición en la columna específica
                if ($search_col == 'nombres') {
                    $obj->where("CONCAT(nombres, ' ', apaterno, ' ', amaterno)", "%$search_text%", "LIKE");
                } else {
                    $obj->where($search_col, "%$search_text%", "LIKE");
                }
            }
        }
    }
    return $data;
}


function beforeTableDataCallBackCriticos($data, $obj){
    if(isset($data['search_col']) && $data['search_col'] == 'all'){
        $obj->setSearchOperator("LIKE");

        if (isset($data['search_text'])) {
            $date = DateTime::createFromFormat('d-m-Y H:i:s', $data['search_text']);

            // Si se ha logrado convertir a una fecha válida
            if ($date) {
                $data['search_text'] = $date->format('Y-m-d H:i:s');
            }
        }
    }

    return $data;
}

function despues_de_insertar_solicitudesapa($data, $obj){
    $id = $data;   
    $queryfy = $obj->getQueryfyObj();

    $queryfy->where("Idsolicitud", $id);
    $solicitudes = $queryfy->select("solicitudesapa");

    $queryfy->insert("historico_caso", array(
        "tipo" => $solicitudes[0]["estado"],
        "fecha_y_hora" => $solicitudes[0]["fecharegistro"],
        "Id_solicitud" => $id
    ));

    return $data;
}

function agregar_detalle_muestra($data, $obj){
    $run = $data["solicitudesapa"]["rut"];
    if (!App\Controllers\HomeController::validaRut($run)) {
        $error_msg = array("message" => "", "error" => "El Run Ingresado no es Válido", "redirectionurl" => "");
        die(json_encode($error_msg));
    }

    $obj->setLangData("success", "Datos Guardados con éxito");
    
    return $data;
}

function seleccionar_solicitudesapa($data, $obj){
    $id = isset($_POST["id"]) ? explode(",", $_POST["id"]) : array();
    $estado = $data["solicitudesapa"]["estado"];
    $fechaderivacion = $data["solicitudesapa"]["fechaderivacion"];

    if(empty($_POST["id"])){
        $error_msg = array("message" => "", "error" => "El campo Ingreso de Ids es Requerido", "redirectionurl" => "");
        die(json_encode($error_msg));
    }

    if(empty($estado)){
        $error_msg = array("message" => "", "error" => "El campo Estado es Requerido", "redirectionurl" => "");
        die(json_encode($error_msg));
    }

    if(empty($fechaderivacion)){
        $error_msg = array("message" => "", "error" => "El campo Fecharecepcion es Requerido", "redirectionurl" => "");
        die(json_encode($error_msg));
    }

    $queryfy = $obj->getQueryfyObj();
    foreach ($id as $Idsolicitud) {
        $queryfy->where("Idsolicitud", $Idsolicitud);
        $queryfy->update("solicitudesapa", array(
            "estado" => $estado, 
            "fechaderivacion" => $fechaderivacion
        ));

        $queryfy->insert("historico_caso", array(
            "tipo" => $estado,
            "fecha_y_hora" => $fechaderivacion,
            "Id_solicitud" => $Idsolicitud
        ));
    }

    $obj->setLangData("success", "Registros actualizados correctamente");

    $newdata = array();
    $newdata["solicitudesapa"]["estado"] = $estado;
    $newdata["solicitudesapa"]["fechaderivacion"] = $fechaderivacion;

    return $newdata;
}

function seleccionar_solicitudesapa_derivacion($data, $obj){
    $id = isset($_POST["id"]) ? explode(",", $_POST["id"]) : array();
    $estado = $data["solicitudesapa"]["estado"];
    $fechaderivacion = $data["solicitudesapa"]["fechaderivacion"];
    $centroderivacion = $data["solicitudesapa"]["centroderivacion"];

    if(empty($_POST["id"])) {
        $error_msg = array("message" => "", "error" => "El campo Ingreso de Ids es Requerido", "redirectionurl" => "");
        die(json_encode($error_msg));
    }

    if(empty($estado)){
        $error_msg = array("message" => "", "error" => "El campo Estado es Requerido", "redirectionurl" => "");
        die(json_encode($error_msg));
    }

    if(empty($fechaderivacion)){
        $error_msg = array("message" => "", "error" => "El campo fechaderivacion es Requerido", "redirectionurl" => "");
        die(json_encode($error_msg));
    }

    if(empty($centroderivacion)){
        $error_msg = array("message" => "", "error" => "El campo centroderivacion es Requerido", "redirectionurl" => "");
        die(json_encode($error_msg));
    }

    $queryfy = $obj->getQueryfyObj();
    foreach ($id as $Idsolicitud) {
        $queryfy->where("Idsolicitud", $Idsolicitud);
        $queryfy->update("solicitudesapa", array(
            "estado" => $estado, 
            "fechaderivacion" => $fechaderivacion, 
            "centroderivacion" => $centroderivacion
        ));

        $queryfy->insert("historico_caso", array(
            "tipo" => $estado,
            "fecha_y_hora" => $fechaderivacion,
            "Id_solicitud" => $Idsolicitud
        ));
    }

    $obj->setLangData("success", "Registros actualizados correctamente");

    $newdata = array();
    $newdata["solicitudesapa"]["estado"] = $estado;
    $newdata["solicitudesapa"]["fechaderivacion"] = $fechaderivacion;

    return $newdata;
}

function actualizar_configuracion($data, $obj){
    $data["configuracion_general"]["logo_login"] = basename($data["configuracion_general"]["logo_login"]);
    $data["configuracion_general"]["imagen_de_fondo_login"] = basename($data["configuracion_general"]["imagen_de_fondo_login"]);
    $data["configuracion_general"]["imagen_de_carga"] = basename($data["configuracion_general"]["imagen_de_carga"]);
    return $data;
}

function carga_masiva_prestaciones_insertar($data, $obj){
    $archivo = basename($data["carga_masiva_prestaciones"]["archivo"]);

    $explode = explode('.', $archivo);
    $extension = array_pop($explode);

    $queryfy = $obj->getQueryfyObj();
   
    if (empty($archivo)) { 
        $error_msg = array("message" => "", "error" => "No se ha subido ningún Archivo", "redirectionurl" => "");
        die(json_encode($error_msg));
    } else {
        if ($extension != "xlsx") { /* comprobamos si la extensión del archivo es diferente de excel */
            unlink(__DIR__ . "/uploads/".$archivo); /* eliminamos el archivo que se subió */
            $error_msg = array("message" => "", "error" => "El Archivo Subido no es un Archivo Excel Válido", "redirectionurl" => "");
            die(json_encode($error_msg));

        } else {

            $records = $queryfy->excelToArray("uploads/".$archivo); /* Acá capturamos el nombre del archivo excel a importar */

            $sql = array();
            foreach ($records as $Excelval) {
                $sql['tipo_solicitud'] = $Excelval['TIPO SOLICITUD'];
                $sql['especialidad'] = $Excelval['ESPECIALIDAD'];
                $sql['tipo_de_examen'] = $Excelval['TIPO DE EXAMEN'];
                $sql['examen'] = $Excelval['EXAMEN'];
                $sql['codigo_fonasa'] = $Excelval['CODIGO FONASA'];
                $sql['glosa'] = $Excelval['GLOSA'];

                $queryfy->insertBatch("prestaciones", array($sql));
            }
            $data["carga_masiva_prestaciones"]["archivo"] = basename($data["carga_masiva_prestaciones"]["archivo"]);
        }
    }
    return $data;
}

function insertar_detalle_solicitud($data, $obj){
    return $data;
}

function insertar_procedimientos($data, $obj){
    $rut = $data["procedimiento"]["rut"];
    $fecha_solicitud = $data["procedimiento"]["fecha_solicitud"];
    $especialidad = $data["procedimiento"]["procedimiento"];
    $procedimiento_2 = $data["procedimiento"]["procedimiento_2"];
    $servicio = $data["procedimiento"]["servicio"];
    $fecha_registro = $data["procedimiento"]["fecha_registro"];
    $nombres = $data["procedimiento"]["nombres"];
    $apellido_paterno = $data["procedimiento"]["apellido_paterno"];
    $apellido_materno = $data["procedimiento"]["apellido_materno"];
    $operacion = $data["procedimiento"]["operacion"];
    $profesional_solicitante = $data["procedimiento"]["profesional_solicitante"];
    $numero_contacto = $data["procedimiento"]["numero_contacto"];
    $numero_contacto_2 = $data["procedimiento"]["numero_contacto_2"];
    $prioridad = $data["procedimiento"]["prioridad"];

    if(empty($rut) && empty($especialidad) && empty($procedimiento_2) && empty($servicio) && empty($nombres) && empty($apellido_paterno) && empty($apellido_materno) && empty($operacion) && empty($profesional_solicitante) && empty($numero_contacto) && empty($numero_contacto_2) && empty($prioridad)){
        $error_msg = array("message" => "", "error" => "Todos los campos son obligatorios", "redirectionurl" => "");
        die(json_encode($error_msg));
    }

    $newdata = array();
    $newdata["procedimiento"]["rut"] = $rut;
    $newdata["procedimiento"]["fecha_solicitud"] = $fecha_solicitud;
    $newdata["procedimiento"]["procedimiento"] = $procedimiento;
    $newdata["procedimiento"]["procedimiento_2"] = $procedimiento_2;
    $newdata["procedimiento"]["servicio"] = $servicio;
    $newdata["procedimiento"]["fecha_registro"] = $fecha_registro;
    $newdata["procedimiento"]["nombres"] = $nombres;
    $newdata["procedimiento"]["apellido_paterno"] = $apellido_paterno;
    $newdata["procedimiento"]["apellido_materno"] = $apellido_materno;
    $newdata["procedimiento"]["operacion"] = $operacion;
    $newdata["procedimiento"]["profesional_solicitante"] = $profesional_solicitante;
    $newdata["procedimiento"]["numero_contacto"] = $numero_contacto;
    $newdata["procedimiento"]["numero_contacto_2"] = $numero_contacto_2;
    $newdata["procedimiento"]["prioridad"] = $prioridad;

    return $newdata;
}

function eliminar_detalle_solicitud($data, $obj){
    /*$id = $data["id"];
    
    $queryfy = $obj->getQueryfyObj();
    $queryfy->where("id_detalle_de_solicitud", $id);
    $result = $queryfy->select("detalle_de_solicitud");
    
    $id_datos_paciente = $result[0]["id_datos_paciente"];
    $queryfy->where("id_datos_paciente", $id_datos_paciente);
    $queryfy->delete("diagnostico_antecedentes_paciente");*/
    return $data;
}

function before_sql_data_estat($data, $obj){
    //print_r($data);
    return $data;
}

/*function editar_procedimientos($data, $obj){
    $id_datos_paciente = $data['datos_paciente']['id_datos_paciente'];
    $estado = $data["detalle_de_solicitud"]["estado"];
    $fecha = $data["detalle_de_solicitud"]["fecha"];
    $fecha_solicitud = $data["detalle_de_solicitud"]["fecha_solicitud"];
    $fundamento = $data['diagnostico_antecedentes_paciente']['fundamento'];
    $adjuntar = $data['diagnostico_antecedentes_paciente']['adjuntar'];
    $id_detalle_de_solicitud = $data["detalle_de_solicitud"]["id_detalle_de_solicitud"];
    $id_diagnostico_antecedentes_paciente = $data["diagnostico_antecedentes_paciente"]["id_diagnostico_antecedentes_paciente"];
 
    $queryfy = $obj->getQueryfyObj();
    $queryfy->where("id_detalle_de_solicitud", $id_detalle_de_solicitud, "=");
    $data_detalle = $queryfy->select("detalle_de_solicitud");
   
    $queryfy->where("id_diagnostico_antecedentes_paciente", $id_diagnostico_antecedentes_paciente, "=");
    $data_diagnostico = $queryfy->select("diagnostico_antecedentes_paciente");
    
    if($data_detalle && $data_diagnostico){
        $queryfy->where("id_detalle_de_solicitud", $id_detalle_de_solicitud, "=", "AND");
        $queryfy->update("detalle_de_solicitud", array("fecha" => $fecha, "estado" => $estado));

        $queryfy->where("id_diagnostico_antecedentes_paciente", $id_diagnostico_antecedentes_paciente);
        $queryfy->update("diagnostico_antecedentes_paciente", array("fundamento" => $fundamento, "adjuntar" => basename($adjuntar)));

        $success = array("message" => "Operación realizada con éxito", "error" => [], "redirectionurl" => "");
        die(json_encode($success));
    }

    $newdata = array();
    $newdata['datos_paciente']['id_datos_paciente'] = $id_datos_paciente;
    $newdata['diagnostico_antecedentes_paciente']['estado'] = $estado;
    $newdata['diagnostico_antecedentes_paciente']['diagnostico'] = $data['diagnostico_antecedentes_paciente']['diagnostico'];

    return $newdata;
}*/

function editar_procedimientos($data, $obj){
    $id_datos_paciente = $data["datos_paciente"]["id_datos_paciente"];
    $estado = $data["detalle_de_solicitud"]["estado"];
    $fecha = $data["detalle_de_solicitud"]["fecha"];
    $fecha_solicitud = $data["detalle_de_solicitud"]["fecha_solicitud"];
    $adjuntar = $data["diagnostico_antecedentes_paciente"]["adjuntar"];
    $diagnostico = $data["diagnostico_antecedentes_paciente"]["diagnostico"];
    $fundamento = $data["diagnostico_antecedentes_paciente"]["fundamento"];

    $queryfy = $obj->getQueryfyObj();
    $queryfy->columns = array("fecha", "datos_paciente.id_datos_paciente", "fecha_solicitud", "diagnostico", "fundamento", "adjuntar", "estado");
    $queryfy->joinTables("detalle_de_solicitud", "detalle_de_solicitud.id_datos_paciente = datos_paciente.id_datos_paciente", "INNER JOIN");
    $queryfy->joinTables("diagnostico_antecedentes_paciente", "diagnostico_antecedentes_paciente.id_datos_paciente = datos_paciente.id_datos_paciente", "INNER JOIN");

    // Filtrar por ID y Fecha
    $queryfy->where("datos_paciente.id_datos_paciente", $id_datos_paciente);
    $queryfy->where("detalle_de_solicitud.fecha_solicitud", $fecha_solicitud);

    // Condiciones para verificar si los valores son diferentes
    $queryfy->where("detalle_de_solicitud.estado", $estado, "=");
    $queryfy->where("detalle_de_solicitud.fecha", $fecha, "=");
    $queryfy->where("diagnostico_antecedentes_paciente.diagnostico", $diagnostico, "=");
    $queryfy->where("diagnostico_antecedentes_paciente.fundamento", $fundamento, "=");
    $queryfy->where("diagnostico_antecedentes_paciente.adjuntar", $adjuntar, "=");

     // Seleccionar para verificar si existen registros con condiciones diferentes
    $result = $queryfy->select("datos_paciente");
    
    if ($result) {
        $error_msg = array("message" => "", "error" => "Modifique los campos para actualizar", "redirectionurl" => "");
        die(json_encode($error_msg));
    }
    
    $queryfy->where("id_datos_paciente", $id_datos_paciente);
    $queryfy->where("detalle_de_solicitud.fecha_solicitud", $fecha_solicitud);
    $queryfy->update("detalle_de_solicitud", array("estado" => $estado, "fecha" => $fecha));
    $queryfy->update("diagnostico_antecedentes_paciente", array("adjuntar" => $adjuntar, "diagnostico" => $diagnostico, "fundamento" => $fundamento));
    
    return $data;
}


function editar_egresar_solicitud($data, $obj) {
    $id_datos_paciente = $data['datos_paciente']['id_datos_paciente'];
    $fecha_egreso = $data['diagnostico_antecedentes_paciente']['fecha_egreso'];
    $motivo_egreso = $data['diagnostico_antecedentes_paciente']['motivo_egreso'];
    $observacion = $_POST['observacion'];

    $queryfy = $obj->getQueryfyObj();
    $queryfy->where("observacion", $observacion, "!=", "AND");
    $queryfy->where("id_datos_paciente", $id_datos_paciente, "=");
    $data_observacion = $queryfy->select("detalle_de_solicitud");

    if($data_observacion){
        $queryfy->where("id_datos_paciente", $id_datos_paciente);
        $queryfy->update("detalle_de_solicitud", array("observacion" => $observacion));

        $success = array("message" => "Operación realizada con éxito", "error" => [], "redirectionurl" => "");
        die(json_encode($success));
    }

    $newdata = array();
    $newdata['datos_paciente']['id_datos_paciente'] = $id_datos_paciente;
    $newdata['diagnostico_antecedentes_paciente']['fecha_egreso'] = $fecha_egreso;
    $newdata['diagnostico_antecedentes_paciente']['motivo_egreso'] = $motivo_egreso;

    return $newdata;
}


function formatTable_datos_paciente($data, $obj){
    if($data){
        for ($i = 0; $i < count($data); $i++) {
            if($data[$i]["fecha_y_hora_ingreso"] != "0000-00-00 00:00:00"){
                $data[$i]["fecha_y_hora_ingreso"] = "<div class='badge badge-success'>" . $data[$i]["fecha_y_hora_ingreso"] . "</div>";
            } else {
                $data[$i]["fecha_y_hora_ingreso"] = "<div class='badge badge-success'>Sin Fecha</div>";
            }

            if($data[$i]["edad"] == "0"){
                $data[$i]["edad"] = "<div class='badge badge-danger'>Sin Edad</div>";
            } else {
                $data[$i]["edad"] = $data[$i]["edad"];
            }
        }
    }
    return $data;
}

function editar_lista_examenes_notas($data, $obj){
    $id_datos_paciente = $data["datos_paciente"]["id_datos_paciente"];
    $fecha_solicitud = $data["detalle_de_solicitud"]["fecha_solicitud"];
    $observacion = $data["detalle_de_solicitud"]["observacion"];

    $queryfy = $obj->getQueryfyObj();
    $queryfy->columns = array("datos_paciente.id_datos_paciente", "fecha_solicitud", "observacion");
    $queryfy->joinTables("detalle_de_solicitud", "detalle_de_solicitud.id_datos_paciente = datos_paciente.id_datos_paciente", "INNER JOIN");

    $queryfy->where("datos_paciente.id_datos_paciente", $id_datos_paciente, "=", "AND");
    $queryfy->where("detalle_de_solicitud.fecha_solicitud", $fecha_solicitud);
    
    $queryfy->where("observacion", $observacion, "=");
    $result = $queryfy->select("datos_paciente");

    if ($result) {
        $error_msg = array("message" => "", "error" => "Modifique los campos para actualizar", "redirectionurl" => "");
        die(json_encode($error_msg));
    }
    
    $queryfy->where("id_datos_paciente", $id_datos_paciente);
    $queryfy->where("detalle_de_solicitud.fecha_solicitud", $fecha_solicitud);
    $queryfy->update("detalle_de_solicitud", array("observacion" => $observacion));

    return $data;
}


function insertar_generador_pdf($data, $obj){
    $data["generador_pdf"]["logo"] = basename($data["generador_pdf"]["logo"]);
    return $data;
}


function formatTable_buscar_examenes($data, $obj){
    if($data){
        for ($i = 0; $i < count($data); $i++) {
            $data[$i]["nombres"] = ucwords($data[$i]["nombres"]) . " " .  ucwords($data[$i]["apellido_paterno"]) . " " . ucwords($data[$i]["apellido_materno"]);

            if($data[$i]["fecha_y_hora_ingreso"] == "0000-00-00 00:00:00"){
                $data[$i]["fecha_y_hora_ingreso"] = "<div class='badge badge-danger'>Sin Fecha</div>";
            } else {
                $data[$i]["fecha_y_hora_ingreso"] = date('d/m/Y H:i:s', strtotime($data[$i]["fecha_y_hora_ingreso"]));
            }

            if($data[$i]["fecha"] != null){
                $data[$i]["fecha"] = date('d/m/Y', strtotime($data[$i]["fecha"]));
            } else {
                $data[$i]["fecha"] = "<div class='badge badge-danger'>Sin Fecha</div>";
            }

            $data[$i]["profesional"] = ucwords($data[$i]["profesional"]);
        }
    }
    return $data;
}

function actualizar_configuracion_api($data, $obj){
    $generar_jwt_token = isset($data["configuraciones_api"]["generar_jwt_token"]) ? $data["configuraciones_api"]["generar_jwt_token"] : null;
    $autenticar_jwt_token = isset($data["configuraciones_api"]["autenticar_jwt_token"]) ? $data["configuraciones_api"]["autenticar_jwt_token"] : null;
    $tiempo_caducidad_token = isset($data["configuraciones_api"]["tiempo_caducidad_token"]) ? $data["configuraciones_api"]["tiempo_caducidad_token"] : null;
   
    $newdata = array();
    $newdata["configuraciones_api"]["generar_jwt_token"] = $generar_jwt_token;
    $newdata["configuraciones_api"]["autenticar_jwt_token"] = $autenticar_jwt_token;
    $newdata["configuraciones_api"]["tiempo_caducidad_token"] = $tiempo_caducidad_token;
    return $newdata;
}

function limpiarTexto($texto) {
    // Reemplazar espacios con guiones bajos
    $texto = str_replace(' ', '_', $texto);

    // Eliminar acentos
    $texto = strtr($texto, [
        'á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u',
        'Á' => 'A', 'É' => 'E', 'Í' => 'I', 'Ó' => 'O', 'Ú' => 'U',
        'ñ' => 'n', 'Ñ' => 'N'
    ]);
    
    // Eliminar cualquier carácter no alfanumérico (opcional)
    $texto = preg_replace('/[^A-Za-z0-9_]/', '', $texto);
    
    return $texto;
}

function deleteRouteFromFile($controller_name, $name_view) {
    $fileName = "app/core/extra_routes.php";
    $filePath = __DIR__ . '/../../../' . $fileName;
    
    // Verificar si el archivo existe antes de proceder
    if (!file_exists($filePath)) {
        echo "El archivo $fileName no existe.";
        return;
    }

    // Leer el contenido del archivo línea por línea
    $lines = file($filePath);
    $phpCode = "\$router->get('/{$controller_name}', '{$controller_name}Controller@{$name_view}');";

    // Filtrar las líneas para excluir la que contiene la ruta que queremos eliminar
    $updatedLines = array_filter($lines, function ($line) use ($phpCode) {
        return trim($line) !== $phpCode;
    });

    // Guardar el contenido actualizado en el archivo
    if (count($lines) !== count($updatedLines)) {
        file_put_contents($filePath, implode("", $updatedLines));
        echo "La ruta ha sido eliminada.";
    } else {
        echo "No se encontró la ruta especificada para eliminar.";
    }
}

function formatTableSolicitudesapa($data, $obj){
    if($data){
        for ($i = 0; $i < count($data); $i++) {

            if($data[$i]["estado"] == "Resultado"){
                $data[$i]["estado"] = "<div class='badge badge-success'>".$data[$i]["estado"]."</div>";
            } else if($data[$i]["estado"] == "Recepcionado"){
                $data[$i]["estado"] = "<div class='badge badge-warning'>".$data[$i]["estado"]."</div>";
            } else if($data[$i]["estado"] == "Solicitado"){
                $data[$i]["estado"] = "<div class='badge badge-secondary'>".$data[$i]["estado"]."</div>";
            } else {
                $data[$i]["estado"] = "<div class='badge badge-primary'>".$data[$i]["estado"]."</div>";
            }
        }
    }
    return $data;
}

function formatTableCriticos($data, $obj){
    if($data){
        foreach($data as &$items){

            if($items["notificado"] == "si"){
                $items["notificado"] = "<div class='badge badge-success'>".$items["notificado"]."</div>";
            } else {
                $items["notificado"] = "<div class='badge badge-danger'>".$items["notificado"]."</div>";
            }
        }
        return $data;
    } 
}

function agregar_profesional($data, $obj){
    $nombre_profesional = $data["profesional"]["nombre_profesional"];
    $apellido_profesional = $data["profesional"]["apellido_profesional"];

    $obj->setLangData("success", "Profesional Agregado con éxito");

    return $data;
}