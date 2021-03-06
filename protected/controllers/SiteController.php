<?php

class SiteController extends AntragsgruenController
{

	public $text_comments = false;

	/**
	 * @param string $veranstaltung_id
	 */
	public function actionIndex($veranstaltung_id = "")
	{
		try {
			if ($veranstaltung_id == "") $veranstaltung_id = Yii::app()->params['standardVeranstaltung'];
			$this->actionVeranstaltung($veranstaltung_id);
		} catch (CDbException $e) {
			echo "Es konnte keine Datenbankverbindung hergestellt werden.<br>";
			if (YII_DEBUG) echo "Die Fehlermeldung lautete:<blockquote>" . $e->getMessage() . "</blockquote>";
		}
	}

	/**
	 * @param string $veranstaltung_id
	 */
	public function actionImpressum($veranstaltung_id = "")
	{
		$this->loadVeranstaltung($veranstaltung_id);
		$this->render('content', array(
			"title"            => "Impressum",
			"breadcrumb_title" => "Impressum",
			"text"             => $this->veranstaltung->getStandardtext("impressum"),
		));
	}

	/**
	 * @param string $veranstaltung_id
	 */
	public function actionWartungsmodus($veranstaltung_id = "")
	{
		$this->loadVeranstaltung($veranstaltung_id);
		$this->render('content', array(
			"title"            => "Wartungsmodus",
			"breadcrumb_title" => "Wartungsmodus",
			"text"             => $this->veranstaltung->getStandardtext("wartungsmodus"),
		));
	}

	/**
	 *
	 */
	public function actionHilfe($veranstaltung_id = "")
	{
		$this->loadVeranstaltung($veranstaltung_id);
		$this->testeWartungsmodus();

		$this->render('content', array(
			"title"            => "Hilfe",
			"breadcrumb_title" => "Hilfe",
			"text"             => $this->veranstaltung->getStandardtext("hilfe"),
		));
	}

	/**
	 * @param string $veranstaltung_id
	 */
	public function actionPdfs($veranstaltung_id = "")
	{
		$this->loadVeranstaltung($veranstaltung_id);
		$this->testeWartungsmodus();

		$antraege = $this->veranstaltung->antraegeSortiert();
		$this->renderPartial('veranstaltung_pdfs', array(
			"sprache"       => $this->veranstaltung->getSprache(),
			"antraege"      => $antraege,
			"veranstaltung" => $this->veranstaltung,
		));
	}

	/**
	 * @param string $veranstaltung_id
	 */
	public function actionAenderungsantragsPdfs($veranstaltung_id = "")
	{
		$this->loadVeranstaltung($veranstaltung_id);
		$this->testeWartungsmodus();

		$criteria        = new CDbCriteria();
		$criteria->alias = "aenderungsantrag";
		$criteria->order = "LPAD(REPLACE(aenderungsantrag.revision_name, 'Ä', ''), 3, '0')";
		$criteria->addNotInCondition("aenderungsantrag.status", IAntrag::$STATI_UNSICHTBAR);
		$aenderungsantraege = Aenderungsantrag::model()->with(array(
			"antrag" => array('condition' => 'antrag.veranstaltung=' . IntVal($this->veranstaltung->id))
		))->findAll($criteria);

		$this->renderPartial('veranstaltung_ae_pdfs', array(
			"sprache"            => $this->veranstaltung->getSprache(),
			"aenderungsantraege" => $aenderungsantraege,
			"veranstaltung"      => $this->veranstaltung,
		));
	}


