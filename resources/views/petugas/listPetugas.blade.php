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

    <form class="flex gap-4"method="GET">
        <input type="text" placeholder="Cari ID" name="search"
            class="border px-3 rounded-full min-w-96 outline-none" />
        <button type="submit" class="btn-outline btn border border-neutral rounded-full w-28">Cari</button>
    </form>

    <div class="overflow-auto">
        <table class="table table-zebra">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>NIP</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($allUser as $item)
                    <tr>
                        <td>{{ $item->nama }}</td>
                        <td>{{ $item->nip }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</body>

</html>
