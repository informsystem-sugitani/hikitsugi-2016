<?php
/**
 * ���ѥǡ����١����������饹
 *
 * �ƥ����ƥ�ˤ����ơ��ǡ����١������˻��Ѥ������ѥ��饹
 * PHP��PDO����Ѥ��롣�������Ѿ�������Ȥ��ƺ���
 * ������¹Ԥ������äƤϡ��ץ�ڥ����ɥ��ơ��ȥ��Ȥ���Ѥ���
 * �ƥ᥽�åɤǥ��顼��ȯ�������ݤˤϡ����顼�����ɤ��֤�
 * ���顼�����ɤ˴ؤ��Ƥϡ�����const�ѿ�����
 *
 * @author 2012/02/ Nambe
 * @link   http://jp.php.net/manual/ja/book.pdo.php
 *
 **/
require_once("clsMail.php");

class clsDataBase
{
	/**
	 * PDO���֥������Ȥ��Ǽ�����ѿ�
	 **/
	protected $_objPdo;

	/**
	 * ��³����ǡ����١����Υ����פ�����
	 **/
	protected $_strDbType;

	/**
	 * ��³����ǡ����١����Υۥ��Ȥ�����
	 **/
	protected $_strHostName;

	/**
	 * ��³����ǡ����١���̾�Τ�����
	 **/
	protected $_strDbName;

	/**
	 * �ǡ����١�������³����桼����̾������
	 **/
	protected $_strDbUserName;

	/**
	 * �ǡ����١�������³����桼�����Υѥ���ɤ�����
	 **/
	protected $_strDbPassWord;

	/**
	 * �ǡ����١����������顼�����������륨�顼�᡼���������
	 **/
	protected $_strAdminMailAddress;

	/**
	 * �����ƥ�̾�����顼�᡼�������η�̾�ǻ���
	 **/
	protected $_strSystemName;

	/**
	 * �᥽�åɼ¹Ի������������票�顼
	 **/
	const ARGUMENT_ERROR = -1;

	/**
	 * �᥽�åɼ¹Ի���DB�������顼
	 **/
	const DATABASE_ERROR = -2;

	/**
	 * �ǡ���������Ͽ������������ȿ
	 **/
	const PRIMARY_ERROR  = -3;


	/**
	 * ���󥹥ȥ饯��
	 *
	 * �ǡ����١�����³���������˻�����PDO���֥������Ȥ�ư������ꤹ�롣<br />
	 * �ǡ����١����ǥ��顼��ȯ�������ݤ��㳰��ȯ��������
	 * 
	 * @author Nambe
	 * 
	 * @param string $dbType    ��³����ǡ����١����μ���򵭽�
	 * @param string $hostName  ��³��Υۥ��Ȥ򵭽�
	 * @param string $dbName    ��³����ǡ����١���̾�Τ򵭽�
	 * @param string $userName  �ǡ����١�������³����桼�����͡���򵭽�
	 * @param string $passWord  �ǡ����١�������³����桼�����Υѥ���ɤ򵭽�
	 * 
	 **/
	function __construct( $dbType, $hostName, $dbName, $userName, $passWord )
	{
		// �ǡ����١�����Ϣ�������
		$this->setDbType( $dbType );		// �ǡ����١��������פ�����
		$this->setHostName( $hostName );	// �ۥ��Ȥ�����
		$this->setDbName( $dbName );		// �ǡ����١���������
		$this->setUserName( $userName );	// �桼����������
		$this->setPassWord( $passWord );	// �ѥ���ɤ�����

		// ���顼��ȯ�������ݤΥ��顼�ȥ᡼���������������
		// ����Ƴ�����ˤϥ��ɥ쥹��"system@inform-us.com"�����ꤹ�뤳��
		$this->_strAdminMailAddress = 'sugitani@inform.co.jp'; 				// ������
		$this->_strSystemName       = '�ڼ���ƥ��ȡ�'.$_SERVER['HTTP_HOST'];	// �����ƥ�̾

		try {
			$this->_objPdo = new PDO("{$this->_strDbType}:host={$this->_strHostName};dbname={$this->_strDbName}", 
									 "{$this->_strDbUserName}",
									 "{$this->_strDbPassWord}");

			// ���顼ȯ�������㳰��ȯ��������褦������
			$this->_objPdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
		} catch( PDOException $ex ) {
			// ���顼�᡼�������
			$this->sendErrorMail($ex);
			// �ȥꥬ�����顼��ȯ�������ơ����顼�ϥ�ɥ顽�˽������ޤ���
			trigger_error( 'PDO���֥������Ȥ���������', E_USER_ERROR);
		}
	}

