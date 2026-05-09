## 概要
Inertiaにはクライアントサイドルーティング機能はなく、APIも不要<br>
これまでと同じようにコントローラーとページビューを構築するだけでOK<br>

Inertiaはフレームワークではなく、サーバーサイドとクライアントサイドをつなぐ接着剤<br>
サーバーサイド→Laravel、Rails、Phoenix、Django<br>
クライアントサイド→React、Vue、Svelte<br>

コントローラーを作成し、データベースからデータを取得し（ORMを介して）、ビューをレンダリング<br>
Inertiaのビューは、React、Vue、またはSvelteで記述されたJavaScriptページコンポーネント<br>

Inertiaはアプリケーションのビューレイヤーを置き換える<br>

PHPやRubyテンプレートによるサーバーサイドレンダリングの代わりに、アプリケーションから返されるビューはJavaScriptページコンポーネントになる<br>

ぺージ全体を再読み込みすることなくページ遷移を行うことができる<br>
<Link>という通常のアンカーリンクを軽量にラップしたコンポーネントを使用して実現される<br>
Inertiaの<Link>をクリックすると、Inertiaが通常のページ遷移を止め、代わりにXHRを介して遷移を行う<br>
JavaScriptからはrouter.visit()を使うことで、<Link>と同様の遷移を行うことができる<br>

InertiaがXHRリクエストを送信すると、サーバーはそれがInertiaからのリクエストであることを検知し、完全なHTMLレスポンスを返す代わりに、JavaScriptページコンポーネント名とデータ（props）を含むJSONレスポンスを返す<br>
その後、Inertiaは以前のページコンポーネントを新しいページコンポーネントに動的に置き換え、ブラウザの履歴状態を更新する<br>

<Link>クリック
 ↓
通常の遷移を止める（preventDefault）
 ↓
fetch（ajax）でLaravelにリクエスト
 ↓
LaravelがJSON返す（component + props）
 ↓
Inertiaがページコンポーネントを解決 reactのコンポーネントを特定している
 ↓
Reactにpropsを渡す
 ↓
Reactが再描画

通常
Laravel → 完成HTML → 表示

Inertia
Laravel → 土台HTML + JSON → React → 表示


## プロトコル
### HTMLレスポンス
最初のリクエストは、Inertia専用のヘッダーを含まない通常のブラウザリクエストとして処理される<br>
HTMLには、Reactが描画するための空の場所（div）と、最初に表示するページのデータ（JSON）が含まれている<br>
Inertiaはそのデータを使ってReactを起動し、画面を表示する<br>
初期レスポンスはHTMLだが、InertiaはJavaScriptページコンポーネントをサーバーサイドレンダリングしない<br>

### Inertiaレスポンス
Inertiaが起動した後のリクエストは、特別なヘッダー（X-Inertia）を付けて送信される<br>
このヘッダーを見たサーバーは、HTMLの代わりにJSONを返す<br>

<Link>やrouter.visit()がX-Inertiaヘッダーを付与し、そのヘッダーをもとにサーバーがInertiaリクエストかどうかを判定している<br>

### リクエストヘッダー
X-Inertia→X-Inertiaリクエストかの判定で、trueならJSONを返しfalseならHTMLを返す<br>
X-Inertia-Version→JSやCSSのバージョン管理で、バージョンが違う場合はHTMLを強制リロードする<br>
X-Inertia-Partial-Data→partial reloadで使用し、必要なデータだけ取得する<br>
X-Inertia-Partial-Component→どのページのデータかを判定する<br>
X-Requested-With→非同期通信（Ajax）であることを示す<br>

### レスポンスヘッダー
X-Inertia→trueなら正しいInertiaレスポンス<br>
Vary→URLが同じなので、Inertiaかどうかでキャッシュ分ける<br>
X-Inertia-Location→強制的にフルリロードさせる<br>

### 仕組みの便利な図
https://inertiajs.com/docs/v3/core-concepts/the-protocol

## アセットのバージョン管理
JS/CSSが更新されたら、強制的にページをリロードする仕組み<br>

1. サーバーがアセットのバージョンを持つ
初回HTMLレスポンスの中に埋め込まれるページオブジェクトにもversionが入っており、InertiaレスポンスのJSONにもversionがある<br>

2. クライアントが自分のバージョンを送る
Inertiaが起動したあとのリクエストでは、クライアントはX-Inertia-Versionヘッダーを付けて送信する<br>

3. サーバーが比較する
サーバーは受け取ったX-Inertia-Versionと、自分が持つ最新のversionを比べる<br>
一致していれば、そのまま通常のInertia JSONレスポンスを返す<br>
不一致なら、このままJSONだけ返しても古いJSで壊れる可能性があると判断し、通常のHTML読み込みに戻して揃え直す<br>

