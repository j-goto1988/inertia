## PHP8.0
### 下位互換性のない変更点
#### each()関数は削除
代わりにforeachやArrayIteratorを使う

#### create_function()関数は削除
無名関数を代わりに使う

#### __autoload()関数を使ってオートローダーを指定する機能は削除
代わりにspl_autoload_register()を使う

#### 無効なcountable型をcount()に渡した場合、TypeErrorがスローされるようになった

#### 定義の変数、未定義のプロパティ、未定義の配列キーがNoticeからWarningになる

#### 非数値文字列の算術演算で、下記はTypeErrorになる
```PHP
$total = '100円' + 20;
```
staticでないメソッドのstatic呼び出はできなくなった
```PHP
class UserService
{
    public function find()
    {
    }
}
UserService::find();
```

#### 親クラスやインターフェースと子クラスのメソッド宣言が互換でない場合、致命的エラーになる
```PHP
class ParentClass
{
    public function execute(array $data): array
    {
        return $data;
    }
}
class ChildClass extends ParentClass
{
    public function execute($data)
    {
        return $data;
    }
}
```

#### マジックメソッドに型を付けている場合、PHPが要求するシグネチャと一致している必要がある

#### array_key_exists()をオブジェクトへ使えない
プロパティが存在するかを確認したいならproperty_exists()、nullでないかを確認したいならisset()

#### call_user_func_array()に渡す配列の文字列キーが、名前付き引数として解釈される
PHP7ではキーが無視されていた

#### 中括弧による配列・文字列アクセスができなくなる
角括弧でアクセスする

#### クラス名と同じ名前のメソッドは、コンストラクタと解釈されなくなる
__construct()メソッドを代わりに使う

#### 三項演算子をネストする場合、明示的に括弧が必要になった

#### ビットシフトや加算、減算に対する連結演算子の優先順位が変更された
```PHP
echo "Sum: " . $a + $b;
// 上記は以前のバージョンでは以下のように解釈されていた
echo ("Sum: " . $a) + $b;
// PHP 8.0.0からは、以下のように解釈される
echo "Sum:" . ($a + $b);
```

#### @でも致命的エラーを隠せない

#### 配列で負の数をキーにした後、自動で付くキーの番号が変わる
配列のキーが負の値であっても、次の暗黙のキーはキーに1を足したものになる
```PHP
$array = [];
$array[-5] = 'A';
$array[] = 'B';

// PHP7まで
[
    -5 => 'A',
     0 => 'B',
]

// PHP8では
[
    -5 => 'A',
    -4 => 'B',
]
```

#### 親クラスがないクラスの内部でparentを使うと、致命的なコンパイルエラーが発生するようになった

#### privateメソッドにfinal修正子を指定した場合、そのメソッドがコンストラクタでない限り、警告が発生するようになった

#### 数値文字列の前後にある空白は無視して数値として扱うようになる
is_numeric()関数、文字列同士の比較、型宣言、インクリメントとデクリメント演算に影響する

#### 文字列と数値の比較
厳密でないやり方で数値と非数値形式の文字列を比較する場合、数値を文字列にキャストし、文字列と比較するようになった
| 比較 | 変更前 | 変更後 |
| :--- | :--- | :--- |
| `0 == "0"` | true | true |
| `0 == "0.0"` | true | true |
| `0 == "foo"` | true | false |
| `0 == " "` | true | false |
| `42 == " 42"` | true | true |
| `42 == "42foo"` | true | false |

#### リソースからオブジェクトへの移行
curl、GD、OpenSSL、Socketなどの一部の戻り値がresourceから専用オブジェクトへ変更された

### 推奨されなくなる機能
#### 任意引数の後ろに必須引数がある
デフォルト値を持つパラメータの後に、必須のパラメータが続く場合、デフォルト値は意味をなさず非推奨になった<br>
引数の順番を入れ替えるのもok
```PHP
function test($a = [], $b) {} // 変更前
function test($a, $b) {}      // 変更後
```
Type $param = nullと書かれたパラメータは許可されているが、nullable型を使うことを推奨
```PHP
function test(A $a = null, $b) {} // まだ許可されている
function test(?A $a, $b) {}       // 推奨される書き方
```

#### ソート比較関数がtrue／falseを返している
0より大きいか、0に等しいか、0より小さい整数を返すようにする

#### libxml_disable_entity_loader
この関数は非推奨になり、削除する<br>
LIBXML_NOENTを使っている場合はlibxml_set_external_entity_loader()を使うようにする

#### 古いZipの手続き型API
Zipの手続き型APIが非推奨となり、ZipArchiveクラスの利用が推奨
下記は非推奨
```PHP
zip_open()
zip_read()
zip_entry_
zip_close()
```

#### 空ファイルをZipArchiveで開いている
空ファイルを有効なZIPとして扱う互換動作が非推奨<br>
新しくZIPを作るなら、空ファイルを事前作成しない

