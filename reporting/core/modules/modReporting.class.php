<?php
/* Copyright (C) 2004-2018  Laurent Destailleur     <eldy@users.sourceforge.net>
 * Copyright (C) 2018-2019  Nicolas ZABOURI         <info@inovea-conseil.com>
 * Copyright (C) 2019-2020  Frédéric France         <frederic.france@netlogic.fr>
 * Copyright (C) 2022 Rance Aaron <ranceaaron941@gmail.com>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

/**
 * 	\defgroup   reporting     Module Reporting
 *  \brief      Reporting module descriptor.
 *
 *  \file       htdocs/reporting/core/modules/modReporting.class.php
 *  \ingroup    reporting
 *  \brief      Description and activation file for module Reporting
 */
include_once DOL_DOCUMENT_ROOT.'/core/modules/DolibarrModules.class.php';

//PHP Excel
use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
use Box\Spout\Common\Entity\Row;
require_once DOL_DOCUMENT_ROOT.'/custom/reporting/spout-3.3.0/src/Spout/Autoloader/autoload.php';
include_once DOL_DOCUMENT_ROOT .'/core/modules/DolibarrModules.class.php';
include_once DOL_DOCUMENT_ROOT.'/custom/reporting/core/modules/BalanceSheet.class.php';
/**
 *  Description and activation class for module Reporting
 */
