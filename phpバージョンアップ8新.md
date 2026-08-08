## PHP8.0
### 新機能
#### match式
switchより安全で、ステータスや区分値の変換に向いている<br>
matchは厳密比較を行い、値を直接返せる
```PHP
$statusName = match ($status) {
    0 => '未処理',
    1 => '処理中',
    2 => '完了',
    9 => 'エラー',
    default => '不明',
};
```
条件が複雑な場合は、無理にmatchへ詰め込まず、通常のifや専用クラスを使った方が読みやすい

#### nullsafe演算子?->
途中のオブジェクトがnullでも、エラーにせず全体をnullにできる
```PHP
$companyName = $user?->company?->name;
```
\?->は存在しなくても正常というデータに向いている

#### コンストラクタプロパティ昇格
コンストラクタ引数とプロパティ宣言をまとめて書く
```PHP
final class UserService
{
    public function __construct(
        private UserRepository $repository,
        private LoggerInterface $logger,
    ) {
    }
}
```

#### union型
複数の型を受け付けることを宣言できる
```PHP
function normalizeId(int|string $id): string
{
    return (string) $id;
}
```
戻り値にも使える
```PHP
function findUser(int $id): User|null
{
    // ...
}
```

#### 名前付き引数
引数名を指定して関数やメソッドを呼べる
```PHP
$result = createUser(
    name: '田中太郎',
    email: 'tanaka@example.com',
    active: true,
);
```
途中の任意引数だけ指定できる<br>
名前付き引数は、呼び出し先の引数名がAPIの一部になる

#### str_contains()・str_starts_with()・str_ends_with()
文字列検索が分かりやすくなる<br>
含まれているか
```PHP
if (str_contains($message, 'ERROR')) {
}
```
先頭一致
```PHP
if (str_starts_with($path, '/admin/')) {
}
```
末尾一致
```PHP
if (str_ends_with($filename, '.csv')) {
}
```

#### throwを式として使用
throwを、null合体演算子やアロー関数の中で使えるようになった
```PHP
$user = $repository->find($id)
    ?? throw new UserNotFoundException();
```

#### static戻り値型
継承先のクラス自身を返すことを宣言できる<br>
子クラスでも正しい型として扱える<br>
単に自分自身を返すだけで、継承を考慮しないならselfでも構わない

#### $object::class
オブジェクトからクラス名を取得できる<br>
get_class($object)と同等
```PHP
$className = $user::class;
```
オブジェクトではない値に使わないようにする

#### mixed型
どんな型でも受け付けることを明示<br>
```PHP
function registerUser(mixed $data): mixed
```
mixedはまだ型を決めていないではなく、本当に複数の型を扱う処理に限定するのがよい

#### 引数末尾のカンマ
複数行の引数定義で、最後にカンマを付けられる
```PHP
public function create(
    string $name,
    string $email,
    bool $active,
): User {
}
```
利点は、引数追加時の差分が小さくなること

#### 例外変数を省略したcatch
例外オブジェクトを使わない場合、変数を省略できる
```PHP
try {
    $service->execute();
} catch (TemporaryException) {
    return false;
}
```
例外内容を本当に使わない場面だけ、省略する

#### get_debug_type()
値の型を、エラーメッセージ向けに分かりやすく取得できる<br>
オブジェクトならクラス名、リソースならリソース種別を返す
```PHP
throw new InvalidArgumentException(
    sprintf(
        '配列が必要ですが、%sが渡されました',
        get_debug_type($value)
    )
);
```

#### preg_last_error_msg()
正規表現エラーを、人が読めるメッセージで取得できる
```PHP
$result = preg_match($pattern, $subject);
if ($result === false) {
    throw new RuntimeException(
        '正規表現エラー: '
        . preg_last_error_msg()
    );
}
```

#### 安定ソート
比較結果が同じ要素について元の順序が維持される、安定ソートになった<br>
例えば、最初に登録順で並んでいるデータを、優先度だけでソートした場合、同じ優先度の要素は登録順を維持
```PHP
$items = [
    ['name' => 'A', 'priority' => 2],
    ['name' => 'B', 'priority' => 1],
    ['name' => 'C', 'priority' => 2],
];
usort(
    $items,
    fn(array $a, array $b): int =>
        $a['priority'] <=> $b['priority']
);
```
結果は、
```PHP
B
A
C
```
となり、同じ優先度のAとCは元の順序を維持