#### ReflectionParameterの古いメソッド
flectionParameter::getType()とReflectionTypeAPIへの移行が推奨

#### Enchant関連
スペルチェック用のEnchant拡張を使用している場合、一部の関数や定数が非推奨になっている<br>
enchant_dict_add_to_personal()はenchant_dict_add()へ置き換える

### その他の変更
#### strict_types=1で数学関数へ文字列を渡していないか
strict_types=1の場合、文字列を渡すとTypeErrorになる可能性がある
```PHP
abs()
ceil()
floor()
round()
```

#### Apacheモジュール名の変更
php7_moduleからphp_moduleへ変更

#### ZIP展開後のファイル更新日時が変わる
ZipArchive::extractTo()がZIP内に記録されているファイルの更新日時も復元するようになった

#### Traversableだけを判定しているコード
複数の組み込みクラスが、単にTraversableを実装するのではなく、より具体的なIteratorAggregateなどを実装するようになった
```PHP
DatePeriod
DOMNamedNodeMap
DOMNodeList
IntlBreakIterator
ResourceBundle
PDOStatement
mysqli_result
```

#### ibXML・XMLセキュリティ設定
外部エンティティの読み込みがデフォルトで無効であることが保証されるようになった

#### JSON拡張が常時有効になる
JSON拡張を無効にできなくなり、PHPの必須機能になった

#### cURLの必要バージョン
cURL拡張では、libcurl7.29.0以上が必要<br>
curl_version()の古いversion引数は削除されている

#### ZipArchiveに新しいフラグが追加された
ファイル名の文字コードや、既存エントリの上書きを制御できるようになった


## PHP8.1
### 下位互換性のない変更点
#### MySQLiのエラーが例外になる
MySQLiのデフォルトエラー処理が何も投げない方式から、例外を投げる方式へ変更された<br>
mysqli_sql_exceptionで処理が停止する可能性がある

#### PDOで取得した数値の型が変わる
PDO MySQLでは、エミュレートされたプリペアドステートメントを使っている場合でも、整数や小数が文字列ではなく、PHPのintやfloatとして返されるようになった

#### htmlspecialchars()のデフォルト値が変わる
デフォルトフラグがENT_COMPATからENT_QUOTES | ENT_SUBSTITUTEへ変更された<br>
シングルクォートもエスケープされ、不正なUTF-8が含まれると空文字列になるからUnicodeの置換文字へ変換される

#### ArrayAccessなどを実装するクラスの戻り値型
PHP内部のインターフェースや組み込みクラスのメソッドを実装・オーバーライドする際、互換性のある戻り値型が求められるようになった<br>
型がない場合、非推奨警告が出る

#### 任意引数の後ろに必須引数がある定義
PHP8.0では非推奨警告だったものが、PHP8.1では実際の呼び出し時にArgumentCountErrorになった

#### resourceがobjectへ変更される
FileInfo、FTP、IMAP、LDAP、PostgreSQL、PSpellなど

#### $GLOBALS全体を変更できない
個別要素へのアクセスは引き続き可能<br>
$GLOBALS配列全体を書き換えたり、配列操作関数の変更対象として渡したりできない

#### 継承されたメソッドのstaticローカル変数が共有される
```PHP
class A {
    public static function counter() {
        static $counter = 0;
        $counter++;
        return $counter;
    }
}
class B extends A {}
var_dump(A::counter()); // int(1)
var_dump(A::counter()); // int(2)
var_dump(B::counter()); // int(3), 以前のバージョンでは int(1)
var_dump(B::counter()); // int(4), 以前のバージョンでは int(2)
```

#### readonlyとneverが予約語になる
クラス名、インターフェース名、トレイト名などに使用していると構文エラーになる

#### version_compare()の省略演算子
ドキュメントに記載されていない演算子の省略形を受け付けなくなった

#### MySQLiのmax_lengthが常に0になる
mysqli_fetch_fields()やmysqli_fetch_field_direct()で取得できるmax_lengthは、PHP8.1では常に0になる

#### 暗号化方式のデフォルト変更
openssl_pkcs7_encrypt()とopenssl_cms_encrypt()のデフォルト暗号方式が、RC2-40からAES-128-CBCへ変更された

### PHP 8.1.x で推奨されなくなる機能
#### 組み込み関数にnullを渡す処理
nullを許可していない組み込み関数の引数へnullを渡すと、非推奨警告が出る

#### FILTER_SANITIZE_STRINGの使用
FILTER_SANITIZE_STRINGとFILTER_SANITIZE_STRIPPEDは非推奨

#### strftime()、gmstrftime()、strptime()
これらは非推奨

#### ctype_*()へ整数を渡す処理
ctype_digit()などのctype_*()関数へ文字列以外を渡すことが非推奨

#### 精度を失うfloatからintへの暗黙変換
小数部分が失われる暗黙変換は非推奨<br>
配列キー、int型引数、整数演算などに影響する

