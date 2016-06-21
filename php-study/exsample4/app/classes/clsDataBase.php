<?php
/**
 * 汎用データベース処理クラス
 *
 * 各システムにおいて、データベース操作に使用する汎用クラス
 * PHPのPDOを使用する。現状、継承を前提として作成
 * クエリ実行に当たっては、プリペアードステートメントを使用する
 * 各メソッドでエラーが発生した際には、エラーコードを返す
 * エラーコードに関しては、下記const変数参照
 *
 * @author 2012/02/ Nambe
 * @link   http://jp.php.net/manual/ja/book.pdo.php
 *
 **/
require_once("clsMail.php");

class clsDataBase
{
	/**
	 * PDOオブジェクトを格納する変数
	 **/
	protected $_objPdo;

	/**
	 * 接続するデータベースのタイプを設定
	 **/
	protected $_strDbType;

	/**
	 * 接続するデータベースのホストを設定
	 **/
	protected $_strHostName;

	/**
	 * 接続するデータベース名称を設定
	 **/
	protected $_strDbName;

	/**
	 * データベースに接続するユーザー名を設定
	 **/
	protected $_strDbUserName;

	/**
	 * データベースに接続するユーザーのパスワードを設定
	 **/
	protected $_strDbPassWord;

	/**
	 * データベース処理エラー時に送信するエラーメールの送信先
	 **/
	protected $_strAdminMailAddress;

	/**
	 * システム名。エラーメール送信の件名で使用
	 **/
	protected $_strSystemName;

	/**
	 * メソッド実行時、引数不整合エラー
	 **/
	const ARGUMENT_ERROR = -1;

	/**
	 * メソッド実行時、DB処理エラー
	 **/
	const DATABASE_ERROR = -2;

	/**
	 * データ新規登録時　一意成約違反
	 **/
	const PRIMARY_ERROR  = -3;


	/**
	 * コンストラクタ
	 *
	 * データベース接続情報を引数に持ち、PDOオブジェクトの動作を設定する。<br />
	 * データベースでエラーが発生した際は例外を発生させる
	 * 
	 * @author Nambe
	 * 
	 * @param string $dbType    接続するデータベースの種類を記述
	 * @param string $hostName  接続先のホストを記述
	 * @param string $dbName    接続するデータベース名称を記述
	 * @param string $userName  データベースに接続するユーザーネームを記述
	 * @param string $passWord  データベースに接続するユーザーのパスワードを記述
	 * 
	 **/
	function __construct( $dbType, $hostName, $dbName, $userName, $passWord )
	{
		// データベース関連初期設定
		$this->setDbType( $dbType );		// データベースタイプを設定
		$this->setHostName( $hostName );	// ホストを設定
		$this->setDbName( $dbName );		// データベースを設定
		$this->setUserName( $userName );	// ユーザーを設定
		$this->setPassWord( $passWord );	// パスワードを設定

		// エラーが発生した際のアラートメールの送信設定初期化
		// 本番導入時にはアドレスは"system@inform-us.com"に設定すること
		$this->_strAdminMailAddress = 'sugitani@inform.co.jp'; 				// 送信先
		$this->_strSystemName       = '【社内テスト】'.$_SERVER['HTTP_HOST'];	// システム名

		try {
			$this->_objPdo = new PDO("{$this->_strDbType}:host={$this->_strHostName};dbname={$this->_strDbName}", 
									 "{$this->_strDbUserName}",
									 "{$this->_strDbPassWord}");

			// エラー発生時に例外を発生させるように設定
			$this->_objPdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
		} catch( PDOException $ex ) {
			// エラーメールを送信
			$this->sendErrorMail($ex);
			// トリガーエラーを発生させて、エラーハンドラ―に処理おまかせ
			trigger_error( 'PDOオブジェクトの生成失敗', E_USER_ERROR);
		}
	}

	/**
	 * 接続するデータベースタイプを設定する関数
	 *
	 * @author Nambe
	 * @param string $dbType : 接続するデータベースタイプを記述
	 **/
	public function setDbType( $dbType )
	{
		$this->_strDbType = $dbType;
	}

