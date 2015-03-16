<?php
require_once('./wp-content/plugins/sharefile-for-gf/sharefile.class.php');
if( class_exists('ShareFile') ) :
	$sf	= new ShareFile( 
		'rochesteroptical.sharefile.com', 
		'CNWSnHFGRIc45FcV4DSkmdObuKxEf57h', 
		'9UAG9lTd78NGCgBPgQ0iHFZUhwRO7BZm1RjsV2N0c02Gu1QF', 
		'tbelknap@holisticnetwork.net', 
		'1Udx2vN0m5' );
	// $sf	= new ShareFile;
else :
	echo 'Boo!';
endif;
?>