	/**
	 * @param Veranstaltung $veranstaltung
	 * @return array
	 */
	private function getFeedAntraegeData(&$veranstaltung)
	{
		$veranstaltung_id = IntVal($veranstaltung->id);

		$antraege = Antrag::holeNeueste($veranstaltung_id, 20);

		$data = array();
		foreach ($antraege as $ant) $data[AntraegeUtils::date_iso2timestamp($ant->datum_einreichung) . "_antrag_" . $ant->id] = array(
			"title"       => "Neuer Antrag: " . $ant->nameMitRev(),
			"link"        => Yii::app()->getBaseUrl(true) . $this->createUrl("antrag/anzeige", array("antrag_id" => $ant->id)),
			"dateCreated" => AntraegeUtils::date_iso2timestamp($ant->datum_einreichung),
			"content"     => "<h2>Antrag</h2>" . HtmlBBcodeUtils::bbcode2html($ant->text) . "<br>\n<br>\n<br>\n<h2>Begründung</h2>" . HtmlBBcodeUtils::bbcode2html($ant->begruendung),
		);
		return $data;
	}

	/**
	 * @param Veranstaltung $veranstaltung
	 * @return array
	 */
	private function getFeedAenderungsantraegeData(&$veranstaltung)
	{
		$veranstaltung_id = IntVal($veranstaltung->id);

		$antraege = Aenderungsantrag::holeNeueste($veranstaltung_id, 20);

		$data = array();
		foreach ($antraege as $ant) $data[AntraegeUtils::date_iso2timestamp($ant->datum_einreichung) . "_aenderungsantrag_" . $ant->id] = array(
			"title"       => "Neuer Änderungsantrag: " . $ant->revision_name . " zu " . $ant->antrag->nameMitRev(),
			"link"        => Yii::app()->getBaseUrl(true) . $this->createUrl("aenderungsantrag/anzeige", array("antrag_id" => $ant->antrag->id, "aenderungsantrag_id" => $ant->id)),
			"dateCreated" => AntraegeUtils::date_iso2timestamp($ant->datum_einreichung),
			"content"     => "<h2>Antrag</h2>" . HtmlBBcodeUtils::bbcode2html($ant->aenderung_text) . "<br>\n<br>\n<br>\n<h2>Begründung</h2>" . HtmlBBcodeUtils::bbcode2html($ant->aenderung_begruendung),
		);
		return $data;
	}

	/**
	 * @param Veranstaltung $veranstaltung
	 * @return array
	 */
	private function getFeedAntragKommentarData(&$veranstaltung)
	{
		$veranstaltung_id = IntVal($veranstaltung->id);

		$antraege = AntragKommentar::holeNeueste($veranstaltung_id, 20);

		$data = array();
		foreach ($antraege as $ant) $data[AntraegeUtils::date_iso2timestamp($ant->datum) . "_kommentar_" . $ant->id] = array(
			"title"       => "Kommentar von " . $ant->verfasser->name . " zu: " . $ant->antrag->nameMitRev(),
			"link"        => Yii::app()->getBaseUrl(true) . $this->createUrl("antrag/anzeige", array("antrag_id" => $ant->antrag->id, "kommentar_id" => $ant->id, "#" => "komm" . $ant->id)),
			"dateCreated" => AntraegeUtils::date_iso2timestamp($ant->datum),
			"content"     => HtmlBBcodeUtils::bbcode2html($ant->text),
		);
		return $data;
	}

	/**
	 * @param string $veranstaltung_id
	 */
	public function actionFeedAntraege($veranstaltung_id = "")
	{
		$veranstaltung = $this->loadVeranstaltung($veranstaltung_id);
		$this->testeWartungsmodus();

		$sprache = $veranstaltung->getSprache();
		$this->renderPartial('feed', array(
			"veranstaltung_id" => $veranstaltung->id,
			"feed_title"       => $sprache->get("Anträge"),
			"feed_description" => str_replace("%veranstaltung%", $veranstaltung->name, $sprache->get("feed_desc_antraege")),
			"data"             => $this->getFeedAntraegeData($veranstaltung),
			"sprache"          => $sprache,
		));
	}

