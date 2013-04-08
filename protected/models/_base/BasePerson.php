<?php

/**
 * This is the model base class for the table "person".
 * DO NOT MODIFY THIS FILE! It is automatically generated by giix.
 * If any changes are necessary, you must set or override the required
 * property or method in class "Person".
 *
 * Columns in table "person" available as properties of the model,
 * followed by relations of table "person" available as properties of the model.
 *
 * @property integer $id
 * @property string $typ
 * @property string $name
 * @property string $email
 * @property string $telefon
 * @property string $auth
 * @property string $angelegt_datum
 * @property integer $admin
 * @property integer $status
 * @property string $pwd_enc
 *
 * @property AenderungsantragKommentar[] $aenderungsantragKommentare
 * @property AenderungsantragUnterstuetzer[] $aenderungsantragUnterstuetzer
 * @property Antrag[] $antraege_abos
 * @property AntragKommentar[] $antragKommentare
 * @property AntragUnterstuetzer[] $antragUnterstuetzer
 * @property VeranstaltungPerson[] $veranstaltungen
 */
abstract class BasePerson extends GxActiveRecord {

	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	public function tableName() {
		return 'person';
	}

	public static function label($n = 1) {
		return Yii::t('app', 'Person|Personen', $n);
	}

	public static function representingColumn() {
		return 'typ';
	}

	public function rules() {
		return array(
			array('typ, name, angelegt_datum, admin, status', 'required'),
			array('admin, status', 'numerical', 'integerOnly'=>true),
			array('typ', 'length', 'max'=>12),
			array('name, telefon', 'length', 'max'=>100),
			array('email, auth', 'length', 'max'=>200),
			array('email, telefon, auth, pwd_enc', 'default', 'setOnEmpty' => true, 'value' => null),
			array('id, typ, name, email, telefon, auth, pwd_enc, angelegt_datum, admin, status', 'safe', 'on'=>'search'),
		);
	}

	public function relations() {
		return array(
			'aenderungsantragKommentare' => array(self::HAS_MANY, 'AenderungsantragKommentar', 'verfasser_id'),
			'aenderungsantragUnterstuetzer' => array(self::HAS_MANY, 'AenderungsantragUnterstuetzer', 'unterstuetzer_id'),
			'antraege_abos' => array(self::MANY_MANY, 'Antrag', 'antrag_abo(abonnent_id, antrag_id)'),
			'antragKommentare' => array(self::HAS_MANY, 'AntragKommentar', 'verfasser_id'),
			'antragUnterstuetzer' => array(self::HAS_MANY, 'AntragUnterstuetzer', 'unterstuetzer_id'),
			'veranstaltungen'  => array(self::HAS_MANY, 'VeranstaltungPerson', 'person_id'),
		);
	}

	public function pivotModels() {
		return array(
			'antraege_abos' => 'AntragAbo',
			'veranstaltungen_personen' => 'VeranstaltungPerson',
		);
	}

	public function attributeLabels() {
		return array(
			'id' => Yii::t('app', 'ID'),
			'typ' => Yii::t('app', 'Typ'),
			'name' => Yii::t('app', 'Name'),
			'email' => Yii::t('app', 'Email'),
			'telefon' => Yii::t('app', 'Telefon'),
			'auth' => Yii::t('app', 'Auth'),
			'pwd_enc' => Yii::t('app', 'Passwort-Hash'),
			'angelegt_datum' => Yii::t('app', 'Angelegt Datum'),
			'admin' => Yii::t('app', 'Admin'),
			'status' => Yii::t('app', 'Status'),
			'aenderungsantragKommentare' => null,
			'aenderungsantragUnterstuetzer' => null,
			'antraege_abos' => null,
			'antragKommentare' => null,
			'antragUnterstuetzer' => null,
			'veranstaltungen' => null,
		);
	}

	public function search() {
		$criteria = new CDbCriteria;

		$criteria->compare('id', $this->id);
		$criteria->compare('typ', $this->typ, true);
		$criteria->compare('name', $this->name, true);
		$criteria->compare('email', $this->email, true);
		$criteria->compare('telefon', $this->telefon, true);
		$criteria->compare('auth', $this->auth, true);
		$criteria->compare('angelegt_datum', $this->angelegt_datum, true);
		$criteria->compare('admin', $this->admin);
		$criteria->compare('status', $this->status);

		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
		));
	}
}