<?php
/**
 *	メール送信関連クラス
 *
 *	メール送信制御をおこなうクラス
 *
 *	@author			Mouri 2012/02/21
 *	@version		1.0
 */
class clsMail{
	/**
	 * 宛先
	 */
	private $_strTo;
	/**
	 * 差出人
	 */
	private $_strFromName;
	/**
	 * 差出人アドレス
	 */
	private $_strFrom;
	/**
	 * 件名
	 */
	private $_strTitle;
	/**
	 * 本文
	 */
	private $_strBody;
	/**
	 * ＣＣ
	 */
	private $_strCc;
	/**
	 * ＢＣＣ
	 */
	private $_strBcc;
	/**
	 * メール不達時の宛先
	 */
	private $_strFOption;
	/**
	 * 内部エンコード
	 */
	private $_strEncode;

	/**
	 * コンストラクタ
	 *
	 * CC,BCC,-foptionについて、
	 * 設定する場合はsetterから設定
	 *
	 * @param	$strTo			宛先
	 * @param	$strFromName	差出人
	 * @param	$strFrom		差出人アドレス
	 * @param	$strTitle		件名
	 * @param	$strBody		本文
	 * @return	なし
	 */
	public function __construct($strTo, $strFromName, $strFrom, $strTitle, $strBody){
		$this->_strTo 		= $strTo;
		$this->_strFromName	= $strFromName;
		$this->_strFrom 	= $strFrom;
		$this->_strTitle 	= $strTitle;
		$this->_strBody		= $strBody;
		$this->_strCc 		= "";
		$this->_strBcc 		= "";
		$this->_strFOption 	= "";
		// $this->_strEncode 	= "UTF-8";
		$this->_strEncode 	= "EUC-JP";
	}

	//▼以下はprivateフィールドへのアクセサメソッド
	/**
	 * 宛先設定
	 *
	 * @param	$strTo		宛先
	 * @return	なし
	 */
	public function setStrTo($strTo){
		$this->_strTo 		= $strTo;
	}
	/**
	 * 宛先取得
	 *
	 * @param	なし
	 * @return	宛先
	 */
	public function getStrTo(){
		return $this->_strTo;
	}
	/**
	 * 差出人設定
	 *
	 * @param	$strFromName	差出人
	 * @return	なし
	 */
	public function setStrFromName($strFromName){
		$this->_strFromName = $strFromName;
	}
	/**
	 * 差出人取得
	 *
	 * @param	なし
	 * @return	差出人
	 */
	public function getStrFromName(){
		return $this->_strFromName;
	}
	/**
	 * 差出人アドレス設定
	 *
	 * @param	$strFrom	送信元
	 * @return	なし
	 */
	public function setStrFrom($strFrom){
		$this->_strFrom 	= $strFrom;
	}
	/**
	 * 差出人アドレス取得
	 *
	 * @param	なし
	 * @return	差出人アドレス
	 */
	public function getStrFrom(){
		return $this->_strFrom;
	}
	/**
	 * 件名設定
	 *
	 * @param	$strTitle	件名
	 * @return	なし
	 */
	public function setStrTitle($strTitle){
		$this->_strTitle 	= $strTitle;
	}
	/**
	 * 件名取得
	 *
	 * @param	なし
	 * @return	件名
	 */
	public function getStrTitle(){
		return $this->_strTitle;
	}
	/**
	 * 本文設定
	 *
	 * @param	$strBody	本文
	 * @return	なし
	 */
	public function setStrBody($strBody){
		$this->_strBody 	= $strBody;
	}
	/**
	 * 本文取得
	 *
	 * @param	なし
	 * @return	本文
	 */
	public function getStrBody(){
		return $this->_strBody;
	}
	/**
	 * ＣＣ設定
	 *
	 * @param	$strCc	本文
	 * @return	なし
	 */
	public function setStrCc($strCc){
		$this->_strCc 	= $strCc;
	}
	/**
	 * ＣＣ取得
	 *
	 * @param	なし
	 * @return	ＣＣ
	 */
	public function getStrCc(){
		return $this->_strCc;
	}
	/**
	 * ＢＣＣ設定
	 *
	 * @param	$strBcc	本文
	 * @return	ＢＣＣ
	 */
	public function setStrBcc($strBcc){
		$this->_strBcc 	= $strBcc;
	}
	/**
	 * ＢＣＣ取得
	 *
	 * @param	なし
	 * @return	ＢＣＣ
	 */
	public function getStrBcc(){
		return $this->_strBcc;
	}
	/**
	 * メール不達時の宛先設定
	 *
	 * @param	$strFOption	本文
	 * @return	なし
	 */
	public function setStrFOption($strFOption){
		$this->_strFOption 	= $strFOption;
	}
	/**
	 * メール不達時の宛先取得
	 *
	 * @param	なし
	 * @return	メール不達時の宛先
	 */
	public function getStrFOption(){
		return $this->_strFOption;
	}
	//▲ここまで

	/**
	 * メール送信メソッド
	 *
	 * 設定されたパラメータを元にメール送信をおこなう
	 *
	 * @param	なし
	 * @return	true:成功、false：失敗
	 */
	public function sendMail(){
		mb_language("japanese");
		mb_internal_encoding($this->_strEncode);

		//差出人日本語設定
		$this->_strFromName = mb_encode_mimeheader($this->_strFromName);

		//各種設定パラメータチェック
		//宛先
		if(strlen($this->_strTo) == 0){
			trigger_error("宛先設定なし",E_USER_ERROR);
		}
		//差出人アドレス
		if(strlen($this->_strFrom) == 0){
			trigger_error("差出人アドレス設定なし",E_USER_ERROR);
		}

		//メール送信
		return mb_send_mail($this->_strTo,
							$this->_strTitle,
							$this->_strBody,
							"From:".$this->_strFromName."<".$this->_strFrom.">".(strlen($this->_strCc) == 0 ? "" : "\r\ncc:".$this->_strCc).(strlen($this->_strBcc) == 0 ? "" : "\r\nBcc:".$this->_strBcc),
							strlen($this->_strFOption) == 0 ? "" : "-f{$this->_strFOption}");
	}

}
?>