	/**
	 * ��³����ǡ����١��������פ����ꤹ��ؿ�
	 *
	 * @author Nambe
	 * @param string $dbType : ��³����ǡ����١��������פ򵭽�
	 **/
	public function setDbType( $dbType )
	{
		$this->_strDbType = $dbType;
	}

	/**
	 * ��³��Υۥ��Ȥ����ꤹ��ؿ�
	 *
	 * @author Nambe
	 * @param string $hostName : ��³����ǡ����١��������פ򵭽�
	 **/
	public function setHostName( $hostName ) 
	{
		$this->_strHostName = $hostName;
	}

	/**
	 * ��³����ǡ����١��������ꤹ��ؿ�
	 *
	 * @author Nambe
	 * @param string $dbName : ��³����ǡ����١���̾�򵭽�
	 **/
	public function setDbName( $dbName ) 
	{
		$this->_strDbName = $dbName;
	}

	/**
	 * �ǡ����١�������³����桼���������ꤹ��ؿ�
	 *
	 * @author Nambe
	 * @param string $userName : ��³����桼�����򵭽�
	 **/
	public function setUserName( $userName ) 
	{
		$this->_strDbUserName = $userName;
	}

	/**
	 * �ǡ����١�������³����桼���������ꤹ��ؿ�
	 *
	 * @author Nambe
	 * @param string $userName : ��³����桼�����򵭽�
	 **/
	public function setPassWord( $passWord ) 
	{
		$this->_strDbPassWord = $passWord;
	}

	/**
	 * ���顼�᡼�������������ꤹ��
	 *
	 * @author Nambe
	 * @param string $mailAddress  ���顼ȯ�����Υ᡼��������
	 **/
	public function setAdminAddress( $mailAddress ) 
	{
		$this->_strAdminMailAddress = $mailAddress;
	}

	/**
	 * �ǡ����򥤥󥵡��Ȥ���ݤ˻��Ѥ���ؿ�
	 *
	 * execQuery�ؿ�����Ѥ��Ƽ¹Է�̤��֤�
	 *
	 * @author Nambe
	 * @see    execQuery
	 * @param string $strQuery       INSERT������
	 * @param array  $aryParamArray  �ץ�ڥ�����ѥ�᡼�����Ǽ����Ϣ������
	 * 
	 * @return mixed : ���󥵡��Ȥ�����������硢true. 
	 * 				   ��������ȿ�ξ�� -3
	 **/
	public function addDbData( $strQuery, $aryParamArray ) 
	{
		// ������¹Է�̤��Ǽ�����ѿ�
		$_mixQueryResult;
		// ������¹Է�̤��ѿ��˳�Ǽ
		$_mixQueryResult = $this->execQuery( $strQuery, $aryParamArray );

		// �����ͤ��֤äƤ����硢���Τޤ��֤�
		if( true === is_bool($_mixQueryResult) ){
			return $_mixQueryResult;
		}elseif( true === is_object($_mixQueryResult) ) {
		// ���֥������Ȥ��֤äƤ��Ƥ����硢���顼��ȯ�����Ƥ���Τ�
		// ���顼�����ɤ�Ƚ�Ǥ�������ͤ��֤�
			// 23505�ξ�硢��������ȿ�ʤΤǡ����-3���֤�
			if( 23505 == $_mixQueryResult->getCode() ){
				return self::PRIMARY_ERROR;
			}else{
				// ����ʳ��ξ�硢��̿Ū�ʥ��顼�ʤΤ���� -2 ���֤�
				return self::DATABASE_ERROR;
			}
		}else{
			return $_mixQueryResult;
		}
	}