	/**
	 * @param string $veranstaltung_id
	 */
	public function actionFeedAenderungsantraege($veranstaltung_id = "")
	{
		$veranstaltung = $this->loadVeranstaltung($veranstaltung_id);
		$this->testeWartungsmodus();

		$sprache = $veranstaltung->getSprache();
		$this->renderPartial('feed', array(
			"veranstaltung_id" => $veranstaltung->id,
			"feed_title"       => $sprache->get("Änderungsanträge"),
			"feed_description" => str_replace("%veranstaltung%", $veranstaltung->name, $sprache->get("feed_desc_aenderungsantraege")),
			"data"             => $this->getFeedAenderungsantraegeData($veranstaltung),
			"sprache"          => $sprache,
		));
	}

	/**
	 * @param string $veranstaltung_id
	 */
	public function actionFeedKommentare($veranstaltung_id = "")
	{
		$veranstaltung = $this->loadVeranstaltung($veranstaltung_id);
		$this->testeWartungsmodus();

		$sprache = $veranstaltung->getSprache();
		$this->renderPartial('feed', array(
			"veranstaltung_id" => $veranstaltung->id,
			"feed_title"       => $sprache->get("Kommentare"),
			"feed_description" => str_replace("%veranstaltung%", $veranstaltung->name, $sprache->get("feed_desc_kommentare")),
			"data"             => $this->getFeedAntragKommentarData($veranstaltung),
			"sprache"          => $veranstaltung->getSprache(),
		));
	}


	/**
	 * @param string $veranstaltung_id
	 */
	public function actionFeedAlles($veranstaltung_id = "")
	{
		$veranstaltung = $this->loadVeranstaltung($veranstaltung_id);
		$this->testeWartungsmodus();

		$sprache = $veranstaltung->getSprache();

		$data1 = $this->getFeedAntraegeData($veranstaltung);
		$data2 = $this->getFeedAenderungsantraegeData($veranstaltung);
		$data3 = $this->getFeedAntragKommentarData($veranstaltung);

		$data = array_merge($data1, $data2, $data3);
		krsort($data);

		$this->renderPartial('feed', array(
			"veranstaltung_id" => $veranstaltung->id,
			"feed_title"       => "Anträge, Änderungsanträge und Kommentare",
			"feed_description" => str_replace("%veranstaltung%", $veranstaltung->name, $sprache->get("feed_desc_alles")),
			"data"             => $data,
			"sprache"          => $veranstaltung->getSprache(),
		));

	}


	public function actionSuche($veranstaltung_id = "")
	{
		$this->layout = '//layouts/column2';

		$veranstaltung = $this->loadVeranstaltung($veranstaltung_id);
		$this->testeWartungsmodus();

		$neueste_aenderungsantraege = Aenderungsantrag::holeNeueste($veranstaltung->id, 5);
		$neueste_antraege           = Antrag::holeNeueste($veranstaltung->id, 5);
		$neueste_kommentare         = AntragKommentar::holeNeueste($veranstaltung->id, 3);

		$suchbegriff        = $_REQUEST["suchbegriff"];
		$antraege           = Antrag::suche($veranstaltung->id, $suchbegriff);
		$aenderungsantraege = Aenderungsantrag::suche($veranstaltung->id, $suchbegriff);

		$this->render('suche', array(
			"veranstaltung"              => $veranstaltung,
			"neueste_antraege"           => $neueste_antraege,
			"neueste_kommentare"         => $neueste_kommentare,
			"neueste_aenderungsantraege" => $neueste_aenderungsantraege,
			"suche_antraege"             => $antraege,
			"suche_aenderungsantraege"   => $aenderungsantraege,
			"suchbegriff"                => $suchbegriff,
			"sprache"                    => $veranstaltung->getSprache(),
		));

	}

