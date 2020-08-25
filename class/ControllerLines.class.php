<?php
class ControllerLines {

	public $db;
	public $OBJECT_COMMANDE = "commande";
	public $OBJECT_PROPAL = "propal";
	public $OBJECT_FACTURE = "facture";
	public $jsonResponse ;
	public $currentElement;
	public $qty;
	public $currentQty;
	public $langs;
	public $fk_fpc_object;
	public $idproduct;
	public $error = 0;
	public $errormysql = -1;
	public $obj;

	public function __construct($db,$langs){
		$this->db = $db;
		$this->jsonResponse = new stdClass();
		$this->langs = $langs;
	}

	/**
	 * @param $qty
	 * @param $currentQty
	 * @param $idproduct
	 * @param $element  /context object Facture / Propale / commande
	 */
	public function init($fk_fpc_object,$qty,$currentQty,$idproduct,$element,$obj){

		$this->qty = $qty;
		$this->currentQty = $currentQty;
		$this->idproduct = $idproduct;
		$this->currentElement = $element;
		$this->fk_fpc_object = $fk_fpc_object;
		$this->obj = $obj;
		$this->checkPositiveQty();
		$this->checkProduct();

	}

	public function processInput(){

		if (!$this->jsonResponse->error) {


			$this->obj->fetch($this->fk_fpc_object);

			$p = new Product($this->db);
			$p->fetch($this->idproduct);

			$updated = false;

			// Itération sur les lignes de Facture/Propal/Commande
			foreach ($this->obj->lines as $l) {
				if ($l->fk_product == $this->idproduct) {
					$res = $this->db->getRow("select price FROM llx_product_price WHERE fk_product = " . $this->idproduct . ' ORDER BY date_price DESC');
					if ($res > 0) {
						// On cherche à supprimer la ligne active
						if ($this->qty == 0) {
							$this->errormysql = $this->deleteLineOfObject($this->obj, $l->id);
							$updated = true;
							break;
						} else {
							// On créé un objet $values contenant toutes les infos nécessaires pour l'update de TOUS les éléments FPC
							$values = $this->prepareValues($l, $this->qty, $res, $p);
							// On update
							$this->errormysql = $this->updateLineInObject($this->obj, $values);
							$updated = true;
							break;
						}
					}
				}
			}
			// On ajoute la ligne, si elle n'est pas présente dans le current FPC
			if (!$updated) {
				$res = $this->db->getRow("select price , price_ttc  FROM llx_product_price WHERE fk_product = " . $this->idproduct . ' ORDER BY date_price DESC');
				if ($res > 0) {
					// On créé un objet $values contenant toutes les infos nécessaires pour l'update de TOUS les éléments FPC
					$values = $this->prepareValues($l, $this->qty, $res, $p, true);
					$this->errormysql = $this->addLineInObject($this->obj, $values, $this->obj->element);
				} else {
					$this->error++;
				}
			}
		}

	}


	public function checkPositiveQty(){

		if ($this->qty < 0 ) {
			$this->jsonResponse->error = $this->langs->trans("NegativeNumberError");
			$this->jsonResponse->currentQty = $this->currentQty;
		}
	}

	public function checkProduct(){
		if  (empty($this->idproduct)){
			$this->jsonResponse->error = $this->langs->trans("NoProductError");
		}
	}

	/**
	 * Update une ligne dans un Objet de type FPC
	 * @param          $currentObj
	 * @param stdClass $values
	 * @return int $error  1 = OK /  < 0 = erreur
	 */
	function updateLineInObject (&$currentObj, stdClass $values){

		/** @var Commande $currentObj */
		if ($currentObj->element == $this->OBJECT_COMMANDE) {
			return $currentObj->updateline($values->rowid, $values->desc, $values->pu, $values->qty, $values->remise_percent, $values->txtva);
		}
		/** @var Propal $currentObj */
		if ($currentObj->element == $this->OBJECT_PROPAL) {
			return $currentObj->updateline($values->rowid, $values->pu, $values->qty, $values->remise_percent, $values->txtva);
		}
		/** @var Facture $currentObj */
		if ($currentObj->element == $this->OBJECT_FACTURE)  {
			return $currentObj->updateline($values->rowid, $values->desc, $values->pu, $values->qty, $values->remise_percent, $values->date_start, $values->date_end, $values->txtva);
		}
	}

	/**
	 * Ajouter une ligne dans un Objet de type FPC
	 * @param          $currentObj
	 * @param stdClass $values
	 * @return int $error  1 = OK /  < 0 = erreur
	 */
	function addLineInObject (&$currentObj, stdClass $values, $element){


		if($element == $this->OBJECT_COMMANDE) {
			return $currentObj->addLine(
				$values->desc,
				$values->pu,
				$values->qty,
				$values->txtva,
				'',
				'',
				$values->idproduct,
				'',
				'',
				'',
				$values->price_base_type,
				$values->pu_ttc);
		}
		if($element == $this->OBJECT_FACTURE) {
			return $currentObj->addLine(
				$values->desc,
				$values->pu,
				$values->qty,
				$values->txtva,
				'',
				'',
				$values->idproduct,
				'',
				'',
				'',
				0,
				0,
				'',
				$values->price_base_type,
				$values->pu_ttc);
		}
		if($element == $this->OBJECT_PROPAL) {
			return $currentObj->addLine(
				$values->desc,
				$values->pu,
				$values->qty,
				$values->txtva,
				'',
				'',
				$values->idproduct,
				'',
				$values->price_base_type,
				$values->pu_ttc);
		}

	}

	/**
	 * Supprimer une ligne dans un Objet de type FPC
	 * @param        $currentObj
	 * @param int    $idLine
	 * @return int $error  1 = OK /  < 0 = erreur
	 */
	function deleteLineOfObject (&$currentObj, $idLine){
		global  $user;

		if ($currentObj->element == $this->OBJECT_COMMANDE) {
			/** @var Commande $currentObj */
			return $currentObj->deleteLine($user, $idLine);

		}
		else {
			// fature / propal
			return $currentObj->deleteLine($idLine);
		}
	}

	/**
	 * Création d'objet contenant les valeurs nécessaires au CRU des objets FPC
	 * @param      $currentLine
	 * @param      $qty
	 * @param      $res
	 * @param      $product
	 * @param bool $add
	 * @return stdClass
	 */
	function prepareValues($currentLine, $qty, $res, $product, $add = false) {
		$values = new stdClass();
		$values->idproduct = $product->id ? $product->id : null;
		$values->rowid = $currentLine->id;
		$values->pu = $res->price;
		$values->qty = $qty;
		$values->date_start = null;
		$values->date_end = null;
		if ($add) {
			$values->desc = $product->label;
			$values->remise_percent = $product->remise_percent;
			$values->txtva = $product->tva_tx;
			$values->pu_ttc = $res->price_ttc; // Obligatoire pour que s'affiche le prix HT dans la fiche du FPC
		}
		else {
			$values->desc = $currentLine->desc;
			$values->remise_percent = $currentLine->remise_percent;
			$values->txtva = $currentLine->tva_tx;
		}

		return $values;
	}

}