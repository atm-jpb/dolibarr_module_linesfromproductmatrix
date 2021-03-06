<?php
/* Copyright (C) 2017  Laurent Destailleur <eldy@users.sourceforge.net>
 * Copyright (C) ---Put here your own copyright and developer email---
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
 * \file        class/bloc.class.php
 * \ingroup     linesfromproductmatrix
 * \brief       This file is a CRUD class file for Bloc (Create/Read/Update/Delete)
 */

// Put here all includes required by your class file
require_once DOL_DOCUMENT_ROOT.'/core/class/commonobject.class.php';
require_once DOL_DOCUMENT_ROOT.'/custom/linesfromproductmatrix/class/matrix.class.php';
//require_once DOL_DOCUMENT_ROOT . '/societe/class/societe.class.php';
require_once DOL_DOCUMENT_ROOT . '/product/class/product.class.php';


/**
 * Class for Bloc
 */
class Bloc extends CommonObject
{
	/**
	 * @var string ID to identify managed object.
	 */
	public $element = 'bloc';

	/**
	 * @var string Name of table without prefix where object is stored. This is also the key used for extrafields management.
	 */
	public $table_element = 'linesfromproductmatrix_bloc';

	/**
	 * @var int  Does this object support multicompany module ?
	 * 0=No test on entity, 1=Test with field entity, 'field@table'=Test with link by field@table
	 */
	public $ismultientitymanaged = 0;

	/**
	 * @var int  Does object support extrafields ? 0=No, 1=Yes
	 */
	public $isextrafieldmanaged = 1;

	/**
	 * @var string String with name of icon for bloc. Must be the part after the 'object_' into object_bloc.png
	 */
	public $picto = 'bloc@linesfromproductmatrix';

	/**
	 * TODO gérer les status pour affichage et autre
	 */
	const STATUS_DRAFT = 0;
	const STATUS_VALIDATED = 1;
	const STATUS_CANCELED = 9;


	/**
	 *  'type' if the field format ('integer', 'integer:ObjectClass:PathToClass[:AddCreateButtonOrNot[:Filter]]', 'varchar(x)', 'double(24,8)', 'real', 'price', 'text', 'html', 'date', 'datetime', 'timestamp', 'duration', 'mail', 'phone', 'url', 'password')
	 *         Note: Filter can be a string like "(t.ref:like:'SO-%') or (t.date_creation:<:'20160101') or (t.nature:is:NULL)"
	 *  'label' the translation key.
	 *  'enabled' is a condition when the field must be managed (Example: 1 or '$conf->global->MY_SETUP_PARAM)
	 *  'position' is the sort order of field.
	 *  'notnull' is set to 1 if not null in database. Set to -1 if we must set data to null if empty ('' or 0).
	 *  'visible' says if field is visible in list (Examples: 0=Not visible, 1=Visible on list and create/update/view forms, 2=Visible on list only, 3=Visible on create/update/view form only (not list), 4=Visible on list and update/view form only (not create). 5=Visible on list and view only (not create/not update). Using a negative value means field is not shown by default on list but can be selected for viewing)
	 *  'noteditable' says if field is not editable (1 or 0)
	 *  'default' is a default value for creation (can still be overwrote by the Setup of Default Values if field is editable in creation form). Note: If default is set to '(PROV)' and field is 'ref', the default value will be set to '(PROVid)' where id is rowid when a new record is created.
	 *  'index' if we want an index in database.
	 *  'foreignkey'=>'tablename.field' if the field is a foreign key (it is recommanded to name the field fk_...).
	 *  'searchall' is 1 if we want to search in this field when making a search from the quick search button.
	 *  'isameasure' must be set to 1 if you want to have a total on list for this field. Field type must be summable like integer or double(24,8).
	 *  'css' is the CSS style to use on field. For example: 'maxwidth200'
	 *  'help' is a string visible as a tooltip on field
	 *  'showoncombobox' if value of the field must be visible into the label of the combobox that list record
	 *  'disabled' is 1 if we want to have the field locked by a 'disabled' attribute. In most cases, this is never set into the definition of $fields into class, but is set dynamically by some part of code.
	 *  'arraykeyval' to set list of value if type is a list of predefined values. For example: array("0"=>"Draft","1"=>"Active","-1"=>"Cancel")
	 *  'autofocusoncreate' to have field having the focus on a create form. Only 1 field should have this property set to 1.
	 *  'comment' is not used. You can store here any text of your choice. It is not used by application.
	 *
	 *  Note: To have value dynamic, you can set value to 0 in definition and edit the value on the fly into the constructor.
	 */

	// BEGIN MODULEBUILDER PROPERTIES
	/**
	 * @var array  Array with all fields and their property. Do not use it as a static var. It may be modified by constructor.
	 */
	public $fields=array(
		'rowid' => array('type'=>'integer', 'label'=>'TechnicalID', 'enabled'=>'1', 'position'=>1, 'notnull'=>1, 'visible'=>0, 'noteditable'=>'1', 'index'=>1, 'comment'=>"Id"),
		'ref' => array('type'=>'varchar(128)', 'label'=>'Ref', 'enabled'=>'1', 'position'=>10, 'notnull'=>1, 'visible'=>-2, 'index'=>1, 'searchall'=>1, 'showoncombobox'=>'1', 'comment'=>"Reference of object"),
		'label' => array('type'=>'varchar(255)', 'label'=>'Label', 'enabled'=>'1', 'position'=>30, 'notnull'=>0, 'visible'=>1, 'searchall'=>1, 'css'=>'minwidth200', 'help'=>"Veuillez renseigner le nom du bloc", 'showoncombobox'=>'1',),
		'fk_rank' => array('type'=>'integer', 'label'=>'fk_rank', 'enabled'=>'1', 'position'=>1, 'notnull'=>1, 'visible'=>0, 'noteditable'=>'1', 'index'=>1, 'comment'=>"fk_rank"),
		'date_creation' => array('type'=>'datetime', 'label'=>'DateCreation', 'enabled'=>'1', 'position'=>500, 'notnull'=>1, 'visible'=>-2,),
	);
	public $rowid;
	public $ref;
	public $label;
	public $date_creation;
	public $fk_status;  // actif ou inactif  (changer $this->status en $this->fk_status )
	public $tms;
	public $fk_rank;
	public $fk_user_creat;
	public $fk_user_modif;
	public $displayMatrix = array();
	public $currentBloc;
	public $THCols = array();
	public $THRows = array();
  	public $langs;

	// END MODULEBUILDER PROPERTIES


	// If this object has a subtable with lines

	/**
	 * @var int    Name of subtable line
	 */
	//public $table_element_line = 'linesfromproductmatrix_blocline';

	/**
	 * @var int    Field with ID of parent key if this object has a parent
	 */
	//public $fk_element = 'fk_bloc';

	/**
	 * @var int    Name of subtable class that manage subtable lines
	 */
	//public $class_element_line = 'Blocline';

	/**
	 * @var array	List of child tables. To test if we can delete object.
	 */
	//protected $childtables = array();

	/**
	 * @var array    List of child tables. To know object to delete on cascade.
	 *               If name matches '@ClassNAme:FilePathClass;ParentFkFieldName' it will
	 *               call method deleteByParentField(parentId, ParentFkFieldName) to fetch and delete child object
	 */
	//protected $childtablesoncascade = array('linesfromproductmatrix_blocdet');