	/**
	 * �ǡ����򥢥åץǡ��Ȥ���ݤ˻��Ѥ���ؿ�
	 *
	 * execQuery�ؿ�����Ѥ��Ƽ¹Է�̤��֤�
	 * 2012/07/11 Nambe
	 * execQuery�᥽�åɤ�����ͤ��Ѥ�äƤ��뤳�Ȥˤ�뽤��
	 * �����ͤ���äƤ��Ƥ����硢���Τޤ��᤹�����֥������Ȥξ�硢��̿Ū���顼�����-2���᤹
	 *
	 * @author Nambe
	 * @see    execQuery
	 * @param string $strQuery       UPDATE������
	 * @param array  $aryParamArray  �ץ�ڥ�����ѥ�᡼�����Ǽ����Ϣ������
	 * 
	 * @return boolean : ���åץǡ��Ȥ�����������硢true. ���Ԥ������false���֤�
	 **/
	public function updDbData( $strQuery, $aryParamArray ) 
	{
		// ������¹Է�̤��Ǽ�����ѿ�
		$_mixQueryResult;
		// ������¹Է�̤��ѿ��˳�Ǽ
		$_mixQueryResult = $this->execQuery( $strQuery, $aryParamArray );
		// �����ͤ��֤äƤ����硢���Τޤ��֤�
		if( true === is_bool($_mixQueryResult) ){
			return $_mixQueryResult;
		}elseif( true === is_object($_mixQueryResult) ) {
		// ���֥������Ȥ��֤äƤ��Ƥ����硢���顼��ȯ�����Ƥ���Τ�
		// ��̿Ū�ʥ��顼�ʤΤ���� -2 ���֤�
			return self::DATABASE_ERROR;
		}else{
			return $_mixQueryResult;
		}
	}

	/**
	 * �ǡ�����������ݤ˻��Ѥ���ؿ�
	 *
	 * execQuery�ؿ�����Ѥ��Ƽ¹Է�̤��֤�
	 * 2012/07/11 Nambe
	 * execQuery�᥽�åɤ�����ͤ��Ѥ�äƤ��뤳�Ȥˤ�뽤��
	 * �����ͤ���äƤ��Ƥ����硢���Τޤ��᤹�����֥������Ȥξ�硢��̿Ū���顼�����-2���᤹
	 *
	 * @author Nambe
	 * @see    execQuery
	 * @param string $strQuery      : DELETE������
	 * @param array  $aryParamArray : �ץ�ڥ�����ѥ�᡼�����Ǽ����Ϣ������
	 * 
	 * @return boolean : ���åץǡ��Ȥ�����������硢true. ���Ԥ������false���֤�
	 **/
	public function delDbData( $strQuery, $aryParamArray ) 
	{
		// ������¹Է�̤��Ǽ�����ѿ�
		$_mixQueryResult;
		// ������¹Է�̤��ѿ��˳�Ǽ
		$_mixQueryResult = $this->execQuery( $strQuery, $aryParamArray );
		// �����ͤ��֤äƤ����硢���Τޤ��֤�
		if( true === is_bool($_mixQueryResult) ){
			return $_mixQueryResult;
		}elseif( true === is_object($_mixQueryResult) ) {
		// ���֥������Ȥ��֤äƤ��Ƥ����硢���顼��ȯ�����Ƥ���Τ�
		// ��̿Ū�ʥ��顼�ʤΤ���� -2 ���֤�
			return self::DATABASE_ERROR;
		}else{
			return $_mixQueryResult;
		}
	}