#### falseを配列へ自動変換するコード
falseに要素を追加して配列へ自動変換する挙動が非推奨

#### Serializableの古い実装
Serializableを実装しているのに、__serialize()と__unserialize()を実装していないクラスは非推奨

#### トレイト名からstatic要素へ直接アクセスする処理
トレイトのstaticメソッドやstaticプロパティへ、トレイト名から直接アクセスすることが非推奨<br>
トレイトを利用するクラス経由で呼ぶ必要がある<br>
非推奨
```PHP
trait CounterTrait
{
    public static int $count = 0;

    public static function increment(): void
    {
        self::$count++;
    }
}
CounterTrait::increment();
```
```PHP
修正後
class Counter
{
    use CounterTrait;
}
Counter::increment();
```

#### オブジェクトへ配列ポインタ関数を使う処理
次の関数をオブジェクトへ使うことが非推奨
```PHP
current($object);
next($object);
prev($object);
reset($object);
end($object);
key($object);
```
オブジェクトを扱うなら、明示的に配列へ変換するか、Iteratorを使う

#### mb_check_encoding()を引数なしで呼ぶ処理
引数なしの呼び出しが非推奨

#### __sleep()が配列以外を返す
__sleep()は、シリアライズ対象のプロパティ名を配列で返す必要がある<br>
配列以外を返すと警告になる

### その他の変更
#### ブジェクトのプロパティ順序が変わる
親クラスで宣言されたプロパティ→子クラスで宣言されたプロパティという自然な順番になる

#### OpenSSL3.0環境で古い暗号方式が使えない可能性
OpenSSL3.0では多くの古い暗号方式がレガシープロバイダ側へ移され、標準では利用できない場合がある<br>
鍵の最小サイズなどの検証も厳しくなっている

#### INIファイルの文字列解釈が一部変わる
php.iniや独自INIファイルに書かれた、ドル記号やバックスラッシュを含むダブルクォート文字列の解釈が整理された<br>
特にWindowsパスや正規表現をINIに書いている場合は注意
```PHP
upload_dir = "C:\foo\"
```
安全なのは、Windowsパスならスラッシュを使うこと
```PHP
upload_dir = "C:/foo/"
```
バックスラッシュを明確にエスケープする
```PHP
upload_dir = "C:\\foo\\"
```

#### ReflectionのsetAccessible()が意味を持たなくなる
ReflectionProperty::setAccessible()とReflectionMethod::setAccessible()を呼ばなくても、リフレクション経由でprivate・protectedなメソッドやプロパティへアクセス可能になった<br>
setAccessible()を呼んでも実質的に何も変わらない


## PHP8.2
### 下位互換性のない変更点
#### 大文字・小文字変換がロケール非依存になった
次の関数がロケールに依存せず、ASCIIだけを基準に大文字・小文字変換するようになった
```PHP
strtolower()
strtoupper()
stristr()
stripos()
strripos()
lcfirst()
ucfirst()
ucwords()
str_ireplace()
array_change_key_case()
```
setlocale()の設定に依存して英語以外の文字を変換していたコードは、結果が変わる可能性がある

#### str_split('')の結果が変わる
空文字列をstr_split()へ渡した場合、空配列を返すようになった
以前は、空文字列が1個入った配列を返していた

#### FilesystemIteratorで.と..が出る可能性
FilesystemIterator::__construct()で、以前は事実上必ず有効だったSKIP_DOTSを、明示的に指定しないと外せるようになった<br>
以前の動作を確実に維持するなら、FilesystemIterator::SKIP_DOTSを明示する必要がある

#### glob()の戻り値が変わる場合がある
open_basedirによって検索対象がすべて制限されている場合、glob()はfalseではなく空配列を返すようになった<br>
一部のパスだけが制限されている場合でも警告が出るようになった

#### ksort()・krsort()の数値文字列比較
SORT_REGULARを使ったksort()・krsort()で、数値形式の文字列を比較するときにPHP8系の比較ルールを使うようになった

#### var_export()の出力が変わる
var_export()がクラス名を出力するとき、完全修飾クラス名の先頭にバックスラッシュを付けるようになった

#### 日付の相対書式で複数の符号が使えない
日付の相対書式で、+-2 days、--2 daysのような複数符号を受け付けなくなった

#### DateTimeの戻り値型変更
DateTime::createFromImmutable()とDateTimeImmutable::createFromMutable()の仮の戻り値型が、具体的なクラス名からstaticへ変更された

#### SPLクラスを継承している場合
SplFileObjectの一部メソッドでシグネチャが厳格化されている
```PHP
SplFileObject::getCsvControl()
SplFileObject::fflush()
SplFileObject::ftell()
SplFileObject::fgetc()
SplFileObject::fpassthru()
```
SplFileObjectを継承して、これらをオーバーライドしている独自クラスがあれば、引数や戻り値型を親と一致させる必要がある