#### アトリビュート
クラスやメソッド、プロパティへメタデータを付けられる
```PHP
#[Required]
private string $name;
```
```PHP
#[Route('/users')]
public function index(): Response
{
}
```
LaravelやSymfony、ORM、DIコンテナなどで使われる<br>
自社でアトリビュートを作るには、Reflectionで読み取る仕組みも必要

#### Stringable
__toString()を持つクラスは、自動的にStringableとして扱われる
```PHP
function output(
    string|Stringable $value
): string {
    return (string) $value;
}
```

#### WeakMap
オブジェクトをキーにし、そのオブジェクトがほかで参照されなくなれば、対応データも自動的に削除される
```PHP
$cache = new WeakMap();
$cache[$user] = [
    'calculated_score' => 100,
];
```

### 新しいクラスとインターフェイス
#### CurlHandle
外部APIや別サイトへのHTTP通信で使う<br>
PHP 8.0では、CurlHandleオブジェクトが返る

#### CurlMultiHandle
複数のHTTP通信を並列的に進める場合に使う
```PHP
$multi = curl_multi_init();
$curl1 = curl_init('https://api.example.com/users');
$curl2 = curl_init('https://api.example.com/orders');
curl_setopt($curl1, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl2, CURLOPT_RETURNTRANSFER, true);
curl_multi_add_handle($multi, $curl1);
curl_multi_add_handle($multi, $curl2);
do {
    $status = curl_multi_exec($multi, $running);

    if ($status !== CURLM_OK) {
        throw new RuntimeException(
            'cURL multi処理に失敗しました'
        );
    }
} while ($running > 0);
$response1 = curl_multi_getcontent($curl1);
$response2 = curl_multi_getcontent($curl2);
curl_multi_remove_handle($multi, $curl1);
curl_multi_remove_handle($multi, $curl2);
curl_close($curl1);
curl_close($curl2);
curl_multi_close($multi);
```

#### GdImage
以前は、次の戻り値はリソースだった
```PHP
$image = imagecreatefromjpeg($path);
```
PHP 8.0では、成功時にGdImageオブジェクトが返る

#### OpenSSL関連クラス
PHP8.0では、次のクラスが追加されている
```PHP
OpenSSLAsymmetricKey // 秘密鍵・公開鍵
OpenSSLCertificate // 証明書
OpenSSLCertificateSigningRequest // CSR（証明書署名要求）
```

#### XMLParser
イベント駆動型のXMLパーサーを使う場合のクラス<br>
xml_parser_create()がXMLParserオブジェクトを返す

#### XMLWriter
XMLを生成するときに使う<br>
専用のXMLWriterクラスとして扱われる

#### DeflateContext・InflateContext
ストリーム形式で圧縮・展開を行うためのコンテキスト<br>
Zlibの新クラスとして追加されている

## PHP8.1
### 新機能
#### Enum
ステータスや種別を、単なる数値・文字列ではなく、決められた値だけに制限できる
```PHP
enum OrderStatus: int
{
    case Pending = 1;
    case Paid = 2;
    case Shipped = 3;
    case Cancelled = 9;
}
```
引数にも指定できる<br>
存在しない値は渡せない

#### readonlyプロパティ
一度設定した値を、後から変更できなくする
```PHP
final class UserData
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $email,
    ) {
    }
}
```
後から変更しようとするとエラーになる<br>
readonlyプロパティには型が必要

#### 文字列キーを持つ配列のアンパック
文字列キーも展開できる
```PHP
$default = [
    'timeout' => 10,
    'retry' => 3,
];
$custom = [
    ...$default,
    'timeout' => 30,
];

// 結果
[
    'timeout' => 30,
    'retry' => 3,
]
```
後ろにある同じ文字列キーが上書きする<br>
数値キーは再採番される
```PHP
$a = [10 => 'A'];
$b = [...$a];
```
結果のキーは10ではなく、通常は0になる

#### 第一級callable
既存の関数やメソッドから、型安全で分かりやすいClosureを作れる<br>
myFunc(...)という第一級callableの記法が追加された
```PHP
$trim = trim(...);
$result = array_map(
    $trim,
    $values
);
```
クラスのメソッドにも使える
```PHP
$validator = $service->validate(...);
```
静的メソッドにも使える
```PHP
$normalizer = UserNormalizer::normalize(...);
```