class modReporting extends DolibarrModules
{
	/**
	 * Constructor. Define names, constants, directories, boxes, permissions
	 *
	 * @param DoliDB $db Database handler
	 */
	public function __construct($db)
	{
		global $langs, $conf;
		$this->db = $db;

		// Id for module (must be unique).
		// Use here a free id (See in Home -> System information -> Dolibarr for list of used modules id).
		$this->numero = 500000; // TODO Go on page https://wiki.dolibarr.org/index.php/List_of_modules_id to reserve an id number for your module

		// Key text used to identify module (for permissions, menus, etc...)
		$this->rights_class = 'reporting';

		// Family can be 'base' (core modules),'crm','financial','hr','projects','products','ecm','technic' (transverse modules),'interface' (link with external tools),'other','...'
		// It is used to group modules by family in module setup page
		$this->family = "other";

		// Module position in the family on 2 digits ('01', '10', '20', ...)
		$this->module_position = '90';

		// Gives the possibility for the module, to provide his own family info and position of this family (Overwrite $this->family and $this->module_position. Avoid this)
		//$this->familyinfo = array('myownfamily' => array('position' => '01', 'label' => $langs->trans("MyOwnFamily")));
		// Module label (no space allowed), used if translation string 'ModuleReportingName' not found (Reporting is name of module).
		$this->name = preg_replace('/^mod/i', '', get_class($this));

		// Module description, used if translation string 'ModuleReportingDesc' not found (Reporting is name of module).
		$this->description = "ReportingDescription";
		// Used only if file README.md and README-LL.md not found.
		$this->descriptionlong = "ReportingDescription";

		// Author
		$this->editor_name = 'Editor name';
		$this->editor_url = 'https://www.example.com';

		// Possible values for version are: 'development', 'experimental', 'dolibarr', 'dolibarr_deprecated' or a version string like 'x.y.z'
		$this->version = '1.0';
		// Url to the file with your last numberversion of this module
		//$this->url_last_version = 'http://www.example.com/versionmodule.txt';

		// Key used in llx_const table to save module status enabled/disabled (where REPORTING is value of property name of module in uppercase)
		$this->const_name = 'MAIN_MODULE_'.strtoupper($this->name);

		// Name of image file used for this module.
		// If file is in theme/yourtheme/img directory under name object_pictovalue.png, use this->picto='pictovalue'
		// If file is in module/img directory under name object_pictovalue.png, use this->picto='pictovalue@module'
		// To use a supported fa-xxx css style of font awesome, use this->picto='xxx'
		$this->picto = 'generic';

		// Define some features supported by module (triggers, login, substitutions, menus, css, etc...)
		$this->module_parts = array(
			// Set this to 1 if module has its own trigger directory (core/triggers)
			'triggers' => 0,
			// Set this to 1 if module has its own login method file (core/login)
			'login' => 0,
			// Set this to 1 if module has its own substitution function file (core/substitutions)
			'substitutions' => 0,
			// Set this to 1 if module has its own menus handler directory (core/menus)
			'menus' => 0,
			// Set this to 1 if module overwrite template dir (core/tpl)
			'tpl' => 0,
			// Set this to 1 if module has its own barcode directory (core/modules/barcode)
			'barcode' => 0,
			// Set this to 1 if module has its own models directory (core/modules/xxx)
			'models' => 0,
			// Set this to 1 if module has its own printing directory (core/modules/printing)
			'printing' => 0,
			// Set this to 1 if module has its own theme directory (theme)
			'theme' => 0,
			// Set this to relative path of css file if module has its own css file
			'css' => array(
				'/reporting/css/reporting.css.php',
			),
			// Set this to relative path of js file if module must load a js on all pages
			'js' => array(
				'/reporting/js/reporting.js.php',
			),
			// Set here all hooks context managed by module. To find available hook context, make a "grep -r '>initHooks(' *" on source code. You can also set hook context to 'all'
			'hooks' => array(
				//   'data' => array(
				//       'hookcontext1',
				//       'hookcontext2',
				//   ),
				//   'entity' => '0',
			),
			// Set this to 1 if features of module are opened to external users
			'moduleforexternal' => 0,
		);

		// Data directories to create when module is enabled.
		// Example: this->dirs = array("/reporting/temp","/reporting/subdir");
		$this->dirs = array("/reporting/temp");

		// Config pages. Put here list of php page, stored into reporting/admin directory, to use to setup module.
		$this->config_page_url = array("setup.php@reporting");

		// Dependencies
		// A condition to hide module
		$this->hidden = false;
		// List of module class names as string that must be enabled if this module is enabled. Example: array('always1'=>'modModuleToEnable1','always2'=>'modModuleToEnable2', 'FR1'=>'modModuleToEnableFR'...)
		$this->depends = array();
		$this->requiredby = array(); // List of module class names as string to disable if this one is disabled. Example: array('modModuleToDisable1', ...)
		$this->conflictwith = array(); // List of module class names as string this module is in conflict with. Example: array('modModuleToDisable1', ...)

		// The language file dedicated to your module
		$this->langfiles = array("reporting@reporting");

		// Prerequisites
		$this->phpmin = array(5, 6); // Minimum version of PHP required by module
		$this->need_dolibarr_version = array(11, -3); // Minimum version of Dolibarr required by module

		// Messages at activation
		$this->warnings_activation = array(); // Warning to show when we activate module. array('always'='text') or array('FR'='textfr','MX'='textmx'...)
		$this->warnings_activation_ext = array(); // Warning to show when we activate an external module. array('always'='text') or array('FR'='textfr','MX'='textmx'...)
		//$this->automatic_activation = array('FR'=>'ReportingWasAutomaticallyActivatedBecauseOfYourCountryChoice');
		//$this->always_enabled = true;								// If true, can't be disabled

		// Constants
		// List of particular constants to add when module is enabled (key, 'chaine', value, desc, visible, 'current' or 'allentities', deleteonunactive)
		// Example: $this->const=array(1 => array('REPORTING_MYNEWCONST1', 'chaine', 'myvalue', 'This is a constant to add', 1),
		//                             2 => array('REPORTING_MYNEWCONST2', 'chaine', 'myvalue', 'This is another constant to add', 0, 'current', 1)
		// );
		$this->const = array();

		// Some keys to add into the overwriting translation tables
		/*$this->overwrite_translation = array(
			'en_US:ParentCompany'=>'Parent company or reseller',
			'fr_FR:ParentCompany'=>'Maison mère ou revendeur'
		)*/

		if (!isset($conf->reporting) || !isset($conf->reporting->enabled)) {
			$conf->reporting = new stdClass();
			$conf->reporting->enabled = 0;
		}

		// Array to add new pages in new tabs
		$this->tabs = array();
		// Example:
		// $this->tabs[] = array('data'=>'objecttype:+tabname1:Title1:mylangfile@reporting:$user->rights->reporting->read:/reporting/mynewtab1.php?id=__ID__');  					// To add a new tab identified by code tabname1
		// $this->tabs[] = array('data'=>'objecttype:+tabname2:SUBSTITUTION_Title2:mylangfile@reporting:$user->rights->othermodule->read:/reporting/mynewtab2.php?id=__ID__',  	// To add another new tab identified by code tabname2. Label will be result of calling all substitution functions on 'Title2' key.
		// $this->tabs[] = array('data'=>'objecttype:-tabname:NU:conditiontoremove');                                                     										// To remove an existing tab identified by code tabname
		//
		// Where objecttype can be
		// 'categories_x'	  to add a tab in category view (replace 'x' by type of category (0=product, 1=supplier, 2=customer, 3=member)
		// 'contact'          to add a tab in contact view
		// 'contract'         to add a tab in contract view
		// 'group'            to add a tab in group view
		// 'intervention'     to add a tab in intervention view
		// 'invoice'          to add a tab in customer invoice view
		// 'invoice_supplier' to add a tab in supplier invoice view
		// 'member'           to add a tab in fundation member view
		// 'opensurveypoll'	  to add a tab in opensurvey poll view
		// 'order'            to add a tab in customer order view
		// 'order_supplier'   to add a tab in supplier order view
		// 'payment'		  to add a tab in payment view
		// 'payment_supplier' to add a tab in supplier payment view
		// 'product'          to add a tab in product view
		// 'propal'           to add a tab in propal view
		// 'project'          to add a tab in project view
		// 'stock'            to add a tab in stock view
		// 'thirdparty'       to add a tab in third party view
		// 'user'             to add a tab in user view

		// Dictionaries
		$this->dictionaries = array();
		/* Example:
		$this->dictionaries=array(
			'langs'=>'reporting@reporting',
			// List of tables we want to see into dictonnary editor
			'tabname'=>array(MAIN_DB_PREFIX."table1", MAIN_DB_PREFIX."table2", MAIN_DB_PREFIX."table3"),
			// Label of tables
			'tablib'=>array("Table1", "Table2", "Table3"),
			// Request to select fields
			'tabsql'=>array('SELECT f.rowid as rowid, f.code, f.label, f.active FROM '.MAIN_DB_PREFIX.'table1 as f', 'SELECT f.rowid as rowid, f.code, f.label, f.active FROM '.MAIN_DB_PREFIX.'table2 as f', 'SELECT f.rowid as rowid, f.code, f.label, f.active FROM '.MAIN_DB_PREFIX.'table3 as f'),
			// Sort order
			'tabsqlsort'=>array("label ASC", "label ASC", "label ASC"),
			// List of fields (result of select to show dictionary)
			'tabfield'=>array("code,label", "code,label", "code,label"),
			// List of fields (list of fields to edit a record)
			'tabfieldvalue'=>array("code,label", "code,label", "code,label"),
			// List of fields (list of fields for insert)
			'tabfieldinsert'=>array("code,label", "code,label", "code,label"),
			// Name of columns with primary key (try to always name it 'rowid')
			'tabrowid'=>array("rowid", "rowid", "rowid"),
			// Condition to show each dictionary
			'tabcond'=>array($conf->reporting->enabled, $conf->reporting->enabled, $conf->reporting->enabled),
			// Tooltip for every fields of dictionaries: DO NOT PUT AN EMPTY ARRAY
			'tabhelp'=>array(array('field1' => 'field1tooltip', 'field2' => 'field2tooltip'), array('field1' => 'field1tooltip', 'field2' => 'field2tooltip'), ...),

		);
		*/

		// Boxes/Widgets
		// Add here list of php file(s) stored in reporting/core/boxes that contains a class to show a widget.
		$this->boxes = array(
			//  0 => array(
			//      'file' => 'reportingwidget1.php@reporting',
			//      'note' => 'Widget provided by Reporting',
			//      'enabledbydefaulton' => 'Home',
			//  ),
			//  ...
		);

		// Cronjobs (List of cron jobs entries to add when module is enabled)
		// unit_frequency must be 60 for minute, 3600 for hour, 86400 for day, 604800 for week
		$this->cronjobs = array(
			//  0 => array(
			//      'label' => 'MyJob label',
			//      'jobtype' => 'method',
			//      'class' => '/reporting/class/myobject.class.php',
			//      'objectname' => 'MyObject',
			//      'method' => 'doScheduledJob',
			//      'parameters' => '',
			//      'comment' => 'Comment',
			//      'frequency' => 2,
			//      'unitfrequency' => 3600,
			//      'status' => 0,
			//      'test' => '$conf->reporting->enabled',
			//      'priority' => 50,
			//  ),
		);
		// Example: $this->cronjobs=array(
		//    0=>array('label'=>'My label', 'jobtype'=>'method', 'class'=>'/dir/class/file.class.php', 'objectname'=>'MyClass', 'method'=>'myMethod', 'parameters'=>'param1, param2', 'comment'=>'Comment', 'frequency'=>2, 'unitfrequency'=>3600, 'status'=>0, 'test'=>'$conf->reporting->enabled', 'priority'=>50),
		//    1=>array('label'=>'My label', 'jobtype'=>'command', 'command'=>'', 'parameters'=>'param1, param2', 'comment'=>'Comment', 'frequency'=>1, 'unitfrequency'=>3600*24, 'status'=>0, 'test'=>'$conf->reporting->enabled', 'priority'=>50)
		// );

		// Permissions provided by this module
		$this->rights = array();
		$r = 0;
		// Add here entries to declare new permissions
		/* BEGIN MODULEBUILDER PERMISSIONS */
		$this->rights[$r][0] = $this->numero . sprintf("%02d", $r + 1); // Permission id (must not be already used)
		$this->rights[$r][1] = 'Read objects of Reporting'; // Permission label
		$this->rights[$r][4] = 'myobject';
		$this->rights[$r][5] = 'read'; // In php code, permission will be checked by test if ($user->rights->reporting->myobject->read)
		$r++;
		$this->rights[$r][0] = $this->numero . sprintf("%02d", $r + 1); // Permission id (must not be already used)
		$this->rights[$r][1] = 'Create/Update objects of Reporting'; // Permission label
		$this->rights[$r][4] = 'myobject';
		$this->rights[$r][5] = 'write'; // In php code, permission will be checked by test if ($user->rights->reporting->myobject->write)
		$r++;
		$this->rights[$r][0] = $this->numero . sprintf("%02d", $r + 1); // Permission id (must not be already used)
		$this->rights[$r][1] = 'Delete objects of Reporting'; // Permission label
		$this->rights[$r][4] = 'myobject';
		$this->rights[$r][5] = 'delete'; // In php code, permission will be checked by test if ($user->rights->reporting->myobject->delete)
		$r++;
		/* END MODULEBUILDER PERMISSIONS */

		// Main menu entries to add
		$this->menu = array();
		$r = 0;
		// Add here entries to declare new menus
		/* BEGIN MODULEBUILDER TOPMENU */
		$this->menu[$r++] = array(
			'fk_menu'=>'', // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
			'type'=>'top', // This is a Top menu entry
			'titre'=>'ModuleReportingName',
			'prefix' => img_picto('', $this->picto, 'class="paddingright pictofixedwidth valignmiddle"'),
			'mainmenu'=>'reporting',
			'leftmenu'=>'',
			'url'=>'/custom/reporting/reportingindex.php',
			'langs'=>'reporting@reporting', // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
			'position'=>1000 + $r,
			'enabled'=>'$conf->reporting->enabled', // Define condition to show or hide menu entry. Use '$conf->reporting->enabled' if entry must be visible if module is enabled.
			'perms'=>'1', // Use 'perms'=>'$user->rights->reporting->myobject->read' if you want your menu with a permission rules
			'target'=>'',
			'user'=>2, // 0=Menu for internal users, 1=external users, 2=both
		);
		/* END MODULEBUILDER TOPMENU */
		/* BEGIN MODULEBUILDER LEFTMENU MYOBJECT */
		$this->menu[$r++]=array(
			'fk_menu'=>'fk_mainmenu=reporting',      // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
			'type'=>'left',                          // This is a Left menu entry
			'titre'=>'instructions',
			'prefix' => img_picto('', $this->picto, 'class="paddingright pictofixedwidth valignmiddle"'),
			'mainmenu'=>'reporting',
			'leftmenu'=>'myobject',
			'url'=>'/custom/reporting/reportingindex.php',
			'langs'=>'reporting@reporting',	        // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
			'position'=>1000+$r,
			'enabled'=>'$conf->reporting->enabled',  // Define condition to show or hide menu entry. Use '$conf->reporting->enabled' if entry must be visible if module is enabled.
			'perms'=>'$user->rights->reporting->myobject->read',			                // Use 'perms'=>'$user->rights->reporting->level1->level2' if you want your menu with a permission rules
			'target'=>'',
			'user'=>2,
			'id'=>'instr'				                // 0=Menu for internal users, 1=external users, 2=both
		);
		$this->menu[$r++]=array(
			'fk_menu'=>'fk_mainmenu=reporting,fk_leftmenu=myobject',	    // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
			'type'=>'left',			                // This is a Left menu entry
			'titre'=>'remarks',
			'mainmenu'=>'reporting',
			'leftmenu'=>'reporting_myobject_list',
			'url'=>'/custom/reporting/reportingindex.php',
			'langs'=>'reporting@reporting',	        // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
			'position'=>1000+$r,
			'enabled'=>'$conf->reporting->enabled',  // Define condition to show or hide menu entry. Use '$conf->reporting->enabled' if entry must be visible if module is enabled. Use '$leftmenu==\'system\'' to show if leftmenu system is selected.
			'perms'=>'$user->rights->reporting->myobject->read',			                // Use 'perms'=>'$user->rights->reporting->level1->level2' if you want your menu with a permission rules
			'target'=>'',
			'user'=>2,				                // 0=Menu for internal users, 1=external users, 2=both
		);
		$this->menu[$r++]=array(
			'fk_menu'=>'fk_mainmenu=reporting,fk_leftmenu=myobject',	    // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
			'type'=>'left',			                // This is a Left menu entry
			'titre'=>'analysis',
			'mainmenu'=>'reporting',
			'leftmenu'=>'reporting_myobject_new',
			'url'=>'/custom/reporting/reportingindex.php',
			'langs'=>'reporting@reporting',	        // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
			'position'=>1000+$r,
			'enabled'=>'$conf->reporting->enabled',  // Define condition to show or hide menu entry. Use '$conf->reporting->enabled' if entry must be visible if module is enabled. Use '$leftmenu==\'system\'' to show if leftmenu system is selected.
			'perms'=>'$user->rights->reporting->myobject->write',			                // Use 'perms'=>'$user->rights->reporting->level1->level2' if you want your menu with a permission rules
			'target'=>'',
			'user'=>2,				                // 0=Menu for internal users, 1=external users, 2=both
		);
		$this->menu[$r++]=array(
			'fk_menu'=>'fk_mainmenu=reporting,fk_leftmenu=myobject',	    // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
			'type'=>'left',			                // This is a Left menu entry
			'titre'=>'sales',
			'mainmenu'=>'reporting',
			'leftmenu'=>'reporting_myobject_list',
			'url'=>'/custom/reporting/reportingindex.php',
			'langs'=>'reporting@reporting',	        // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
			'position'=>1000+$r,
			'enabled'=>'$conf->reporting->enabled',  // Define condition to show or hide menu entry. Use '$conf->reporting->enabled' if entry must be visible if module is enabled. Use '$leftmenu==\'system\'' to show if leftmenu system is selected.
			'perms'=>'$user->rights->reporting->myobject->read',			                // Use 'perms'=>'$user->rights->reporting->level1->level2' if you want your menu with a permission rules
			'target'=>'',
			'user'=>2,				                // 0=Menu for internal users, 1=external users, 2=both
		);
		$this->menu[$r++]=array(
			'fk_menu'=>'fk_mainmenu=reporting,fk_leftmenu=myobject',	    // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
			'type'=>'left',			                // This is a Left menu entry
			'titre'=>'marketing',
			'mainmenu'=>'reporting',
			'leftmenu'=>'reporting_myobject_new',
			'url'=>'/custom/reporting/reportingindex.php',
			'langs'=>'reporting@reporting',	        // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
			'position'=>1000+$r,
			'enabled'=>'$conf->reporting->enabled',  // Define condition to show or hide menu entry. Use '$conf->reporting->enabled' if entry must be visible if module is enabled. Use '$leftmenu==\'system\'' to show if leftmenu system is selected.
			'perms'=>'$user->rights->reporting->myobject->write',			                // Use 'perms'=>'$user->rights->reporting->level1->level2' if you want your menu with a permission rules
			'target'=>'',
			'user'=>2,				                // 0=Menu for internal users, 1=external users, 2=both
		);
		$this->menu[$r++]=array(
			'fk_menu'=>'fk_mainmenu=reporting,fk_leftmenu=myobject',	    // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
			'type'=>'left',			                // This is a Left menu entry
			'titre'=>'forward',
			'mainmenu'=>'reporting',
			'leftmenu'=>'reporting_myobject_new',
			'url'=>'/custom/reporting/reportingindex.php',
			'langs'=>'reporting@reporting',	        // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
			'position'=>1000+$r,
			'enabled'=>'$conf->reporting->enabled',  // Define condition to show or hide menu entry. Use '$conf->reporting->enabled' if entry must be visible if module is enabled. Use '$leftmenu==\'system\'' to show if leftmenu system is selected.
			'perms'=>'$user->rights->reporting->myobject->write',			                // Use 'perms'=>'$user->rights->reporting->level1->level2' if you want your menu with a permission rules
			'target'=>'',
			'user'=>2,				                // 0=Menu for internal users, 1=external users, 2=both
		);
		$this->menu[$r++]=array(
			'fk_menu'=>'fk_mainmenu=reporting,fk_leftmenu=myobject',	    // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
			'type'=>'left',			                // This is a Left menu entry
			'titre'=>'report',
			'mainmenu'=>'reporting',
			'leftmenu'=>'reporting_myobject_new',
			'url'=>'/custom/reporting/reportingindex.php',
			'langs'=>'reporting@reporting',	        // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
			'position'=>1000+$r,
			'enabled'=>'$conf->reporting->enabled',  // Define condition to show or hide menu entry. Use '$conf->reporting->enabled' if entry must be visible if module is enabled. Use '$leftmenu==\'system\'' to show if leftmenu system is selected.
			'perms'=>'$user->rights->reporting->myobject->write',			                // Use 'perms'=>'$user->rights->reporting->level1->level2' if you want your menu with a permission rules
			'target'=>'',
			'user'=>2,				                // 0=Menu for internal users, 1=external users, 2=both
		);
		/*END MODULEBUILDER LEFTMENU MYOBJECT */
		// Exports profiles provided by this module
		$r = 1;
		/* BEGIN MODULEBUILDER EXPORT MYOBJECT */
		/*
		$langs->load("reporting@reporting");
		$this->export_code[$r]=$this->rights_class.'_'.$r;
		$this->export_label[$r]='MyObjectLines';	// Translation key (used only if key ExportDataset_xxx_z not found)
		$this->export_icon[$r]='myobject@reporting';
		// Define $this->export_fields_array, $this->export_TypeFields_array and $this->export_entities_array
		$keyforclass = 'MyObject'; $keyforclassfile='/reporting/class/myobject.class.php'; $keyforelement='myobject@reporting';
		include DOL_DOCUMENT_ROOT.'/core/commonfieldsinexport.inc.php';
		//$this->export_fields_array[$r]['t.fieldtoadd']='FieldToAdd'; $this->export_TypeFields_array[$r]['t.fieldtoadd']='Text';
		//unset($this->export_fields_array[$r]['t.fieldtoremove']);
		//$keyforclass = 'MyObjectLine'; $keyforclassfile='/reporting/class/myobject.class.php'; $keyforelement='myobjectline@reporting'; $keyforalias='tl';
		//include DOL_DOCUMENT_ROOT.'/core/commonfieldsinexport.inc.php';
		$keyforselect='myobject'; $keyforaliasextra='extra'; $keyforelement='myobject@reporting';
		include DOL_DOCUMENT_ROOT.'/core/extrafieldsinexport.inc.php';
		//$keyforselect='myobjectline'; $keyforaliasextra='extraline'; $keyforelement='myobjectline@reporting';
		//include DOL_DOCUMENT_ROOT.'/core/extrafieldsinexport.inc.php';
		//$this->export_dependencies_array[$r] = array('myobjectline'=>array('tl.rowid','tl.ref')); // To force to activate one or several fields if we select some fields that need same (like to select a unique key if we ask a field of a child to avoid the DISTINCT to discard them, or for computed field than need several other fields)
		//$this->export_special_array[$r] = array('t.field'=>'...');
		//$this->export_examplevalues_array[$r] = array('t.field'=>'Example');
		//$this->export_help_array[$r] = array('t.field'=>'FieldDescHelp');
		$this->export_sql_start[$r]='SELECT DISTINCT ';
		$this->export_sql_end[$r]  =' FROM '.MAIN_DB_PREFIX.'myobject as t';
		//$this->export_sql_end[$r]  =' LEFT JOIN '.MAIN_DB_PREFIX.'myobject_line as tl ON tl.fk_myobject = t.rowid';
		$this->export_sql_end[$r] .=' WHERE 1 = 1';
		$this->export_sql_end[$r] .=' AND t.entity IN ('.getEntity('myobject').')';
		$r++; */
		/* END MODULEBUILDER EXPORT MYOBJECT */

		// Imports profiles provided by this module
		$r = 1;
		/* BEGIN MODULEBUILDER IMPORT MYOBJECT */
		/*
		 $langs->load("reporting@reporting");
		 $this->export_code[$r]=$this->rights_class.'_'.$r;
		 $this->export_label[$r]='MyObjectLines';	// Translation key (used only if key ExportDataset_xxx_z not found)
		 $this->export_icon[$r]='myobject@reporting';
		 $keyforclass = 'MyObject'; $keyforclassfile='/reporting/class/myobject.class.php'; $keyforelement='myobject@reporting';
		 include DOL_DOCUMENT_ROOT.'/core/commonfieldsinexport.inc.php';
		 $keyforselect='myobject'; $keyforaliasextra='extra'; $keyforelement='myobject@reporting';
		 include DOL_DOCUMENT_ROOT.'/core/extrafieldsinexport.inc.php';
		 //$this->export_dependencies_array[$r]=array('mysubobject'=>'ts.rowid', 't.myfield'=>array('t.myfield2','t.myfield3')); // To force to activate one or several fields if we select some fields that need same (like to select a unique key if we ask a field of a child to avoid the DISTINCT to discard them, or for computed field than need several other fields)
		 $this->export_sql_start[$r]='SELECT DISTINCT ';
		 $this->export_sql_end[$r]  =' FROM '.MAIN_DB_PREFIX.'myobject as t';
		 $this->export_sql_end[$r] .=' WHERE 1 = 1';
		 $this->export_sql_end[$r] .=' AND t.entity IN ('.getEntity('myobject').')';
		 $r++; */
		/* END MODULEBUILDER IMPORT MYOBJECT */
	}

