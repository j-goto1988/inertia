## PHP7.1
### 下位互換性のない変更点
#### 関数の引数不足がErrorになる
PHP7.0まではwarningだったものが、PHP7.1以降はArgumentCountErrorになる
```PHP
function test($param) {}
test(); // PHP7.1以降はError
```

#### 動的呼び出しできない関数がある
compact()、extract()、func_get_args()、get_defined_vars()、parse_str()などを$func()やarray_map()経由で呼ぶコードは危険

#### void/iterableがクラス名に使えない
PHP7.1からvoidとiterableは予約語扱いで、クラス・インターフェイス・トレイト名に使えない
```PHP
class Iterable {} // NG
```

#### rand()系の結果が変わる
rand()/srand()がmt_rand()/mt_srand()のエイリアスになり、rand()、shuffle()、str_shuffle()、array_rand()の結果が変わる可能性がある

#### $str[] = $x が致命的エラー
文字列に対して空のインデックス演算子を使うとfatalになる<br>
PHP7.0までは配列に変換されていた
```PHP
$str = '';
$str[] = 'a'; // PHP 7.1以降NG
```

#### ソート結果の同値要素の順番
比較結果が同じ要素の並び順が変わる可能性がある<br>
同値な要素の並び順に依存するコードは書かないようにする
```PHP
usort($items, function ($a, $b) {
    return $a['score'] <=> $b['score'];
});
```
同じscoreの中で順番に意味があるなら、第二条件を入れる
```PHP
return [$a['score'], $a['id']] <=> [$b['score'], $b['id']];
```

### PHP 7.1.x で推奨されなくなる機能
#### mcrypt拡張
PHP7.1で非推奨、PHP7.2でコアから削除予定とされている<br>
代替はOpenSSL

#### mb_ereg_replace()/mb_eregi_replace()のe修飾子
mb_ereg_replace()とmb_eregi_replace()のパターン修飾子eが非推奨

### 変更された関数
#### parse_url()が厳格化
PHP7.1でparse_url()がRFC3986対応でより厳格になっている<br>
URL解析まわりは要注意

#### session_start()失敗時の挙動変更
PHP7.1では、セッション開始に失敗した場合falseを返し、$_SESSIONを初期化しなくなった

#### mb_ereg()/mb_ereg_replace()が無効なバイト列を拒否
文字化けした文字列、不正なエンコーディングを正規表現にかけている場合に影響する可能性がある

### その他の変更
#### 文字列を数値演算している箇所
PHP7.1では、数値でない文字列を+ - * / %などで計算するとE_WARNING/E_NOTICEが出るようになった

#### $thisを変数名にしている古いコード
PHP7.1以降、$thisをユーザー定義変数として使ったり再代入したりできない

#### serialize_precisionの変更
serialize_precisionのデフォルトが-1になっている<br>
浮動小数点をJSONやシリアライズで扱っている場合、表示桁が変わる可能性がある


## PHP7.2
### 下位互換性のない変更点
#### count(null)でWarning
count($value)で$valueがnull/文字列/数値/CountableでないオブジェクトだとE_WARNINGが出る<br>
PHP7.2からcount()/sizeof()はcountableでない型に警告を出す
対策
```PHP
$count = is_countable($value) ? count($value) : 0;
```
PHP7.3未満も見るなら
```PHP
$count = is_array($value) || $value instanceof Countable
    ? count($value)
    : 0;
```

#### 未定義定数の扱い
PHP7.2ではE_WARNINGになり、将来はErrorになる

#### objectをクラス名に使えない
PHP7.2からobjectはクラス・trait・interface名として使えない

#### array_unique($array, SORT_STRING)の数値キー
PHP7.2からarray_unique()の内部処理が変わり、数値インデックスが以前と変わる可能性がある<br>
順番やキーに意味がある場合は注意
```PHP
$result = array_values(array_unique($array, SORT_STRING));
```
のように、必要なら明示的に詰め直す

#### number_format()が-0を返さない
以前は-0が出ることがあったが、PHP7.2では0になる

#### get_class(null)
PHP7.2ではget_class(null)が警告になる

#### hash_hmac()系
hash_hmac()などが非暗号化ハッシュを受け付けなくなった

#### date_parse()のzone
date_parse()/date_parse_from_format()のzoneが「分」ではなく「秒」、符号も逆になる

### PHP 7.2.x で推奨されなくなる機能
#### each()
```PHP
while (list($key, $value) = each($array)) {
}
```
foreachに置き換え<br>
PHP7.2で非推奨

#### create_function()
eval() のラッパーなのでセキュリティ的にも危険で、無名関数へ置き換え推奨
```PHP
$fn = function ($a, $b) {
    return $a + $b;
};
```

#### __autoload()
spl_autoload_register()に置き換える

#### parse_str()の第2引数なし
必ず第2引数へ配列を渡す

#### クォートしない文字列
```PHP
$array[key]
```
みたいな書き方<br>
未定義定数の場合、PHP7.2からE_WARNINGになる

#### assert()に文字列を渡す
```PHP
assert('$x > 0');
```
文字列評価なので危険<br>
boolean式にする
```PHP
assert($x > 0);
```