	/**
	 * 接続先のホストを設定する関数
	 *
	 * @author Nambe
	 * @param string $hostName : 接続するデータベースタイプを記述
	 **/
	public function setHostName( $hostName ) 
	{
		$this->_strHostName = $hostName;
	}

	/**
	 * 接続するデータベースを設定する関数
	 *
	 * @author Nambe
	 * @param string $dbName : 接続するデータベース名を記述
	 **/
	public function setDbName( $dbName ) 
	{
		$this->_strDbName = $dbName;
	}

	/**
	 * データベースに接続するユーザーを設定する関数
	 *
	 * @author Nambe
	 * @param string $userName : 接続するユーザーを記述
	 **/
	public function setUserName( $userName ) 
	{
		$this->_strDbUserName = $userName;
	}

	/**
	 * データベースに接続するユーザーを設定する関数
	 *
	 * @author Nambe
	 * @param string $userName : 接続するユーザーを記述
	 **/
	public function setPassWord( $passWord ) 
	{
		$this->_strDbPassWord = $passWord;
	}

	/**
	 * エラーメールの送信先を設定する
	 *
	 * @author Nambe
	 * @param string $mailAddress  エラー発生時のメール送信先
	 **/
	public function setAdminAddress( $mailAddress ) 
	{
		$this->_strAdminMailAddress = $mailAddress;
	}

	/**
	 * データをインサートする際に使用する関数
	 *
	 * execQuery関数を使用して実行結果を返す
	 *
	 * @author Nambe
	 * @see    execQuery
	 * @param string $strQuery       INSERTクエリ
	 * @param array  $aryParamArray  プリぺアするパラメータを格納した連想配列
	 * 
	 * @return mixed : インサートに成功した場合、true. 
	 * 				   一意制約違反の場合 -3
	 **/
	public function addDbData( $strQuery, $aryParamArray ) 
	{
		// クエリ実行結果を格納する変数
		$_mixQueryResult;
		// クエリ実行結果を変数に格納
		$_mixQueryResult = $this->execQuery( $strQuery, $aryParamArray );

		// 論理値が返っている場合、そのまま返す
		if( true === is_bool($_mixQueryResult) ){
			return $_mixQueryResult;
		}elseif( true === is_object($_mixQueryResult) ) {
		// オブジェクトが返ってきている場合、エラーが発生しているので
		// エラーコードで判断して戻り値を返す
			// 23505の場合、一意成約違反なので、定数-3を返す
			if( 23505 == $_mixQueryResult->getCode() ){
				return self::PRIMARY_ERROR;
			}else{
				// それ以外の場合、致命的なエラーなので定数 -2 を返す
				return self::DATABASE_ERROR;
			}
		}else{
			return $_mixQueryResult;
		}
	}

	/**
	 * データをアップデートする際に使用する関数
	 *
	 * execQuery関数を使用して実行結果を返す
	 * 2012/07/11 Nambe
	 * execQueryメソッドの戻り値が変わっていることによる修正
	 * 論理値が戻ってきている場合、そのまま戻す。オブジェクトの場合、致命的エラーの定数-2を戻す
	 *
	 * @author Nambe
	 * @see    execQuery
	 * @param string $strQuery       UPDATEクエリ
	 * @param array  $aryParamArray  プリぺアするパラメータを格納した連想配列
	 * 
	 * @return boolean : アップデートに成功した場合、true. 失敗した場合falseを返す
	 **/
	public function updDbData( $strQuery, $aryParamArray ) 
	{
		// クエリ実行結果を格納する変数
		$_mixQueryResult;
		// クエリ実行結果を変数に格納
		$_mixQueryResult = $this->execQuery( $strQuery, $aryParamArray );
		// 論理値が返っている場合、そのまま返す
		if( true === is_bool($_mixQueryResult) ){
			return $_mixQueryResult;
		}elseif( true === is_object($_mixQueryResult) ) {
		// オブジェクトが返ってきている場合、エラーが発生しているので
		// 致命的なエラーなので定数 -2 を返す
			return self::DATABASE_ERROR;
		}else{
			return $_mixQueryResult;
		}
	}