	/**
	 *  Function called when module is enabled.
	 *  The init function add constants, boxes, permissions and menus (defined in constructor) into Dolibarr database.
	 *  It also creates data directories
	 *
	 *  @param      string  $options    Options when enabling module ('', 'noboxes')
	 *  @return     int             	1 if OK, 0 if KO
	 */
	public function init($options = '')
	{
		global $conf, $langs;

		//$result = $this->_load_tables('/install/mysql/tables/', 'reporting');
		$result = $this->_load_tables('/reporting/sql/');
		if ($result < 0) {
			return -1; // Do not activate module if error 'not allowed' returned when loading module SQL queries (the _load_table run sql with run_sql with the error allowed parameter set to 'default')
		}

		// Create extrafields during init
		//include_once DOL_DOCUMENT_ROOT.'/core/class/extrafields.class.php';
		//$extrafields = new ExtraFields($this->db);
		//$result1=$extrafields->addExtraField('reporting_myattr1', "New Attr 1 label", 'boolean', 1,  3, 'thirdparty',   0, 0, '', '', 1, '', 0, 0, '', '', 'reporting@reporting', '$conf->reporting->enabled');
		//$result2=$extrafields->addExtraField('reporting_myattr2', "New Attr 2 label", 'varchar', 1, 10, 'project',      0, 0, '', '', 1, '', 0, 0, '', '', 'reporting@reporting', '$conf->reporting->enabled');
		//$result3=$extrafields->addExtraField('reporting_myattr3', "New Attr 3 label", 'varchar', 1, 10, 'bank_account', 0, 0, '', '', 1, '', 0, 0, '', '', 'reporting@reporting', '$conf->reporting->enabled');
		//$result4=$extrafields->addExtraField('reporting_myattr4', "New Attr 4 label", 'select',  1,  3, 'thirdparty',   0, 1, '', array('options'=>array('code1'=>'Val1','code2'=>'Val2','code3'=>'Val3')), 1,'', 0, 0, '', '', 'reporting@reporting', '$conf->reporting->enabled');
		//$result5=$extrafields->addExtraField('reporting_myattr5', "New Attr 5 label", 'text',    1, 10, 'user',         0, 0, '', '', 1, '', 0, 0, '', '', 'reporting@reporting', '$conf->reporting->enabled');

		// Permissions
		$this->remove($options);

		$sql = array();

		// Document templates
		$moduledir = dol_sanitizeFileName('reporting');
		$myTmpObjects = array();
		$myTmpObjects['MyObject'] = array('includerefgeneration'=>0, 'includedocgeneration'=>0);

		foreach ($myTmpObjects as $myTmpObjectKey => $myTmpObjectArray) {
			if ($myTmpObjectKey == 'MyObject') {
				continue;
			}
			if ($myTmpObjectArray['includerefgeneration']) {
				$src = DOL_DOCUMENT_ROOT.'/install/doctemplates/'.$moduledir.'/template_myobjects.odt';
				$dirodt = DOL_DATA_ROOT.'/doctemplates/'.$moduledir;
				$dest = $dirodt.'/template_myobjects.odt';

				if (file_exists($src) && !file_exists($dest)) {
					require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
					dol_mkdir($dirodt);
					$result = dol_copy($src, $dest, 0, 0);
					if ($result < 0) {
						$langs->load("errors");
						$this->error = $langs->trans('ErrorFailToCopyFile', $src, $dest);
						return 0;
					}
				}

				$sql = array_merge($sql, array(
					"DELETE FROM ".MAIN_DB_PREFIX."document_model WHERE nom = 'standard_".strtolower($myTmpObjectKey)."' AND type = '".$this->db->escape(strtolower($myTmpObjectKey))."' AND entity = ".((int) $conf->entity),
					"INSERT INTO ".MAIN_DB_PREFIX."document_model (nom, type, entity) VALUES('standard_".strtolower($myTmpObjectKey)."', '".$this->db->escape(strtolower($myTmpObjectKey))."', ".((int) $conf->entity).")",
					"DELETE FROM ".MAIN_DB_PREFIX."document_model WHERE nom = 'generic_".strtolower($myTmpObjectKey)."_odt' AND type = '".$this->db->escape(strtolower($myTmpObjectKey))."' AND entity = ".((int) $conf->entity),
					"INSERT INTO ".MAIN_DB_PREFIX."document_model (nom, type, entity) VALUES('generic_".strtolower($myTmpObjectKey)."_odt', '".$this->db->escape(strtolower($myTmpObjectKey))."', ".((int) $conf->entity).")"
				));
			}
		}

		return $this->_init($sql, $options);
	}