	/**
	 * @var BlocLine[]     Array of subtable lines
	 */
	//public $lines = array();



	/**
	 * Constructor
	 *
	 * @param DoliDb $db Database handler
	 */
	public function __construct(DoliDB $db)
	{
		$this->date_creation = time();
		global $conf, $langs;

		$this->db = $db;

		if (empty($conf->global->MAIN_SHOW_TECHNICAL_ID) && isset($this->fields['rowid'])) $this->fields['rowid']['visible'] = 0;
		if (empty($conf->multicompany->enabled) && isset($this->fields['entity'])) $this->fields['entity']['enabled'] = 0;

		// Example to show how to set values of fields definition dynamically
		/*if ($user->rights->linesfromproductmatrix->bloc->read) {
			$this->fields['myfield']['visible'] = 1;
			$this->fields['myfield']['noteditable'] = 0;
		}*/

		// Unset fields that are disabled
		foreach ($this->fields as $key => $val)
		{
			if (isset($val['enabled']) && empty($val['enabled']))
			{
				unset($this->fields[$key]);
			}
		}

		// Translate some data of arrayofkeyval
		if (is_object($langs))
		{
			foreach ($this->fields as $key => $val)
			{
				if (is_array($val['arrayofkeyval']))
				{
					foreach ($val['arrayofkeyval'] as $key2 => $val2)
					{
						$this->fields[$key]['arrayofkeyval'][$key2] = $langs->trans($val2);
					}
				}
			}
		}
		$this->langs = $langs;
	}

	/**
	 * Create object into database
	 *
	 * @param  User $user      User that creates
	 * @param  bool $notrigger false=launch triggers after, true=disable triggers
	 * @return int             <0 if KO, Id of created object if OK
	 */
	public function create(User $user, $notrigger = false)
	{
		return $this->createCommon($user, $notrigger);
	}

	/**
	 * Clone an object into another one
	 *
	 * @param  	User 	$user      	User that creates
	 * @param  	int 	$fromid     Id of object to clone
	 * @return 	mixed 				New object created, <0 if KO
	 */
	public function createFromClone(User $user, $fromid)
	{
		global $langs, $extrafields;
		$error = 0;

		dol_syslog(__METHOD__, LOG_DEBUG);

		$object = new self($this->db);

		$this->db->begin();

		// Load source object
		$result = $object->fetchCommon($fromid);
		if ($result > 0 && !empty($object->table_element_line)) $object->fetchLines();

		// get lines so they will be clone
		//foreach($this->lines as $line)
		//	$line->fetch_optionals();

		// Reset some properties
		unset($object->id);
		unset($object->fk_user_creat);
		unset($object->import_key);


		// Clear fields
		$object->ref = empty($this->fields['ref']['default']) ? "copy_of_".$object->ref : $this->fields['ref']['default'];
		$object->label = empty($this->fields['label']['default']) ? $langs->trans("CopyOf")." ".$object->label : $this->fields['label']['default'];
		$object->status = self::STATUS_DRAFT;
		// ...
		// Clear extrafields that are unique
		if (is_array($object->array_options) && count($object->array_options) > 0)
		{
			$extrafields->fetch_name_optionals_label($this->table_element);
			foreach ($object->array_options as $key => $option)
			{
				$shortkey = preg_replace('/options_/', '', $key);
				if (!empty($extrafields->attributes[$this->element]['unique'][$shortkey]))
				{
					//var_dump($key); var_dump($clonedObj->array_options[$key]); exit;
					unset($object->array_options[$key]);
				}
			}
		}

		// Create clone
		$object->context['createfromclone'] = 'createfromclone';
		$result = $object->createCommon($user);
		if ($result < 0) {
			$error++;
			$this->error = $object->error;
			$this->errors = $object->errors;
		}

		if (!$error)
		{
			// copy internal contacts
			if ($this->copy_linked_contact($object, 'internal') < 0)
			{
				$error++;
			}
		}

		if (!$error)
		{
			// copy external contacts if same company
			if (property_exists($this, 'socid') && $this->socid == $object->socid)
			{
				if ($this->copy_linked_contact($object, 'external') < 0)
					$error++;
			}
		}

		unset($object->context['createfromclone']);

		// End
		if (!$error) {
			$this->db->commit();
			return $object;
		} else {
			$this->db->rollback();
			return -1;
		}
	}

	/**
	 * Load object in memory from the database
	 *
	 * @param int    $id   Id object
	 * @param string $ref  Ref
	 * @return int         <0 if KO, 0 if not found, >0 if OK
	 */
	public function fetch($id, $ref = null)
	{
		$result = $this->fetchCommon($id, $ref);
		if ($result > 0 && !empty($this->table_element_line)) $this->fetchLines();
		return $result;
	}

	/**
	 * Load object lines in memory from the database
	 *
	 * @return int         <0 if KO, 0 if not found, >0 if OK
	 */
	public function fetchLines()
	{
		$this->lines = array();

		$result = $this->fetchLinesCommon();
		return $result;
	}


	/**
	 * Load list of objects in memory from the database.
	 *
	 * @param  string      $sortorder    Sort Order
	 * @param  string      $sortfield    Sort field
	 * @param  int         $limit        limit
	 * @param  int         $offset       Offset
	 * @param  array       $filter       Filter array. Example array('field'=>'valueforlike', 'customurl'=>...)
	 * @param  string      $filtermode   Filter mode (AND or OR)
	 * @return array|int                 int <0 if KO, array of pages if OK
	 */
	public function fetchAll($sortorder = '', $sortfield = '', $limit = 0, $offset = 0, array $filter = array(), $filtermode = 'AND')
	{
		global $conf;

		dol_syslog(__METHOD__, LOG_DEBUG);

		$records = array();

		$sql = 'SELECT ';
		$sql .= $this->getFieldList();
		$sql .= ' FROM '.MAIN_DB_PREFIX.$this->table_element.' as t';
		if (isset($this->ismultientitymanaged) && $this->ismultientitymanaged == 1) $sql .= ' WHERE t.entity IN ('.getEntity($this->table_element).')';
		else $sql .= ' WHERE 1 = 1';
		// Manage filter
		$sqlwhere = array();
		if (count($filter) > 0) {
			foreach ($filter as $key => $value) {
				if ($key == 't.rowid') {
					$sqlwhere[] = $key.'='.$value;
				}
				elseif (strpos($key, 'date') !== false) {
					$sqlwhere[] = $key.' = \''.$this->db->idate($value).'\'';
				}
				elseif ($key == 'customsql') {
					$sqlwhere[] = $value;
				}
				else {
					$sqlwhere[] = $key.' LIKE \'%'.$this->db->escape($value).'%\'';
				}
			}
		}
		if (count($sqlwhere) > 0) {
			$sql .= ' AND ('.implode(' '.$filtermode.' ', $sqlwhere).')';
		}

		if (!empty($sortfield)) {
			$sql .= $this->db->order($sortfield, $sortorder);
		}
		if (!empty($limit)) {
			$sql .= ' '.$this->db->plimit($limit, $offset);
		}

		$resql = $this->db->query($sql);
		if ($resql) {
			$num = $this->db->num_rows($resql);
			$i = 0;
			while ($i < ($limit ? min($limit, $num) : $num))
			{
				$obj = $this->db->fetch_object($resql);

				$record = new self($this->db);
				$record->setVarsFromFetchObj($obj);

				$records[$record->id] = $record;

				$i++;
			}
			$this->db->free($resql);

			return $records;
		} else {
			$this->errors[] = 'Error '.$this->db->lasterror();
			dol_syslog(__METHOD__.' '.join(',', $this->errors), LOG_ERR);

			return -1;
		}
	}

