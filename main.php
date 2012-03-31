<?php
require_once("phpQuery/phpQuery.php");
require_once("sdk_builder.php");
require_once("sdk_parser.php");

$sdk_url = "http://getmoai.com/docs/";

//setup SDK parser
SDKParser::$CACHE = './cache/';
SDKParser::$SDK_URL = $sdk_url;

//setup SDK builder
SDKBuilder::$OUTPUT = '../moai';
SDKBuilder::$SDK_TITLE = "MOAI SDK";
SDKBuilder::$SDK_REMOTE_URL = $sdk_url;

$data = SDKParser::parse();
SDKBuilder::build($data);