	/**
	 *  Function called when module is disabled.
	 *  Remove from database constants, boxes and permissions from Dolibarr database.
	 *  Data directories are not deleted
	 *
	 *  @param      string	$options    Options when enabling module ('', 'noboxes')
	 *  @return     int                 1 if OK, 0 if KO
	 */
	public function remove($options = '')
	{
		$sql = array();
		return $this->_remove($sql, $options);
	}

	public function current_projects($x, $databasetable){       
        $data = array();
        if(!isset($_REQUEST['select_year'])){
            $stmt_year = 2022;
        }else{
            $stmt_year = $_REQUEST['select_year'];
        }
        
        $sql = "SELECT
                    A.rowid,
                    dateo as opened, 
                    title,
                    A.description,
                    fk_categorie,
                    label,
                    round(budget_amount,2) as budget
                FROM
                    ".$databasetable."_projet A
                left join db_categorie_project B on (A.rowid = B.fk_project)
                left join db_categorie C on (B.fk_categorie = C.rowid)
                where year(datec) in ($stmt_year);";
                
        $result=$this->db->query($sql);			
        $data = array();
        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {           
            $data[] = $row;
        }

        $this->db->free($result);
        
        $t = '<h4 style="text-align:center">Projects</h4>';
        $t .= '<table class="noborder">';
        foreach($data as $d){
            if($d['label'] == $x){
                $t .= "<tr><td>".$d['opened']."</td><td>".$d['title']."</td><td>".$d['description']."</td><td>".$d['budget']."</td></tr>";
            }            
        }

        $t .= '</table>';

        return $t;
    }

