<?php

class clsCommonDatabase extends clsDataBase {

	private static $objInstance = null;

	/**
	 * シングルトンインスタンスの取得関数
	 *
	 * @return clsCommonDatabaseオブジェクト
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
	 * クエリの実行結果が成功か失敗かのみ判定する関数
	 *
	 * @param $aryResult pullDbData等のクエリ実行関数の戻り値 
	 * @return boolean true:エラー false:成功
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
			// システム管理者に引数不整合のエラーメールを送信する
			$this->sendArgumentError( $strQuery, $aryParameter );
			// 関数実行時引数エラーを返す
			return false;
		}

		$_objPdoStatement;

		try {
			// プリペアを実行
			$_objPdoStatement = $this->_objPdo->prepare($strQuery);
			// パラメータの数だけループ処理を実行し、値をセットする
			foreach ($aryParameter as $key => $value){
				$_objPdoStatement->bindValue( $key, $value );
			}

			// パラメータをセットしたクエリを実行
			$_objPdoStatement->execute();
			// 実行結果をフェッチして返す
			return $_objPdoStatement->fetchAll(PDO::FETCH_ASSOC);

		} catch( PDOException $objException ) {
			// システム管理者にメール送信処理を行う
			$this->sendErrorMail($objException);

			return false;
		}
	}

	public function execute( $strQuery, $aryParameter ) {
		if ( !is_string($strQuery) || !is_array($aryParameter) ) {
			// システム管理者に引数不整合のエラーメールを送信する
			$this->sendArgumentError( $strQuery, $aryParameter );
			// 関数実行時引数エラーを返す
			return false;
		}

		try {
			// プリペアを実行
			$_objPdoStatement = $this->_objPdo->prepare($strQuery);
			// パラメータの数だけループ処理を実行し、値をセットする
			foreach ($aryParameter as $key => $value){
				$_objPdoStatement->bindValue( $key, $value );
			}

			// パラメータをセットしたクエリを実行
			return $_objPdoStatement->execute();

		} catch( PDOException $objException ) {
			// システム管理者にメール送信処理を行う
			$this->sendErrorMail($objException);

			return false;
		}
	}


	public function executeMulti( $strQuery, $aryParameters ) {
		if ( !is_string($strQuery) || !is_array($aryParameters) ) {
			// システム管理者に引数不整合のエラーメールを送信する
			$this->sendArgumentError( $strQuery, $aryParameters );
			// 関数実行時引数エラーを返す
			return false;
		}

		try {
			// プリペアを実行
			$_objPdoStatement = $this->_objPdo->prepare($strQuery);

			foreach ( $aryParameters as $aryParameter ){
				// パラメータの数だけループ処理を実行し、値をセットする
				foreach ($aryParameter as $key => $value){
					$_objPdoStatement->bindValue( $key, $value );
				}
				// パラメータをセットしたクエリを実行
				if( !$_objPdoStatement->execute() ){
					return false;
				}
			}
			return true;


		} catch( PDOException $objException ) {
			// システム管理者にメール送信処理を行う
			$this->sendErrorMail($objException);

			return false;
		}
	}
}