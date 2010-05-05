<!DOCTYPE html>

<?php
    //=======================================================================
    // A few configuration values.  Change these as you see fit.
    //=======================================================================

    // showReadme
    //   If true, the contents of an (optional) readme.html file will appear before
    //   the directory listing.  This file should be an HTML snippet; no head/body/etc
    //   tags.  You can do paragraph tags or whatever.
    $showReadme = true;

    // titleFormat
    //   How to format the <title> and <h1> text.  %DIR is replaced with the directory path.
    // for instance:
    //   $titleFormat = "Now Viewing: %DIR";
    $titleFormat = "Index of %DIR";
    
    // indices path
    $mindexesPath = '/mindexes';

    //=======================================================================
    // (end of config)
    //=======================================================================

    $uri = urldecode($_SERVER['REQUEST_URI']);
    $uri = preg_replace("/\?.*$/", "", $uri);
    $uri = preg_replace("/\/ *$/", "", $uri);
    
    // $absPath = $_SERVER["DOCUMENT_ROOT"] . $uri;

    $titletext = str_replace("%DIR", $uri, $titleFormat). '/';

    // generate title path, with links to each parent folder
    $folders = explode('/',$uri);
    $folderCount = count($folders);
    $pathMarkup = '';
    foreach ($folders as $i => $folder) {
        $link = '';
        $backCount = $folderCount - $i -1;
        for ($j=0; $j < $backCount; $j++) { 
            $link .= '../';
        }
        $pathMarkup .= '<strong><a href="'.$link.'">'.$folder.'/</a></strong>';
    }    
    
    $h1text = str_replace("%DIR", $pathMarkup, $titleFormat);


    $readmeMarkup = '';
    $currentDir = $_SERVER["DOCUMENT_ROOT"] . $uri . '/';
    if ($showReadme && is_dir($currentDir)) {
        if ($dh = opendir($currentDir)) {
            while (($file = readdir($dh)) !== false) {
                // go thru files, find the first README.*
                if( preg_match('/^README(\.[A-z0-9]+)?$/i', $file) && !is_dir($currentDir.$file) ) {
                    // echo $file . '<br />';
                    $readmeFile = $file;
                    break;
                }
            }
            closedir($dh);
        }
        
        if (isset($readmeFile)) {
            $fileInfo = pathinfo($readmeFile);
            $ext = $fileInfo['extension'];
            // echo $readmeFile.'<br />';
            // echo $ext.'<br />';
        
            $readmeRaw = file_get_contents($currentDir.$readmeFile);
        
            // echo 'readmeRaw::::: '.$readmeRaw.'<br />';
        
            if ($ext == 'textile') {
                require_once( $_SERVER["DOCUMENT_ROOT"]. $mindexesPath . '/textile.php');
                $textile = new Textile();
                $readmeContent = $textile->TextileThis($readmeRaw);
            } else if ($ext == 'markdown' || $ext == 'md') {
                require_once( $_SERVER["DOCUMENT_ROOT"]. $mindexesPath . '/markdown.php');
                $readmeContent = Markdown($readmeRaw);
            } else if($ext == 'html' || $ext == 'htm') {
                $readmeContent = $readmeRaw;
            } else {
                $readmeContent = '<pre>'."\n".$readmeRaw."\n".'</pre>';
            }
        
            $readmeMarkup = '<div id="readme">'."\n".'<h2 class="readme-title"><a href="'.$readmeFile.'">'.$readmeFile.'</a></h2>'."\n".$readmeContent."\n".'</div> <!-- #readme -->';
            
        }
    }


?>
<html>
<head>
    <!--
         Minimal directory listings generated with Mindexes
         
         A mod of Indices:
         http://antisleep.com/software/indices
    -->
    
    <title><?=$titletext?></title>

    <meta http-equiv="Content-type" content="text/html;charset=UTF-8" />
    <meta name="viewport" content=" initial-scale=1.0; minimum-scale=1.0; maximum-scale=1.0; user-scalable=0;"/>
    

    <link rel="stylesheet" media="screen and (max-device-width: 480px)" href="/mindexes/iphone.css" />
    <link rel="stylesheet" media="screen and (min-device-width: 481px)" href="/mindexes/screen.css">


</head>

<body>
    <?php

    ?>

    
    <div id="pagecontainer">
        
        <div class="header">
            <h1 id="page-title"><?= $h1text ?></h1>
            
        </div>
