<?php
session_cache_limiter('none');
session_id($_COOKIE['parent_sid']);
session_start();
$path = "../../../../../libraries/";

/** The configuration file.*/
include_once $path."configuration.php";

$dir = '../../../../content/editor_templates/'.$_SESSION['s_login'];
if (!is_dir($dir) && !mkdir($dir, 0755)) {
	throw new Exception(_COULDNOTCREATEDIRECTORY);
}
$templatesFileSystemTree = new FileSystemTree($dir);
foreach (new EfrontFileTypeFilterIterator(new EfrontFileOnlyFilterIterator(new EfrontNodeFilterIterator(new RecursiveIteratorIterator($templatesFileSystemTree -> tree, RecursiveIteratorIterator :: SELF_FIRST))), array('html')) as $key => $value) {
	$existingTemplates[basename($key)] = basename($key);
}
if ($_POST['templateName']) {
	if (eF_checkParameter($_POST['templateName'], 'alnum')) {
		$dir = '../../../../content/editor_templates/'.$_SESSION['s_login'];
		if (!is_dir($dir) && !mkdir($dir, 0755)) {
			throw new Exception(_COULDNOTCREATEDIRECTORY);
		}

		$filename = $dir.'/'.$_POST['templateName'].'.html';
		$templateContent = $_POST['templateContent'];
		if(file_exists($filename) === false) {
			$ok = file_put_contents($filename, $templateContent);
			chmod($filename, 0644);
			if ($ok !== false) {
				$message = '<div class="messageDivGreen">{#save_template_dlg.filesaved}</div>';
			} else {
				$message = '<div class="messageDivRed">{#save_template_dlg.problem}</div>';
			}
		} else {
			$message = '<div class="messageDivRed">{#save_template_dlg.fileexists}</div>';
		}
	} else {
		$message = '<div class="messageDivRed">{#save_template_dlg.problem}</div>';
	}
} elseif($_POST['delete_file'] && in_array($_POST['delete_file'], array_keys($existingTemplates), true) !== false) {
		$file = new EfrontFile($dir.'/'.$_POST['delete_file']);
		$file -> delete();
}
	
$existingTemplates = array();
$dir = '../../../../content/editor_templates/'.$_SESSION['s_login'];
$templatesFileSystemTree = new FileSystemTree($dir);
foreach (new EfrontFileTypeFilterIterator(new EfrontFileOnlyFilterIterator(new EfrontNodeFilterIterator(new RecursiveIteratorIterator($templatesFileSystemTree -> tree, RecursiveIteratorIterator :: SELF_FIRST))), array('html')) as $key => $value) {
	$existingTemplates[basename($key)] = basename($key);
}
//error_reporting( E_ALL );ini_set("display_errors", true);define("NO_OUTPUT_BUFFERING", true);

//pr($existingTemplates);			
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>{#save_template_dlg.save_template_title}</title>
	<script type="text/javascript" src="../../tiny_mce_popup.js"></script>
	<link href="css/save_template.css" rel="stylesheet" type="text/css" />
	<base target="_self" />
</head>
<body style="display: none">
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" onSubmit = "document.getElementById('templateContent').value=tinyMCE.activeEditor.getContent();">
	<table border="0" cellpadding="4" cellspacing="0">
		<tr>
			<td colspan="2" class="title">{#save_template_dlg.save_template_desc}</td>
		</tr>
<? if (isset($message) && $message != "") {?>		
		<tr>
			<td colspan="2" class="title"><? echo $message; ?></td>
		</tr>
<?}?>		
		<tr>
			<td nowrap="nowrap">{#save_template_dlg.save_template_name}:</td>
			<td><input name="templateName" type="text" class="mceFocus" id="templateName" value="" style="width: 200px" />.html
				<input name="templateContent" id = "templateContent" type="hidden" value=""/>
			</td>
		</tr>
	</table>

	<div class="mceActionPanel">
		<div style="float: left">
			<input type="submit" id="insert" name="insert" value="{#save_template_dlg.save}" />
		</div>

		<div style="float: right">
			<input type="button" id="cancel" name="cancel" value="{#cancel}" onclick="tinyMCEPopup.close();" />
		</div>
	</div>
</form>
<br/><br/><br/>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">

	<table border="0" cellpadding="4" cellspacing="0">
		<tr>
			<td colspan="2" class="title">{#save_template_dlg.save_template_templateslist}</td>
		</tr>
<?php 
foreach ($existingTemplates as $key => $value) {
	echo "<tr><td width='80%'>".$value."</td><td><button type='submit' name='delete_file' value=".urlencode($value).">{#save_template_dlg.save_template_delete}</button></td></tr>";
}

?>
</table>
</form>
</body>
</html>