#### 初期化時にnewを使える
引数のデフォルト値やアトリビュートの引数など、初期化式でnewが使えるようになった
```PHP
final class UserService
{
    public function __construct(
        private Logger $logger = new NullLogger(),
    ) {
    }
}
```
従来は、次のようにコンストラクタ内で補完する必要があった
```PHP
final class UserService
{
    private Logger $logger;

    public function __construct(
        ?Logger $logger = null
    ) {
        $this->logger = $logger ?? new NullLogger();
    }
}
```

#### never型
関数が正常終了せず、必ず例外を投げるか、exit()することを示す<br>
戻り値専用のnever型が追加された
```PHP
function throwUserNotFound(int $id): never
{
    throw new UserNotFoundException(
        "ユーザーが見つかりません: {$id}"
    );
}

function abortProcess(string $message): never
{
    fwrite(STDERR, $message . PHP_EOL);
    exit(1);
}
```

#### 交差型
複数のインターフェースをすべて満たすオブジェクトを要求できる
```PHP
function export(
    Countable&Iterator $items
): void {
}
```
この場合、Countableでもあり、同時にIteratorでもある必要がある<br>
union型と交差型を組み合わせることはできない

#### fputcsv()で改行コードを指定
fputcsv()とSplFileObject::fputcsv()に、改行コードを指定するeol引数が追加された<br>
以前は、環境や実装に応じて改行を追加処理する必要があった

#### CURLStringFile
メモリ上の文字列を、一時ファイルへ保存せずに、ファイルとしてPOSTできる<br>
CURLStringFileなら、一時ファイルの作成・削除が不要<br>
巨大なファイルを文字列として保持すると、メモリ使用量が増える<br>
数百MBなどの大容量ファイルなら、通常のCURLFileやストリーム送信の方が適している

#### ファイルアップロードのfull_path
ディレクトリ単位のアップロードで、ブラウザから相対パス情報を受け取れるようになった<br>
PHP側では、通常の項目に加えてfull_pathを参照できる<br>
ファイルアップロード時のfull_pathキーが追加されている<br>

#### AVIF画像対応
GDがAVIFに対応した環境では、次が使える
```PHP
$image = imagecreatefromavif($path);
imageavif(
    $image,
    $outputPath,
    80
);
```
GDのimagecreatefromavif()とimageavif()が追加された<br>
PHPが利用するlibgd自体にAVIF対応が組み込まれている必要がある

#### MySQLiのexecute()へ配列を渡せる
MySQLiを使っている場合、実行時に配列でパラメータを渡せる
```PHP
$stmt = $mysqli->prepare(
    'INSERT INTO users(id, name) VALUES(?, ?)'
);
$stmt->execute([
    100,
    '田中太郎',
]);
```
値はすべて文字列としてバインドされる<br>
連想配列ではなくリスト形式が必要で、libmysqlclientでビルドされた環境では利用できない

#### mysqli_result::fetch_column()
MySQLiで、検索結果から単一の値を簡単に取得できる
```PHP
$result = $mysqli->query(
    'SELECT COUNT(*) FROM users'
);
$count = $result->fetch_column();
```
取得したい列は0始まりの番号で指定できる
```PHP
$value = $result->fetch_column(1);
```

#### xxHash・MurmurHash3
高速な非暗号学的ハッシュとして、xxHashとMurmurHash3が追加された
```PHP
$hash = hash('xxh3', $data);
```
xxHashとMurmurHash3は、高速性を重視した非暗号学的ハッシュとして使う

#### Fiber
Fiberは、処理を途中で停止して、後から再開できる仕組み
```PHP
$fiber = new Fiber(
    function (): void {
        echo "開始\n";
        Fiber::suspend();
        echo "再開\n";
    }
);
$fiber->start();
$fiber->resume();
```

### 新しいクラスとインターフェイス
#### finfo
finfoは、アップロードされたファイルの実際のMIMEタイプを調べるクラス