	public function get_prospects($databasetable){       
        $data = array();
                
        $sql = "SELECT 
						nom as name,
						date(A.datec) as date,
						phone,
						A.email,
						firstname,
						lastname
				FROM
					".$databasetable."_societe A 
				left join db_societe_commerciaux B on (A.rowid = B.rowid)
				left join db_user C on (B.fk_user = C.rowid)
				WHERE
					client = 2
						AND YEAR(A.datec) IN (2021 , 2022)
				LIMIT 10;";
                
        $result=$this->db->query($sql);			
        $data = array();
        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {           
            $data[] = $row;
        }

        $this->db->free($result);
        
        $t = '<h4 style="text-align:center">Prospects</h4>';
        $t .= '<table class="noborder">';
		$t .= '<th>Date</th><th>Name</th><th>Phone</th><th>Email</th><th>Rep Firstname</th><th>Rep Lastname</th>';
        foreach($data as $d){           
            
                $t .= "<tr><td>".$d['date']."</td><td>".$d['name']."</td><td>".$d['phone']."</td><td>".$d['email']."</td><td>".$d['firstname']."</td><td>".$d['lastname']."</td></tr>";
                        
        }

        $t .= '</table>';

        return $t;
    }

	public function get_meetings($databasetable){       
        $data = array();               
        $sql = "SELECT 
                    *
                FROM
                    ".$databasetable."_actioncomm
                where fk_action = 5
                order by datec desc;";
                            
                    $result=$this->db->query($sql);			
                    $data = array();
                    while ($row = $result->fetch_array(MYSQLI_ASSOC)) {           
                        $data[] = $row;
                    }

                    $this->db->free($result);
                    $t = '<h4 style="text-align:center">Meetings</h4>';
                    $t .= '<table class="noborder">';
                    foreach($data as $d){           
                        
                            $t .= "<tr><td>".$d['datep']."</td><td>".$d['label']."</td><td>".$d['note']."</td></tr>";
                                    
                    }
            
                    $t .= '</table>';
            
                    return $t;
    }