	/**
	 * データを削除する際に使用する関数
	 *
	 * execQuery関数を使用して実行結果を返す
	 * 2012/07/11 Nambe
	 * execQueryメソッドの戻り値が変わっていることによる修正
	 * 論理値が戻ってきている場合、そのまま戻す。オブジェクトの場合、致命的エラーの定数-2を戻す
	 *
	 * @author Nambe
	 * @see    execQuery
	 * @param string $strQuery      : DELETEクエリ
	 * @param array  $aryParamArray : プリぺアするパラメータを格納した連想配列
	 * 
	 * @return boolean : アップデートに成功した場合、true. 失敗した場合falseを返す
	 **/
	public function delDbData( $strQuery, $aryParamArray ) 
	{
		// クエリ実行結果を格納する変数
		$_mixQueryResult;
		// クエリ実行結果を変数に格納
		$_mixQueryResult = $this->execQuery( $strQuery, $aryParamArray );
		// 論理値が返っている場合、そのまま返す
		if( true === is_bool($_mixQueryResult) ){
			return $_mixQueryResult;
		}elseif( true === is_object($_mixQueryResult) ) {
		// オブジェクトが返ってきている場合、エラーが発生しているので
		// 致命的なエラーなので定数 -2 を返す
			return self::DATABASE_ERROR;
		}else{
			return $_mixQueryResult;
		}
	}

	/**
	 * データベースからデータを取得する際に実行する関数
	 *
	 * execQuery関数を使用して実行結果を返す
	 * execQueryメソッドの実行結果によって、戻り値が違うので分岐処理を行ってから返す
	 *
	 * @author Nambe
	 * @see    execQuery
	 * @param string $strQuery      : SELECTクエリ
	 * @param array  $aryParamArray : プリぺアするパラメータを格納した連想配列
	 * 
	 * @return mixed : データ取得処理が正常に行われた場合、クエリで取得したデータ件数分「カラムをキー、値をvalueに持つ」多次元配列。
	 *				   該当データが無かった場合は、空の配列。
	 * 				   エラーが発生した場合、int型のエラー定数
	 **/
	public function pullDbData( $strQuery, $aryParamArray ) 
	{
		/**
		 * クエリを実行する為のプリペアードステートメントの実行結果を格納するオブジェクト
		 **/
		$_objPdoStatement;

		try {
			// 第一引数が文字列 && （第二引数が配列 && 値が存在する）場合
			// プリペアを実行する
			if ( true == is_string($strQuery) 
			     && ( true == is_array($aryParamArray) && 0 < count($aryParamArray) ) 
			) {
				// プリペアを実行
				$_objPdoStatement = $this->_objPdo->prepare($strQuery);
				// パラメータの数だけループ処理を実行し、値をセットする
				foreach ($aryParamArray as $key => $value){
					$_objPdoStatement->bindValue( $key, $value );
				}
				// パラメータをセットしたクエリを実行
				$_objPdoStatement->execute();
				// 実行結果をフェッチして返す
				return $_objPdoStatement->fetchAll(PDO::FETCH_ASSOC);

			} // 第一引数が文字列 && ( 第二引数が配列 && 値が存在しない) 場合
			  // プリペアする必要がないため、直接クエリを実行
			elseif ( true == is_string($strQuery) 
					 && true == is_array($aryParamArray) && 0 == count($aryParamArray) 
			) {
				// クエリを実行しPDOStatementを取得
				$_objPdoStatement = $this->_objPdo->query($strQuery);
				// 実行結果をフェッチして返す
				return $_objPdoStatement->fetchAll(PDO::FETCH_ASSOC);

			} // それ以外の場合引数の設定を間違えているので、メールを送信しエラーを返す
			else {
				// システム管理者に引数不整合のエラーメールを送信する
				$this->sendArgumentError( $strQuery, $aryParamArray );
				// 関数実行時引数エラーを返す
				return self::ARGUMENT_ERROR;
			}
		} // PDO処理でエラーが発生した場合
		catch( PDOException $objException ) {
			// システム管理者にメール送信処理を行う
			$this->sendErrorMail($objException);
			// 実行した処理によって、戻り値を判別する必要があるので
			// エラーコードではなく例外オブジェクト自体を戻し、
			// 各クエリ実行メソッドで判断して戻り値を返す様に修正
			return $objException;
		}

	}

