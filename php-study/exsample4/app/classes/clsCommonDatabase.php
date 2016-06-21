<?php

class clsCommonDatabase extends clsDataBase {

	private static $objInstance = null;

	/**
	 * ���󥰥�ȥ󥤥󥹥��󥹤μ����ؿ�
	 *
	 * @return clsCommonDatabase���֥�������
	 */
	static function getInstance(){
		if( !self::$objInstance ){
			self::$objInstance = new clsCommonDatabase(
				clsDbDefinition::DB_TYPE,
				clsDbDefinition::DB_HOST,
				clsDbDefinition::DB_NAME,
				clsDbDefinition::DB_USER_NAME,
				clsDbDefinition::DB_PASSWORD
			);
		}

		return self::$objInstance;
	}

	static function beginTransaction(){
		self::getInstance()->execTransaction();
	}

	static function rollback(){
		self::getInstance()->execRollback();
	}

	static function commit(){
		self::getInstance()->execCommit();
	}


	/**
	 * ������μ¹Է�̤����������Ԥ��Τ�Ƚ�ꤹ��ؿ�
	 *
	 * @param $aryResult pullDbData���Υ�����¹Դؿ�������� 
	 * @return boolean true:���顼 false:����
	 */
	public function isError($aryResult){

		if( $aryResult === true ){
			return false;
		} else if( false === is_array($aryResult)
			|| false === $aryResult
			|| self::PRIMARY_ERROR  === $aryResult
			|| self::DATABASE_ERROR === $aryResult
			|| self::ARGUMENT_ERROR === $aryResult
		){
			return true;
		} else {
			return false;
		}
	}

	public function fetch( $strQuery, $aryParameter ) {
		if ( !is_string($strQuery) || !is_array($aryParameter) ) {
			// �����ƥ�����Ԥ˰���������Υ��顼�᡼�����������
			$this->sendArgumentError( $strQuery, $aryParameter );
			// �ؿ��¹Ի��������顼���֤�
			return false;
		}

		$_objPdoStatement;

		try {
			// �ץ�ڥ���¹�
			$_objPdoStatement = $this->_objPdo->prepare($strQuery);
			// �ѥ�᡼���ο������롼�׽�����¹Ԥ����ͤ򥻥åȤ���
			foreach ($aryParameter as $key => $value){
				$_objPdoStatement->bindValue( $key, $value );
			}

			// �ѥ�᡼���򥻥åȤ����������¹�
			$_objPdoStatement->execute();
			// �¹Է�̤�ե��å������֤�
			return $_objPdoStatement->fetchAll(PDO::FETCH_ASSOC);

		} catch( PDOException $objException ) {
			// �����ƥ�����Ԥ˥᡼������������Ԥ�
			$this->sendErrorMail($objException);

			return false;
		}
	}

	public function execute( $strQuery, $aryParameter ) {
		if ( !is_string($strQuery) || !is_array($aryParameter) ) {
			// �����ƥ�����Ԥ˰���������Υ��顼�᡼�����������
			$this->sendArgumentError( $strQuery, $aryParameter );
			// �ؿ��¹Ի��������顼���֤�
			return false;
		}

		try {
			// �ץ�ڥ���¹�
			$_objPdoStatement = $this->_objPdo->prepare($strQuery);
			// �ѥ�᡼���ο������롼�׽�����¹Ԥ����ͤ򥻥åȤ���
			foreach ($aryParameter as $key => $value){
				$_objPdoStatement->bindValue( $key, $value );
			}

			// �ѥ�᡼���򥻥åȤ����������¹�
			return $_objPdoStatement->execute();

		} catch( PDOException $objException ) {
			// �����ƥ�����Ԥ˥᡼������������Ԥ�
			$this->sendErrorMail($objException);

			return false;
		}
	}


	public function executeMulti( $strQuery, $aryParameters ) {
		if ( !is_string($strQuery) || !is_array($aryParameters) ) {
			// �����ƥ�����Ԥ˰���������Υ��顼�᡼�����������
			$this->sendArgumentError( $strQuery, $aryParameters );
			// �ؿ��¹Ի��������顼���֤�
			return false;
		}

		try {
			// �ץ�ڥ���¹�
			$_objPdoStatement = $this->_objPdo->prepare($strQuery);

			foreach ( $aryParameters as $aryParameter ){
				// �ѥ�᡼���ο������롼�׽�����¹Ԥ����ͤ򥻥åȤ���
				foreach ($aryParameter as $key => $value){
					$_objPdoStatement->bindValue( $key, $value );
				}
				// �ѥ�᡼���򥻥åȤ����������¹�
				if( !$_objPdoStatement->execute() ){
					return false;
				}
			}
			return true;


		} catch( PDOException $objException ) {
			// �����ƥ�����Ԥ˥᡼������������Ԥ�
			$this->sendErrorMail($objException);

			return false;
		}
	}
}