	public function id_form($y){		
		$z = explode('?', $_SERVER['HTTP_REFERER']);
		$w = explode('&', $z[1]);
		$r = explode('=', $w[0]);
		$x = '<form id='.$y.' method="post">';
		$x .=  '<textarea name="text" id="editor1">'.$this->read($y, $r).'</textarea>';
		$x .=  '<input name="area" type="hidden" value="'.$y.'" />';
		$x .=  '<input type="submit" value="submit"  />';
		$x .=  '</form>';
		$x .=  "<script>CKEDITOR.replace( 'editor1' );</script>";
	
		return $x;
	}

	public function get_title(){
		if(isset($_REQUEST['rptid']) && !empty($_REQUEST['rptname'])){
			return $_REQUEST['rptname'];				
		}else{
			return 'Business Reporting';
		}
	}

	public function save($x){		
		$table = $x['area'];
		$text = json_encode($x['text']);
		$sql = "INSERT INTO $table ($table) VALUES ($text) ON DUPLICATE KEY UPDATE ".$x['area']." = '".$x['text']."'";
		
		$result=$this->db->query($sql);
	}

	public function read($x, $y){
		$sql = "Select $x from fcreports where id = ".$_SESSION['rptid'];
					
		$result=$this->db->query($sql);			
		$data = array();
		while ($row = $result->fetch_array(MYSQLI_ASSOC)) {           
			$data[] = $row;
		}

		$this->db->free($result);

		$d = array_shift($data);
		$d2 = array_shift($d);
		return $d2;			
	}