	/**
	 * データベースクエリを実行する関数
	 *
	 * 実行するSELECTクエリはプリペアードステートメントの形式で記述し
	 * 第二引数の配列にパラメータをKey、クエリに当てはめる値をValueとする連想配列を設定する
	 * "SELECT * FROM hoge_Tbl;"のようなパラメーターが不要なクエリの場合
	 * 第二引数には空の配列を設定すること
	 *
	 * @author Nambe
	 * @link   http://jp.php.net/manual/ja/pdo.prepare.php
	 * @see    sendErrorMail
	 * @param string $strQuery      : データを取得する為のクエリをプリペアードステートメントの形式で記述
	 * @param array  $aryParamArray : プリペアードステートメントのパラメーターをKey,値をValueに持つ連想配列
	 * 								  パラメーターが不要な場合、宣言のみの空配列を引数に渡す
	 * 
	 * @return mixed : PDOStatement 又は true 又は false
	 **/
	public function execQuery( $strQuery, $aryParamArray ) 
	{
		/**
		 * クエリを実行する為のプリペアードステートメントの実行結果を格納するオブジェクト
		 **/
		$_objPdoStatement;

		/**
		 * 実行結果行数を格納する変数
		 **/
		$_mixQueryResult;

		try {
			// 第一引数が文字列 && （第二引数が配列 && 値が存在する）場合
			// プリペアを実行する
			if ( true == is_string($strQuery) 
			     && ( true == is_array($aryParamArray) && 0 < count($aryParamArray) ) 
			) {
				// プリペアを実行
				$_objPdoStatement = $this->_objPdo->prepare($strQuery);
				// パラメータの数だけループ処理を実行し、値をセットする
				foreach ($aryParamArray as $key => $value){
					$_objPdoStatement->bindValue( $key, $value );
				}
				// パラメータをセットしたクエリの実行結果(true)を返す。
				// 実行時エラーの場合例外処理に回るのでfalseは戻らない
				$_mixQueryResult = $_objPdoStatement->execute();

				// 実行結果がtrueの場合、実際に何行更新されたかを調べ、
				// 更新結果が0件の場合、0を返す。実行結果が1行以上ある場合、trueを返す
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

			} // 第一引数が文字列 && ( 第二引数が配列 && 値が存在しない) 場合
			  // プリペアする必要がないため、直接クエリを実行
			elseif ( true == is_string($strQuery) 
					 && true == is_array($aryParamArray) && 0 == count($aryParamArray) 
			) {
				// クエリを実行
				$this->_objPdo->query($strQuery);
				// 実行結果の成功を返す。失敗した場合、例外処理に回るので下記return trueは評価されない
				return true;

			} // それ以外の場合引数の設定を間違えているので、メールを送信しエラーを返す
			else {
				// システム管理者に引数不整合のエラーメールを送信する
				$this->sendArgumentError( $strQuery, $aryParamArray );
				// 関数実行時引数エラーを返す
				return self::ARGUMENT_ERROR;
			}
		} // PDO処理でエラーが発生した場合
		catch( PDOException $objException ) {
			// システム管理者にメール送信処理を行う
			$this->sendErrorMail($objException);
			// DB処理エラーを返す
			// 使用するプログラムによって、返すエラーコードを分ける必要がある為
			// エラーオブジェクトを返すように修正
			return $objException;
		}
	}