	/**
	 * @param string $veranstaltung_id
	 * @return Veranstaltung|null
	 */
	private function actionVeranstaltung_loadData($veranstaltung_id)
	{
		$att = (is_numeric($veranstaltung_id) ? "id" : "yii_url");

		/** @var Veranstaltung $veranstaltung */
		$this->veranstaltung = Veranstaltung::model()->
			with(array(
				'antraege'                    => array(
					'joinType' => "LEFT OUTER JOIN",
					'on'       => "`antraege`.`veranstaltung` = `t`.`id` AND `antraege`.`status` NOT IN (" . implode(", ", IAntrag::$STATI_UNSICHTBAR) . ")",
				),
				'antraege.aenderungsantraege' => array(
					'joinType' => "LEFT OUTER JOIN",
					"on"       => "`aenderungsantraege`.`antrag_id` = `antraege`.`id` AND `aenderungsantraege`.`status` NOT IN (" . implode(", ", IAntrag::$STATI_UNSICHTBAR) . ")",
				),
			))->findByAttributes(array($att => $veranstaltung_id));
		return $this->veranstaltung;
	}


	/**
	 * @param string $veranstaltung_id
	 */
	public function actionVeranstaltung($veranstaltung_id = "")
	{
		$this->layout = '//layouts/column2';

		if ($veranstaltung_id == "") $this->redirect("/");
		$this->loadVeranstaltung($veranstaltung_id);
		$this->testeWartungsmodus();

		$veranstaltung = $this->actionVeranstaltung_loadData($veranstaltung_id);
		if (is_null($veranstaltung)) {
			if (Yii::app()->params['standardVeranstaltungAutoCreate']) {
				$veranstaltung                                   = new Veranstaltung();
				$veranstaltung->id                               = $veranstaltung_id;
				$veranstaltung->name                             = "Standard-Veranstaltung";
				$veranstaltung->freischaltung_antraege           = 1;
				$veranstaltung->freischaltung_aenderungsantraege = 1;
				$veranstaltung->freischaltung_kommentare         = 1;
				$veranstaltung->policy_kommentare                = Veranstaltung::$POLICY_NUR_ADMINS;
				$veranstaltung->policy_aenderungsantraege        = Veranstaltung::$POLICY_NUR_ADMINS;
				$veranstaltung->policy_antraege                  = Veranstaltung::$POLICY_NUR_ADMINS;
				$veranstaltung->typ                              = Veranstaltung::$TYP_PROGRAMM;
				$veranstaltung->save();

				$veranstaltung = $this->actionVeranstaltung_loadData($veranstaltung_id);
			} else {
				if (isset($_SERVER["HTTP_HOST"]) && stripos($_SERVER["HTTP_HOST"], "konzepte-fuer-hessen.de") !== false) $this->redirect("http://konzepte-fuer-hessen.de/");
				$this->render('error', array(
					"code"    => 404,
					"message" => "Diese Seite existiert nicht."
				));
				return;
			}
		}

		$antraege_sorted = $veranstaltung->antraegeSortiert();

		/** @var null|Person $ich */
		if (Yii::app()->user->isGuest) $ich = null;
		else {
			$ich = Person::model()->findByAttributes(array("auth" => Yii::app()->user->id));
		}

		$neueste_aenderungsantraege = Aenderungsantrag::holeNeueste($this->veranstaltung->id, 5);
		$neueste_antraege           = Antrag::holeNeueste($this->veranstaltung->id, 5);
		$neueste_kommentare         = AntragKommentar::holeNeueste($this->veranstaltung->id, 3);

		$meine_antraege           = array();
		$meine_aenderungsantraege = array();

		if ($ich) {
			$oCriteria        = new CDbCriteria();
			$oCriteria->alias = "antrag_unterstuetzer";
			$oCriteria->join  = "JOIN `antrag` ON `antrag`.`id` = `antrag_unterstuetzer`.`antrag_id`";
			$oCriteria->addCondition("`antrag`.`veranstaltung` = " . IntVal($this->veranstaltung->id));
			$oCriteria->addCondition("`antrag_unterstuetzer`.`unterstuetzer_id` = " . IntVal($ich->id));
			$oCriteria->addCondition("`antrag`.`status` != " . IAntrag::$STATUS_GELOESCHT);
			$oCriteria->order = '`datum_einreichung` DESC';
			$dataProvider     = new CActiveDataProvider('AntragUnterstuetzer', array(
				'criteria' => $oCriteria,
			));
			$meine_antraege   = $dataProvider->data;

			$oCriteria        = new CDbCriteria();
			$oCriteria->alias = "aenderungsantrag_unterstuetzer";
			$oCriteria->join  = "JOIN `aenderungsantrag` ON `aenderungsantrag`.`id` = `aenderungsantrag_unterstuetzer`.`aenderungsantrag_id`";
			$oCriteria->join .= " JOIN `antrag` ON `aenderungsantrag`.`antrag_id` = `antrag`.`id`";
			$oCriteria->addCondition("`antrag`.`veranstaltung` = " . IntVal($this->veranstaltung->id));
			$oCriteria->addCondition("`aenderungsantrag_unterstuetzer`.`unterstuetzer_id` = " . IntVal($ich->id));
			$oCriteria->addCondition("`antrag`.`status` != " . IAntrag::$STATUS_GELOESCHT);
			$oCriteria->addCondition("`aenderungsantrag`.`status` != " . IAntrag::$STATUS_GELOESCHT);
			$oCriteria->order         = '`aenderungsantrag`.`datum_einreichung` DESC';
			$dataProvider             = new CActiveDataProvider('AenderungsantragUnterstuetzer', array(
				'criteria' => $oCriteria,
			));
			$meine_aenderungsantraege = $dataProvider->data;
		}

		$einleitungstext = $veranstaltung->getStandardtext("startseite");

		$this->render('veranstaltung_index', array(
			"veranstaltung"              => $veranstaltung,
			"einleitungstext"            => $einleitungstext,
			"antraege"                   => $antraege_sorted,
			"ich"                        => $ich,
			"neueste_antraege"           => $neueste_antraege,
			"neueste_kommentare"         => $neueste_kommentare,
			"neueste_aenderungsantraege" => $neueste_aenderungsantraege,
			"meine_antraege"             => $meine_antraege,
			"meine_aenderungsantraege"   => $meine_aenderungsantraege,
			"sprache"                    => $veranstaltung->getSprache(),
		));
	}