#### ODBC接続文字列のエスケープ
ODBCおよびPDO_ODBCでは、ユーザー名・パスワードを接続文字列へ組み込む際のエスケープ方法が変更された

### PHP 8.2.x で推奨されなくなる機能
#### 動的プロパティの作成
クラスで宣言していないプロパティを後から追加すると、非推奨警告が出る<br>
stdClass、__get()／__set()を使うクラス、#[AllowDynamicProperties]付きクラスは例外
```PHP
class User
{
    public string $name;
}
$user = new User();
$user->age = 37;
```

#### 一部の古いcallable形式
call_user_func()なら呼べるものの、直接 $callable()では呼べない、曖昧なcallable形式が非推奨になった<br>
対象例
```PHP
"self::method"
"parent::method"
"static::method"

["self", "method"]
["parent", "method"]
["static", "method"]

["Foo", "Bar::method"]
[new Foo(), "Bar::method"]
```

#### "${var}"形式の文字列展開
次の書き方は非推奨
```PHP
$name = '田中';
$message = "こんにちは、${name}さん";

$message = "${user->name}"
```

#### tf8_encode()とutf8_decode()
utf8_encode()とutf8_decode()は非推奨

#### mbstringで擬似エンコーディングを使う処理
次のエンコーディング名をmbstring関数で使うことが非推奨
```PHP
QPrint
Base64
Uuencode
HTML-ENTITIES
```

#### SplFileInfo::_bad_state_ex()
SplFileInfo::_bad_state_ex()は非推奨

### その他の変更
#### strcmp()系の戻り値を-1や1と決め打ちしない
文字列の長さが違う場合でも、具体的な差を返す保証がなくなり、単に-1または1を返すことがある
```PHP
strcmp()
strcasecmp()
strncmp()
strncasecmp()
substr_compare()
```

#### Intl関連オブジェクトをシリアライズできなくなった
次のような国際化関連のオブジェクトは、シリアライズできない
```PHP
IntlDateFormatter
Collator
MessageFormatter
ResourceBundle
Transliterator
IntlCalendar
IntlTimeZone
```

#### MySQLiのreconnect設定が削除された
MySQLiはlibmysqlを使えなくなり、mysqlndのみになった<br>
これに伴って次が削除・非推奨化されている
```PHP
mysqli_driver::$reconnect
mysqli.reconnect
MYSQLI_IS_MARIADB
```

#### セッション設定を変更するタイミングが厳格化
セッション開始後やHTTPヘッダー送信後に、session.cookie_samesiteを変更しようとすると、失敗して警告が出る

#### 不正なINI設定値で警告が出るようになった
一部のINI設定に不正な値を指定すると、以前は静かに無視されていたものが警告を出すようになった

#### INIの数値で0b・0oを使えるようになった
INIファイルで、2進数・8進数のプレフィックスが使えるようになった

#### CookieのExpires書式が変わる
PHPが送信するCookieの期限フォーマットが変更された

#### random_bytes()・random_int()の例外クラス
乱数生成に失敗した場合、\Random\RandomExceptionを投げるようになった<br>
以前は通常の\Exceptionだった

#### DatePeriodなどのプロパティ宣言変更
DatePeriodやtidy関連クラスのプロパティが正式に宣言されるようになった

#### getimagesize()のAVIF情報が正しくなる
AVIF画像に対するgetimagesize()が、幅・高さ・ビット数・チャンネル情報を正しく返すようになった<br>
以前は幅と高さが0になることがあった


## PHP8.3
### 下位互換性のない変更点
#### range()の引数チェックと結果が厳格化
不正な引数に対して警告・TypeError・ValueErrorが発生するケースが増え、文字と数値が混在する場合の結果も変わった<br>
stepが0の場合はValueErrorになる<br>
増加範囲なのに負のstepはValueErrorになる<br>
配列・オブジェクトなどを渡すはTypeError<br>
文字と数字の混在は境界の値の一方が数字の場合、もう片方の境界の値を整数にキャストせず、文字のリストを生成するようになった
```PHP
range('9', 'A'); // PHP8.3.0以降は、["9", ":", ";", "<", "=", ">", "?", "@", "A"]
range('9', 'A'); // PHP8.3.0より前は、[9, 8, 7, 6, 5, 4, 3, 2, 1, 0]
```

#### number_format()で負の小数桁を指定したときの結果変更
number_format()の第2引数に負数を指定すると、整数部分の指定桁で丸めるようになった<br>
以前は負数が実質無視され、0として扱われていた

#### file()へ不正なフラグを渡すとエラーになる
file()の第2引数に指定できないフラグを厳密にチェックするようになった

#### Date関連のエラーが専用例外になる
日付・時刻処理で発生する問題に、DateErrorやDateException以下の専用例外クラスが使われるようになった<br>
従来の警告や汎用例外から変更されるケースがある

