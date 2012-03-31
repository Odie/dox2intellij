<?php
/*
 * An SDK Builder for the free Lua IntelliJ Plugin
 * Copyright (C) 2012 by Andrey Gayvoronsky
 * Version: 1.0
 * Email: plandem at gmail dot com
 *
 * Lua plugin official site:
 * http://bitbucket.org/sylvanaar2/lua-for-idea/
 *
 * IntelliJ official site:
 * http://www.jetbrains.com/idea/download/index.html
 *
 */
class SDKBuilder
{
	static $OUTPUT = null;
	static $SDK_REMOTE_URL = null;
	static $SDK_TITLE = null;

	static function build(&$index)
	{
		if (self::$OUTPUT === null || self::$SDK_REMOTE_URL === null || self::$SDK_TITLE === null)
			throw new Exception("Wrong settings for builder.");

		//build SDK.lua
		$sdk = fopen(self::$OUTPUT . '.lua', 'w');

		//save SDK doc header
		$doc_file = self::$OUTPUT . '.doclua';
		$doc_header = file_get_contents('sdk.doclua.proto');
		$doc_header = str_replace('<<<SDK_REMOTE_URL>>>', self::$SDK_REMOTE_URL, $doc_header);
		$doc_header = str_replace("<<<SDK_TITLE>>>", self::$SDK_TITLE, $doc_header);
		file_put_contents($doc_file, $doc_header);

		//save signatures now
		$doc = fopen($doc_file, 'a');
		if (!$sdk || !$doc)
			throw new Exception("Can't open file for writing...");

		fputs($doc, "\n\nSIGNATURES = {\n\n");

		//save rest information
		foreach ($index as $class_name => $class_info) {
			fputs($sdk, "-- {$class_name}\n");
			fputs($sdk, "{$class_name} = { }\n");

			fputs($doc, "{$class_name} = [=[{$class_info['hint']}]=],\n");

			if (isset($class_info['property'])) {
				foreach ($class_info['property'] as $property_name => $property_hint) {
					fputs($sdk, "{$property_name} = true\n");
					if (strlen($property_hint))
						fputs($doc, "[\"{$property_name}\"] = [=[{$property_hint}]=],\n");
				}
			}

			if (isset($class_info['member'])) {
				fputs($sdk, "\n");
				foreach ($class_info['member'] as $member_name => $member_info) {
					fputs($sdk, "function {$class_name}.{$member_name}( ) end\n");
					fputs($doc, "[\"{$class_name}.{$member_name}\"] = {[=[{$member_info['hint']}]=], [=[{$member_info['url']}]=]},\n");
				}
			}

			fputs($sdk, "\n");
			fputs($doc, "\n");
		} //foreach(index)

		fclose($sdk);
		fputs($doc, "}\n");
		fclose($doc);
	}
}