	/**
	 * Update object into database
	 *
	 * @param  User $user      User that modifies
	 * @param  bool $notrigger false=launch triggers after, true=disable triggers
	 * @return int             <0 if KO, >0 if OK
	 */
	public function update(User $user, $notrigger = false)
	{
		return $this->updateCommon($user, $notrigger);
	}

	/**
	 * Delete object in database
	 *
	 * @param User $user       User that deletes
	 * @param bool $notrigger  false=launch triggers after, true=disable triggers
	 * @return int             <0 if KO, >0 if OK
	 */
	public function delete(User $user, $notrigger = false)
	{

		$bh = new Blochead($this->db);
		$res1 = $bh->db->query('DELETE FROM ' . MAIN_DB_PREFIX . 'linesfromproductmatrix_blochead' . ' WHERE ' . $this->fk_bloc . ' = ' . $this->id);
		// Delete record in child table

        $m = new Matrix($this->db);
		$res2 = $bh->db->query('DELETE FROM ' . MAIN_DB_PREFIX . 'linesfromproductmatrix_matrix' . ' WHERE ' . $this->fk_bloc . ' = ' . $this->id);


		return $this->deleteCommon($user, $notrigger);


		//return $this->deleteCommon($user, $notrigger, 1);


	}

	/**
	 *  Delete a line of object in database
	 *
	 *	@param  User	$user       User that delete
	 *  @param	int		$idline		Id of line to delete
	 *  @param 	bool 	$notrigger  false=launch triggers after, true=disable triggers
	 *  @return int         		>0 if OK, <0 if KO
	 */
	public function deleteLine(User $user, $idline, $notrigger = false)
	{
		if ($this->status < 0)
		{
			$this->error = 'ErrorDeleteLineNotAllowedByObjectStatus';
			return -2;
		}

		return $this->deleteLineCommon($user, $idline, $notrigger);
	}


	/**
	 *	Validate object
	 *
	 *	@param		User	$user     		User making status change
	 *  @param		int		$notrigger		1=Does not execute triggers, 0= execute triggers
	 *	@return  	int						<=0 if OK, 0=Nothing done, >0 if KO
	 */
	public function validate($user, $notrigger = 0)
	{
		global $conf, $langs;

		require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';

		$error = 0;

		// Protection
		if ($this->status == self::STATUS_VALIDATED)
		{
			dol_syslog(get_class($this)."::validate action abandonned: already validated", LOG_WARNING);
			return 0;
		}

		/*if (! ((empty($conf->global->MAIN_USE_ADVANCED_PERMS) && ! empty($user->rights->linesfromproductmatrix->bloc->write))
		 || (! empty($conf->global->MAIN_USE_ADVANCED_PERMS) && ! empty($user->rights->linesfromproductmatrix->bloc->bloc_advance->validate))))
		 {
		 $this->error='NotEnoughPermissions';
		 dol_syslog(get_class($this)."::valid ".$this->error, LOG_ERR);
		 return -1;
		 }*/

		$now = dol_now();

		$this->db->begin();

		// Define new ref
		if (!$error && (preg_match('/^[\(]?PROV/i', $this->ref) || empty($this->ref))) // empty should not happened, but when it occurs, the test save life
		{
			$num = $this->getNextNumRef();
		}
		else
		{
			$num = $this->ref;
		}
		$this->newref = $num;

		if (!empty($num)) {
			// Validate
			$sql = "UPDATE ".MAIN_DB_PREFIX.$this->table_element;
			$sql .= " SET ref = '".$this->db->escape($num)."',";
			$sql .= " status = ".self::STATUS_VALIDATED;
			if (!empty($this->fields['date_validation'])) $sql .= ", date_validation = '".$this->db->idate($now)."',";
			if (!empty($this->fields['fk_user_valid'])) $sql .= ", fk_user_valid = ".$user->id;
			$sql .= " WHERE rowid = ".$this->id;

			dol_syslog(get_class($this)."::validate()", LOG_DEBUG);
			$resql = $this->db->query($sql);
			if (!$resql)
			{
				dol_print_error($this->db);
				$this->error = $this->db->lasterror();
				$error++;
			}

			if (!$error && !$notrigger)
			{
				// Call trigger
				$result = $this->call_trigger('BLOC_VALIDATE', $user);
				if ($result < 0) $error++;
				// End call triggers
			}
		}

		if (!$error)
		{
			$this->oldref = $this->ref;

			// Rename directory if dir was a temporary ref
			if (preg_match('/^[\(]?PROV/i', $this->ref))
			{
				// Now we rename also files into index
				$sql = 'UPDATE '.MAIN_DB_PREFIX."ecm_files set filename = CONCAT('".$this->db->escape($this->newref)."', SUBSTR(filename, ".(strlen($this->ref) + 1).")), filepath = 'bloc/".$this->db->escape($this->newref)."'";
				$sql .= " WHERE filename LIKE '".$this->db->escape($this->ref)."%' AND filepath = 'bloc/".$this->db->escape($this->ref)."' and entity = ".$conf->entity;
				$resql = $this->db->query($sql);
				if (!$resql) { $error++; $this->error = $this->db->lasterror(); }

				// We rename directory ($this->ref = old ref, $num = new ref) in order not to lose the attachments
				$oldref = dol_sanitizeFileName($this->ref);
				$newref = dol_sanitizeFileName($num);
				$dirsource = $conf->linesfromproductmatrix->dir_output.'/bloc/'.$oldref;
				$dirdest = $conf->linesfromproductmatrix->dir_output.'/bloc/'.$newref;
				if (!$error && file_exists($dirsource))
				{
					dol_syslog(get_class($this)."::validate() rename dir ".$dirsource." into ".$dirdest);

					if (@rename($dirsource, $dirdest))
					{
						dol_syslog("Rename ok");
						// Rename docs starting with $oldref with $newref
						$listoffiles = dol_dir_list($conf->linesfromproductmatrix->dir_output.'/bloc/'.$newref, 'files', 1, '^'.preg_quote($oldref, '/'));
						foreach ($listoffiles as $fileentry)
						{
							$dirsource = $fileentry['name'];
							$dirdest = preg_replace('/^'.preg_quote($oldref, '/').'/', $newref, $dirsource);
							$dirsource = $fileentry['path'].'/'.$dirsource;
							$dirdest = $fileentry['path'].'/'.$dirdest;
							@rename($dirsource, $dirdest);
						}
					}
				}
			}
		}

		// Set new ref and current status
		if (!$error)
		{
			$this->ref = $num;
			$this->status = self::STATUS_VALIDATED;
		}

		if (!$error)
		{
			$this->db->commit();
			return 1;
		}
		else
		{
			$this->db->rollback();
			return -1;
		}
	}