	/**
	 * This is the action to handle external exceptions.
	 */
	public function actionError()
	{
		if ($error = Yii::app()->errorHandler->error) {
			if (Yii::app()->request->isAjaxRequest)
				echo $error['message'];
			else
				$this->render('error', $error);
		}
	}

	/**
	 *
	 */
	public function actionLogin($veranstaltung_id = "", $back = "")
	{
		$this->loadVeranstaltung($veranstaltung_id);

		$model = new OAuthLoginForm();
		if (isset($_REQUEST["OAuthLoginForm"])) $model->attributes = $_REQUEST["OAuthLoginForm"];

		if (isset($_REQUEST["password"]) && $_REQUEST["password"] != "" && isset($_REQUEST["OAuthLoginForm"]["wurzelwerk"])) {
			$username = "openid:https://" . $_REQUEST["OAuthLoginForm"]["wurzelwerk"] . ".netzbegruener.in/";

			/** @var Person $user */
			$user = Person::model()->findByAttributes(array("auth" => $username));
			if ($user === null) {
				Yii::app()->user->setFlash("error", "Benutzername nicht gefunden.");
				$this->render('login', array("model" => $model));
				return;
			}
			$correct = $user->validate_password($_REQUEST["password"]);
			if ($correct) {
				$identity = new AntragUserIdentityPasswd($_REQUEST["OAuthLoginForm"]["wurzelwerk"]);
				Yii::app()->user->login($identity);

				if ($user->admin) {
					//$openid->setState("role", "admin");
					Yii::app()->user->setState("role", "admin");
				}

				Yii::app()->user->setState("person_id", $user->id);
				Yii::app()->user->setFlash('success', 'Willkommen!');
				if ($back == "") $back = Yii::app()->homeUrl;

				$this->redirect($back);
			} else {
				Yii::app()->user->setFlash("error", "Falsches Passwort.");
				$this->render('login', array("model" => $model));
				return;
			}

			//Yii::app()->user->login($us);
			die();
		} elseif (isset($_REQUEST["openid_mode"])) {
			/** @var LightOpenID $loid */
			$loid = Yii::app()->loid->load();
			if ($_REQUEST['openid_mode'] == 'cancel') {
				$err = Yii::t('core', 'Authorization cancelled');
			} else {
				try {
					$us = new AntragUserIdentityOAuth($loid);
					if ($us->authenticate()) {
						Yii::app()->user->login($us);
						$user = Person::model()->findByAttributes(array("auth" => $us->getId()));
						if (!$user) {
							$user                 = new Person;
							$user->auth           = $us->getId();
							$user->name           = $us->getName();
							$user->email          = $us->getEmail();
							$user->angelegt_datum = date("Y-m-d H:i:s");
							$user->status         = Person::$STATUS_CONFIRMED;
							$user->typ            = Person::$TYP_PERSON;
							if (Person::model()->count() == 0) {
								$user->admin = 1;
								Yii::app()->user->setState("role", "admin");
							} else {
								$user->admin = 0;
							}
							$user->save();
						} else {
							if ($user->admin) {
								//$openid->setState("role", "admin");
								Yii::app()->user->setState("role", "admin");
							}
						}
						Yii::app()->user->setState("person_id", $user->id);
						Yii::app()->user->setFlash('success', 'Willkommen!');
						if ($back == "") $back = Yii::app()->homeUrl;
						$this->redirect($back);
					} else {
						Yii::app()->user->setFlash("error", "Leider ist beim Einloggen ein Fehler aufgetreten.");
						$this->render('login', array("model" => $model));
						return;
					}
				} catch (Exception $e) {
					$err = Yii::t('core', $e->getMessage());
					Yii::app()->user->setFlash("error", "Leider ist beim Einloggen ein Fehler aufgetreten:<br>" . $e->getMessage());
					$this->render('login', array("model" => $model));
					return;
				}
			}

			if (!empty($err)) Yii::app()->user->setFlash("error", $err);
		} elseif (isset($_REQUEST["OAuthLoginForm"])) {

			if (stripos($model->openid_identifier, "yahoo") !== false) {
				if (!empty($err)) Yii::app()->user->setFlash("error", "Leider ist wegen technischen Problemen ein Login mit Yahoo momentan nicht möglich.");
			} else {
				/** @var LightOpenID $loid */
				$loid = Yii::app()->loid->load();
				if ($model->wurzelwerk != "") $loid->identity = "https://" . $model->wurzelwerk . ".netzbegruener.in/";
				else $loid->identity = $model->openid_identifier;

				$loid->required  = array('namePerson/friendly', 'contact/email'); //Try to get info from openid provider
				$loid->realm     = (!empty($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'];
				$loid->returnUrl = $loid->realm . yii::app()->getRequest()->requestUri;
				if (empty($err)) {
					try {
						$url = $loid->authUrl();
						$this->redirect($url);
					} catch (Exception $e) {
						$err = Yii::t('core', $e->getMessage());
					}
				}
				if (!empty($err)) Yii::app()->user->setFlash("error", $err);
			}
		}

		$this->render('login', array("model" => $model));
	}


	/**
	 *
	 */
	public function actionLogout($veranstaltung_id = "", $back = "")
	{
		$this->loadVeranstaltung($veranstaltung_id);

		Yii::app()->user->logout();
		Yii::app()->user->setFlash("success", "Bis bald!");
		if ($back == "") $back = Yii::app()->homeUrl;
		$this->redirect($back);
	}
}
