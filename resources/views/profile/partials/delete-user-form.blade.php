<section class="space-y-6">
    <p class="text-sm text-muted">
        Setelah akun Anda dihapus, semua sumber daya dan data akan dihapus secara permanen. Silakan unduh data atau informasi apa pun yang ingin Anda simpan sebelum menghapus akun Anda.
    </p>

    <button type="button" class="btn btn-danger" onclick="confirmDeleteAccount()">
        <i class="fas fa-trash-alt mr-1"></i> Hapus Akun
    </button>

    {{-- Form Hidden untuk Submit --}}
    <form id="delete-account-form" method="post" action="{{ route('profile.destroy') }}" class="d-none">
        @csrf
        @method('delete')
        <input type="password" name="password" id="delete-password-input">
    </form>

    @push('js')
    <script>
        function confirmDeleteAccount() {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Akun yang dihapus tidak dapat dikembalikan. Masukkan password Anda untuk konfirmasi.",
                input: 'password',
                inputAttributes: {
                    autocapitalize: 'off',
                    placeholder: 'Password Anda'
                },
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus Akun',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#d33',
                showLoaderOnConfirm: true,
                preConfirm: (password) => {
                    if (!password) {
                        Swal.showValidationMessage('Password wajib diisi!')
                    } else {
                        // Masukkan password ke input hidden dan submit form
                        document.getElementById('delete-password-input').value = password;
                        document.getElementById('delete-account-form').submit();
                    }
                }
            });
        }
        
        // Menangani error jika password salah saat redirect kembali
        @if($errors->userDeletion->has('password'))
            Swal.fire({
                icon: 'error',
                title: 'Gagal Menghapus',
                text: '{{ $errors->userDeletion->first('password') }}'
            });
        @endif
    </script>
    @endpush
</section>