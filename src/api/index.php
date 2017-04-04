<?php
require 'flight/Flight.php';
require 'flight/helpers.php';
//require_once 'passwordHash.php';

$dbuser = 'zk1woweu_admin';
$dbpass = '6S8,fs)u.9Ra';

///////
// Connection to database
///////
Flight::register('db', 'PDO', array('mysql:host=localhost;dbname=zk1woweu_terres',$dbuser,$dbpass),
  function($db){
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  }
);

///////
// List all competitors
///////
Flight::route('GET /', function(){
  print_r('jury');

  // $db = Flight::db();
  //
  // $sql = "SELECT fullName,comName,country,paymentproof,payment FROM competitors";
  // $comp = $db->prepare($sql);
  // $comp->execute();
  // $comps = $comp->fetch(PDO::FETCH_ASSOC);
  //
  // $db = NULL;
  //
  // Flight::json($comps);
});

///////
// Login to jury APP
///////
Flight::route('POST /login', function(){
  $db = Flight::db();
  $post = Flight::request()->data;
  $data = [];

  $dades = file_get_contents('php://input');
  $post = json_decode($dades,true);

  $sql = "SELECT * FROM jury WHERE email = :email AND password = :password";
  $query = $db->prepare($sql);
  $query->bindParam(':email', $post['username']);
  $query->bindParam(':password', $post['password']);
  $query->execute();
  $count = $query->rowCount();
  if ($count > 0) {
    $user = $query->fetch(PDO::FETCH_ASSOC);
    $data['user']['id'] = $user['id'];
    $data['user']['email'] = $user['email'];
    $data['user']['name'] = $user['name'];
    $data['id'] = get_auth_token(date('Y-m-d H:i:s'),$user['id']);

    Flight::json($data);
  } else {
    $data = 'Bad credentials or user not exists';

    Flight::json($data, 401);
  }

  $db = NULL;

  Flight::json($data);
});

///////
// List all films by section
///////
Flight::route('GET /films/@jury', function($jury){
  $db = Flight::db();

  $films = [];

  $sql = "SELECT corporatefilms.id, competitors.fullName, title, director, translate FROM corporatefilms LEFT JOIN corporate ON corporatefilms.id_cat_user = corporate.id LEFT JOIN competitors ON corporate.user = competitors.id";
  $q = $db->prepare($sql);
  $q->execute();
  $corporate = [];
  while ($row = $q->fetch(PDO::FETCH_ASSOC)) {
    $sql = "SELECT id FROM evaluation_corporate WHERE film = :film AND jury = :jury";
    $y = $db->prepare($sql);
    $y->bindParam(':film', $row['id']);
    $y->bindParam(':jury', $jury);
    $y->execute();
    $count = $y->rowCount();
    if ($count > 0) {
      $row['evaluation'] = 'true';
    } else {
      $row['evaluation'] = 'false';
    }
    $corporate[] = $row;
  }

  $films['corporate'] = $corporate;

  $sql = "SELECT documentaryfilms.id, competitors.fullName, title, director, translate FROM documentaryfilms LEFT JOIN documentary ON documentaryfilms.id_cat_user = documentary.id LEFT JOIN competitors ON documentary.user = competitors.id";
  $q = $db->prepare($sql);
  $q->execute();
  $documentary = [];
  while ($row = $q->fetch(PDO::FETCH_ASSOC)) {
    $sql = "SELECT id FROM evaluation_documentary WHERE film = :film AND jury = :jury";
    $y = $db->prepare($sql);
    $y->bindParam(':film', $row['id']);
    $y->bindParam(':jury', $jury);
    $y->execute();
    $count = $y->rowCount();
    if ($count > 0) {
      $row['evaluation'] = 'true';
    } else {
      $row['evaluation'] = 'false';
    }
    $documentary[] = $row;
  }

  $films['documentary'] = $documentary;

  $sql = "SELECT tourismfilms.id, competitors.fullName, title, director, translate FROM tourismfilms LEFT JOIN tourism ON tourismfilms.id_cat_user = tourism.id LEFT JOIN competitors ON tourism.user = competitors.id";
  $q = $db->prepare($sql);
  $q->execute();
  $tourism = [];
  while ($row = $q->fetch(PDO::FETCH_ASSOC)) {
    $sql = "SELECT id FROM evaluation_tourism WHERE film = :film AND jury = :jury";
    $y = $db->prepare($sql);
    $y->bindParam(':film', $row['id']);
    $y->bindParam(':jury', $jury);
    $y->execute();
    $count = $y->rowCount();
    if ($count > 0) {
      $row['evaluation'] = 'true';
    } else {
      $row['evaluation'] = 'false';
    }
    $tourism[] = $row;
  }

  $films['tourism'] = $tourism;

  $db = NULL;

  Flight::json($films);
});