	/**
	 * �ǡ����١�������ǡ������������ݤ˼¹Ԥ���ؿ�
	 *
	 * execQuery�ؿ�����Ѥ��Ƽ¹Է�̤��֤�
	 * execQuery�᥽�åɤμ¹Է�̤ˤ�äơ�����ͤ��㤦�Τ�ʬ��������ԤäƤ����֤�
	 *
	 * @author Nambe
	 * @see    execQuery
	 * @param string $strQuery      : SELECT������
	 * @param array  $aryParamArray : �ץ�ڥ�����ѥ�᡼�����Ǽ����Ϣ������
	 * 
	 * @return mixed : �ǡ�����������������˹Ԥ�줿��硢������Ǽ��������ǡ������ʬ�֥����򥭡����ͤ�value�˻��ġ�¿��������
	 *				   �����ǡ�����̵���ä����ϡ���������
	 * 				   ���顼��ȯ��������硢int���Υ��顼���
	 **/
	public function pullDbData( $strQuery, $aryParamArray ) 
	{
		/**
		 * �������¹Ԥ���٤Υץ�ڥ����ɥ��ơ��ȥ��Ȥμ¹Է�̤��Ǽ���륪�֥�������
		 **/
		$_objPdoStatement;

		try {
			// ��������ʸ���� && ��������������� && �ͤ�¸�ߤ���˾��
			// �ץ�ڥ���¹Ԥ���
			if ( true == is_string($strQuery) 
			     && ( true == is_array($aryParamArray) && 0 < count($aryParamArray) ) 
			) {
				// �ץ�ڥ���¹�
				$_objPdoStatement = $this->_objPdo->prepare($strQuery);
				// �ѥ�᡼���ο������롼�׽�����¹Ԥ����ͤ򥻥åȤ���
				foreach ($aryParamArray as $key => $value){
					$_objPdoStatement->bindValue( $key, $value );
				}
				// �ѥ�᡼���򥻥åȤ����������¹�
				$_objPdoStatement->execute();
				// �¹Է�̤�ե��å������֤�
				return $_objPdoStatement->fetchAll(PDO::FETCH_ASSOC);

			} // ��������ʸ���� && ( ������������� && �ͤ�¸�ߤ��ʤ�) ���
			  // �ץ�ڥ�����ɬ�פ��ʤ����ᡢľ�ܥ������¹�
			elseif ( true == is_string($strQuery) 
					 && true == is_array($aryParamArray) && 0 == count($aryParamArray) 
			) {
				// �������¹Ԥ�PDOStatement�����
				$_objPdoStatement = $this->_objPdo->query($strQuery);
				// �¹Է�̤�ե��å������֤�
				return $_objPdoStatement->fetchAll(PDO::FETCH_ASSOC);

			} // ����ʳ��ξ������������ְ㤨�Ƥ���Τǡ��᡼������������顼���֤�
			else {
				// �����ƥ�����Ԥ˰���������Υ��顼�᡼�����������
				$this->sendArgumentError( $strQuery, $aryParamArray );
				// �ؿ��¹Ի��������顼���֤�
				return self::ARGUMENT_ERROR;
			}
		} // PDO�����ǥ��顼��ȯ���������
		catch( PDOException $objException ) {
			// �����ƥ�����Ԥ˥᡼������������Ԥ�
			$this->sendErrorMail($objException);
			// �¹Ԥ��������ˤ�äơ�����ͤ�Ƚ�̤���ɬ�פ�����Τ�
			// ���顼�����ɤǤϤʤ��㳰���֥������ȼ��Τ��ᤷ��
			// �ƥ�����¹ԥ᥽�åɤ�Ƚ�Ǥ�������ͤ��֤��ͤ˽���
			return $objException;
		}

	}

