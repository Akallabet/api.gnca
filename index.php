<?php

require_once("Slim/Slim.php");
\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim();

session_start();

$app->post('/login', function() use($app){
    $req= json_decode($app->request()->getBody());
    $username= $req->username;
    $password= $req->password;
	require_once("./models/users.php");
    require_once("./models/colletta.php");
	$obj= new Users();
	$colletta= new Colletta();

    $ret= $obj->login($username, sha1($password));
	if(count($ret)==1)
	{
	    $_SESSION['timestamp']= time();
        $_SESSION['id_user']= $ret[0]->id;
	    $_SESSION['user']= array(
           'username'=>$ret[0]->username,
           'api_key'=>$ret[0]->api_key,
           'privilegi'=>$ret[0]->privilegi,
           'ruolo'=>$ret[0]->ruolo,
           'nome'=>$ret[0]->nome,
           'cognome'=>$ret[0]->cognome,
           'email'=>$ret[0]->email,
           'id_area'=>$ret[0]->id_area,
           'telefono'=>$ret[0]->telefono,
           'colletta'=>$colletta->getActive()[0]
        );
        $token=sha1($ret[0]->api_key.$_SESSION['timestamp']."-");
		echo json_encode(array('error'=>false,'token'=>$token));
	}
	else echo json_encode(array('error'=>true));
});

$app->get('/logout', function(){
    session_unset();
	session_destroy();
    echo json_encode(array('error'=>false));
});

$app->get('/:token/get/user', function ($token) {
    $ret= array();
    $error= false;
    $actions= array();
    if(checkPermissions($token))
        $ret= $_SESSION['user'];
    else $error= true;
    echo json_encode(array('user'=>$ret,'error'=>$error));
});

function doAction($token, $method, $property, $l_start, $l_end, $values)
{
    $ret= array();
    if(isset($_SESSION['user']))
    {
        $obj= null;
        switch ($property) {
            case 'carichi':
                require_once("./models/carichi.php");
                if(checkPermissions($token,4))
                    $obj= new Carichi();
                break;
            case 'prodotti':
                require_once("./models/prodotti.php");
                if($method=='insert')
                {
                    foreach ($values->values as $key => $value) {
                        $values->values[$key]->id_user= $_SESSION['id_user'];
                    }
                }
                if(checkPermissions($token,4))
                    $obj= new Prodotti();
                break;
            case 'prodotti_tipi':
                require_once("./models/prodotti_tipi.php");
                if(checkPermissions($token,4))
                    $obj= new ProdottiTipi();
                break;
            case 'supermercati':
                require_once("./models/supermercati.php");

                if($method=='get')
                {
                    if($_SESSION['user']['privilegi']>1)
                    {
                        $values->id_area= $_SESSION['user']['id_area'];
                    }
                }
                else if($method=='update')
                {
                    if($_SESSION['user']['privilegi']>1)
                    {
                        foreach ($values->values as $key => $value) {
                            $values->values[$key]->id_area= $_SESSION['user']['id_area'];
                        }
                    }
                    // deleteCache($_SESSION['id_user']);
                    deleteAllCache();
                }
                else if($method=='insert')
                {
                    $params= array("id_colletta"=>$values->values[0]->id_colletta);
                    $sup= new Supermercati();

                    $ret= call_user_func_array(array($sup, "maxIdSupermercato"), array('id_supermercato',$params, $l_start, $l_end));

                    $i=1;
                    foreach ($values->values as $key => $value) {
                        $values->values[$key]->id= 'NULL';
                        $values->values[$key]->id_supermercato= $ret[0]->max+$i;
                        $values->values[$key]->id_area= $_SESSION['user']['id_area'];
                        $i++;
                    }

                    // deleteCache($_SESSION['id_user']);
                    deleteAllCache();
                }
                if(checkPermissions($token,4))
                    $obj= new Supermercati();
                break;
            case 'report':
                require_once("./models/report.php");

                if($method=='get')
                {
                    if($_SESSION['user']['privilegi']>1)
                    {
                        $values->id_area= $_SESSION['user']['id_area'];
                    }
                }
                if(checkPermissions($token,4))
                    $obj= new Report();
                break;
            case 'comuni':
                require_once("./models/comuni.php");

                if($method=='get')
                {
                    if($_SESSION['user']['privilegi']>1)
                    {
                        $values->id_area= $_SESSION['user']['id_area'];
                    }
                }
                if(checkPermissions($token,4))
                    $obj= new Comuni();
                break;
            case 'catene':
                require_once("./models/catene.php");
                if(checkPermissions($token,4))
                    $obj= new Catene();
                break;
            case 'capi_equipe':
                require_once("./models/capi_equipe.php");
                if(checkPermissions($token,4))
                    $obj= new CapiEquipe();
                if($method=='update' || $method=='insert')
                {
                    deleteCache($_SESSION['id_user']);
                }
                break;
            case 'capi_equipe_supermercati':
                require_once("./models/capi_equipe_supermercati.php");
                if(checkPermissions($token,4))
                    $obj= new CapiEquipeSupermercati();
                break;
            case 'aree':
                require_once("./models/aree.php");
                if($_SESSION['user']['privilegi']==1)
                {
                    $obj= new Aree();
                    if($method=='update')
                    {
                        deleteCache($_SESSION['id_user']);
                    }
                }
                break;
            case 'colletta':
                require_once("./models/colletta.php");
                if(checkPermissions($token,4))
                    $obj= new Colletta();
                if($method=='update')
                {
                    if($_SESSION['user']['privilegi']==1)
                    {
                        deleteCache($_SESSION['id_user']);
                    }
                }
                break;
            default:

                break;
        }
        $mtime= ((3600)*1)*1000;

        if($obj instanceof Model)
        {
            //Cache control
            if($method=='get' &&
                ($property=='supermercati' ||
                 $property=='comuni' ||
                 $property=='provincie' ||
                 $property=='catene' ||
                 $property=='aree' ||
                 $property=='regioni')
            )
            {
                $strin=stringify($values);
                $filename= "resources/cache/{$_SESSION['id_user']}/".md5("{$token}{$property}{$strin}{$l_start}{$l_end}").".js";

                if (file_exists($filename)) {
                    $ret= json_decode(file_get_contents($filename));
                } else {
                    if ($property =='supermercati' && property_exists($values, 'id_area')) $method = 'getByIdArea';
                    $ret= call_user_func_array(array($obj, $method), array($values, $l_start, $l_end));
                }
                $fp = fopen($filename, 'w');
                fwrite($fp, json_encode($ret, true));

                fclose($fp);
            }
            else {
              $ret= call_user_func_array(array($obj, $method), array($values, $l_start, $l_end));
            }
        }
        else $ret= array('error'=>'Non hai i permessi disponibili per questa azione!');

        echo json_encode(array($property=>$ret));
    }
    else
    {
        echo json_encode(array('error'=>true));
    }
}

