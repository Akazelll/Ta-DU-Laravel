@extends('adminlte::page')

@section('title', 'Data Anggota')

@section('content_header')
    <h1>Data Anggota</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Daftar Pengguna Aplikasi</h3>
                    <div class="card-tools">
                        <div class="d-flex">
                            {{-- Search --}}
                            <form action="{{ route('users.index') }}" method="GET" class="mr-2">
                                <div class="input-group input-group-sm" style="width: 250px;">
                                    <input type="text" name="search" class="form-control" placeholder="Cari nama/email/kode..." value="{{ request('search') }}">
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-default">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                            </form>
                            
                            {{-- Download PDF --}}
                            <a href="{{ route('users.download') }}" target="_blank" class="btn btn-sm btn-info" title="Download Laporan Anggota">
                                <i class="fas fa-file-pdf"></i> <span class="d-none d-md-inline">Unduh PDF</span>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Kode Anggota</th>
                                <th>Nama Lengkap</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Bergabung</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($users as $key => $user)
                                <tr>
                                    <td>{{ $users->firstItem() + $key }}</td>
                                    <td>
                                        @if($user->kode_anggota)
                                            <span class="badge badge-info">{{ $user->kode_anggota }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        @if ($user->role == 'admin')
                                            <span class="badge badge-success">Admin</span>
                                        @else
                                            <span class="badge badge-secondary">User</span>
                                        @endif
                                    </td>
                                    <td>{{ $user->created_at->isoFormat('D MMM Y') }}</td>
                                    <td class="text-center">
                                        <div class="btn-group">
                                            {{-- Edit --}}
                                            <a href="{{ route('users.edit', $user) }}" class="btn btn-xs btn-warning" title="Edit Data">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            
                                            {{-- Reset Password (Dropdown / Button) --}}
                                            <button type="button" onclick="confirmReset({{ $user->id }}, '{{ $user->name }}')" class="btn btn-xs btn-default" title="Reset Password Default (12345678)">
                                                <i class="fas fa-key"></i>
                                            </button>

                                            {{-- Hapus --}}
                                            @if (Auth::id() != $user->id) {{-- Mencegah hapus diri sendiri --}}
                                                <button type="button" onclick="confirmDelete({{ $user->id }}, '{{ $user->name }}')" class="btn btn-xs btn-danger" title="Hapus User">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            @endif
                                        </div>

                                        {{-- Hidden Forms --}}
                                        <form id="delete-form-{{ $user->id }}" action="{{ route('users.destroy', $user->id) }}" method="POST" style="display: none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                        
                                        <form id="reset-form-{{ $user->id }}" action="{{ route('users.resetPassword', $user->id) }}" method="POST" style="display: none;">
                                            @csrf
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-3">Tidak ada data pengguna yang ditemukan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="card-footer clearfix">
                    {{ $users->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Alert Delete
        function confirmDelete(id, name) {
            Swal.fire({
                title: 'Hapus Pengguna?',
                text: "Anda akan menghapus user: " + name,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + id).submit();
                }
            })
        }

        // Alert Reset Password
        function confirmReset(id, name) {
            Swal.fire({
                title: 'Reset Password?',
                text: "Password user " + name + " akan direset menjadi '12345678'.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#ffc107',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Reset!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('reset-form-' + id).submit();
                }
            })
        }
        
        // Menampilkan pesan sukses dari session (jika ada)
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: '{{ session('success') }}',
                timer: 3000,
                showConfirmButton: false
            });
        @endif
    </script>
@stop