///////
// List all touristic films
///////
Flight::route('GET /tourfilm/@id/@jury', function($id,$jury){
  $db = Flight::db();

  $film = [];

  $sql = "SELECT * FROM tourismfilms WHERE id = :id";
  $q = $db->prepare($sql);
  $q->bindParam(':id', $id);
  $q->execute();
  $film['film'] = $q->fetch(PDO::FETCH_ASSOC);

  $sql = "SELECT * FROM evaluation_tourism WHERE film = :id AND jury = :jury";
  $q = $db->prepare($sql);
  $q->bindParam(':id', $id);
  $q->bindParam(':jury', $jury);
  $q->execute();
  $film['evaluation'] = $q->fetch(PDO::FETCH_ASSOC);

  $db = NULL;

  Flight::json($film);
});

///////
// Save evaluation for a tourism film
///////
Flight::route('POST /tourfilm/@id/@jury', function($id,$jury){
  $db = Flight::db();
  $post = Flight::request()->data;

  $res = [];

  $sql = "SELECT id FROM evaluation_tourism WHERE film = :id AND jury = :jury";
  $q = $db->prepare($sql);
  $q->bindParam(':id', $id);
  $q->bindParam(':jury', $jury);
  $q->execute();
  $count = $q->rowCount();
  if ($count == 0) {
    $sql = "INSERT INTO evaluation_tourism(";
    $sql .= "jury, film, originalityscript, rythm, length, photography, sound, edition, specialeffects, iseffective, plot, convincing, ";
    $sql .= "attractive, place_viewer, place_stimulate, specific_sell, specific_clear, specific_provide, ";
    $sql .= "specific_focus, specific_promote, discuss, attention, awareness, comment) VALUES (";
    $sql .= ":val1,:val2,:val3,:val4,:val5,:val6,:val7,:val8,:val9,:val10,:val11,:val12,";
    $sql .= ":val13,:val14,:val15,:val16,:val17,:val18,";
    $sql .= ":val19,:val20,:val21,:val22,:val23,:val24)";
    $q = $db->prepare($sql);
    $q->bindParam(':val1', $jury);
    $q->bindParam(':val2', $id);
    $q->bindValue(':val3', $post['originalityscript'], PDO::PARAM_INT);
    $q->bindValue(':val4', $post['rythm'], PDO::PARAM_INT);
    $q->bindValue(':val5', $post['length'], PDO::PARAM_INT);
    $q->bindValue(':val6', $post['photography'], PDO::PARAM_INT);
    $q->bindValue(':val7', $post['sound'], PDO::PARAM_INT);
    $q->bindValue(':val8', $post['edition'], PDO::PARAM_INT);
    $q->bindValue(':val9', $post['specialeffects'], PDO::PARAM_INT);
    $q->bindValue(':val10', $post['iseffective'], PDO::PARAM_INT);
    $q->bindValue(':val11', $post['plot'], PDO::PARAM_INT);
    $q->bindValue(':val12', $post['convincing'], PDO::PARAM_INT);
    $q->bindValue(':val13', $post['attractive'], PDO::PARAM_INT);
    $q->bindValue(':val14', $post['place_viewer'], PDO::PARAM_INT);
    $q->bindValue(':val15', $post['place_stimulate'], PDO::PARAM_INT);
    $q->bindValue(':val16', $post['specific_sell'], PDO::PARAM_INT);
    $q->bindValue(':val17', $post['specific_clear'], PDO::PARAM_INT);
    $q->bindValue(':val18', $post['specific_provide'], PDO::PARAM_INT);
    $q->bindValue(':val19', $post['specific_focus'], PDO::PARAM_INT);
    $q->bindValue(':val20', $post['specific_promote'], PDO::PARAM_INT);
    $q->bindValue(':val21', $post['discuss'], PDO::PARAM_INT);
    $q->bindValue(':val22', $post['attention'], PDO::PARAM_INT);
    $q->bindValue(':val23', $post['awareness'], PDO::PARAM_INT);
    $q->bindValue(':val24', $post['comment'], PDO::PARAM_STR);
    try {
      $q->execute();
      $res['message'] = "Evaluation succesfully saved";
      $header = 200;
    } catch (Exception $e) {
      $res['message'] = "There are an error $e, please try later";
      $header = 404;
    }
  } else {
    $sql = "UPDATE evaluation_tourism SET";
    $sql .= " originalityscript=:val3, rythm=:val4, length=:val5, photography=:val6, sound=:val7, edition=:val8, specialeffects=:val9, ";
    $sql .= "iseffective=:val10, plot=:val11, convincing=:val12, attractive=:val13, place_viewer=:val14, place_stimulate=:val15, ";
    $sql .= "specific_sell=:val16, specific_clear=:val17, specific_provide=:val18, specific_focus=:val19, specific_promote=:val20, ";
    $sql .= "discuss=:val21, attention=:val22, awareness=:val23, comment=:val24 WHERE film = :id AND jury = :jury";
    $q = $db->prepare($sql);
    $q->bindParam(':id', $id);
    $q->bindParam(':jury', $jury);
    $q->bindValue(':val3', $post['originalityscript'], PDO::PARAM_INT);
    $q->bindValue(':val4', $post['rythm'], PDO::PARAM_INT);
    $q->bindValue(':val5', $post['length'], PDO::PARAM_INT);
    $q->bindValue(':val6', $post['photography'], PDO::PARAM_INT);
    $q->bindValue(':val7', $post['sound'], PDO::PARAM_INT);
    $q->bindValue(':val8', $post['edition'], PDO::PARAM_INT);
    $q->bindValue(':val9', $post['specialeffects'], PDO::PARAM_INT);
    $q->bindValue(':val10', $post['iseffective'], PDO::PARAM_INT);
    $q->bindValue(':val11', $post['plot'], PDO::PARAM_INT);
    $q->bindValue(':val12', $post['convincing'], PDO::PARAM_INT);
    $q->bindValue(':val13', $post['attractive'], PDO::PARAM_INT);
    $q->bindValue(':val14', $post['place_viewer'], PDO::PARAM_INT);
    $q->bindValue(':val15', $post['place_stimulate'], PDO::PARAM_INT);
    $q->bindValue(':val16', $post['specific_sell'], PDO::PARAM_INT);
    $q->bindValue(':val17', $post['specific_clear'], PDO::PARAM_INT);
    $q->bindValue(':val18', $post['specific_provide'], PDO::PARAM_INT);
    $q->bindValue(':val19', $post['specific_focus'], PDO::PARAM_INT);
    $q->bindValue(':val20', $post['specific_promote'], PDO::PARAM_INT);
    $q->bindValue(':val21', $post['discuss'], PDO::PARAM_INT);
    $q->bindValue(':val22', $post['attention'], PDO::PARAM_INT);
    $q->bindValue(':val23', $post['awareness'], PDO::PARAM_INT);
    $q->bindValue(':val24', $post['comment'], PDO::PARAM_STR);
    try {
      $q->execute();
      $res['message'] = "Evaluation succesfully updated";
      $header = 200;
    } catch (Exception $e) {
      $res['message'] = "There are an error $e, please try later";
      $header = 404;
    }
  }
  Flight::json($res,$header);
});

