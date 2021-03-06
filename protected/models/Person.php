<?php

Yii::import('application.models._base.BasePerson');

class Person extends BasePerson
{
	public static $TYP_ORGANISATION = 'organisation';
	public static $TYP_PERSON = 'person';
	public static $TYPEN = array(
		'organisation' => "Organisation",
		'person'       => "Natürliche Person",
	);

	public static $STATUS_UNCONFIRMED = 1;
	public static $STATUS_CONFIRMED = 0;
	public static $STATUS_DELETED = -1;
	public static $STATUS = array(
		1  => "Nicht bestätigt",
		0  => "Bestätigt",
		-1 => "Gelöscht",
	);

	/** @var bool */
	private $email_required = false;

	/**
	 * @var $className string
	 * @return GxActiveRecord
	 */
	public static function model($className = __CLASS__)
	{
		return parent::model($className);
	}

	public static function label($n = 1)
	{
		return Yii::t('app', 'Person|Personen', $n);
	}

	public function rules() {
		$rules = array(
			array('typ, name, angelegt_datum, admin, status', 'required'),
			array('admin, status', 'numerical', 'integerOnly'=>true),
			array('typ', 'length', 'max'=>12),
			array('name, telefon', 'length', 'max'=>100),
			array('email, auth', 'length', 'max'=>200),
			array('email, telefon, auth, pwd_enc', 'default', 'setOnEmpty' => true, 'value' => null),
			array('id, typ, name, email, telefon, auth, pwd_enc, angelegt_datum, admin, status', 'safe', 'on'=>'search'),
		);
		if ($this->email_required) $rules[] = array('email', 'required');
		return $rules;
	}



	/**
	 * @param bool $required
	 */
	public function setEmailRequired($required) {
		$this->email_required = $required;
	}


	/**
	 * @param string $a
	 * @param string $b
	 * @return bool
	 */
	private function slow_equals($a, $b)
	{
		$diff = strlen($a) ^ strlen($b);
		for ($i = 0; $i < strlen($a) && $i < strlen($b); $i++) {
			$diff |= ord($a[$i]) ^ ord($b[$i]);
		}
		return $diff === 0;
	}


	/**
	 * @static
	 * @param string $password
	 * @return string
	 */
	public static function create_hash($password)
	{
		// from: http://crackstation.net/hashing-security.htm
		// format: algorithm:iterations:salt:hash
		$salt = base64_encode(mcrypt_create_iv(24, MCRYPT_DEV_URANDOM));
		return "sha256:1000:" . $salt . ":" . base64_encode(static::pbkdf2("sha256", $password, $salt, 1000, 24, true));
	}


	/*
	 * PBKDF2 key derivation function as defined by RSA's PKCS #5: https://www.ietf.org/rfc/rfc2898.txt
	 * $algorithm - The hash algorithm to use. Recommended: SHA256
	 * $password - The password.
	 * $salt - A salt that is unique to the password.
	 * $count - Iteration count. Higher is better, but slower. Recommended: At least 1000.
	 * $key_length - The length of the derived key in bytes.
	 * $raw_output - If true, the key is returned in raw binary format. Hex encoded otherwise.
	 * Returns: A $key_length-byte key derived from the password and salt.
	 *
	 * Test vectors can be found here: https://www.ietf.org/rfc/rfc6070.txt
	 *
	 * This implementation of PBKDF2 was originally created by https://defuse.ca
	 * With improvements by http://www.variations-of-shadow.com
	 */
	/**
	 * @param string $algorithm
	 * @param string $password
	 * @param string $salt
	 * @param int $count
	 * @param int $key_length
	 * @param bool $raw_output
	 * @return string
	 */
	private static function pbkdf2($algorithm, $password, $salt, $count, $key_length, $raw_output = false)
	{
		$algorithm = strtolower($algorithm);
		if (!in_array($algorithm, hash_algos(), true))
			die('PBKDF2 ERROR: Invalid hash algorithm.');
		if ($count <= 0 || $key_length <= 0)
			die('PBKDF2 ERROR: Invalid parameters.');

		$hash_length = strlen(hash($algorithm, "", true));
		$block_count = ceil($key_length / $hash_length);

		$output = "";
		for ($i = 1; $i <= $block_count; $i++) {
			// $i encoded as 4 bytes, big endian.
			$last = $salt . pack("N", $i);
			// first iteration
			$last = $xorsum = hash_hmac($algorithm, $last, $password, true);
			// perform the other $count - 1 iterations
			for ($j = 1; $j < $count; $j++) {
				$xorsum ^= ($last = hash_hmac($algorithm, $last, $password, true));
			}
			$output .= $xorsum;
		}

		if ($raw_output)
			return substr($output, 0, $key_length);
		else
			return bin2hex(substr($output, 0, $key_length));
	}


	/**
	 * @param string $password
	 * @return bool
	 */
	public function validate_password($password)
	{
		$params = explode(":", $this->pwd_enc);
		if (count($params) < 4)
			return false;
		$pbkdf2 = base64_decode($params[3]);
		return $this->slow_equals(
			$pbkdf2,
			static::pbkdf2(
				$params[0],
				$password,
				$params[2],
				(int)$params[1],
				strlen($pbkdf2),
				true
			)
		);
	}


	/*
    public function attributeLabels() {
        $ret = parent::attributeLabels();
        $ret["abonnenten"] = "Hat abonniert";
        return $ret;
    }
	*/

}