#### IntlDatePatternGenerator
利用者の言語や地域に合わせて、適切な日付表示パターンを生成するクラス<br>
IntlDateFormatterで使用する、ローカライズされた日付・時刻のフォーマットパターンを生成<br>
日本向けの日付
```PHP
$generator = new IntlDatePatternGenerator(
    'ja_JP'
);
$pattern = $generator->getBestPattern(
    'yyyyMMdd'
);
```
取得したパターンをIntlDateFormatterへ渡す
```PHP
$formatter = new IntlDateFormatter(
    'ja_JP',
    IntlDateFormatter::NONE,
    IntlDateFormatter::NONE,
    'Asia/Tokyo',
    IntlDateFormatter::GREGORIAN,
    $pattern,
);
echo $formatter->format(
    new DateTimeImmutable(
        '2026-07-25 15:30:00'
    )
);
```
日本向けなら、例えば次のような表示パターンになる<br>
2026/07/25<br>
あるいは指定内容によって、<br>
2026年7月25日<br>
のようになる<br>
IntlDatePatternGeneratorを使うと、地域ごとの自然な並びを自動的に選択できる

#### FTP\Connection
FTP接続を表す専用オブジェクト<br>
FTP接続がリソース型ではなく、FTP\Connectionオブジェクトとして扱われるようになった<br>
成功してもis_resource()がfalseになる

#### IMAP\Connection
メールサーバーとのIMAP接続を表す専用オブジェクト

#### LDAP
次の専用オブジェクトが追加されている
```PHP
LDAP\Connection
LDAP\Result
LDAP\ResultEntry
```
検索結果も専用オブジェクトになる

#### PostgreSQL関連クラス
次のクラスが追加されている
```PHP
PgSql\Connection
PgSql\Lob
PgSql\Result
```
従来のPostgreSQLリソースを、専用オブジェクトとして扱うための変更

### 新しく追加された関数
#### array_is_list()
配列が次のような「0から始まる連続した数値キー」かどうかを判定
```PHP
[
    0 => '田中',
    1 => '佐藤',
    2 => '鈴木',
]
```
連想配列ならfalse<br>
配列のキーが0からcount(\$array) - 1まで連続している場合にtrueを返し、空配列もリストとして扱われる<br>
キーが数値でも、0から始まっていない場合はfalse<br>
途中のキーが欠けている場合もfalse

#### fsync()
データとファイル関連メタデータを同期<br>
PHPのファイルバッファにある内容を、OSのストレージへ同期する関数<br>
fsync()は、書き込み済みデータと関連するメタデータをストレージへ同期するための関数<br>
OSに対してディスクなどの永続ストレージへ同期するよう要求

#### fdatasync()
主にファイルデータをストレージへ同期<br>
ファイルサイズなどデータを読み出すために必要な一部メタデータは同期されるが、すべてのメタデータ更新を必ず同期するとは限らない

#### ReflectionFunctionAbstract::getClosureUsedVariables()
クロージャがuseで取り込んでいる変数を取得

### 新しいグローバル定数
#### IMG_AVIF
GDがAVIF画像に対応しているかを、imagetypes()の戻り値から確認するためのビットフラグ

#### IMG_WEBP_LOSSLESS
WebPの可逆圧縮に関連するGD定数

#### CURLOPT_SSLCERT_BLOB
SSLクライアント証明書を、ファイルパスではなく文字列データとしてcURLへ渡すための定数<br>
証明書の内容を直接指定できる

#### CURLOPT_SSLKEY_BLOB
クライアント証明書に対応する秘密鍵を、文字列データとしてcURLへ渡す

#### Proxy用の証明書定数
次の定数も追加されている
```PHP
CURLOPT_PROXY_ISSUERCERT
CURLOPT_PROXY_ISSUERCERT_BLOB
CURLOPT_PROXY_SSLCERT_BLOB
CURLOPT_PROXY_SSLKEY_BLOB
```
HTTPSプロキシへ接続するときの、<br>
発行元証明書<br>
クライアント証明書<br>
秘密鍵<br>
を指定するためのもの

#### CURLOPT_ISSUERCERT_BLOB
接続先の証明書が、指定した発行元証明書によって発行されたものか確認する際に使う<br>
接続先証明書の発行元をさらに限定する必要がある特殊な連携で使う

#### CURLOPT_DOH_URL
cURLが名前解決に使うDNS-over-HTTPSサーバーのURLを指定<br>