//Get with limits
$app->post('/:token/get/:property/:limit_start/:limit_end', function($token, $property, $l_start, $l_end) use($app){
    $req= json_decode($app->request()->getBody());
    doAction($token, 'get', $property, $l_start, $l_end, $$req);
});

$app->post('/:token/get/:property', function($token, $property) use($app){
    $req= json_decode($app->request()->getBody());
    doAction($token, 'get', $property, null, null, $req);
});

$app->post('/:token/set/:property', function($token, $property) use($app){
    $req= json_decode($app->request()->getBody());
    doAction($token, 'update', $property, null, null, $req);
});

$app->post('/:token/delete/:property', function($token, $property) use($app){
    $req= json_decode($app->request()->getBody());
    doAction($token, 'delete', $property, null, null, $req);
});

$app->post('/:token/save/:property', function($token, $property) use($app){
    $req= json_decode($app->request()->getBody());
    doAction($token, 'insert', $property, null, null, $req);
});

$app->post('/:token/upload/supermercati/:id_colletta', function($token, $id_colletta) use($app){
    $req= json_decode($app->request()->getBody());
    //doAction($token, 'insert', $property, null, null, $req);
});

$app->get('/:token/info/update/:year', function($token, $year) use($app){
    $req= json_decode($app->request()->getBody());
    $res= false;

    if(checkPermissions($token,1))
    {
        $res=updateFiles($year);
        deleteCache($_SESSION['id_user']);
    }
    echo json_encode(array("result"=>$res));
});

$app->get('/:token/cache/delete/:all', function($token, $all){
    $res= false;

    if($all==1)
    {
        if(checkPermissions($token,1))
        {
            deleteAllCache();
            $res=true;
        }
    }
    else
    {
        deleteCache($_SESSION['id_user']);
    }
    echo json_encode(array("result"=>$res));
});

function deleteAllCache()
{
    for ($i=1; $i < 23; $i++) {
        deleteCache($i);
    }
}

$app->get('/:token/files/:year', function($token, $year){
    echo json_encode(array("files"=>getUploadedFiles()));
});

$app->post('/:token/files/:year', function($token, $year){
    move_uploaded_file($_FILES[$year]["tmp_name"],"resources/uploaded/{$year}/".$_FILES[$year]["name"]);
    deleteCache($_SESSION['id_user']);
    echo json_encode(array("files"=>getUploadedFiles()));
});

$app->run();