	/**
	 * �ǡ����١����������¹Ԥ���ؿ�
	 *
	 * �¹Ԥ���SELECT������ϥץ�ڥ����ɥ��ơ��ȥ��Ȥη����ǵ��Ҥ�
	 * �������������˥ѥ�᡼����Key������������ƤϤ���ͤ�Value�Ȥ���Ϣ����������ꤹ��
	 * "SELECT * FROM hoge_Tbl;"�Τ褦�ʥѥ�᡼���������פʥ�����ξ��
	 * ��������ˤ϶�����������ꤹ�뤳��
	 *
	 * @author Nambe
	 * @link   http://jp.php.net/manual/ja/pdo.prepare.php
	 * @see    sendErrorMail
	 * @param string $strQuery      : �ǡ������������٤Υ������ץ�ڥ����ɥ��ơ��ȥ��Ȥη����ǵ���
	 * @param array  $aryParamArray : �ץ�ڥ����ɥ��ơ��ȥ��ȤΥѥ�᡼������Key,�ͤ�Value�˻���Ϣ������
	 * 								  �ѥ�᡼���������פʾ�硢����Τߤζ������������Ϥ�
	 * 
	 * @return mixed : PDOStatement ���� true ���� false
	 **/
	public function execQuery( $strQuery, $aryParamArray ) 
	{
		/**
		 * �������¹Ԥ���٤Υץ�ڥ����ɥ��ơ��ȥ��Ȥμ¹Է�̤��Ǽ���륪�֥�������
		 **/
		$_objPdoStatement;

		/**
		 * �¹Է�̹Կ����Ǽ�����ѿ�
		 **/
		$_mixQueryResult;

		try {
			// ��������ʸ���� && ��������������� && �ͤ�¸�ߤ���˾��
			// �ץ�ڥ���¹Ԥ���
			if ( true == is_string($strQuery) 
			     && ( true == is_array($aryParamArray) && 0 < count($aryParamArray) ) 
			) {
				// �ץ�ڥ���¹�
				$_objPdoStatement = $this->_objPdo->prepare($strQuery);
				// �ѥ�᡼���ο������롼�׽�����¹Ԥ����ͤ򥻥åȤ���
				foreach ($aryParamArray as $key => $value){
					$_objPdoStatement->bindValue( $key, $value );
				}
				// �ѥ�᡼���򥻥åȤ���������μ¹Է��(true)���֤���
				// �¹Ի����顼�ξ���㳰�����˲��Τ�false�����ʤ�
				$_mixQueryResult = $_objPdoStatement->execute();

				// �¹Է�̤�true�ξ�硢�ºݤ˲��Թ������줿����Ĵ�١�
				// ������̤�0��ξ�硢0���֤����¹Է�̤�1�԰ʾ夢���硢true���֤�
				if( true === $_mixQueryResult) {
					$_intResultRows = $_objPdoStatement->rowCount();
					if( 0 === $_intResultRows ){
						return 0;
					}else{
						return true;
					}
				}elseif( false === $_mixQueryResult) {
					return false;
				}

			} // ��������ʸ���� && ( ������������� && �ͤ�¸�ߤ��ʤ�) ���
			  // �ץ�ڥ�����ɬ�פ��ʤ����ᡢľ�ܥ������¹�
			elseif ( true == is_string($strQuery) 
					 && true == is_array($aryParamArray) && 0 == count($aryParamArray) 
			) {
				// �������¹�
				$this->_objPdo->query($strQuery);
				// �¹Է�̤��������֤������Ԥ�����硢�㳰�����˲��Τǲ���return true��ɾ������ʤ�
				return true;

			} // ����ʳ��ξ������������ְ㤨�Ƥ���Τǡ��᡼������������顼���֤�
			else {
				// �����ƥ�����Ԥ˰���������Υ��顼�᡼�����������
				$this->sendArgumentError( $strQuery, $aryParamArray );
				// �ؿ��¹Ի��������顼���֤�
				return self::ARGUMENT_ERROR;
			}
		} // PDO�����ǥ��顼��ȯ���������
		catch( PDOException $objException ) {
			// �����ƥ�����Ԥ˥᡼������������Ԥ�
			$this->sendErrorMail($objException);
			// DB�������顼���֤�
			// ���Ѥ���ץ����ˤ�äơ��֤����顼�����ɤ�ʬ����ɬ�פ������
			// ���顼���֥������Ȥ��֤��褦�˽���
			return $objException;
		}
	}

	/**
	 * �ؿ��ΰ������ְ�äƤ���ݤ˥᡼����������ؿ�
	 *
	 * @author Nambe
	 * @param string $strQuery      : �ؿ���Ϳ����줿������
	 * @param string $aryParamArray : �ؿ���Ϳ����줿�ץ�ڥ��ѤΥѥ�᡼����
	 * 
	 **/
	protected function sendArgumentError( $strQuery, $aryParamArray )
	{
		/**
		 * �����ƥ�����Ԥ���������᡼�륯�饹���Ǽ�����ѿ�
		 **/
		$objMailClass;

		/**
		 * �᡼�륵�֥������Ȥ��Ǽ�����ѿ�
		 **/
		$strMailSubject;

		/**
		 * �᡼����ʬ���Ǽ�����ѿ�
		 **/
		$strMailBody;

		/**
		 * �ץ�ڥ��ѿ��ν���ɽ���˻��Ѥ��륤�󥯥�����ѿ�
		 **/
		$intArgumentNumber;

		// �᡼�륵�֥�����������
		$strMailSubject = "{$this->_strSystemName}���ؿ��¹Ի��������顼";
		// �᡼����ʬ
		$strMailBody    = "���顼ȯ�������С�̾��{$_SERVER['SERVER_NAME']}\r\n"
						. "���顼ȯ���ե����롡��{$_SERVER['SCRIPT_FILENAME']}\r\n"
						. "�ڥ��顼ȯ���������\r\n{$strQuery}\r\n"
						. "�ڥץ�ڥ��ѿ�����ȡ�\r\n";

		$intArgumentNumber = 1;

		if ( true == is_array($aryParamArray) ){
			// �����η��ʬ�롼�׽�����Ԥäƥ᡼��ܥǥ����ɲ�
			foreach ( $aryParamArray as $key => $value ) {
					$strMailBody .= "����{$intArgumentNumber}��KEY��{$key} �͡�{$value}\r\n";
				$intArgumentNumber++;
			}
		}else{
			$strMailBody .= "�ץ�ڥ��ѿ�������ʳ��Ǽ����Ϥ���Ƥ��ޤ�\r\n"
						 . "�ǡ�������" . gettype($aryParamArray) ."\r\n"
						 . "�͡�������{$aryParamArray}";
		}

		// �᡼����������
		$objMailClass = new clsMail( $this->_strAdminMailAddress, 
									 $_SERVER['SERVER_NAME'], 
									 "hoge@inform.co.jp",
									 $strMailSubject,
									 $strMailBody
									);
		// �᡼������
		$objMailClass->sendMail();
	}

