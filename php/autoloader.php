<?php
include "../config.php";

	// access php session
	session_start();

	// site global defines

	// include config (creds and things we keep out of www and repo)
	//include_once __DIR__ . ( PHP_OS == 'Linux' ? '' : '/' ) . 'config.php';

	// include global functions
	//include_once __DIR__  . '/php/functions.php';

	// include facebook api functions
	//include_once __DIR__  . '/php/facebook_api.php';

	// include twitter api functions
	//include_once __DIR__  . '/php/twitter_api.php';

	// include twitch api functions
	include_once __DIR__ . '/twitch_api.php';
?>