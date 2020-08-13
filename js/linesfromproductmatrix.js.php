<?php
/* Copyright (C) 2018 John BOTELLA
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * Library javascript to enable Browser notifications
 */

if (!defined('NOREQUIREUSER'))  define('NOREQUIREUSER', '1');
if (!defined('NOREQUIREDB'))    define('NOREQUIREDB','1');
if (!defined('NOREQUIRESOC'))   define('NOREQUIRESOC', '1');
//if (!defined('NOREQUIRETRAN'))  define('NOREQUIRETRAN','1');
//if (!defined('NOCSRFCHECK'))    define('NOCSRFCHECK', 1);
if (!defined('NOTOKENRENEWAL')) define('NOTOKENRENEWAL', 1);
if (!defined('NOLOGIN'))        define('NOLOGIN', 1);
if (!defined('NOREQUIREMENU'))  define('NOREQUIREMENU', 1);
if (!defined('NOREQUIREHTML'))  define('NOREQUIREHTML', 1);
if (!defined('NOREQUIREAJAX'))  define('NOREQUIREAJAX','1');


/**
 * \file    js/linesfromproductmatrix.js.php
 * \ingroup linesfromproductmatrix
 * \brief   JavaScript file for module linesfromproductmatrix.
 */

// Load Dolibarr environment
$res=0;
// Try main.inc.php into web root known defined into CONTEXT_DOCUMENT_ROOT (not always defined)
if (! $res && ! empty($_SERVER["CONTEXT_DOCUMENT_ROOT"])) $res=@include($_SERVER["CONTEXT_DOCUMENT_ROOT"]."/main.inc.php");
// Try main.inc.php into web root detected using web root caluclated from SCRIPT_FILENAME
$tmp=empty($_SERVER['SCRIPT_FILENAME'])?'':$_SERVER['SCRIPT_FILENAME'];$tmp2=realpath(__FILE__); $i=strlen($tmp)-1; $j=strlen($tmp2)-1;
while($i > 0 && $j > 0 && isset($tmp[$i]) && isset($tmp2[$j]) && $tmp[$i]==$tmp2[$j]) { $i--; $j--; }
if (! $res && $i > 0 && file_exists(substr($tmp, 0, ($i+1))."/main.inc.php")) $res=@include(substr($tmp, 0, ($i+1))."/main.inc.php");
if (! $res && $i > 0 && file_exists(substr($tmp, 0, ($i+1))."/../main.inc.php")) $res=@include(substr($tmp, 0, ($i+1))."/../main.inc.php");
// Try main.inc.php using relative path
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");
if (! $res && file_exists("../../../main.inc.php")) $res=@include("../../../main.inc.php");
if (! $res) die("Include of main fails");

// Define js type
header('Content-Type: application/javascript');
// Important: Following code is to cache this file to avoid page request by browser at each Dolibarr page access.
// You can use CTRL+F5 to refresh your browser cache.
if (empty($dolibarr_nocache)) header('Cache-Control: max-age=3600, public, must-revalidate');
else header('Cache-Control: no-cache');


// Load traductions files requiredby by page
$langs->loadLangs(array("linesfromproductmatrix@linesfromproductmatrix","other"));
?>

/* Javascript library of module linesfromproductmatrix */
$(document).ready(function(){
	$(document).on("click", ".fa-pencil-alt", function (){
		var pencil = $(this);
		var check = $(this).next(".fa-check");
		check.toggle(0);
		pencil.toggle(0);
	});

	$(document).on("click", ".pictodelete", function () {
		var idMatrix = $(this).data("id");
		var result = confirm("Êtes-vous certain de vouloir supprimer tout le bloc ?")
		if (result) {
			$.ajax({
				url: "scripts/interface.php",
				method: "POST",
				dataType: "json",  // format de réponse attendu
				data: {
					idMatrix: idMatrix,
					action: 'deleteMatrix',
				}
			})
				.done(function() {
					location.reload();
					alert("Le bloc a bien été supprimé");
				});
		}

	});

	$(document).on("click", ".fa-grip-lines", function () {
			$.ajax({
				url: "scripts/interface.php",
				method: "POST",
				dataType: "json",  // format de réponse attendu
				data: {
					idMatrix: idMatrix,
					action: 'addLineMatrix',
				}
			})
				.done(function() {
					location.reload();
				});
	});


	$(document).on("change", ".inputBloc", function () {
		var labelBloc = $(this).val(); // On récupère la valeur de l\'input
		var idBloc = $(this).data("id");  // On récupère l\'id de l\'input
		var self = $(this);
		var parentBlocTitle = $(this).closest("div");
		$.ajax({
			url: "scripts/interface.php",
			method: "POST",
			dataType : "json",  // format de réponse attendu
			data: {id: idBloc,
				action: 'updateLabelBloc',
				label:labelBloc}
		})
			.done(function() {
				alert("OK");  // TODO fonction JS à faire
				parentBlocTitle.css("background-color", "green");
				setTimeout(function () {
					parentBlocTitle.css("background-color", "white");
				}, 700)
				var pencilToShow = self.next().children(".fa-pencil-alt");
				var check = self.next().children(".fa-check");

				check.toggle(0);
				pencilToShow.toggle(0);

			});
	});

});