### その他の変更
#### mcryptがPHPコアから外れた
PHP7.2でmcrypt拡張はPHPコアからPECLへ移動した<br>
mcryptライブラリは2007年から更新されておらず、使用は推奨できないため、OpenSSLまたはSodiumを使う

#### session_module_name("user")
使っていると、PHP7.2ではE_RECOVERABLE_ERRORになる


## PHP7.3
### 下位互換性のない変更点
#### switch内のcontinue
PHPではswitch内のcontinueは実質breakと同じ扱いだが、PHP7.3から警告が出る<br>
外側のwhile/foreachを続けたいならcontinue2にする

#### compact()に未定義変数
PHP7.3から、compact()に未定義変数を渡すとNoticeが出る

#### heredoc/nowdocの終了ラベル
PHP7.3でheredoc/nowdocの構文が柔軟になった代わりに、文字列内に終了ラベルっぽい行があると解釈が変わる可能性がある

#### ArrayAccessの数値文字列キー
ArrayAccess実装オブジェクトで"123"が123に暗黙変換される<br>
配列自体には影響ない

### PHP 7.3.x で推奨されなくなる機能
#### 文字列検索関数の第2引数に数値
strpos()、strstr()、strrchr()などで、needleに文字列以外を渡すのが非推奨になっている

#### 大文字小文字を区別しない定数
define()の第3引数trueが非推奨<br>
定数は大文字小文字を区別する前提に寄せるべき

#### fgetss()/gzgetss()/SplFileObject::fgetss()
HTMLタグ除去しながら1行読む系<br>
非推奨なので、使っていたらfgets()+strip_tags()に置き換える

#### FILTER_FLAG_SCHEME_REQUIRED/FILTER_FLAG_HOST_REQUIRED
FILTER_VALIDATE_URLに含まれているので、明示指定は不要

#### mbereg_*()系エイリアス
mbereg()ではなくmb_ereg()のように、アンダースコアありへ寄せる

### その他の変更
#### JSON_THROW_ON_ERROR
json_encode()/json_decode()で失敗時にJsonExceptionを投げられるようになった

#### Cookie/SessionのSameSite
setcookie()、setrawcookie()、session_set_cookie_params()が配列形式の$optionsを受け取れるようになり、samesiteも指定できる

#### PCREがPCRE2へ変更
正規表現エンジンがPCRE2になり、既存の正規表現で一部挙動が変わる可能性がある<br>
特に文字クラス内の範囲指定が厳密になっている

#### preg_quote()が#もエスケープ
PHP7.3から preg_quote()が#もエスケープする

#### FTPのデフォルト転送モードがbinaryに変更
PHP7.3ではデフォルトの転送モードがbinaryになっている


## PHP7.4
### 下位互換性のない変更点
#### 配列でない値を配列アクセス
PHP7.4では、null/bool/int/float/resourceを配列のようにアクセスすると警告が出る

#### fnが予約語
PHP7.4からアロー関数用にfnが予約語になった<br>
関数名・クラス名として使えない

#### fread()/fwrite()の失敗時戻り値
失敗時にfalseを返すようになった<br>
以前は空文字列や0を返すことがあった<br>
if (!$result)だと、0バイト書き込みと失敗を混同しやすいので、=== falseが安全

#### openssl_random_pseudo_bytes()
エラー時に例外を投げるようになった<br>
今後はrandom_bytes()の方が基本

#### DateIntervalの比較
DateInterval同士を==や<で比較すると警告が出て、常にfalseを返すようになった

#### PASSWORD_*定数の型変更
PASSWORD_DEFAULTなどが数値ではなく文字列になった

#### ArrayObject + get_object_vars()
ArrayObjectに対するget_object_vars()の返り方が変わっている

#### BCMathに"32foo"みたいな値
不完全な数値で警告が出る

### PHP 7.4.x で推奨されなくなる機能
#### implode() の引数順
古い順番が非推奨
```PHP
implode($array, ',');
```
正しくはこっち
```PHP
implode(',', $array);
```

#### 波括弧での配列・文字列アクセス
ダメな例
```PHP
$str{0};
$arr{0};
```
正しくはこれ
```PHP
$str[0];
$arr[0];
```

#### ネストした三項演算子
ダメな例
```PHP
$a ? $b : $c ? $d : $e;
```
括弧を付ける
```PHP
($a ? $b : $c) ? $d : $e;
$a ? $b : ($c ? $d : $e);
```

#### array_key_exists() にオブジェクト
ダメな例
```PHP
array_key_exists('name', $object);
```
オブジェクトなら、
```PHP
property_exists($object, 'name');
```
配列なら、
```PHP
array_key_exists('name', $array);
に分ける
```

#### get_magic_quotes_gpc()
常にfalseを返すので削除でOK

#### (real)/is_real()
ダメな例
```PHP
$price = (real) $value;
is_real($value);
```
置き換え
```PHP
$price = (float) $value;
is_float($value);
```

### その他の変更
#### CSV処理
fputcsv()/fgetcsv()/SplFileObject::fgetcsv()で$escapeに空文字を指定できるようになった

#### imagecropauto()の挙動変更
MG_CROP_DEFAULTがIMG_CROP_SIDESにフォールバックしなくなったり、アルゴリズムが変わってる

#### zend.exception_ignore_args
例外のスタックトレースに引数を含める/除外するINI設定が追加されている