#### トレイトのstaticプロパティが継承クラスごとに分離される
親クラスと子クラスの両方で同じstaticプロパティを持つトレイトを使用した場合、子クラス側に別のstaticプロパティが作られるようになった
```PHP
trait CounterTrait
{
    public static int $count = 0;
}
class ParentClass
{
    use CounterTrait;
}
class ChildClass extends ParentClass
{
    use CounterTrait;
}

ParentClass::$count++;
ChildClass::$count++;
echo ParentClass::$count; // 1
echo ChildClass::$count;  // 1
```
親と子で別の値を持ち、以前は、同じstaticプロパティを共有するような動作になる場合があった

#### 空配列に負のキーを設定した後の自動キー
空配列へ負の整数キーnを設定した場合、その後の[]による自動キーが必ずn+1になる
```PHP
$array = [];

$array[-5] = 'A';
$array[] = 'B';

// PHP 8.3
[
    -5 => 'A',
    -4 => 'B',
]
```
以前は、次の暗黙キーが0になる場合があった
```PHP
[
    -5 => 'A',
     0 => 'B',
]
```

#### roc_get_status()とproc_close()の終了コードが変わる
POSIX環境でproc_get_status()を複数回呼び出しても、正しい終了コードが返されるようになった<br>
その後にproc_close()を呼んだ場合も、従来の-1ではなく正しい終了コードを返すようになった<br>
proc_get_status()の戻り値には、結果がキャッシュされたかを示すcachedキーも追加されている

#### 再帰が深すぎる処理がErrorで停止する
コールスタックを使い切る前にErrorを投げる仕組みが追加された<br>
無限再帰や極端に深い再帰処理は、従来とは異なる時点・エラー内容で停止

#### インターフェースから継承したクラス定数のアクセス権
インターフェースから継承したクラス定数についても、アクセス権が正しくチェックされるようになった<br>
public constのような公開定数には問題ない

#### DOM
DOMでは、親が存在しないノードに対して、
```PHP
$node->after();
$node->before();
$node->replaceWith();
```
を呼び出しても、親子関係の例外を投げず、何もしないようになった<br>
名前空間属性の処理に関する不具合も修正されている<br>
DOMクラスを継承した独自クラスでは、PHP8.3で追加されたメソッドやプロパティと名前が衝突すると、シグネチャ不一致でコンパイルエラーになる可能性がある

#### FFIのvoid関数の戻り値
FFIでC言語のvoid関数を呼び出した場合、PHP 8.3ではFFI\CData:voidオブジェクトではなくnullを返す

### PHP 8.3.x で推奨されなくなる機能
#### 文字列に対する++・--
次のような文字列に対するインクリメント・デクリメントが非推奨になった
```PHP
$value = '';
$value++;
$value = '!';
$value++;
$value = 'abc';
$value--;
```
特に--は、空文字列や数値形式でない文字列に使うと非推奨<br>
++についても、空文字列・英数字でない文字列などへの使用が非推奨になった<br>
英数字文字列のインクリメント自体も新規コードでは使わないほうがよいという扱い<br>
代替としてstr_increment()が追加されている

#### 引数なしのget_class()とget_parent_class()
引数を渡さずに呼ぶことが非推奨

#### assert_options()とASSERT_*
assert_options()、関連するASSERT_*定数、assert.*のINI設定が非推奨<br>
PHPファイルの場合
```PHP
assert_options(ASSERT_ACTIVE, 1);
assert_options(ASSERT_WARNING, 1);
assert_options(ASSERT_BAIL, 1);
assert_options(ASSERT_CALLBACK, 'handleAssert');
```
設定ファイルの場合
```PHP
assert.active = 1
assert.warning = 1
assert.exception = 1
```

#### ReflectionProperty::setValue()の1引数呼び出し
staticプロパティに対してReflectionProperty::setValue()を1引数だけで呼ぶ形式が非推奨になった<br>
最初の引数にnullを明示する必要がある

#### ldap_connect()のホスト・ポート分離指定
ldap_connect()にホスト名とポート番号を別々の引数で渡す形式が非推奨

#### mb_strimwidth()の負のwidth
mb_strimwidth()の第3引数widthへ負数を渡すことが非推奨

#### MT_RAND_PHP
ランダム関数等で使われるメルセンヌ・ツイスタの古い互換モードMT_RAND_PHPが非推奨になった

#### SQLite3の例外を無効化する設定
SQLite3について、例外を使う方式が推奨され、次を呼ぶと非推奨警告が発生する
```PHP
$db->enableExceptions(false);
```

#### Intl関連の定数
次の国際化関連定数が非推奨
```PHP
U_MULTIPLE_DECIMAL_SEPERATORS
NumberFormatter::TYPE_CURRENCY
```
U_MULTIPLE_DECIMAL_SEPERATORSはU_MULTIPLE_DECIMAL_SEPARATORSに置き換える

#### Phar::setStub()のresource指定
resourceとlengthを渡す古い形式が非推奨
\$phar->setStub(stream_get_contents($resource));に置き換える

