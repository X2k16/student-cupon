<?php
include_once(dirname(__FILE__) . "/../../config.php");
include_once(dirname(__FILE__) . "/../../common.php");

try{
	$pdo = new PDO(DB_DSN, DB_USER, DB_PASSWORD);

	// sql実行時のエラーをexceptionでとるようにする
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	// 重複チェック
	$stmt = $pdo->prepare("SELECT COUNT(id) AS count FROM students WHERE email = :mail");
	$stmt->bindValue(':mail', $data['mail'], PDO::PARAM_STR);
	$stmt->execute();
	$result = $stmt->fetch(PDO::FETCH_ASSOC);
	if ($result["count"] > 0) {
		response(422, array("result"=>"error", "message"=>"duplicated"));
		exit(0);
	}

	// 登録
	$stmt = $pdo->prepare("INSERT INTO students (name, sex, email, university, department) VALUES (:name, :sex, :mail, :university, :department)");

	$stmt->bindValue(':name', $data['name'], PDO::PARAM_STR);
	$stmt->bindValue(':sex', $data['sex'], PDO::PARAM_INT);
	$stmt->bindValue(':mail', $data['mail'], PDO::PARAM_STR);
	$stmt->bindValue(':university', $data['university'], PDO::PARAM_STR);
	$stmt->bindValue(':department', $data['department'], PDO::PARAM_STR);

	$stmt->execute();

	// 登録したユーザのIDを取得
	$stmt = $pdo->prepare("SELECT id FROM students WHERE email=:mail limit 1");

	$stmt->bindValue(':mail', $data['mail'], PDO::PARAM_STR);
	$stmt->execute();
	$student_id = $stmt->fetchColumn();

	// メールとクーポンコードを紐付け
	$stmt = $pdo->prepare("UPDATE student_cupons SET student_id=:student_id where student_id is NULL limit 1");
	$stmt->bindValue(':student_id', $student_id, PDO::PARAM_INT);
	$stmt->execute();

	// ひも付けたクーポンを取得
	$stmt = $pdo->prepare("SELECT code FROM student_cupons WHERE student_id=:student_id limit 1");
	$stmt->bindValue(':student_id', $student_id, PDO::PARAM_INT);
	$stmt->execute();
	$code = $stmt->fetchColumn();

	// メール本文
	$name = $data['name'];
	$message = "$name 様

エンジニアサポート CROSS 2016実行委員会です。
在学情報を登録頂き、ありがとうございます。

学割クーポンを発行いたしました。
Peatixのチケット販売サイトから購入手続きに進んでいただき、
枚数入力ページにて以下の割引コードをご入力ください。

割引コード　　　　    : $code
チケット販売サイトURL : https://peatix.com/sales/event/125846/tickets


その他、ご要望、ご質問などは、
エンジニアサポート CROSS 2016公式Facebookページ、
Twitterアカウントまでお問い合わせください。

facebook: https://www.facebook.com/engineersupportCROSS
Twitter : https://twitter.com/e_s_cross

-- 
エンジニアサポート CROSS 2016
http://2016.cross-party.com/
"
;

	$subject = '[エンジニアサポートCROSS2016] 学割クーポン発行のお知らせ';


	// メール送信
	if (!sendmail($data['mail'], $subject, $message)){

	};

	// 成功
	response(200, array("result"=>"success"));

}catch (PDOException $e){
	response(500, array("result"=>"error", "message"=>"database exception"));
}
