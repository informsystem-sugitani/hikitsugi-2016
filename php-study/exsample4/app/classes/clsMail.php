<?php
/**
 *	�᡼��������Ϣ���饹
 *
 *	�᡼����������򤪤��ʤ����饹
 *
 *	@author			Mouri 2012/02/21
 *	@version		1.0
 */
class clsMail{
	/**
	 * ����
	 */
	private $_strTo;
	/**
	 * ���п�
	 */
	private $_strFromName;
	/**
	 * ���пͥ��ɥ쥹
	 */
	private $_strFrom;
	/**
	 * ��̾
	 */
	private $_strTitle;
	/**
	 * ��ʸ
	 */
	private $_strBody;
	/**
	 * �ã�
	 */
	private $_strCc;
	/**
	 * �£ã�
	 */
	private $_strBcc;
	/**
	 * �᡼����ã���ΰ���
	 */
	private $_strFOption;
	/**
	 * �������󥳡���
	 */
	private $_strEncode;

	/**
	 * ���󥹥ȥ饯��
	 *
	 * CC,BCC,-foption�ˤĤ��ơ�
	 * ���ꤹ�����setter��������
	 *
	 * @param	$strTo			����
	 * @param	$strFromName	���п�
	 * @param	$strFrom		���пͥ��ɥ쥹
	 * @param	$strTitle		��̾
	 * @param	$strBody		��ʸ
	 * @return	�ʤ�
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

	//���ʲ���private�ե�����ɤؤΥ��������᥽�å�
	/**
	 * ��������
	 *
	 * @param	$strTo		����
	 * @return	�ʤ�
	 */
	public function setStrTo($strTo){
		$this->_strTo 		= $strTo;
	}
	/**
	 * �������
	 *
	 * @param	�ʤ�
	 * @return	����
	 */
	public function getStrTo(){
		return $this->_strTo;
	}
	/**
	 * ���п�����
	 *
	 * @param	$strFromName	���п�
	 * @return	�ʤ�
	 */
	public function setStrFromName($strFromName){
		$this->_strFromName = $strFromName;
	}
	/**
	 * ���пͼ���
	 *
	 * @param	�ʤ�
	 * @return	���п�
	 */
	public function getStrFromName(){
		return $this->_strFromName;
	}
	/**
	 * ���пͥ��ɥ쥹����
	 *
	 * @param	$strFrom	������
	 * @return	�ʤ�
	 */
	public function setStrFrom($strFrom){
		$this->_strFrom 	= $strFrom;
	}
	/**
	 * ���пͥ��ɥ쥹����
	 *
	 * @param	�ʤ�
	 * @return	���пͥ��ɥ쥹
	 */
	public function getStrFrom(){
		return $this->_strFrom;
	}
	/**
	 * ��̾����
	 *
	 * @param	$strTitle	��̾
	 * @return	�ʤ�
	 */
	public function setStrTitle($strTitle){
		$this->_strTitle 	= $strTitle;
	}
	/**
	 * ��̾����
	 *
	 * @param	�ʤ�
	 * @return	��̾
	 */
	public function getStrTitle(){
		return $this->_strTitle;
	}
	/**
	 * ��ʸ����
	 *
	 * @param	$strBody	��ʸ
	 * @return	�ʤ�
	 */
	public function setStrBody($strBody){
		$this->_strBody 	= $strBody;
	}
	/**
	 * ��ʸ����
	 *
	 * @param	�ʤ�
	 * @return	��ʸ
	 */
	public function getStrBody(){
		return $this->_strBody;
	}
	/**
	 * �ã�����
	 *
	 * @param	$strCc	��ʸ
	 * @return	�ʤ�
	 */
	public function setStrCc($strCc){
		$this->_strCc 	= $strCc;
	}
	/**
	 * �ãü���
	 *
	 * @param	�ʤ�
	 * @return	�ã�
	 */
	public function getStrCc(){
		return $this->_strCc;
	}
	/**
	 * �£ã�����
	 *
	 * @param	$strBcc	��ʸ
	 * @return	�£ã�
	 */
	public function setStrBcc($strBcc){
		$this->_strBcc 	= $strBcc;
	}
	/**
	 * �£ãü���
	 *
	 * @param	�ʤ�
	 * @return	�£ã�
	 */
	public function getStrBcc(){
		return $this->_strBcc;
	}
	/**
	 * �᡼����ã���ΰ�������
	 *
	 * @param	$strFOption	��ʸ
	 * @return	�ʤ�
	 */
	public function setStrFOption($strFOption){
		$this->_strFOption 	= $strFOption;
	}
	/**
	 * �᡼����ã���ΰ������
	 *
	 * @param	�ʤ�
	 * @return	�᡼����ã���ΰ���
	 */
	public function getStrFOption(){
		return $this->_strFOption;
	}
	//�������ޤ�

	/**
	 * �᡼�������᥽�å�
	 *
	 * ���ꤵ�줿�ѥ�᡼���򸵤˥᡼�������򤪤��ʤ�
	 *
	 * @param	�ʤ�
	 * @return	true:������false������
	 */
	public function sendMail(){
		mb_language("japanese");
		mb_internal_encoding($this->_strEncode);

		//���п����ܸ�����
		$this->_strFromName = mb_encode_mimeheader($this->_strFromName);

		//�Ƽ�����ѥ�᡼�������å�
		//����
		if(strlen($this->_strTo) == 0){
			trigger_error("��������ʤ�",E_USER_ERROR);
		}
		//���пͥ��ɥ쥹
		if(strlen($this->_strFrom) == 0){
			trigger_error("���пͥ��ɥ쥹����ʤ�",E_USER_ERROR);
		}

		//�᡼������
		return mb_send_mail($this->_strTo,
							$this->_strTitle,
							$this->_strBody,
							"From:".$this->_strFromName."<".$this->_strFrom.">".(strlen($this->_strCc) == 0 ? "" : "\r\ncc:".$this->_strCc).(strlen($this->_strBcc) == 0 ? "" : "\r\nBcc:".$this->_strBcc),
							strlen($this->_strFOption) == 0 ? "" : "-f{$this->_strFOption}");
	}

}
?>