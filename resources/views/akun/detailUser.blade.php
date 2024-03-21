<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <title>Document</title>
</head>

<body class="p-10">
    @if ($errors->any())
        <div role="alert" class="alert alert-error mb-8 text-white">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('success'))
        <div role="alert" class="alert alert-success mb-8 text-white">
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <div class="mb-20">
        <h1>{{ $user->nama }}</h1>
        <h1>{{ $user->id_user }} - {{ $user->password }}</h1>
    </div>


    @foreach ($headData as $properti)
        @if ($properti === 'id_user' || $properti === 'password')
        @else
            <form action="" method="POST" class="card mb-10">
                @csrf
                <input type="hidden" value="{{ $properti }}" name="namaKolom">
                <input type="hidden" value="{{ $user->$properti }}" name="nilaiAwal">

                <h3 class="text-gray-800 font-bold">{{ $properti }}</h3>
                <p>{{ $user->$properti }}</p>

                <button class="btn btn-neutral" type="input">Ganti</button>
            </form>
        @endif
    @endforeach

</body>

</html>
