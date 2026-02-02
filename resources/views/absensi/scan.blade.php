@extends('layouts.app')

@section('content')
    <div class="container text-center py-4">
        <h3 class="mb-3">Scan QR Siswa</h3>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div id="reader" style="width:320px;margin:auto;"></div>

        <form id="scanForm" method="POST" action="{{ route('absensi.prosesScan') }}">
            @csrf
            <input type="hidden" name="qr_code" id="qr_code">
        </form>
    </div>

    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <script>
        function onScanSuccess(decodedText) {
            document.getElementById('qr_code').value = decodedText;
            document.getElementById('scanForm').submit();
        }

        new Html5QrcodeScanner("reader", { fps: 10, qrbox: 250 })
            .render(onScanSuccess);
    </script>
@endsection