	/**
	 *	Set draft status
	 *
	 *	@param	User	$user			Object user that modify
	 *  @param	int		$notrigger		1=Does not execute triggers, 0=Execute triggers
	 *	@return	int						<0 if KO, >0 if OK
	 */
	public function setDraft($user, $notrigger = 0)
	{
		// Protection
		if ($this->status <= self::STATUS_DRAFT)
		{
			return 0;
		}

		/*if (! ((empty($conf->global->MAIN_USE_ADVANCED_PERMS) && ! empty($user->rights->linesfromproductmatrix->write))
		 || (! empty($conf->global->MAIN_USE_ADVANCED_PERMS) && ! empty($user->rights->linesfromproductmatrix->linesfromproductmatrix_advance->validate))))
		 {
		 $this->error='Permission denied';
		 return -1;
		 }*/

		return $this->setStatusCommon($user, self::STATUS_DRAFT, $notrigger, 'BLOC_UNVALIDATE');
	}

	/**
	 *	Set cancel status
	 *
	 *	@param	User	$user			Object user that modify
	 *  @param	int		$notrigger		1=Does not execute triggers, 0=Execute triggers
	 *	@return	int						<0 if KO, 0=Nothing done, >0 if OK
	 */
	public function cancel($user, $notrigger = 0)
	{
		// Protection
		if ($this->status != self::STATUS_VALIDATED)
		{
			return 0;
		}

		/*if (! ((empty($conf->global->MAIN_USE_ADVANCED_PERMS) && ! empty($user->rights->linesfromproductmatrix->write))
		 || (! empty($conf->global->MAIN_USE_ADVANCED_PERMS) && ! empty($user->rights->linesfromproductmatrix->linesfromproductmatrix_advance->validate))))
		 {
		 $this->error='Permission denied';
		 return -1;
		 }*/

		return $this->setStatusCommon($user, self::STATUS_CANCELED, $notrigger, 'BLOC_CLOSE');
	}

	/**
	 *	Set back to validated status
	 *
	 *	@param	User	$user			Object user that modify
	 *  @param	int		$notrigger		1=Does not execute triggers, 0=Execute triggers
	 *	@return	int						<0 if KO, 0=Nothing done, >0 if OK
	 */
	public function reopen($user, $notrigger = 0)
	{
		// Protection
		if ($this->status != self::STATUS_CANCELED)
		{
			return 0;
		}

		/*if (! ((empty($conf->global->MAIN_USE_ADVANCED_PERMS) && ! empty($user->rights->linesfromproductmatrix->write))
		 || (! empty($conf->global->MAIN_USE_ADVANCED_PERMS) && ! empty($user->rights->linesfromproductmatrix->linesfromproductmatrix_advance->validate))))
		 {
		 $this->error='Permission denied';
		 return -1;
		 }*/

		return $this->setStatusCommon($user, self::STATUS_VALIDATED, $notrigger, 'BLOC_REOPEN');
	}

	/**
	 *  Return a link to the object card (with optionaly the picto)
	 *
	 *  @param  int     $withpicto                  Include picto in link (0=No picto, 1=Include picto into link, 2=Only picto)
	 *  @param  string  $option                     On what the link point to ('nolink', ...)
	 *  @param  int     $notooltip                  1=Disable tooltip
	 *  @param  string  $morecss                    Add more css on link
	 *  @param  int     $save_lastsearch_value      -1=Auto, 0=No save of lastsearch_values when clicking, 1=Save lastsearch_values whenclicking
	 *  @return	string                              String with URL
	 */
	public function getNomUrl($withpicto = 0, $option = '', $notooltip = 0, $morecss = '', $save_lastsearch_value = -1)
	{
		global $conf, $langs, $hookmanager;

		if (!empty($conf->dol_no_mouse_hover)) $notooltip = 1; // Force disable tooltips

		$result = '';

		$label = '<u>'.$langs->trans("Bloc").'</u>';
		$label .= '<br>';
		$label .= '<b>'.$langs->trans('Ref').':</b> '.$this->ref;
		if (isset($this->status)) {
			$label .= '<br><b>'.$langs->trans("Status").":</b> ".$this->getLibStatut(5);
		}

		$url = dol_buildpath('/linesfromproductmatrix/bloc_card.php', 1).'?id='.$this->id;

		if ($option != 'nolink')
		{
			// Add param to save lastsearch_values or not
			$add_save_lastsearch_values = ($save_lastsearch_value == 1 ? 1 : 0);
			if ($save_lastsearch_value == -1 && preg_match('/list\.php/', $_SERVER["PHP_SELF"])) $add_save_lastsearch_values = 1;
			if ($add_save_lastsearch_values) $url .= '&save_lastsearch_values=1';
		}

		$linkclose = '';
		if (empty($notooltip))
		{
			if (!empty($conf->global->MAIN_OPTIMIZEFORTEXTBROWSER))
			{
				$label = $langs->trans("ShowBloc");
				$linkclose .= ' alt="'.dol_escape_htmltag($label, 1).'"';
			}
			$linkclose .= ' title="'.dol_escape_htmltag($label, 1).'"';
			$linkclose .= ' class="classfortooltip'.($morecss ? ' '.$morecss : '').'"';
		}
		else $linkclose = ($morecss ? ' class="'.$morecss.'"' : '');

		$linkstart = '<a href="'.$url.'"';
		$linkstart .= $linkclose.'>';
		$linkend = '</a>';

		$result .= $linkstart;

		if (empty($this->showphoto_on_popup)) {
			if ($withpicto) $result .= img_object(($notooltip ? '' : $label), ($this->picto ? $this->picto : 'generic'), ($notooltip ? (($withpicto != 2) ? 'class="paddingright"' : '') : 'class="'.(($withpicto != 2) ? 'paddingright ' : '').'classfortooltip"'), 0, 0, $notooltip ? 0 : 1);
		} else {
			if ($withpicto) {
				require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';

				list($class, $module) = explode('@', $this->picto);
				$upload_dir = $conf->$module->multidir_output[$conf->entity]."/$class/".dol_sanitizeFileName($this->ref);
				$filearray = dol_dir_list($upload_dir, "files");
				$filename = $filearray[0]['name'];
				if (!empty($filename)) {
					$pospoint = strpos($filearray[0]['name'], '.');

					$pathtophoto = $class.'/'.$this->ref.'/thumbs/'.substr($filename, 0, $pospoint).'_mini'.substr($filename, $pospoint);
					if (empty($conf->global->{strtoupper($module.'_'.$class).'_FORMATLISTPHOTOSASUSERS'})) {
						$result .= '<div class="floatleft inline-block valignmiddle divphotoref"><div class="photoref"><img class="photo'.$module.'" alt="No photo" border="0" src="'.DOL_URL_ROOT.'/viewimage.php?modulepart='.$module.'&entity='.$conf->entity.'&file='.urlencode($pathtophoto).'"></div></div>';
					}
					else {
						$result .= '<div class="floatleft inline-block valignmiddle divphotoref"><img class="photouserphoto userphoto" alt="No photo" border="0" src="'.DOL_URL_ROOT.'/viewimage.php?modulepart='.$module.'&entity='.$conf->entity.'&file='.urlencode($pathtophoto).'"></div>';
					}

					$result .= '</div>';
				}
				else {
					$result .= img_object(($notooltip ? '' : $label), ($this->picto ? $this->picto : 'generic'), ($notooltip ? (($withpicto != 2) ? 'class="paddingright"' : '') : 'class="'.(($withpicto != 2) ? 'paddingright ' : '').'classfortooltip"'), 0, 0, $notooltip ? 0 : 1);
				}
			}
		}

		if ($withpicto != 2) $result .= $this->ref;

		$result .= $linkend;
		//if ($withpicto != 2) $result.=(($addlabel && $this->label) ? $sep . dol_trunc($this->label, ($addlabel > 1 ? $addlabel : 0)) : '');

		global $action, $hookmanager;
		$hookmanager->initHooks(array('blocdao'));
		$parameters = array('id'=>$this->id, 'getnomurl'=>$result);
		$reshook = $hookmanager->executeHooks('getNomUrl', $parameters, $this, $action); // Note that $action and $object may have been modified by some hooks
		if ($reshook > 0) $result = $hookmanager->resPrint;
		else $result .= $hookmanager->resPrint;

		return $result;
	}