#### DBAのdba_fetch()
dba_fetch()の第3引数にDBAリソースを渡す古い引数順が非推奨

#### FFIのstatic呼び出し
次をstaticメソッドとして呼ぶ形式が非推奨
```PHP
FFI::cast(...);
FFI::new(...);
FFI::type(...);
```
FFIオブジェクトから呼ぶ形へ変更する

#### ZipArchive::FL_RECOMPRESS
ZipArchive::FL_RECOMPRESSは非推奨

### その他の変更
#### unserialize()の警告が増える
unserialize()の不正データに対して発生していたE_NOTICEがE_WARNINGへ変更された<br>
シリアライズ済みデータの後ろに未処理の文字列が残っている場合も、新たに警告が出る

#### array_sum()・array_product()で警告が出るケース
配列内にintまたはfloatへ変換できない値がある場合、array_sum()とarray_product()が警告を出すようになった<br>
以前は配列やオブジェクトが無視されるなど、より緩い挙動

#### proc_open()の失敗時の挙動
proc_open()のコマンドを配列で渡した場合、空でない要素を最低1つ含む必要がある<br>
空配列などではValueErrorになる<br>
不正なコマンドの場合、後から警告を出すリソースではなく、呼び出し時点でfalseを返すようになった環境がある

#### number_format()の負の桁数
第2引数に負数を渡すと、小数点より左側を丸める<br>
以前は、負数が実質的に0として扱われていた
```PHP
echo number_format(12345, -2);
12,300 // 8.3
12,345 // 以前
```

#### str_getcsv()・fgetcsv()の結果変更
最後のフィールドが、閉じられていない引用符だけの場合、NULバイトを含む文字列ではなく、空文字列を返すようになった

#### mb_detect_encoding()の判定結果が変わる可能性
strictをfalseにしたmb_detect_encoding()の動作が、最も近いと判定された文字エンコーディングを返す方向へ修正された<br>
以前は、不正なバイトがあると候補を早い段階で除外し、誤ってfalseや不適切な候補を返す場合があった

#### MIMEメールの件名処理
mb_decode_mimeheader()が、Quoted-Printable形式のMIMEエンコード語に含まれるアンダースコアを、RFC2047に従ってスペースとして扱うようになった<br>
mb_encode_mimeheader()の出力も一部のケースで変わる

#### open_basedirの実行時設定が厳格化
```PHP
ini_set('open_basedir', $path);
```
で実行時にopen_basedirを設定する際、パスの途中に親ディレクトリを示す..が含まれていると受け付けられなくなった
以前は、パスが..から始まる場合だけ拒否していた<br>

#### ユーザー定義の例外ハンドラがシャットダウン時の例外も捕捉
set_exception_handler()で登録した例外ハンドラが、シャットダウン処理中に発生した例外も捕捉するようになった

#### fread()のソケット読み込みタイミング
ブロッキングソケットでfread()した際、すでにバッファにデータがあれば、指定サイズまで追加データを待たず、すぐに返すようになった

#### mt_srand(null)・srand(null)の扱い
mt_srand()とsrand()にnullを渡すと、引数省略時と同様にランダムなシードが生成される<br>
一方、0を渡した場合は、明確に0がシードとして使用される

#### booleanやnullへの++・--
booleanに対する++と--、およびnullに対する--で警告が出るようになった

#### Intl関数の戻り値や例外の変更
一部のIntl関数の戻り値がnullからtrueまたはfalseへ変更されている<br>
IntlDateFormatterへ無効なロケールを渡すと、例外が発生するようになった

#### highlight_string()・highlight_file()のHTML変更
出力HTML構造が変更された<br>
\<pre>で全体が囲まれ、外側のタグ構造や空白・改行の扱いも変わっている

## PHP8.4
### 下位互換性のない変更点
#### exit()・die()の引数チェックが厳格化
exit()とdie()が関数に近い扱いになり、strict_typesの影響も受ける<br>
無効な型を渡すと、従来のように文字列へ強引に変換されず、TypeErrorになる場合がある

#### round()の不正な丸めモードがValueError
round()の第3引数に不正なモードを渡すと、ValueErrorになる<br>
以前は不正な値でもPHP_ROUND_HALF_UPとして扱われていた

#### str_getcsv()の区切り文字などが厳格化
str_getcsv()の区切り文字・囲み文字・エスケープ文字の長さが正しく検証される<br>
不正ならValueError

#### unserialize()のallowed_classesが厳格化
unserialize()のallowed_classesオプションに不正な値を渡すと、TypeErrorまたはValueErrorになる

#### PDO MySQLの属性が整数ではなくboolになる
PDO MySQLの次の属性が、整数属性ではなく論理値属性として扱われる
```PHP
PDO::ATTR_AUTOCOMMIT
PDO::ATTR_EMULATE_PREPARES
PDO::MYSQL_ATTR_DIRECT_QUERY
```

