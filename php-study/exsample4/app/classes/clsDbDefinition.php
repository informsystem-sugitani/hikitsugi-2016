<?php
/**
 *	定義クラス
 *
 *	項目の定義をおこなうクラス
 *
 *	@author			sugitani 2016/03/11
 *	@version		1.0
 */

class clsDbDefinition{

	// ***開発環境*** データベース接続情報




  // // ***本番環境*** データベース接続情報
  const DB_TYPE      = 'pgsql';       //接続するデータベースの種類
  const DB_HOST      = 'localhost';     //接続先のホスト
  const DB_NAME      = 'login_db';     //接続するデータベース名称
  const DB_USER_NAME = 'pgsql';      //データベースに接続するユーザーネーム
  const DB_PASSWORD  = '';      //データベースに接続するユーザーのパスワード


}


?>