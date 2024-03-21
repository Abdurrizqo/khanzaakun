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

    <form class="flex gap-4 mb-10" action="buat-akun" method="POST">
        @csrf
        <input type="text" placeholder="ID yang akan di salin" name="userUtama"
            class="border px-3 rounded-full min-w-96 outline-none" />

        <input type="text" placeholder="id pegawai baru" name="userBaru"
            class="border px-3 rounded-full min-w-96 outline-none" />

        <button type="submit" class="btn-outline btn border border-neutral rounded-full w-28">Buat Akun</button>
    </form>

    <form class="flex gap-4 mb-10" action="copy-akses" method="POST">
        @csrf
        <input type="text" placeholder="ID yang akan di salin" name="userUtamaSalin"
            class="border px-3 rounded-full min-w-96 outline-none" />

        <input type="text" placeholder="id yang di terima" name="userGanti"
            class="border px-3 rounded-full min-w-96 outline-none" />

        <button type="submit" class="btn-outline btn border border-neutral rounded-full w-28">Salin</button>
    </form>


    <form class="flex gap-4"method="GET" action="cari">
        <input type="text" placeholder="Cari ID" name="search"
            class="border px-3 rounded-full min-w-96 outline-none" />
        <button type="submit" class="btn-outline btn border border-neutral rounded-full w-28">Cari</button>
    </form>

    <div class="overflow-auto">
        <table class="table table-zebra">
            <thead>
                <tr>
                    <th>Kontrol</th>
                    <th>Nama</th>
                    @foreach ($headData as $item)
                        <th>{{ $item }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach ($allUser as $item)
                    <tr>
                        <td class="flex gap-8 items-center justify-center">
                            <a class="btn btn-success text-white" href="/update-user/{{ $item->id_user }}">edit</a>
                            <a class="btn btn-warning text-white" href="/akun/{{ $item->id_user }}">detail</a>
                            <a class="btn btn-error text-white" href="/delete-user/{{ $item->id_user }}">delete</a>
                        </td>
                        <td>{{ $item->nama }}</td>
                        @foreach ($headData as $properti)
                            <td>{{ $item->$properti }}</td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{ $allUser->links() }}
</body>

</html>
