<?php
include "Library_Mongo.php";
use Library_Mongo as Mongo;

session_start();

$dbo = new Mongo();

if (!isset($_POST['action'])) {
    http_response_code(405);
    include_once('../error/405.php');
}

header('Content-Type: application/json');

$action = $_POST['action'];
$content = json_decode($_POST['content'],true);
$column = json_decode($_POST['column'],true);
$rcsid = $_SESSION["rcsid"];

$id = $dbo->selectSIS('users','user',array('rcsid'=>'qianh'),array('_id'))[0]['_id'];

$err = false;
$success = true;
if ($action == "store") {
    for ($i=0;$i<count($content);$i++) {
        $thisContent = $content[$i];
        $thisColumn = $column[$i];
        if ($thisColumn == "group_answers") {
            $thisColumn = "group";
            $success = $dbo->updateSIS('users',array("group_answers" => $thisContent),$thisColumn,array(),array('_id'=>$id));
        } else {
            $success = $dbo->updateSIS('users',$thisContent,$thisColumn,array(),array('_id'=>$id));
        }
        if ($success != true) {
            $err = true;
        }
    }
}

$response = array();
if ($err) {
    $response = [
        "status" => -1,
        "error" => "An error occur!",
    ];
} else {
    $response = [
        "status" => 0,
        "error" => null,
    ];
}

echo json_encode($response);