///////
// List all corporate films
///////
Flight::route('GET /corpfilm/@id/@jury', function($id,$jury){
  $db = Flight::db();

  $film = [];

  $sql = "SELECT * FROM corporatefilms WHERE id = :id";
  $q = $db->prepare($sql);
  $q->bindParam(':id', $id);
  $q->execute();
  $film['film'] = $q->fetch(PDO::FETCH_ASSOC);

  $sql = "SELECT * FROM evaluation_corporate WHERE film = :id AND jury = :jury";
  $q = $db->prepare($sql);
  $q->bindParam(':id', $id);
  $q->bindParam(':jury', $jury);
  $q->execute();
  $film['evaluation'] = $q->fetch(PDO::FETCH_ASSOC);

  $db = NULL;

  Flight::json($film);
});

///////
// Save evaluation for a corporate film
///////
Flight::route('POST /corpfilm/@id/@jury', function($id,$jury){
  $db = Flight::db();
  $post = Flight::request()->data;

  $res = [];

  $sql = "SELECT id FROM evaluation_corporate WHERE film = :id AND jury = :jury";
  $q = $db->prepare($sql);
  $q->bindParam(':id', $id);
  $q->bindParam(':jury', $jury);
  $q->execute();
  $count = $q->rowCount();
  if ($count == 0) {
    $sql = "INSERT INTO evaluation_corporate(";
    $sql .= "jury, film, originalityscript, rythm, length, photography, sound, edition, specialeffects, iseffective, plot, convincing, ";
    $sql .= "attractive, place_viewer, place_stimulate, specific_green, specific_csr, specific_provide, ";
    $sql .= "specific_portray, discuss, attention, awareness, comment) VALUES (";
    $sql .= ":val1,:val2,:val3,:val4,:val5,:val6,:val7,:val8,:val9,:val10,:val11,:val12,";
    $sql .= ":val13,:val14,:val15,:val16,:val17,:val18,";
    $sql .= ":val19,:val20,:val21,:val22,:val23)";
    $q = $db->prepare($sql);
    $q->bindParam(':val1', $jury);
    $q->bindParam(':val2', $id);
    $q->bindValue(':val3', $post['originalityscript'], PDO::PARAM_INT);
    $q->bindValue(':val4', $post['rythm'], PDO::PARAM_INT);
    $q->bindValue(':val5', $post['length'], PDO::PARAM_INT);
    $q->bindValue(':val6', $post['photography'], PDO::PARAM_INT);
    $q->bindValue(':val7', $post['sound'], PDO::PARAM_INT);
    $q->bindValue(':val8', $post['edition'], PDO::PARAM_INT);
    $q->bindValue(':val9', $post['specialeffects'], PDO::PARAM_INT);
    $q->bindValue(':val10', $post['iseffective'], PDO::PARAM_INT);
    $q->bindValue(':val11', $post['plot'], PDO::PARAM_INT);
    $q->bindValue(':val12', $post['convincing'], PDO::PARAM_INT);
    $q->bindValue(':val13', $post['attractive'], PDO::PARAM_INT);
    $q->bindValue(':val14', $post['place_viewer'], PDO::PARAM_INT);
    $q->bindValue(':val15', $post['place_stimulate'], PDO::PARAM_INT);
    $q->bindValue(':val16', $post['specific_green'], PDO::PARAM_INT);
    $q->bindValue(':val17', $post['specific_csr'], PDO::PARAM_INT);
    $q->bindValue(':val18', $post['specific_provide'], PDO::PARAM_INT);
    $q->bindValue(':val19', $post['specific_portray'], PDO::PARAM_INT);
    $q->bindValue(':val20', $post['discuss'], PDO::PARAM_INT);
    $q->bindValue(':val21', $post['attention'], PDO::PARAM_INT);
    $q->bindValue(':val22', $post['awareness'], PDO::PARAM_INT);
    $q->bindValue(':val23', $post['comment'], PDO::PARAM_STR);
    try {
      $q->execute();
      $res['message'] = "Evaluation succesfully saved";
      $header = 200;
    } catch (Exception $e) {
      $res['message'] = "There are an error $e, please try later";
      $header = 404;
    }
  } else {
    $sql = "UPDATE evaluation_corporate SET";
    $sql .= " originalityscript=:val3, rythm=:val4, length=:val5, photography=:val6, sound=:val7, edition=:val8, specialeffects=:val9, ";
    $sql .= "iseffective=:val10, plot=:val11, convincing=:val12, attractive=:val13, place_viewer=:val14, place_stimulate=:val15, ";
    $sql .= "specific_green=:val16, specific_csr=:val17, specific_provide=:val18, specific_portray=:val19, ";
    $sql .= "discuss=:val20, attention=:val21, awareness=:val22, comment=:val23 WHERE film = :id AND jury = :jury";
    $q = $db->prepare($sql);
    $q->bindParam(':id', $id);
    $q->bindParam(':jury', $jury);
    $q->bindValue(':val3', $post['originalityscript'], PDO::PARAM_INT);
    $q->bindValue(':val4', $post['rythm'], PDO::PARAM_INT);
    $q->bindValue(':val5', $post['length'], PDO::PARAM_INT);
    $q->bindValue(':val6', $post['photography'], PDO::PARAM_INT);
    $q->bindValue(':val7', $post['sound'], PDO::PARAM_INT);
    $q->bindValue(':val8', $post['edition'], PDO::PARAM_INT);
    $q->bindValue(':val9', $post['specialeffects'], PDO::PARAM_INT);
    $q->bindValue(':val10', $post['iseffective'], PDO::PARAM_INT);
    $q->bindValue(':val11', $post['plot'], PDO::PARAM_INT);
    $q->bindValue(':val12', $post['convincing'], PDO::PARAM_INT);
    $q->bindValue(':val13', $post['attractive'], PDO::PARAM_INT);
    $q->bindValue(':val14', $post['place_viewer'], PDO::PARAM_INT);
    $q->bindValue(':val15', $post['place_stimulate'], PDO::PARAM_INT);
    $q->bindValue(':val16', $post['specific_green'], PDO::PARAM_INT);
    $q->bindValue(':val17', $post['specific_csr'], PDO::PARAM_INT);
    $q->bindValue(':val18', $post['specific_provide'], PDO::PARAM_INT);
    $q->bindValue(':val19', $post['specific_portray'], PDO::PARAM_INT);
    $q->bindValue(':val20', $post['discuss'], PDO::PARAM_INT);
    $q->bindValue(':val21', $post['attention'], PDO::PARAM_INT);
    $q->bindValue(':val22', $post['awareness'], PDO::PARAM_INT);
    $q->bindValue(':val23', $post['comment'], PDO::PARAM_STR);
    try {
      $q->execute();
      $res['message'] = "Evaluation succesfully updated";
      $header = 200;
    } catch (Exception $e) {
      $res['message'] = "There are an error $e, please try later";
      $header = 404;
    }
  }
  Flight::json($res,$header);
});