## PHP8.2
### 新機能
#### #[SensitiveParameter]
パスワードやアクセストークンなどの引数を、例外のスタックトレース上で伏せるための属性<br>
この関数で例外が発生した場合、$passwordの実際の値ではなく、SensitiveParameterValueというオブジェクトとして表示される

#### readonly class
クラス全体をreadonlyにできる<br>
readonly classを指定すると、宣言されたすべてのプロパティがreadonlyとして扱われ、動的プロパティも作れない<br>
型なしプロパティは不可<br>
staticプロパティは不可<br>
継承先もreadonlyが必要<br>
内部オブジェクトまでは不変ではない

#### Random拡張
乱数生成機能を整理・統合した新しいRandom拡張が追加された<br>
新しい乱数エンジンとRandom\Randomizerを利用できる<br>
ランダムな整数
```PHP
$randomizer = new Random\Randomizer();
$number = $randomizer->getInt(1, 100);
```

#### DNF型
Union型と交差型を組み合わせられるようになった<br>
DNF形式で書く必要がある
```PHP
(A&B)|C
```
AとBの両方を実装しているオブジェクトまたはC

#### true、false、nullを単独型として指定
true型は成功時に必ずtrueだけを返す関数を表現できる<br>
false型は常に失敗を表す特殊なインターフェース互換などで使える<br>
null型は常に値を返さない実装を表現できる

#### Enumのプロパティを定数式で利用
Enumのnameやvalueを定数式内で参照できる
```PHP
enum UserStatus: int
{
    case Active = 1;
    case Suspended = 2;
}
```
クラス定数で使える
```PHP
final class UserConfig
{
    public const ACTIVE_STATUS =
        UserStatus::Active->value;
    
    echo UserConfig::ACTIVE_STATUS; // 1
}
```

#### Traitに定数を定義
Trait内に定数を定義できる

#### 正規表現のn修飾子
PCREにn修飾子が追加された<br>
nを付けると、通常の丸括弧()はキャプチャされず、名前付きグループだけがキャプチャされる

#### CURLINFO_EFFECTIVE_METHOD
実際に使用されたHTTPメソッドを、curl_getinfo()から取得できる

#### curl_upkeep()
長時間維持するcURL接続に対して、接続の保守処理を実行する関数

#### error_log_mode
PHPのエラーログファイルを新規作成するときのパーミッションを設定できるINI項目

#### OpenSSLのChaCha20-Poly1305対応
OpenSSLでChaCha20-Poly1305のAEADがサポートされた<br>
AEADは、暗号化と改ざん検出をまとめて行う方式

#### OCI8のLOB取得最適化
Oracle Databaseを使う場合、LOB取得時の往復回数を減らすための設定と関数が追加された
```PHP
oci8.prefetch_lob_size
oci_set_prefetch_lob()
```

#### ODBC接続文字列のクォート関数
次の関数が追加された
```PHP
odbc_connection_string_is_quoted()
odbc_connection_string_should_quote()
odbc_connection_string_quote()
```

### 新しく追加された関数
#### mysqli_execute_query()
SQLの準備、パラメータのバインド、実行、結果取得を1回で行える<br>
prepare()、bind_param()、execute()、get_result()をまとめたショートカット

#### ini_parse_quantity()
128Mや2GのようなPHP設定値を、バイト数へ変換する関数

#### memory_reset_peak_usage()
memory_get_peak_usage()が保持している、メモリ使用量の最大値をリセットする

#### openssl_cipher_key_length()
暗号方式に必要な鍵の長さを取得する

#### imap_is_open()
IMAP接続が開いているか確認する

#### ODBC接続文字列関数()
追加されたのは次
```PHP
odbc_connection_string_is_quoted()
odbc_connection_string_should_quote()
odbc_connection_string_quote()
```

#### curl_upkeep()
長時間維持しているcURL接続の保守処理を実行

### 新しいグローバル定数
#### CURLSSLOPT_NATIVE_CA
OS標準のCA証明書ストアを利用する

## PHP8.3
### 新機能
#### 型付きクラス定数
クラス定数に型を付けられるようになった
```PHP
class User
{
    public const string STATUS = 'active';
}

// エラーにならない
class AdminUser extends User
{
    public const string STATUS = 'admin';
}

// エラーになる
class AdminUser extends User
{
    public const string STATUS = [];
}
```

