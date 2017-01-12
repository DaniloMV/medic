<?php

//chmod('.htaccess', 0777);
//recursiveChmod(".");
//recursiveChmod("app/code/local/Aitoc");
recursiveChmod("var");

	function recursiveChmod($path, $filePerm=0777, $dirPerm=0777)
		{
			if(!file_exists($path))
		{
			return(FALSE);
		}
		if(is_file($path))
		{
			chmod($path, $filePerm);
		} elseif(is_dir($path)) {
			$foldersAndFiles = scandir($path);
			$entries = array_slice($foldersAndFiles, 2);
			foreach($entries as $entry)
			{
				recursiveChmod($path."/".$entry, $filePerm, $dirPerm);
			}
			chmod($path, $dirPerm);
		}
		return(TRUE);
	}