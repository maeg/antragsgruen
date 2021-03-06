<?php

class PolicyAntraegeEingeloggte extends IPolicyAntraege
{

	/**
	 * @static
	 * @return int
	 */
	static public function getPolicyID()
	{
		return 4;
	}

	/**
	 * @static
	 * @return string
	 */
	static public function getPolicyName()
	{
		return "Nur Eingeloggte";
	}


	/**
	 * @return bool
	 */
	public function checkCurUserHeuristically() {
		return (!Yii::app()->user->isGuest && !$this->checkAntragsschlussVorbei());
	}

	/**
	 * @abstract
	 * @return string
	 */
	public function getPermissionDeniedMsg() {
		if (Yii::app()->user->isGuest) return "Bitte logge dich ein";
		return "";
	}


	/**
	 * @return bool
	 */
	public function checkAntragSubmit()
	{
		return (!Yii::app()->user->isGuest && !$this->checkAntragsschlussVorbei());
	}


	/**
	 * @return bool
	 */
	public function checkAenderungsantragSubmit()
	{
		return (!Yii::app()->user->isGuest && !$this->checkAntragsschlussVorbei());
	}


	/**
	 * @return string
	 */
	public function getOnCreateDescription()
	{
		return "Nur Eingeloggte";
	}

}