#### MySQL接続タイムアウトのエラーコード変更
mysqlndでは、MySQL8.0.24以降のサーバー待機タイムアウトについて、エラーコードが従来の2006から4031になる場合がある

#### セッションGC設定の不正値で警告
次の設定値が下記の条件の場合、警告が発生する<br>
session.gc_divisorが0以下<br>
session.gc_probabilityが負数<br>

#### 画像出力関数の不正な品質値がValueError
次の画像出力関数へ不正な品質値を渡すとValueErrorになる
```PHP
imagejpeg()
imagepng()
imagewebp()
imageavif()
```

#### PCRE2更新による正規表現の解釈変更
同梱されるPCRE2が10.44へ更新され、特に次の書式が変わる
```PHP
{,3}
```
以前は文字列として解釈されることがあったが、最大3回の量指定子として認識される

#### SimpleXMLのループ中の挙動変更
SimpleXMLElementをループ中にasXML()、getName()、文字列キャストなどを呼んでも、イテレータが暗黙的に先頭へ戻らなくなった

#### XMLパーサーのハンドラ指定が厳格化
xml_set_*_handler()関数で、ハンドラが有効なcallableかどうかがより厳格に確認される<br>
xml_set_object()を使う場合は、ハンドラ登録より先に呼び出す必要がある

#### リソースからオブジェクトへの変更
DBA、ODBC、一部SOAP内部値などがリソースからオブジェクトへ変更されている<br>
is_resource()による成功判定が通らなくなる

#### 相対日付書式で+-2が再び許可される
日付の相対書式で複数の符号が再び許可される

#### 一時ファイル名が長くなる
アップロードファイルやtempnam()で生成される一時ファイル名が、以前より13バイト長くなる

#### JIT設定方法の変更
JITのデフォルト設定表現が変わった<br>
JIT自体は引き続きデフォルト無効だが、opcache.jit_buffer_sizeだけではJITが有効にならないケースがある

### PHP 8.4.x で推奨されなくなる機能
#### 暗黙的なnullableパラメータ
従来は、型を付けた引数のデフォルト値をnullにすると、明示しなくてもnullを受け取れた<br>
下記が非推奨
```PHP
function findUser(User $user = null)
{
}
```
PHP7.1以降なら下記の修正
```PHP
function findUser(?User $user = null)
{
}
```
PHP8.0以降なら下記の修正
```PHP
function findUser(User|null $user = null)
{
}
```

#### fgetcsv()などでescape引数の省略が非推奨
これらの関数・メソッドで、escape引数のデフォルト値に依存することが非推奨になった
```PHP
fgetcsv()
fputcsv()
str_getcsv()
SplFileObject::fgetcsv()
SplFileObject::fputcsv()
SplFileObject::setCsvControl()
```

#### trigger_error(..., E_USER_ERROR)の非推奨
次の使い方が非推奨
```PHP
trigger_error(
    '致命的なエラーです',
    E_USER_ERROR
);
```
例外を投げるか、exit()を使用する

#### セッション関連の古い設定
複数のセッション設定の変更が非推奨になっている
```PHP
session.sid_length
session.sid_bits_per_character
session.use_only_cookies
session.use_trans_sid
session.trans_sid_tags
session.trans_sid_hosts
session.referer_check
```
SID定数も非推奨

#### session_set_save_handler()の古い多引数形式
session_set_save_handler()を2個より多い引数で呼ぶ形式が非推奨<br>
2引数形式を使う

#### MySQLiのping()など
MySQLiでは、次が非推奨になった
```PHP
mysqli_ping()
$mysqli->ping()
mysqli_kill()
$mysqli->kill()
mysqli_refresh()
$mysqli->refresh()
```
MYSQLI_REFRESH_*定数と、mysqli_store_result()へmodeを明示する形式も非推奨

#### E_STRICTの使用
E_STRICTのエラーレベルが削除され、定数自体が非推奨

#### CURLOPT_BINARYTRANSFER
CURLOPT_BINARYTRANSFER定数が非推奨

#### DatePeriodへISO 8601文字列を直接渡す形式
次のコンストラクタ形式が非推奨
```PHP
$period = new DatePeriod(
    'R4/2026-01-01T00:00:00Z/P1D'
);
```
代わりに、DatePeriod::createFromISO8601String()を使用
```PHP
$period = DatePeriod::createFromISO8601String(
    'R4/2026-01-01T00:00:00Z/P1D'
);
```

#### ハッシュ関数へ無効なオプションを渡す処理
ハッシュ関数へ無効なオプションを渡すことが非推奨

#### ReflectionMethodの1引数コンストラクタ
次の形式が非推奨
```PHP
$method = new ReflectionMethod(
    'UserService::execute'
);
```
代わりに、下記を使用
```PHP
$method = ReflectionMethod::createFromMethodName(
    'UserService::execute'
);
```

