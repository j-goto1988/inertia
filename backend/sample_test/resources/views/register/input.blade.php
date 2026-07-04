<!DOCTYPE html>
<html>
<body>
    <h1>会員登録</h1>
    @if ($errors->any())
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif
    <form method="POST" action="/register/confirm">
        @csrf
        <div>
            名前
            <input type="text" name="name" value="{{ old('name') }}">
        </div>

        <div>
            メールアドレス
            <input type="email" name="email" value="{{ old('email') }}">
        </div>

        <div>
            パスワード
            <input type="password" name="password">
        </div>

        <button type="submit">
            確認
        </button>
    </form>
</body>
</html>