#### クラス定数の動的取得
```PHP
class User
{
    public const ADMIN = 1;
    public const MEMBER = 2;
}

$name = 'ADMIN';
echo User::{$name}; // 1
```

#### readonly匿名クラス
匿名クラスでも使えるようになった
```PHP
$obj = new readonly class {
    public int $id;
};
```

#### php.iniのデフォルト値構文
環境変数が無ければ256Mを使う
```PHP
memory_limit=${PHP_MEMORY_LIMIT:-256M}
```

### 新しいクラスとインターフェイス
#### Date関連の例外クラス
Date/Timeが投げる例外が細かく分類された<br>
追加されたクラスは次の9個
```PHP
DateError
DateObjectError
DateRangeError
DateException
DateInvalidOperationException
DateInvalidTimeZoneException
DateMalformedStringException
DateMalformedIntervalStringException
DateMalformedPeriodStringException
```

#### Random\IntervalBoundary
Random拡張専用で、下記は0は含むが1は含まない
```PHP
$randomizer->getFloat(
    0,
    1,
    Random\IntervalBoundary::ClosedOpen
);
```

### 新しく追加された関数
#### json_validate()
JSON文字列が、文法的に正しいかだけを判定
```PHP
$json = '{"id": 100, "name": "田中"}';
if (!json_validate($json)) {
    throw new InvalidArgumentException(
        'JSON形式が不正です: ' . json_last_error_msg()
    );
}
```
直後にjson_decode()する場合は、先にjson_validate()を呼ぶ必要はなく、json_decode()だけを呼ぶ<br>
json_validate()は、デコード結果を使わず、妥当性だけを確認する場合にjson_decode()よりメモリを抑えられる

#### mb_str_pad()
mb_str_pad()はUnicodeコードポイント単位で長さを計算する<br>
画面上の表示幅をそろえる関数ではない<br>
全角文字と半角文字の見た目の幅を考慮したい場合は、mb_strwidth()などを組み合わせる必要がある

#### str_increment()・str_decrement()
英数字文字列を次または前の値へ進める

#### DatePeriod::createFromISO8601String()
ISO 8601形式の繰り返し文字列からDatePeriodを作成する
```PHP
$period = DatePeriod::createFromISO8601String(
    'R4/2026-08-01T00:00:00+09:00/P1D'
);
foreach ($period as $date) {
    echo $date->format('Y-m-d') . PHP_EOL;
}
```

#### Random\Randomizer::getBytesFromString()
指定した文字集合から、ランダムな文字列を生成できる

#### Random\Randomizer::nextFloat()・getFloat()
ランダムな小数を取得する

#### stream_context_set_options()
複数のコンテキストオプションを一度に設定する

#### DOM関連
次のDOMメソッドが追加されている
```PHP
DOMElement::getAttributeNames()
DOMElement::insertAdjacentElement()
DOMElement::insertAdjacentText()
DOMElement::toggleAttribute()
DOMNode::contains()
DOMNode::getRootNode()
DOMNode::isEqualNode()
DOMParentNode::replaceChildren()
```

#### Intlの日付生成メソッド
次が追加されている
```PHP
IntlCalendar::setDate()
IntlCalendar::setDateTime()
IntlGregorianCalendar::createFromDate()
IntlGregorianCalendar::createFromDateTime()
```

#### ReflectionMethod::createFromMethodName()
ClassName::methodName形式の文字列からReflectionを作成する

## PHP8.4
### 新機能
#### Property Hooks
プロパティ自体に処理を書ける
```PHP
class User
{
    public string $name {
        get => strtoupper($this->name);
        set => $this->name = trim($value);
    }
}
```

#### Asymmetric Visibility
読み取りと書き込みを別々にできる<br>
最初のpublicが読む側の権限で、後のprivateが書く側の権限
```PHP
public private(set) string $name;
```

#### Lazy Objects
遅延生成
```PHP
$user = $repository->find(1);
```
実際にはDBアクセスせず、\$user->nameを読んだ瞬間にDBアクセス

#### 新DOM API
HTML5対応DOMで、CSSセレクタを使える
```PHP
Dom\HTMLDocument::createFromString(...)
$node = $document->querySelector(
    'main > article'
);
```

#### newの括弧省略
下記のように書ける
```PHP
new Foo->method();
```