///////
// List all documentary films
///////
Flight::route('GET /docfilm/@id/@jury', function($id,$jury){
  $db = Flight::db();

  $film = [];

  $sql = "SELECT * FROM documentaryfilms WHERE id = :id";
  $q = $db->prepare($sql);
  $q->bindParam(':id', $id);
  $q->execute();
  $film['film'] = $q->fetch(PDO::FETCH_ASSOC);

  $sql = "SELECT * FROM evaluation_documentary WHERE film = :id AND jury = :jury";
  $q = $db->prepare($sql);
  $q->bindParam(':id', $id);
  $q->bindParam(':jury', $jury);
  $q->execute();
  $film['evaluation'] = $q->fetch(PDO::FETCH_ASSOC);

  $db = NULL;

  Flight::json($film);
});

///////
// Save evaluation for a corporate film
///////
Flight::route('POST /docfilm/@id/@jury', function($id,$jury){
  $db = Flight::db();
  $post = Flight::request()->data;

  $res = [];
  $header = 500;

  $sql = "SELECT id FROM evaluation_documentary WHERE film = :id AND jury = :jury";
  $q = $db->prepare($sql);
  $q->bindParam(':id', $id);
  $q->bindParam(':jury', $jury);
  $q->execute();
  $count = $q->rowCount();
  if ($count == 0) {
    $sql = "INSERT INTO evaluation_documentary(";
    $sql .= "jury, film, originalityscript, rythm, length, photography, sound, edition, specialeffects, iseffective, plot, convincing, ";
    $sql .= "attractive, place_viewer, place_stimulate, specific_travel, specific_sustain, specific_narrative, ";
    $sql .= "specific_focus, specific_reflection, specific_suggest, discuss, attention, awareness, comment) VALUES (";
    $sql .= ":val1,:val2,:val3,:val4,:val5,:val6,:val7,:val8,:val9,:val10,:val11,:val12,";
    $sql .= ":val13,:val14,:val15,:val16,:val17,:val18,";
    $sql .= ":val19,:val20,:val21,:val22,:val23,:val24,:val25)";
    $q = $db->prepare($sql);
    $q->bindParam(':val1', $jury);
    $q->bindParam(':val2', $id);
    $q->bindValue(':val3', $post['originalityscript'], PDO::PARAM_INT);
    $q->bindValue(':val4', $post['rythm'], PDO::PARAM_INT);
    $q->bindValue(':val5', $post['length'], PDO::PARAM_INT);
    $q->bindValue(':val6', $post['photography'], PDO::PARAM_INT);
    $q->bindValue(':val7', $post['sound'], PDO::PARAM_INT);
    $q->bindValue(':val8', $post['edition'], PDO::PARAM_INT);
    $q->bindValue(':val9', $post['specialeffects'], PDO::PARAM_INT);
    $q->bindValue(':val10', $post['iseffective'], PDO::PARAM_INT);
    $q->bindValue(':val11', $post['plot'], PDO::PARAM_INT);
    $q->bindValue(':val12', $post['convincing'], PDO::PARAM_INT);
    $q->bindValue(':val13', $post['attractive'], PDO::PARAM_INT);
    $q->bindValue(':val14', $post['place_viewer'], PDO::PARAM_INT);
    $q->bindValue(':val15', $post['place_stimulate'], PDO::PARAM_INT);
    $q->bindValue(':val16', $post['specific_travel'], PDO::PARAM_INT);
    $q->bindValue(':val17', $post['specific_sustain'], PDO::PARAM_INT);
    $q->bindValue(':val18', $post['specific_narrative'], PDO::PARAM_INT);
    $q->bindValue(':val19', $post['specific_focus'], PDO::PARAM_INT);
    $q->bindValue(':val20', $post['specific_reflection'], PDO::PARAM_INT);
    $q->bindValue(':val21', $post['specific_suggest'], PDO::PARAM_INT);
    $q->bindValue(':val22', $post['discuss'], PDO::PARAM_INT);
    $q->bindValue(':val23', $post['attention'], PDO::PARAM_INT);
    $q->bindValue(':val24', $post['awareness'], PDO::PARAM_INT);
    $q->bindValue(':val25', $post['comment'], PDO::PARAM_STR);
    try {
      $q->execute();
      $res['message'] = "Evaluation succesfully saved";
      $header = 200;
    } catch (Exception $e) {
      $res['message'] = "There are an error $e, please try later";
      $header = 404;
    }
  } else {
    $sql = "UPDATE evaluation_documentary SET";
    $sql .= " originalityscript=:val3, rythm=:val4, length=:val5, photography=:val6, sound=:val7, edition=:val8, specialeffects=:val9, ";
    $sql .= "iseffective=:val10, plot=:val11, convincing=:val12, attractive=:val13, place_viewer=:val14, place_stimulate=:val15, ";
    $sql .= "specific_travel=:val16, specific_sustain=:val17, specific_narrative=:val18, specific_focus=:val19, ";
    $sql .= "specific_reflection=:val20, specific_suggest=:val21, discuss=:val22, attention=:val23, awareness=:val24, comment=:val25 WHERE film = :id AND jury = :jury";
    $q = $db->prepare($sql);
    $q->bindParam(':id', $id);
    $q->bindParam(':jury', $jury);
    $q->bindValue(':val3', $post['originalityscript'], PDO::PARAM_INT);
    $q->bindValue(':val4', $post['rythm'], PDO::PARAM_INT);
    $q->bindValue(':val5', $post['length'], PDO::PARAM_INT);
    $q->bindValue(':val6', $post['photography'], PDO::PARAM_INT);
    $q->bindValue(':val7', $post['sound'], PDO::PARAM_INT);
    $q->bindValue(':val8', $post['edition'], PDO::PARAM_INT);
    $q->bindValue(':val9', $post['specialeffects'], PDO::PARAM_INT);
    $q->bindValue(':val10', $post['iseffective'], PDO::PARAM_INT);
    $q->bindValue(':val11', $post['plot'], PDO::PARAM_INT);
    $q->bindValue(':val12', $post['convincing'], PDO::PARAM_INT);
    $q->bindValue(':val13', $post['attractive'], PDO::PARAM_INT);
    $q->bindValue(':val14', $post['place_viewer'], PDO::PARAM_INT);
    $q->bindValue(':val15', $post['place_stimulate'], PDO::PARAM_INT);
    $q->bindValue(':val16', $post['specific_travel'], PDO::PARAM_INT);
    $q->bindValue(':val17', $post['specific_sustain'], PDO::PARAM_INT);
    $q->bindValue(':val18', $post['specific_narrative'], PDO::PARAM_INT);
    $q->bindValue(':val19', $post['specific_focus'], PDO::PARAM_INT);
    $q->bindValue(':val20', $post['specific_reflection'], PDO::PARAM_INT);
    $q->bindValue(':val21', $post['specific_suggest'], PDO::PARAM_INT);
    $q->bindValue(':val22', $post['discuss'], PDO::PARAM_INT);
    $q->bindValue(':val23', $post['attention'], PDO::PARAM_INT);
    $q->bindValue(':val24', $post['awareness'], PDO::PARAM_INT);
    $q->bindValue(':val25', $post['comment'], PDO::PARAM_STR);
    try {
      $q->execute();
      $res['message'] = "Evaluation succesfully updated";
      $header = 200;
    } catch (Exception $e) {
      $res['message'] = "There are an error $e, please try later";
      $header = 404;
    }
  }
  Flight::json($res,$header);
});

Flight::start();
