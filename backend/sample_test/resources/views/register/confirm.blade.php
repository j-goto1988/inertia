<!DOCTYPE html>
<html>
<body>
    <h1>確認画面</h1>
    <div>
        名前：{{ $form['name'] }}
    </div>
    <div>
        メール：{{ $form['email'] }}
    </div>
    <form method="POST" action="/register/store">
        @csrf
        @foreach($form as $key => $value)
            <input
                type="hidden"
                name="{{ $key }}"
                value="{{ $value }}"
            >
        @endforeach
        <button type="submit">
            登録する
        </button>
    </form>
    <form method="POST" action="/register/back">
        @csrf
        @foreach($form as $key => $value)
            <input
                type="hidden"
                name="{{ $key }}"
                value="{{ $value }}"
            >
        @endforeach
        <button type="submit">
            戻る
        </button>
    </form>
</body>
</html>