Inertiaリクエスト
↓
サーバー「バージョン違う」
↓
409 Conflict + X-Inertia-Locationを返す
↓
Inertiaクライアントがそれを受ける
↓
window.locationで通常のフルページ遷移
↓
HTMLを再取得
↓
最新JS/CSSで起動し直す

## Partial Reloads
必要なデータだけ再取得する仕組み<br>
propsの差分更新をし、再描画の影響を最小化<br>

### 部分的なデータ
部分的な再読み込みを実行するには、router.visitのonlyオプションを指定して、サーバーが返すデータを指定
```js
import { router } from "@inertiajs/react";
router.visit(url, {
  only: ["users"],
});
```
Linkでonlyプロパティを使用して、部分的な再読み込みを実行できる
```js
import { Link } from "@inertiajs/react";
<Link href="/users?active=true" only={["users"]}>
  Show active
</Link>;
```

### 部分的なデータを除外
サーバーが除外するデータを指定する場合は、router.visitのexceptオプションを指定
```js
import { router } from "@inertiajs/react";
router.visit(url, {
  except: ["users"],
});
```

### 遅延データ評価
partial reloadを最大限活用するには、コントローラーで返すデータをそのまま実行するのではなく、クロージャで包んで遅延実行にすることで、必要なデータだけ取得されるようにする
```js
return Inertia::render('Users/Index', [
    'users' => fn () => User::all(),
    'companies' => fn () => Company::all(),
]);
```

Inertiaはリクエスト時に「どのデータが必要か」を判断し、そのデータだけを後から実行する<br>
そのため、不要な処理を避けることができ、多くのオプションデータを持つページでもパフォーマンスが向上する<br>

さらに、Inertia::optional()を使うと、明示的に要求されたときだけデータを返し、それ以外の場合は一切実行されないようにできる
```js
return Inertia::render('Users/Index', [
    'users' => Inertia::optional(fn () => User::all()),
]);
```

Inertia::always()を使うと、partial reloadで対象に含めていなくても、そのプロパティは常にレスポンスに含まれるようになる
```js
return Inertia::render('Users/Index', [
    'users' => Inertia::always(User::all()),
]);
```

① 通常
'users' => User::all()
常に実行

② Lazy
'users' => fn () => User::all()
必要なときだけ実行

③ optional
'users' => Inertia::optional(fn () => User::all())
onlyで指定しないと実行されない

④ always
'users' => Inertia::always(User::all())
必ず毎回実行

| 書き方 | 通常アクセス | partial reload | User::all()の実行 |
| ---- | ---- | ---- | ---- |
| User::all() | 必ず含まれる | 必要なら含まれる | 毎回実行 |
| fn () => User::all() | 必ず含まれる | 必要なら含まれる | 必要な時だけ実行 |
| Inertia::optional(fn () => User::all()) | 含まれない | onlyで指定した時だけ含まれる | 指定時だけ実行 |
| Inertia::always(fn () => User::all()) | 必ず含まれる | 必ず含まれる | 毎回実行 |


表の通常アクセスは、Linkやrouter.visitの場合のこと<br>
表のpartial reloadは、partial reload時にonlyで指定されたら含まれる場合のこと<br>


### エラーの保存
LaravelのInertiaでは、エラー情報は常にレスポンスに含まれる（Inertia::always()のような扱い）<br>
そのため、partial reloadでバリデーションが実行されていない場合でも、空のエラーが返されると、既存のエラーは上書きされて消えてしまう<br>
もし既存のエラーを保持したい場合は、preserveErrorsオプションを使う

### Once Propsとの組み合わせ
once()を付けたデータは最初のリクエスト時にだけ取得され、その後の遷移では再度サーバーから取得されず、クライアントに保存された値が使われる<br>

## instant visits
サーバーの返答を待たずに、先に画面を切り替える<br>
先にコンポーネントだけ表示して、あとからpropsを入れる<br>
instant visitsを使うには、Linkに遷移先のコンポーネント名（component）を指定する<br>
```js
import { Link } from "@inertiajs/react";
<Link href="/dashboard" component="Dashboard">
  Dashboard
</Link>;
```

<Link>クリック
↓
即座に画面切り替え（仮）
↓
裏でサーバー通信
↓
データ届いたら更新


https://inertiajs.com/?utm_source=chatgpt.com
https://inertiajs.com/docs/v3/getting-started
https://inertiajs.com/docs/v3/core-concepts/how-it-works
https://inertiajs.com/docs/v3/core-concepts/the-protocol
https://inertiajs.com/docs/v3/the-basics/instant-visits
https://inertiajs.com/docs/v3/data-props/partial-reloads