	/**
	 *  Return label of the status
	 *
	 *  @param  int		$mode          0=long label, 1=short label, 2=Picto + short label, 3=Picto, 4=Picto + long label, 5=Short label + Picto, 6=Long label + Picto
	 *  @return	string 			       Label of status
	 */
	public function getLibStatut($mode = 0)
	{
		return $this->LibStatut($this->status, $mode);
	}

	// phpcs:disable PEAR.NamingConventions.ValidFunctionName.ScopeNotCamelCaps
	/**
	 *  Return the status
	 *
	 *  @param	int		$status        Id status
	 *  @param  int		$mode          0=long label, 1=short label, 2=Picto + short label, 3=Picto, 4=Picto + long label, 5=Short label + Picto, 6=Long label + Picto
	 *  @return string 			       Label of status
	 */
	public function LibStatut($status, $mode = 0)
	{
		// phpcs:enable
		if (empty($this->labelStatus) || empty($this->labelStatusShort))
		{
			global $langs;
			//$langs->load("linesfromproductmatrix");
			$this->labelStatus[self::STATUS_DRAFT] = $langs->trans('Draft');
			$this->labelStatus[self::STATUS_VALIDATED] = $langs->trans('Enabled');
			$this->labelStatus[self::STATUS_CANCELED] = $langs->trans('Disabled');
			$this->labelStatusShort[self::STATUS_DRAFT] = $langs->trans('Draft');
			$this->labelStatusShort[self::STATUS_VALIDATED] = $langs->trans('Enabled');
			$this->labelStatusShort[self::STATUS_CANCELED] = $langs->trans('Disabled');
		}

		$statusType = 'status'.$status;
		//if ($status == self::STATUS_VALIDATED) $statusType = 'status1';
		if ($status == self::STATUS_CANCELED) $statusType = 'status6';

		return dolGetStatus($this->labelStatus[$status], $this->labelStatusShort[$status], '', $statusType, $mode);
	}

	/**
	 *	Load the info information in the object
	 *
	 *	@param  int		$id       Id of object
	 *	@return	void
	 */
	public function info($id)
	{
		$sql = 'SELECT rowid, date_creation as datec, tms as datem,';
		$sql .= ' fk_user_creat, fk_user_modif';
		$sql .= ' FROM '.MAIN_DB_PREFIX.$this->table_element.' as t';
		$sql .= ' WHERE t.rowid = '.$id;
		$result = $this->db->query($sql);
		if ($result)
		{
			if ($this->db->num_rows($result))
			{
				$obj = $this->db->fetch_object($result);
				$this->id = $obj->rowid;
				if ($obj->fk_user_author)
				{
					$cuser = new User($this->db);
					$cuser->fetch($obj->fk_user_author);
					$this->user_creation = $cuser;
				}

				if ($obj->fk_user_valid)
				{
					$vuser = new User($this->db);
					$vuser->fetch($obj->fk_user_valid);
					$this->user_validation = $vuser;
				}

				if ($obj->fk_user_cloture)
				{
					$cluser = new User($this->db);
					$cluser->fetch($obj->fk_user_cloture);
					$this->user_cloture = $cluser;
				}

				$this->date_creation     = $this->db->jdate($obj->datec);
				$this->date_modification = $this->db->jdate($obj->datem);
				$this->date_validation   = $this->db->jdate($obj->datev);
			}

			$this->db->free($result);
		}
		else
		{
			dol_print_error($this->db);
		}
	}

	/**
	 * Initialise object with example values
	 * Id must be 0 if object instance is a specimen
	 *
	 * @return void
	 */
	public function initAsSpecimen()
	{
		$this->initAsSpecimenCommon();
	}

	/**require_once DOL_DOCUMENT_ROOT . '/htdocs/core/lib/ajax.lib.php';
	 * 	Create an array of lines
	 *
	 * 	@return array|int		array of lines if OK, <0 if KO
	 */
	public function getLinesArray()
	{
		$this->lines = array();

		$objectline = new BlocLine($this->db);
		$result = $objectline->fetchAll('ASC', 'position', 0, 0, array('customsql'=>'fk_bloc = '.$this->id));

		if (is_numeric($result))
		{
			$this->error = $this->error;
			$this->errors = $this->errors;
			return $result;
		}
		else
		{
			$this->lines = $result;
			return $this->lines;
		}
	}

	/**
	 *  Returns the reference to the following non used object depending on the active numbering module.
	 *
	 *  @return string      		Object free reference
	 */
	public function getNextNumRef()
	{
		global $langs, $conf;
		$langs->load("linesfromproductmatrix@bloc");

		if (empty($conf->global->LINESFROMPRODUCTMATRIX_BLOC_ADDON)) {
			$conf->global->LINESFROMPRODUCTMATRIX_BLOC_ADDON = 'mod_bloc_standard';
		}

		if (!empty($conf->global->LINESFROMPRODUCTMATRIX_BLOC_ADDON))
		{
			$mybool = false;

			$file = $conf->global->LINESFROMPRODUCTMATRIX_BLOC_ADDON.".php";
			$classname = $conf->global->LINESFROMPRODUCTMATRIX_BLOC_ADDON;

			// Include file with class
			$dirmodels = array_merge(array('/'), (array) $conf->modules_parts['models']);
			foreach ($dirmodels as $reldir)
			{
				$dir = dol_buildpath($reldir."core/modules/linesfromproductmatrix/");

				// Load file with numbering class (if found)
				$mybool |= @include_once $dir.$file;
			}

			if ($mybool === false)
			{
				dol_print_error('', "Failed to include file ".$file);
				return '';
			}

			if (class_exists($classname)) {
				$obj = new $classname();
				$numref = $obj->getNextValue($this);

				if ($numref != '' && $numref != '-1')
				{
					return $numref;
				}
				else
				{
					$this->error = $obj->error;
					//dol_print_error($this->db,get_class($this)."::getNextNumRef ".$obj->error);
					return "";
				}
			} else {
				print $langs->trans("Error")." ".$langs->trans("ClassNotFound").' '.$classname;
				return "";
			}
		}
		else
		{
			print $langs->trans("ErrorNumberingModuleNotSetup", $this->element);
			return "";
		}
	}


