<?php
/*
This is a custom mailer written by Greg Davis

It's got a couple layers of protection after the UI and forwards to a page on success or error.
*/


//custom mailer function - has some specs that need to be set by the website administrator/server administrator
//this can be used, if there are problems with the default functionality used below
function SendMail($ToName, $ToEmail, $FromName, $FromEmail, $Subject, $Body, $Header) {
$SMTP = fsockopen("smtp.sitename.com", 25);//will need to be customized

$InputBuffer = fgets($SMTP, 1024);

fputs($SMTP, "HELO sitename.com\n");//will need to customized
$InputBuffer = fgets($SMTP, 1024);
fputs($SMTP, "MAIL From: $FromEmail\n");
$InputBuffer = fgets($SMTP, 1024);
fputs($SMTP, "RCPT To: $ToEmail\n");
$InputBuffer = fgets($SMTP, 1024);
fputs($SMTP, "DATA\n");
$InputBuffer = fgets($SMTP, 1024);
fputs($SMTP, "$Header");
fputs($SMTP, "From: $FromName <$FromEmail>\n");
fputs($SMTP, "To: $ToName <$ToEmail>\n");
fputs($SMTP, "Subject: $Subject\n\n");
fputs($SMTP, "$Body\r\n.\r\n");
fputs($SMTP, "QUIT\n");
$InputBuffer = fgets($SMTP, 1024);

fclose($SMTP);
}

//this function keeps the email sanitized and prevents an injection of multiple email addresses
function spamcheck($field)
  {
  //filter_var() sanitizes the e-mail
  //address using FILTER_SANITIZE_EMAIL
  $field=filter_var($field, FILTER_SANITIZE_EMAIL);

  //filter_var() validates the e-mail
  //address using FILTER_VALIDATE_EMAIL
  if(filter_var($field, FILTER_VALIDATE_EMAIL))
    {
    return TRUE;
    }
  else
    {
    return FALSE;
    }
  }

//bring data over from the form
$email = $_REQUEST['Email'];
$subject = "Your subject here";
//make sure to include a text field called "noRobot" in your form and set it's style to display: none
//this is a substitute for captcha text
$noRobot = $_REQUEST['noRobot'];
//the following headers are included in order to send a nicely formatted html message
$headers = "From: no-reply@yourdomain.com\r\n";
$headers.="Return-Path: no-reply@yourdomain.com\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

//the body of the email is declared as a long string
$mailString = <<<EOD
	<html>
		<body>
			Email content goes here!
		</body>
	</html>
EOD;

if (($noRobot == null) && (spamcheck($email) == true)) {
	//echo ($mailString);//display string for proofing
	if(mail($email, $subject, $mailString, $headers)) {
		header( 'Location: http://www.yoursite.com/success' );
	}
	else {
		echo("There was an internal server error, please go back and try again.");
	}
}
else {
	//error page if something doesn't work
	//This should never occur if the form is functioning, all errors will be prevented by the UI
	echo("There was an internal server error, please go back and try again.");
}

?>