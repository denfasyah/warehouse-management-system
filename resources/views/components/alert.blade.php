@if (session('success') || session('error'))
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            @if (session('success'))
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: '{{ session('success') }}',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    customClass: {
                        popup: 'rounded-xl shadow-lg border border-gray-100',
                        title: 'font-body-md'
                    }
                });
            @endif

            @if (session('error'))
                Swal.fire({
                    title: 'Terjadi Kesalahan',
                    text: '{{ session('error') }}',
                    icon: 'error',
                    confirmButtonColor: '#ba1a1a',
                    customClass: {
                        popup: 'rounded-2xl shadow-xl border border-gray-100',
                        title: 'font-headline-md text-gray-800',
                        confirmButton: 'rounded-lg px-6',
                    }
                });
            @endif
        });
    </script>
@endif

@if ($errors->any())
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            Swal.fire({
                title: 'Validasi Gagal',
                html: '<ul class="text-left list-disc list-inside text-sm text-gray-600">' +
                      @foreach ($errors->all() as $error)
                      '<li>{{ $error }}</li>' +
                      @endforeach
                      '</ul>',
                icon: 'error',
                confirmButtonColor: '#ba1a1a',
                customClass: {
                    popup: 'rounded-2xl shadow-xl border border-gray-100',
                    title: 'font-headline-md text-gray-800',
                    confirmButton: 'rounded-lg px-6',
                }
            });
        });
    </script>
@endif