	/**
	 * 関数の引数が間違っている際にメール送信する関数
	 *
	 * @author Nambe
	 * @param string $strQuery      : 関数に与えられたクエリ
	 * @param string $aryParamArray : 関数に与えられたプリペア用のパラメーター
	 * 
	 **/
	protected function sendArgumentError( $strQuery, $aryParamArray )
	{
		/**
		 * システム管理者に送信するメールクラスを格納する変数
		 **/
		$objMailClass;

		/**
		 * メールサブジェクトを格納する変数
		 **/
		$strMailSubject;

		/**
		 * メール本分を格納する変数
		 **/
		$strMailBody;

		/**
		 * プリペア変数の順番表示に使用するインクリメント変数
		 **/
		$intArgumentNumber;

		// メールサブジェクト設定
		$strMailSubject = "{$this->_strSystemName}：関数実行時引数エラー";
		// メール本分
		$strMailBody    = "エラー発生サーバー名：{$_SERVER['SERVER_NAME']}\r\n"
						. "エラー発生ファイル　：{$_SERVER['SCRIPT_FILENAME']}\r\n"
						. "【エラー発生クエリ】\r\n{$strQuery}\r\n"
						. "【プリペア変数の中身】\r\n";

		$intArgumentNumber = 1;

		if ( true == is_array($aryParamArray) ){
			// 引数の件数分ループ処理を行ってメールボディに追加
			foreach ( $aryParamArray as $key => $value ) {
					$strMailBody .= "引数{$intArgumentNumber}のKEY：{$key} 値：{$value}\r\n";
				$intArgumentNumber++;
			}
		}else{
			$strMailBody .= "プリぺア変数が配列以外で受け渡されています\r\n"
						 . "データ型：" . gettype($aryParamArray) ."\r\n"
						 . "値　　　：{$aryParamArray}";
		}

		// メール送信設定
		$objMailClass = new clsMail( $this->_strAdminMailAddress, 
									 $_SERVER['SERVER_NAME'], 
									 "hoge@inform.co.jp",
									 $strMailSubject,
									 $strMailBody
									);
		// メール送信
		$objMailClass->sendMail();
	}

	/**
	 * DB操作でエラーが発生した際にメール送信する関数
	 *
	 * @author Nambe
	 * @param string $errException : エラーが発生した例外オブジェクト
	 * 
	 **/
	protected function sendErrorMail($errException)
	{
		/**
		 * システム管理者に送信するメールクラスを格納する変数
		 **/
		$objMailClass;

		/**
		 * メールサブジェクトを格納する変数
		 **/
		$strMailSubject;

		/**
		 * メール本分を格納する変数
		 **/
		$strMailBody;

		/**
		 * プリペア変数の順番表示に使用するインクリメント変数
		 **/
		$intArgumentNumber;

		/**
		 * エラーオブジェクトの内容を格納する配列
		 **/
		$aryErrorArray;

		// メールサブジェクト設定
		$strMailSubject = "{$this->_strSystemName}：クエリ実行時エラー";

		// 実行クラス側のエラー情報記述
		$strMailBody    = "PDOエラーメッセージ：" . $errException->getMessage() . "\r\n"
						. "エラー発生サーバー名：{$_SERVER['SERVER_NAME']}\r\n"
						. "【エラー情報】\r\n";

		// エラー情報を配列に取得
		$aryErrorArray = $errException->getTrace();
		// 出力をバッファリングする
		ob_start();
		// エラー情報を出力
		var_dump($aryErrorArray);
		// 出力したエラー情報をメールボディに格納
		$strMailBody    .= ob_get_contents();
		// バッファをクリア
		ob_end_clean();

		// メール送信設定
		$objMailClass = new clsMail( $this->_strAdminMailAddress, 
									 $_SERVER['SERVER_NAME'], 
									 "hoge@inform.co.jp",
									 $strMailSubject,
									 $strMailBody
									);
		// メール送信
		$objMailClass->sendMail();

	}

	/**
	 * トランザクション
	 *
	 **/
	public function execTransaction() {
		$this->_objPdo->beginTransaction();
	}
		
	/**
	 * ロールバック
	 *
	 **/
	public function execRollback() {
		$this->_objPdo->rollBack();
	}
		
	/**
	 * コミット
	 *
	 **/
	public function execCommit() {
		$this->_objPdo->commit();
	}


	/**
	 * クエリの実行結果が成功か失敗かのみ判定する関数
	 * 
	 * @param $aryResult pullDbData等のクエリ実行関数の戻り値 
	 * @return boolean true:エラー false:成功
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