	/**
	 * DB���ǥ��顼��ȯ�������ݤ˥᡼����������ؿ�
	 *
	 * @author Nambe
	 * @param string $errException : ���顼��ȯ�������㳰���֥�������
	 * 
	 **/
	protected function sendErrorMail($errException)
	{
		/**
		 * �����ƥ�����Ԥ���������᡼�륯�饹���Ǽ�����ѿ�
		 **/
		$objMailClass;

		/**
		 * �᡼�륵�֥������Ȥ��Ǽ�����ѿ�
		 **/
		$strMailSubject;

		/**
		 * �᡼����ʬ���Ǽ�����ѿ�
		 **/
		$strMailBody;

		/**
		 * �ץ�ڥ��ѿ��ν���ɽ���˻��Ѥ��륤�󥯥�����ѿ�
		 **/
		$intArgumentNumber;

		/**
		 * ���顼���֥������Ȥ����Ƥ��Ǽ��������
		 **/
		$aryErrorArray;

		// �᡼�륵�֥�����������
		$strMailSubject = "{$this->_strSystemName}��������¹Ի����顼";

		// �¹ԥ��饹¦�Υ��顼���󵭽�
		$strMailBody    = "PDO���顼��å�������" . $errException->getMessage() . "\r\n"
						. "���顼ȯ�������С�̾��{$_SERVER['SERVER_NAME']}\r\n"
						. "�ڥ��顼�����\r\n";

		// ���顼���������˼���
		$aryErrorArray = $errException->getTrace();
		// ���Ϥ�Хåե���󥰤���
		ob_start();
		// ���顼��������
		var_dump($aryErrorArray);
		// ���Ϥ������顼�����᡼��ܥǥ��˳�Ǽ
		$strMailBody    .= ob_get_contents();
		// �Хåե��򥯥ꥢ
		ob_end_clean();

		// �᡼����������
		$objMailClass = new clsMail( $this->_strAdminMailAddress, 
									 $_SERVER['SERVER_NAME'], 
									 "hoge@inform.co.jp",
									 $strMailSubject,
									 $strMailBody
									);
		// �᡼������
		$objMailClass->sendMail();

	}

	/**
	 * �ȥ�󥶥������
	 *
	 **/
	public function execTransaction() {
		$this->_objPdo->beginTransaction();
	}
		
	/**
	 * ����Хå�
	 *
	 **/
	public function execRollback() {
		$this->_objPdo->rollBack();
	}
		
	/**
	 * ���ߥå�
	 *
	 **/
	public function execCommit() {
		$this->_objPdo->commit();
	}


	/**
	 * ������μ¹Է�̤����������Ԥ��Τ�Ƚ�ꤹ��ؿ�
	 * 
	 * @param $aryResult pullDbData���Υ�����¹Դؿ�������� 
	 * @return boolean true:���顼 false:����
	 */
	public function isError($aryResult){

		if( $aryResult === true || $aryResult === 0 ){
			return false;
		} else if( false === is_array($aryResult) 
			|| false === $aryResult 
			|| self::DATABASE_ERROR === $aryResult 
			|| self::ARGUMENT_ERROR === $aryResult  
		){
			return true;
		} else {
			return false;
		}
	}

}
?>