#### クロージャ名の改善
無名関数がデバッグしやすくなった
スタックトレースで{closure}より詳しい名前が表示される

### 新しいクラス、列挙型、インターフェイス
#### BcMath\Number
任意精度数値をオブジェクトとして扱うクラス<br>
加減乗除、比較、丸めなどをメソッドや演算子で扱える

#### RoundingMode
丸め方法を表すenum<br>
round()やBcMath\Number::round()で使用できる

#### Dom\HTMLDocumentなどの新DOM API
Dom\HTMLDocumentはHTML文書を表すクラス<br>

#### #[Deprecated]属性
メソッド、関数、クラス定数などを今後使わないでほしいと明示するための属性

#### RequestParseBodyException
request_parse_body()で、不正なリクエストボディを解析したときに投げられる例外<br>
判定はContent-Typeに基づく

#### Pdo\MysqlなどのPDOドライバ別クラス
PDOの各ドライバを表すクラスが追加されている
```PHP
Pdo\Mysql
Pdo\Pgsql
Pdo\Sqlite
Pdo\Odbc
Pdo\Firebird
Pdo\DbLib
```

#### ReflectionConstant
グローバル定数をReflectionで調査するためのクラス

### 新しく追加された関数
#### array_find()・array_find_key()
配列から、条件に合う最初の要素やキーを取得できる

#### array_any()・array_all()
配列の要素が条件を満たすかを簡潔に判定できる

#### mb_trim()・mb_ltrim()・mb_rtrim()
マルチバイト文字を考慮して、文字列の前後を削除できる

#### mb_ucfirst()・mb_lcfirst()
文字列の先頭1文字を、Unicode対応で大文字・小文字へ変換

#### grapheme_str_split()
文字列を、利用者から見た1文字単位に分割

#### DateTimeImmutable::createFromTimestamp()
Unixタイムスタンプから、DateTimeImmutableを直接作れる<br>
DateTime版も追加されている

#### マイクロ秒の取得・設定
次のメソッドが追加されている
```PHP
$date->getMicrosecond();
$date->setMicrosecond(123456);
```

#### BCMathの丸め関数
次が追加されている
```PHP
bcceil() // 切り上げ
bcfloor() // 切り捨て
bcround() // 任意精度で丸める
bcdivmod() // 商と余り
```

#### request_parse_body()
HTTPリクエストのボディを解析し、フォームデータとアップロードファイルを取得する
```PHP
try {
    [$post, $files] = request_parse_body();
} catch (RequestParseBodyException $e) {
    http_response_code(400);
}
```
主に、PUTやPATCHで送られたmultipart/form-dataなどを、フレームワークを介さず解析する場合に使える

#### HTTPレスポンスヘッダー取得関数
次が追加されている
```PHP
http_get_last_response_headers(); // HTTPストリームで、直前のレスポンスヘッダーを扱いやすくする
http_clear_last_response_headers(); // 処理後に消す
```

#### XMLReader・XMLWriterのファクトリーメソッド
次のメソッドが追加されている
```PHP
XMLReader::fromStream()
XMLReader::fromUri() // ファイルから読む
XMLReader::fromString() // XML文字列を読む
XMLWriter::toStream()
XMLWriter::toUri()
XMLWriter::toMemory() // メモリ上へXMLを書く
```

#### DOMXPath::quote()
XPathへ文字列を安全に埋め込むための値を作れる

### 新しいグローバル定数
#### LIBXML_NO_XXE
外部エンティティを読み込ませずにXMLを解析するための安全対策として使える

#### CURL_HTTP_VERSION_3 / CURL_HTTP_VERSION_3ONLY
HTTP/3を使うためのcURL定数

#### CURLOPT_DEBUGFUNCTION と CURLINFO_*
cURLのデバッグ用コールバックと、それに対応する情報種別が追加されている
```PHP
CURLOPT_DEBUGFUNCTION
CURLINFO_TEXT
CURLINFO_HEADER_IN
CURLINFO_DATA_IN
CURLINFO_DATA_OUT
CURLINFO_SSL_DATA_IN
CURLINFO_SSL_DATA_OUT
```

#### XML_OPTION_PARSE_HUGE
非常に大きなXMLを解析する場合、通常のXMLパーサーの制限を緩和