	/**
	 *  Create a document onto disk according to template module.
	 *
	 *  @param	    string		$modele			Force template to use ('' to not force)
	 *  @param		Translate	$outputlangs	objet lang a utiliser pour traduction
	 *  @param      int			$hidedetails    Hide details of lines
	 *  @param      int			$hidedesc       Hide description
	 *  @param      int			$hideref        Hide ref
	 *  @param      null|array  $moreparams     Array to provide more information
	 *  @return     int         				0 if KO, 1 if OK
	 */
	public function generateDocument($modele, $outputlangs, $hidedetails = 0, $hidedesc = 0, $hideref = 0, $moreparams = null)
	{
		global $conf, $langs;

		$result = 0;
		$includedocgeneration = 0;

		$langs->load("linesfromproductmatrix@linesfromproductmatrix");

		if (!dol_strlen($modele)) {
			$modele = 'standard_bloc';

			if ($this->modelpdf) {
				$modele = $this->modelpdf;
			} elseif (!empty($conf->global->BLOC_ADDON_PDF)) {
				$modele = $conf->global->BLOC_ADDON_PDF;
			}
		}

		$modelpath = "core/modules/linesfromproductmatrix/doc/";

		if ($includedocgeneration) {
			$result = $this->commonGenerateDocument($modelpath, $modele, $outputlangs, $hidedetails, $hidedesc, $hideref, $moreparams);
		}

		return $result;
	}

	/**
	 * Action executed by scheduler
	 * CAN BE A CRON TASK. In such a case, parameters come from the schedule job setup field 'Parameters'
	 * Use public function doScheduledJob($param1, $param2, ...) to get parameters
	 *
	 * @return	int			0 if OK, <>0 if KO (this function is used also by cron so only 0 is OK)
	 */
	public function doScheduledJob()
	{
		global $conf, $langs;

		//$conf->global->SYSLOG_FILE = 'DOL_DATA_ROOT/dolibarr_mydedicatedlofile.log';

		$error = 0;
		$this->output = '';
		$this->error = '';

		dol_syslog(__METHOD__, LOG_DEBUG);

		$now = dol_now();

		$this->db->begin();

		// ...

		$this->db->commit();

		return $error;
	}

	/**
	 * Load specific bloc
	 * @param $id
	 */
	public function fetchBloc($id){

		$b = new Bloc($this->db);
		$b->fetch($id);
		$this->fetchMatrix($b);
	}

	/**
	 *
	 * Retourne le template html d'un bloc et de sa matrice.
	 * @param Bloc $b
	 * @return string
	 */
	public function displayBloc(Bloc $b, $reloadBlocView = false, $mode = 'view'){
			global $user;
			$out = '';

			if (!$reloadBlocView) {
				$out .= '<div class="matrix-item" id="item-matrix' . $b->id . '" data-id="' . $b->id . '">';
			}

			$out .= '<div class="matrix-head">';

			if($mode == 'config' && $user->rights->linesfromproductmatrix->bloc->write) {
				$out .= '<input id="bloc-label-' . $b->id . '" class="inputBloc" type="text"  value="'.dol_htmlentities($b->label, ENT_QUOTES).'" name="bloclabel" data-id="' . $b->id . '">
						<a class="editfielda reposition" data-id="' . $b->id . '" href="#bloc-label-' . $b->id . '">
						<span id="' . $b->id . '" data-id="' . $b->id . '" class="fas fa-pencil-alt" title="Modifier"></span>
						<span id="' . $b->id . '" data-id="' . $b->id . '" class="fa fa-check" style="color:lightgrey; display: none" ></span>
						</a>';
			}else{
				$out .= '<span class="bloc-label">'.$b->label.'</span>';
			}

			 if($mode == 'config' && $user->rights->linesfromproductmatrix->bloc->delete) {
					$out .= '<a id="matrix-delete-' . $b->id . '">
						<span data-id="' . $b->id . '" class="fas fa-trash pictodelete pull-right classfortooltip" style="" title="Supprimer"></span>
						</a>
						</div>';
			}
			else
			{
				$out .= '</div>';
			}



		// Part to display a confirm message when delete a bloc OR matrix line/col
		$out .= '<div id="dialog-confirm" style="display:none" title="Confirmation de suppression">
		<p><span class="ui-icon ui-icon-alert" style="float:left; margin:12px 12px 20px 0;"></span>'.$this->langs->trans("msgDeleteAllDataForThisBloc").'</p>
		</div>';

		$out .= '<div id="deleteHead-confirm" style="display:none" title="'.$this->langs->trans("msgConfirmDelete").'">
		<p><span class="ui-icon ui-icon-alert" style="float:left; margin:12px 12px 20px 0;"></span>'.$this->langs->trans("msgDeleteAllDataForThisBlocMore").'</p>
		</div>';


		$b->fetchMatrix($b);
		$out .= $b->displayMatrix($mode);
		// FOOTER AJOUTER LIGNE COL
		if($mode == 'config' && $user->rights->linesfromproductmatrix->bloc->write) {
			$out .= '<div class="matrix-footer">';


			$out .= '<span data-type="1" data-id="' . $b->id . '" class="matrix-add-btn --line classfortooltip" title="'.$this->langs->trans("descriptionAddLine").'">';
			$out .= '<span class="fas fa-grip-lines"></span> ';
			$out .= $this->langs->trans('AddLineToMatrix');
			$out .= '</span>';

			$out .= '<span data-type="0" data-id="' . $b->id . '" class="matrix-add-btn --col classfortooltip">';
			$out .= '<span class="fas fa-grip-lines --rotate90neg" title="'.$this->langs->trans("descriptionAddCol").'"></span>';
		 	$out .= $this->langs->trans('AddColToMatrix');
			$out .= '</span>';

			$out .= '</div><!-- end .matrix-footer -->';
		}
			if (!$reloadBlocView) {
				$out .= '</div>';
			}



		return $out;
	}
	/**
	 *
	 *  Récupère les infos sur un bloc en db et crée la matrice de données.
	 * @param Bloc $bloc
	 */
	public function fetchMatrix(Bloc $bloc){
		$this->currentBloc = $bloc->id;
		$headBloc = new BlocHead($this->db);
		// récupèration de tous les éléments de la matrice de produit (ou autre obj ...)
		$m = new Matrix($this->db);
		$mm = $m->db->getRows('select * from '.MAIN_DB_PREFIX.'linesfromproductmatrix_matrix WHERE fk_bloc = '.$this->currentBloc);
		// classement des elements dans un array
		$Tmatrix = array();
		foreach ($mm as $key => $val){
			$Tmatrix[$val->fk_blochead_row][$val->fk_blochead_column] = $val->fk_product;
		}

		// chargement des headers col type : 0
		$this->THCols = $headBloc->db->getRows("SELECT rowid,fk_bloc,label,type,fk_rank FROM ".MAIN_DB_PREFIX."linesfromproductmatrix_blochead WHERE fk_bloc = ".$this->currentBloc ." AND type = '0' ORDER BY fk_rank ASC, rowid");

		// chargement des headers row type : 1
		$this->THRows = $headBloc->db->getRows("SELECT rowid,fk_bloc,label,type,fk_rank FROM ".MAIN_DB_PREFIX."linesfromproductmatrix_blochead WHERE fk_bloc = ".$this->currentBloc ." AND type = 1 ORDER BY fk_rank ASC, rowid");

		// on ajoute 1 pour tenir compte des [header col] et [header row]
		$nbCols = count($this->THCols) + 1;
		$nbRows = count($this->THRows) + 1;

		// on remplie la matrice d'affichage ( $this->displayMatrix ) sans formatage visuel.
		for($row = 0 ;$row < $nbRows;$row++){
			for($col = 0 ;$col < $nbCols; $col++){

				$matrixCell = new stdClass();
				$matrixCell->label = '';
				$matrixCell->type = -1;
				$matrixCell->fk_product = 0;
				$matrixCell->fk_blocHeaderCol = 0;
				$matrixCell->fk_blocHeaderRow = 0;



				// col label
				if ($row == 0  && $col > 0){
					$matrixCell->headId =  $this->THCols[$col-1]->rowid;
					$matrixCell->label = $this->THCols[$col-1]->label;
					$matrixCell->type = $this->THCols[$col-1]->type;
				}
				// row label
				if ($row > 0 && $col == 0){
					$matrixCell->headId =  $this->THRows[$row-1]->rowid;
					$matrixCell->label = $this->THRows[$row-1]->label;
					$matrixCell->type = $this->THRows[$row-1]->type;
				}
				// on tient compte du décalage dûe à la
				$rowMatrixKey = $row - 1;
				$colMatrixKey = $col - 1;

				if ($row == 0  && $col == 0){
					$matrixCell->label = '&nbsp;';
					$matrixCell->type = -2;
				}else{
						$matrixCell->fk_product = false;
						if(isset($this->THRows[$rowMatrixKey]) && isset($this->THCols[$colMatrixKey])){
							$matrixCell->fk_product = $Tmatrix[$this->THRows[$rowMatrixKey]->rowid][$this->THCols[$colMatrixKey]->rowid];
							$matrixCell->type = -1;
							//- stockage des ids headers
							$matrixCell->fk_blocHeaderCol = $this->THCols[$colMatrixKey]->rowid;
							$matrixCell->fk_blocHeaderRow = $this->THRows[$rowMatrixKey]->rowid;
					}
				}
				$this->displayMatrix[$row][$col] = $matrixCell;
			}
		}
		//------------------------------------------------------------------------------

		// TODO add hook

		//------------------------------------------------------------------

		//--------------------------------------------------------------------

	}