	public function save_report(){
        $n = 'report_'.rand();

        $sql = "INSERT INTO fcreports(
                    analysis,
                    remarks,
                    marketing,
                    sales,
                    forward,
                    rptname
                )
                select
                (select analysis from analysis) as analysis,
                (select remarks from remarks) as remarks,
                (select mrkt from mrkt) as marketing,
                (select sales from sales) as sales,
                (select forward from forward) as forward,
                '$n' as rptname";
       
        $this->db->query($sql);
       
       return;        
    }

	public function load_options(){         
        $sql = "SELECT id,rptname FROM fcreports;";
                
        $result=$this->db->query($sql);			
        $data = array();
        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {           
            $data[$row['id']] = $row;
        }

        $this->db->free($result);
        
        //create updated dropdown options
        $t = '';
        foreach($data as $d){            
				$t .= '<a class="dropdown-item" href="?rptid='.$d["id"].'&rptname='.$d["rptname"].'">'.$d["rptname"].'</a>';                  
        }       
        
        return $t;       
    }

	public function export($n, $y, $z){
        $r = rand();
        $filePath = './'.$n.'_'.$r.'.xls';
        $writer = WriterEntityFactory::createXLSXWriter();       
        $writer->openToFile($filePath); // write data to a file or to a PHP stream
        
        //get statement data
        $v1 = array_shift($y[1]);
		$v2 = array_shift($z[1]);
		//$v3 = array_shift($v2);               
       
        foreach($v1 as $value){			
            $rowFromValues = WriterEntityFactory::createRowFromArray($value);
            $writer->addRow($rowFromValues);
        }

		// Customizing the sheet name when writing
		$sheet = $writer->getCurrentSheet();
		$sheet->setName('Income Stmt');

		$newSheet = $writer->addNewSheetAndMakeItCurrent();

		foreach($v2 as $value){			
            $rowFromValues = WriterEntityFactory::createRowFromArray($value);
            $writer->addRow($rowFromValues);
        }

		// Customizing the sheet name when writing
		$sheet = $writer->getCurrentSheet();
		$sheet->setName('Balance Sheet');
        
        $writer->close();		
    }

	public function read_menu($rowid){
		
			$sql = "SELECT
						titre
					FROM
						db_menu
					where module = 'reporting' and rowid = $rowid and type = 'left';";			
			$result=$this->db->query($sql);			
			$data = array();
			while ($row = $result->fetch_array(MYSQLI_ASSOC)) {           
				$data[$row['rowid']] = $row;
			}

			$this->db->free($result);

			$d = array_shift($data);

			return $d['titre'];						
	}

	public function read_data(){
		
		$bb = new BalanceSheet($db);
		$o = $bb->get_balancesheet();
		return $o;
	}
}
