<?php
include('database.php');
$msg="";
if(isset($_POST['name']) && isset($_POST['email']) && isset($_POST['mobile']) && isset($_POST['message'])){
	$name=mysqli_real_escape_string($con,$_POST['name']);
	$subject=mysqli_real_escape_string($con,$_POST['subject']);
	$email=mysqli_real_escape_string($con,$_POST['email']);
	$mobile=mysqli_real_escape_string($con,$_POST['mobile']);
	$comment=mysqli_real_escape_string($con,$_POST['message']);
	
	mysqli_query($con,"insert into contact_us(name,subject,email,mobile,message) values('?','?',?','?','?')");
	$msg="Thanks ! Message Received, We will get intouch soon";
	
	$html="<table><tr><td>Name : </td><td>$name</td></tr><tr><td>Subject : </td><td>$subject</td></tr><tr><td>Email : </td><td>$email</td></tr><tr><td>Mobile : </td><td>$mobile</td></tr><tr><td>Comment : </td><td>$comment</td></tr></table>";
	
	include('smtp/PHPMailerAutoload.php');
	$mail=new PHPMailer(true);
	$mail->Host="mail.psamalawi.org";
	$mail->Port=465;
	$mail->SMTPSecure="tls";
	$mail->SMTPAuth=true;
	$mail->Username="admin@psamalawi.org";
	$mail->Password="heroic.19091@psa";
	$mail->SetFrom("admin@psamalawi.org");
	$mail->addAddress("admin@psamalawi.org");
	$mail->IsHTML(true);
	$mail->Subject='' .$_POST ['subject'];
	$mail->Body=$html;
	$mail->SMTPOptions=array('ssl'=>array(
		'verify_peer'=>false,
		'verify_peer_name'=>false,
		'allow_self_signed'=>false
	));
	if($mail->send()){
		//echo "Mail sent";
	}else{
		//echo "Error occur";
	}
	echo $msg;
}
?>