#### stream_context_set_option()の2引数形式
次の形式が非推奨
```PHP
stream_context_set_option(
    $context,
    [
        'http' => [
            'timeout' => 10,
        ],
    ]
);
```
代わりに、複数設定用のstream_context_set_options()を使用
```PHP
stream_context_set_options(
    $context,
    [
        'http' => [
            'timeout' => 10,
        ],
    ]
);
```

#### unserialize()の大文字Sタグ
シリアライズ文字列で、大文字のSタグを使った文字列の復元が非推奨

#### XMLの古いハンドラ登録
xml_set_object()が非推奨<br>
xml_set_*関数へcallableではない文字列を渡すことも非推奨

### その他の変更
#### PASSWORD_BCRYPTのデフォルトコストが10から12へ変更
PASSWORD_BCRYPTハッシュアルゴリズムのデフォルトのが10だったが、12になった<br>
コストが上がるため、パスワードハッシュの生成・検証に必要なCPU時間が増える

#### round()の結果が一部変わる
round()の内部実装が書き直され、複数の丸め処理の不具合が修正された<br>
そのため、境界付近の浮動小数点数では、PHP 8.3以前と異なる結果になる可能性がある
```PHP
round(0.49999999999999994);
0.0 // 8.4
1.0  // 以前
```

#### idn_to_ascii()・idn_to_utf8()がValueErrorを投げる
国際化ドメイン名を変換する関数で、不正な引数に対してValueErrorが発生する

#### クロージャ名が変わる
クロージャの内部的な名前に、親関数の名前と定義行番号が含まれるようになった<br>
スタックトレースなどでクロージャを識別しやすくするための変更

#### trigger_error()の戻り値型が常にtrueになる
次の戻り値型がboolではなくtrueになった
```PHP
trigger_error()
user_error()
```
実際には以前から成功時にtrueしか返せなかったため、型表現が実態に合わせて変更されている

#### hash_update()などの戻り値型がtrueになる
実際には成功時に常にtrueしか返さない複数の関数・メソッドについて、戻り値型がboolからtrueへ変更された
```PHP
hash_update()
DOMDocument::registerNodeClass()
Phar::setAlias()
Phar::setDefaultStub()
SplPriorityQueue::insert()
SplPriorityQueue::recoverFromCorruption()
SplHeap::insert()
SplHeap::recoverFromCorruption()
```

#### mb_strcut()の不正な文字列に対する結果変更
mb_strcut()が無効なUTF-8・UTF-16文字列を処理するときの動作が、より一貫したものになった

#### UnicodeデータがUnicode 16.0へ更新
mbstringでは、UnicodeデータテーブルがUnicode 16.0へ更新された

#### OpenSSLのCSR属性の扱いが修正
openssl_csr_new()のextra_attributesは、正しくCSR属性を設定する<br>
以前は誤ってSubject DNへ設定されていた

#### openssl_x509_parse()の証明書解析差
ASN.1 UTCTimeで秒が省略されている不完全な証明書について、利用するOpenSSLのバージョンにより、解析失敗や証明書自体の読み込み失敗が起こる場合がある

#### SplFixedArrayの例外クラスが変わる
SplFixedArrayで範囲外アクセスをした場合、RuntimeExceptionではなくOutOfBoundsExceptionが投げられる<br>
OutOfBoundsExceptionはRuntimeExceptionの子クラスなので、親をcatchしている既存コードはそのまま動く

#### output_add_rewrite_var()の対象ホスト設定変更
output_add_rewrite_var()がURLを書き換える対象ホストの選択に、session.trans_sid_hostsではなくurl_rewriter.hostsを使う

#### ob_start()の出力ハンドラへ渡されるフラグ変更
ob_start()のコールバックへ渡されるステータスフラグから、出力ハンドラ自身の制御用フラグが除去されるようになった<br>
第2引数$phaseを細かく判定している独自処理では、値が以前と異なる可能性がある

#### Fiberのデストラクタ実行タイミング
デストラクタ実行中にFiberを切り替えられるようになり、ガベージコレクションによるデストラクタが別のFiber内で実行される場合がある

#### Apacheの最低バージョンが2.4へ
Apacheモジュールでは、Apache2.0および2.2のサポートが削除され、最低要件はApache2.4になった

#### 必要ライブラリの最低バージョン変更
複数の外部ライブラリの最低要件が上がっている
libcurl 7.61.0以上<br>
libxml2 2.9.4以上<br>
OpenSSL 1.1.1以上<br>
zlib 1.2.11以上<br>
PostgreSQLのlibpq 10.0以上<br>

#### CURLOPT_DNS_USE_GLOBAL_CACHEが完全に無視される
効果がなく、警告なしで無視される<br>
基盤となるlibcurl側ですでに削除されている機能

#### PDO MySQL関連は基本的に追加機能
PDO MySQLで次の属性をPDO::getAttribute()から取得できるようになった
```PHP
PDO::ATTR_FETCH_TABLE_NAMES
```
PDO全体では下記もも取得できるようになった
```PHP
PDO::ATTR_STRINGIFY_FETCHES
```