function checkPermissions($token,$level=100)
{
	$ret= true;
	if(isset($_SESSION['user']) && sha1($_SESSION['user']['api_key'].$_SESSION['timestamp']."-")==$token)
    {
        if($level && $_SESSION['user']['privilegi']>$level) echo $ret= false;
        else $ret=true;
	}
	else $ret=false;
	return $ret;
}

function stringify($inJSON)
{
    $ret='';
    foreach ($inJSON as $key => $value) {
        $ret.= $key."=>".$value;
    }

    return $ret;
}

function deleteCache($id)
{
    $files = glob("resources/cache/{$id}/*"); // get all file names
    foreach($files as $file){ // iterate files
      if(is_file($file))
        unlink($file); // delete file
    }
}

function getUploadedFiles()
{
    $path= "resources/uploaded";
    $files= array("supermercati"=>array("checked"=>true),"catene"=>array("checked"=>true),"capi_equipe"=>array("checked"=>true));
    $dirs = array_filter(glob("resources/uploaded/*"), 'is_dir');
    $ret= array();

    foreach ($dirs as $key => $year) {
        $tmp=explode("/", $year);
        $ret[$tmp[count($tmp)-1]]= array();

        foreach ($files as $name=>$file) {
            $ret[$tmp[count($tmp)-1]][$name]= array();
            $ret[$tmp[count($tmp)-1]][$name]["checked"]= file_exists("{$year}/{$name}.csv");
        }
    }
    return $ret;
}

function getFileCSvContent($filename)
{
    $contents= new stdClass();
    $contents->values= array();
    if (file_exists($filename)) {
        $fp = fopen($filename, "r");
        $line=0;
        $columns= array();

        while (($data = fgetcsv($fp, 100000, "\r")) !== FALSE) {
            $num = count($data);
            for($i=0; $i<count($data);$i++)
            {
                if($i==0)
                {
                    $columns= explode(";",$data[$i]);
                }
                else
                {
                    $row= explode(";",$data[$i]);
                    //if(count($row)<6) print_r($row);
                    for($j=0;$j<count($columns);$j++)
                    {
                        $contents->values[$line][$columns[$j]]= '"'.utf8_encode($row[$j]).'"';
                    }
                    $line++;
                }
            }
        }
        fclose($fp);
    }
    //print_r($contents);
    return $contents;
}

function updateFiles($year)
{
    $ret= true;

    require_once("./models/comuni.php");
    require_once("./models/catene.php");
    require_once("./models/colletta.php");
    require_once("./models/supermercati.php");
    require_once("./models/capi_equipe.php");
    require_once("./models/capi_equipe_supermercati.php");

    //Database
    //$comuni= call_user_func_array(array(new Comuni(), 'get'), array(array()));
    //$catene= call_user_func_array(array(new Catene(), 'get'), array(array()));
    //$capi_equipe= call_user_func_array(array(new CapiEquipe(), 'get'), array(array()));
    $colletta= call_user_func_array(array(new Colletta(), 'get'), array(array("anno"=>$year)));

    //Files
    $catene= getFileCSvContent("resources/uploaded/{$year}/catene.csv");
    $capi_equipe= getFileCSvContent("resources/uploaded/{$year}/capi_equipe.csv");
    $supermercati= getFileCSvContent("resources/uploaded/{$year}/supermercati.csv");
    $capi_equipe_supermercati= new stdClass();
    $capi_equipe_supermercati->values= array();

    foreach ($supermercati->values as $key => $supermercato) {
        //$found= false;
        /*
        foreach ($comuni as $comune) {
            if('"'.$comune->nome.'"'==$supermercato["comune"])
            {
                $supermercato["id_comune"]= $comune->id;
                $found= true;
                break;
            }
        }
        if(!$found) $supermercato["id_comune"]= "NULL";

        $supermercato["id_diocesi"]= "NULL";
        $supermercato["id_area"]= 1;
        */
        $supermercato["id_colletta"]= '"'.$colletta[0]->id.'"';

        //Setting Capi_equipe_supermercati
        //$resp= is_numeric($supermercato["id_capo_equipe"]);
        if($supermercato["id_capo_equipe"]!='""')
            $capi_equipe_supermercati->values[]= array("id_capo_equipe"=>$supermercato["id_capo_equipe"], "id_supermercato"=>$supermercato["id"]);
        unset($supermercato["id_capo_equipe"]);
        $supermercati->values[$key]= $supermercato;
    }
    call_user_func_array(array(new Catene(), 'insertOrUpdate'), array($catene));
    call_user_func_array(array(new CapiEquipe(), 'insertOrUpdate'), array($capi_equipe));
    //print_r($supermercati);
    call_user_func_array(array(new Supermercati(), 'insertOrUpdate'), array($supermercati));
    call_user_func_array(array(new CapiEquipeSupermercati(), 'insert'), array($capi_equipe_supermercati));

    return $ret;
}
?>
