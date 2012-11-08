<?php

//////////////////////////////////////////////////////////////////////////
// Disk Usage
//
// Purpose: Calculates the used bytes in a directory and its files and subdirectories.
//
// Version: 1.0 (27. April 2005)
//
// Copyright (c) 2005 by SmartFTP


//////////////////////////////////////////////////////////////////////////
// class CDiskUsage
//
class CDiskUsage
{	
	var $m_Debug = false;
	var $m_nFiles = 0;
	var $m_nDirectories = 0;

	// ctor
	function CDiskUsage()
	{
	}

	function SetDebug($val)
	{
		$this->m_Debug = $val;
	}

	function GetFiles()
	{
		return $this->m_nFiles;		
	}

	function GetDirectories()
	{
		return $this->m_nDirectories;
	}

	function Reset()
	{
		$this->m_nFiles = 0;
		$this->m_nDirectories = 0;
	}

	function CalculateUsage($dir)
	{
		$this->Reset();
		return $this->_CalculateUsage($dir);
	}

	// called recursively
	function _CalculateUsage($dir) 
	{
		$size = 0;
		if ($dh = opendir($dir)) 
		{
			while (($item = readdir($dh)) !== false) 
			{
				if ($item !== '.' 
					&& $item !== '..') 
				{
					$file = $dir."/".$item;
					$this->Log($file."  ".filesize($file));
					if (is_file($file)) 
					{
						$size += filesize($file);
						$this->m_nFiles++;
					} 
					else if (is_dir($file)) 
					{
						$size += $this->_CalculateUsage($file);
						$this->m_nDirectories++;
					}
				}
			}
		}
		return $size;
	}

	function Log($str)
	{
		if($this->m_Debug)
		{
			print($str);
			print("<br>");                 	
		}
	}

}

//////////////////////////////////////////////////////////////
// Entry Point

if($_REQUEST["dir"])
	$dir = $_REQUEST["dir"];
else
	$dir = getcwd();

// header
print("<html><head><title>Disk Usage of ".$dir."</title></head><body>\r\n");
print("<h1>Disk Usage Calculator</h1>\r\n");

// form
print("<form method=\"GET\" action=\"".$_SERVER["PHP_SELF"]."\">");
print("Directory: <input type=\"text\" name=\"dir\" value=\"".$dir."\" size=\"100\"><br>\r\n");

// show details checkbox
print("<input type=\"checkbox\" name=\"showdetails\"");
if($_REQUEST["showdetails"] == "1")
	print(" CHECKED");
print(" value=\"1\">Show Details<br>\r\n");

print("<input type=\"submit\" value=\"Calculate\"></form>");

print($dir);
print("<br>");

$obj = new CDiskUsage();

if($_REQUEST["showdetails"] == "1")
	$obj->SetDebug(true);

$size = $obj->CalculateUsage($dir);

print("<br>");
print("<table>");
print("<td>Number of files</td><td>".$obj->GetFiles()."</td></tr>\r\n");
print("<td>Number of directories</td><td>".$obj->GetDirectories()."</td></tr>\r\n");
print("<td>Disk usage</td><td>".sprintf("%.2f", $size/1024/1024)." MB</td></tr>\r\n");
print("</table>");

// footer
print("</body></html>");

?>