	/**
	 * affiche la matrice
	 * @param string $mode view | config
	 * @return string
	 */
	public function displayMatrix($mode = 'view'){

		global $user;
		$nbCols = count($this->THCols) + 1;
		$nbRows = count($this->THRows) + 1;
		$output = '';

		if ($this->THCols && $this->THRows) {
			$output  .= '<div class="bloc-table">';

			for ($row = 0; $row < $nbRows; $row++) {

				$output  .= '<div class="bloc-table-row">';

				for ($col = 0; $col < $nbCols; $col++) {

					$matrixCell = $this->displayMatrix[$row][$col];
					// Design fa icon en fonction du type de cellules
					// Si on est sur des headers colonnes
					if ($matrixCell->type == 0 ) {
						$output .= '<div class="bloc-table-cell bloc-table-head">';

						if($mode == 'config' && $user->rights->linesfromproductmatrix->bloc->delete) {
						$output .= '<a class="matrix-col-delete classfortooltip"  data-blocid="'.$this->id.'" data-id="'.$matrixCell->headId.'" title="'.$this->langs->trans("tooltipDeleteCol").'"><i class="fas fa-trash deleteHead pull-right"></i></a>';
						}
					}else{
						// Si on est sur des headers lignes
						if ($matrixCell->type > 0) {
							$output .= '<div class="bloc-table-cell">';
							if ($mode == 'config' && $user->rights->linesfromproductmatrix->bloc->delete) {
								$output .= '<a class="matrix-line-delete classfortooltip" data-type="' . $matrixCell->type . '" data-blocid="' . $this->id . '" data-id="' . $matrixCell->headId . '" title="'.$this->langs->trans("tooltipDeleteLine").'"><i class="fas fa-trash deleteHead"></i></a>';
							}
						}else {
								$output  .='<div class="bloc-table-cell">';
							}
						}

					if (!empty($matrixCell->overrideHtmlOutput)) {
						// probablement issue de la modification par un hook
						$output  .= $matrixCell->overrideHtmlOutput;
					} else {

						// AFFICHAGE PRODUIT
						if ($matrixCell->type === -1 ) {
							if ($mode == 'config' && $user->rights->linesfromproductmatrix->bloc->write) {
								// htmlname en premier
								$fkproduct = $matrixCell->fk_product ? $matrixCell->fk_product : '';
								$output .= $this->select_produits($matrixCell->fk_blocHeaderCol, $matrixCell->fk_blocHeaderRow, $fkproduct, 'idprod_' . $matrixCell->fk_blocHeaderCol . '_' . $matrixCell->fk_blocHeaderRow, '', 20, 0, 1, 2);
							}else {
								$output .= '<input class="classfortooltip" type="number" id="quantity-input" name="quantity" min="0" title="'.$this->langs->trans("quantityInput").'" placeholder="'.$this->langs->trans("quantity").'">';
							}
							//$output  .= $this->getSelectElement($matrixCell->fk_product,$matrixCell->fk_blocHeaderCol,$matrixCell->fk_blocHeaderRow);

						} else { // AFFICHAGE HEADER
								// COl/ROW label
								if ($matrixCell->type >= 0){
									if ($mode == 'config' && $user->rights->linesfromproductmatrix->bloc->write) {
										$output .= '<input placeholder="'.$this->langs->trans("YourLabelHere").'" data-currentValue="'.$matrixCell->label.'" required id="blocHead-label-' . $this->displayMatrix[$row][$col]->headId . '" class="input-bloc-header"  type="text" size="6" name="blocHeadlabel" data-idhead="' . $this->displayMatrix[$row][$col]->headId . '" value="'.dol_htmlentities($matrixCell->label, ENT_QUOTES).'" >';
									}else {
										$output .= $matrixCell->label;
										}
								}
						}
						// la affichage produit, headr etc...
					}
					$output  .='</div>';
				}
				$output  .='</div>';
			}
			$output  .='</div>';
		}
		$res = new stdClass();
		$res->out = $output;
  		return $output;

	}

	/**
	 *  DEPRECATED
	 *
	 * @param int $idproduct
	 * @param $headerColId
	 * @param $headerRowId
	 * @return string
	 */
	public function getSelectElement($idproduct = 0,$headerColId,$headerRowId){

		//var_dump($headerColId,$headerRowId);
		//linesfromproductmatrix_
		$p = new Product($this->db);
		$res = $p->db->getRows('SELECT rowid,label FROM '. MAIN_DB_PREFIX .'product');
		$output = '<select id="product-select-'. rand(0,200000) . '" data-blocheadercolid="'.$headerColId.'"data-blocheaderrowid="'.$headerRowId.'" data-blocid="'.$this->currentBloc.'" >';
		$output .= '<option value="">'.$this->langs->lang("chooseOption").'--Please choose an option--</option>';
		if ($res){
			foreach ($res as $element){
				$output .= '<option value="'.$element->rowid;

				if ($idproduct == $element->rowid){
					$output .= '" selected>'.$element->label.'</option>';
				}else{
					$output .= '">'.$element->label.'</option>';
				}

			}
			$output .='</select>';
			//var_dump($output);
		}
		return $output;
	}


