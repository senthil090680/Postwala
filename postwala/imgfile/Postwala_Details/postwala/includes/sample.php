<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title></title>
<meta name="generator" content="tsWebEditor (tswebeditor.net.tc - www.tswebeditor.tk)" />
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
<?php

    # ***** BEGIN LICENSE BLOCK *****
    # This file is part of HTML Sanitizer.
    # Copyright (c) 2005-2009 Frederic Minne <zefredz@gmail.com>.
    # All rights reserved.
    #
    # HTML Sanitizer is free software; you can redistribute it and/or modify
    # it under the terms of the GNU Lesser General Public License as published by
    # the Free Software Foundation; either version 3 of the License, or
    # (at your option) any later version.
    #
    # HTML Sanitizer is distributed in the hope that it will be useful,
    # but WITHOUT ANY WARRANTY; without even the implied warranty of
    # MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    # GNU General Public License for more details.
    #
    # You should have received a copy of the GNU Lesser General Public License
    # along with HTML Sanitizer; if not, see <http://www.gnu.org/licenses/>.
    #
    # ***** END LICENSE BLOCK *****
    
    /**
     * @author  Frederic Minne <zefredz@claroline.net>
     * @copyright Copyright &copy; 2006-2007, Frederic Minne
     * @license http://www.gnu.org/licenses/lgpl.txt GNU Lesser General Public License version 3 or later
     * @version 1.0
     * @package HTML
     */

    require_once dirname(__FILE__) . '/sanitizer.class.php';
    
    $test = '<p>Hello</p><script type="text/javascript">alert("plop !")</script>'
        . '<a href="javascript:alert(\'plop !\')">click me!</a>'
        . '<a href="" onclick="alert(\'plop !\');return false;">click me too !</a>'
        . '<img src="javascript:alert(\'xss\')" />'
        . '<img dynsrc="javascript:alert(\'xss\')" />'
        ;

    echo '<h1>Original code</h1>';
    echo "\n";
    echo '<pre>';
    echo htmlspecialchars( $test );
    echo '</pre>';
    echo "\n";

    echo '<h1>Default sanitizer options</h1>';
    echo "\n";
    $san = new HTML_Sanitizer;
    echo '<pre>';
    echo htmlspecialchars( $san->sanitize( $test ) );
    echo '</pre>';
    echo "\n";

    echo '<h1>Allow some dangerous tags</h1>';
    echo "\n";
    $san->allowScript();
    $san->allowDOMEvents();
    echo '<pre>';
    echo htmlspecialchars( $san->sanitize( $test ) );
    echo '</pre>';
    echo "\n";
?>
</body>
</html>