	// phpcs:disable PEAR.NamingConventions.ValidFunctionName.ScopeNotCamelCaps
	/**
	 *  Return list of products for customer in Ajax if Ajax activated or go to select_produits_list
	 *
	 * @param       int        	$headerColId            id du header Col actif
	 * @param 		int 		$headerRowId			id du header Row actif
	 *  @param		int			$selected				Preselected products
	 *  @param		string		$htmlname				Name of HTML select field (must be unique in page)
	 *  @param		int			$filtertype				Filter on product type (''=nofilter, 0=product, 1=service)
	 *  @param		int			$limit					Limit on number of returned lines
	 *  @param		int			$price_level			Level of price to show
	 *  @param		int			$status					Sell status -1=Return all products, 0=Products not on sell, 1=Products on sell
	 *  @param		int			$finished				2=all, 1=finished, 0=raw material
	 *  @param		string		$selected_input_value	Value of preselected input text (for use with ajax)
	 *  @param		int			$hidelabel				Hide label (0=no, 1=yes, 2=show search icon (before) and placeholder, 3 search icon after)
	 *  @param		array		$ajaxoptions			Options for ajax_autocompleter
	 *  @param      int			$socid					Thirdparty Id (to get also price dedicated to this customer)
	 *  @param		string		$showempty				'' to not show empty line. Translation key to show an empty line. '1' show empty line with no text.
	 * 	@param		int			$forcecombo				Force to use combo box
	 *  @param      string      $morecss                Add more css on select
	 *  @param      int         $hidepriceinlabel       1=Hide prices in label
	 *  @param      string      $warehouseStatus        Warehouse status filter to count the quantity in stock. Following comma separated filter options can be used
	 *										            'warehouseopen' = count products from open warehouses,
	 *										            'warehouseclosed' = count products from closed warehouses,
	 *										            'warehouseinternal' = count products from warehouses for internal correct/transfer only
	 *  @param 		array 		$selected_combinations 	Selected combinations. Format: array([attrid] => attrval, [...])

	 *  @return		void
	 */
	public function select_produits($headerColId, $headerRowId, $selected = '', $htmlname = 'productid', $filtertype = '', $limit = 20, $price_level = 0, $status = 1, $finished = 2, $selected_input_value = '', $hidelabel = 0, $ajaxoptions = array(), $socid = 0, $showempty = '1', $forcecombo = 0, $morecss = '', $hidepriceinlabel = 0, $warehouseStatus = '', $selected_combinations = array())
	{


		// phpcs:enable
		global $langs, $conf;

		$conf->global->MAIN_AUTO_OPEN_SELECT2_ON_FOCUS_FOR_CUSTOMER_PRODUCTS = 1;
		$conf->global->JS_QUERY_AUTOCOMPLETE_RENDERITEM = 1;
		$conf->global->JS_QUERY_AUTOCOMPLETE_ITEM = 1;

		$out = '';
		// check parameters
		$price_level = (!empty($price_level) ? $price_level : 0);
		if (is_null($ajaxoptions)) {
			$ajaxoptions = array();
		}

		if (strval($filtertype) === '' && (!empty($conf->product->enabled) || !empty($conf->service->enabled))) {
			if (!empty($conf->product->enabled) && empty($conf->service->enabled)) {
				$filtertype = '0';
			}
			elseif (empty($conf->product->enabled) && !empty($conf->service->enabled)) {
				$filtertype = '1';
			}
		}


			$placeholder = '';

			if ($selected && empty($selected_input_value))
			{
				require_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';
				$producttmpselect = new Product($this->db);
				$producttmpselect->fetch($selected);
				$selected_input_value = $producttmpselect->ref;
				$selected_input_id= $producttmpselect->id;

				unset($producttmpselect);
			}
			// handle case where product or service module is disabled + no filter specified
			if ($filtertype == '')
			{
				if (empty($conf->product->enabled)) { // when product module is disabled, show services only
					$filtertype = 1;
				}
				elseif (empty($conf->service->enabled)) { // when service module is disabled, show products only
					$filtertype = 0;
				}
			}
			// mode=1 means customers products
			$urloption = 'htmlname='.$htmlname.'&outjson=1&price_level='.$price_level.'&type='.$filtertype.'&mode=1&status='.$status.'&finished='.$finished.'&hidepriceinlabel='.$hidepriceinlabel.'&warehousestatus='.$warehouseStatus;
			//Price by customer
			if (!empty($conf->global->PRODUIT_CUSTOMER_PRICES) && !empty($socid)) {
				$urloption .= '&socid='.$socid;
			}
			$out.=  ajax_autocompleter($selected, $htmlname, DOL_URL_ROOT.'/product/ajax/products.php', $urloption, $conf->global->PRODUIT_USE_SEARCH_TO_SELECT, 0, $ajaxoptions);

			if (!empty($conf->variants->enabled)) {
				$out.= '<script>
					//TODO  LET OR VAR
					var selected = '.json_encode($selected_combinations).';
					combvalues = {};
					jQuery(document).ready(function () {

						jQuery("input[name=\'prod_entry_mode\']").change(function () {
							if (jQuery(this).val() == \'free\') {
								jQuery(\'div#attributes_box\').empty();
							}
						});

						jQuery("input#'.$htmlname. '").change(function () {

							if (!jQuery(this).val()) {
								jQuery(\'div#attributes_box\').empty();
								return;
							}

							jQuery.getJSON("'.dol_buildpath('/variants/ajax/getCombinations.php', 2).'", {
								id: jQuery(this).val()

							}, function (data) {

								jQuery(\'div#attributes_box\').empty();

								// select option
								jQuery.each(data, function (key, val) {

									combvalues[val.id] = val.values;

									var span = jQuery(document.createElement(\'div\')).css({
										\'display\': \'table-row\'
									});

									span.append(
										jQuery(document.createElement(\'div\')).text(val.label).css({
											"fon-weight": "bold",
											"display": "table-cell",
											"text-align": "right"
										})
									);

									var html = jQuery(document.createElement(\'select\')).attr(\'name\', \'combinations[\' + val.id + \']\').css({
										\'margin-left\': \'15px\',
										\'white-space\': \'pre\'
									}).append(
										jQuery(document.createElement(\'option\')).val(\'\')
									);

									jQuery.each(combvalues[val.id], function (key, val) {
										var tag = jQuery(document.createElement(\'option\')).val(val.id).html(val.value);

										if (selected[val.fk_product_attribute] == val.id) {';
											$out .= 'tag.attr(\'selected\', \'selected\');
										}

										html.append(tag);
									});

									span.append(html);
									jQuery(\'div#attributes_box\').append(span);
								});
							})
						})  if ($selected){ jQuery("input#'.$htmlname.'").change() }

					});</script>';
			}

			$placeholder = ' placeholder="'.$langs->trans("RefOrLabel").'"';

			$out.=  '<input type="text" class="minwidth100 inputproductmatric" data-idproduct="'.$selected_input_id.'" name="search_'.$htmlname.  '" data-blocheadercolid="'.$headerColId.'"data-blocheaderrowid="'.$headerRowId.'" data-blocid="'.$this->currentBloc.'" id="search_'.$htmlname.'" value="'.dol_htmlentities($selected_input_value, ENT_QUOTES).'"'.$placeholder.' '.(!empty($conf->global->PRODUCT_SEARCH_AUTOFOCUS) ? 'autofocus' : '').' />';
			if ($hidelabel == 3) {
				$out.=  img_picto($langs->trans("Search"), 'search');
			}

			return $out;

	}


	private function getMaxCol(){


	}
	private function getMaxBloc(){

	}

}



/**
 * Class BlocLine. You can also remove this and generate a CRUD class for lines objects.
 */
class BlocLine
{
	// To complete with content of an object BlocLine
	// We should have a field rowid, fk_bloc and position
}
