@extends('layouts.app', ['title' => 'CMS User'])

@push('vendor_styles')
<link href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css" rel="stylesheet">
@endpush

@section('content')
<header class="app-header">
    <div class="container-fluid px-4 py-3 d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div>
            <p class="text-uppercase text-muted fw-semibold small mb-1">Dashboard</p>
            <h1 class="h4 mb-0">CMS User</h1>
        </div>
        <div class="d-flex flex-wrap align-items-center gap-2">
            <span class="text-muted small">{{ auth()->user()->name }}</span>
            <button type="button" id="addUserButton" class="btn btn-primary icon-button">
                <i data-lucide="user-plus"></i>
                <span>Tambah User</span>
            </button>
            <button type="button" id="logoutButton" class="btn btn-outline-secondary icon-button">
                <i data-lucide="log-out"></i>
                <span>Logout</span>
            </button>
        </div>
    </div>
</header>

<main class="container-fluid cms-main px-4">
    <div id="pageAlert" class="alert d-none" role="alert"></div>

    <section class="tool-panel">
        <div class="table-responsive">
            <table id="usersTable" class="table table-striped table-hover align-middle w-100">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Email</th>
                        <th>Nama</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
            </table>
        </div>
    </section>
</main>

<div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="userModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title h5" id="userModalTitle">Tambah User</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <form id="userForm" enctype="multipart/form-data">
                <div class="modal-body">
                    <div id="formAlert" class="alert alert-danger d-none" role="alert"></div>

                    <div class="row g-4">
                        <div class="col-md-7">
                            <div class="mb-3">
                                <label for="userEmail" class="form-label">Email</label>
                                <input type="email" id="userEmail" name="email" class="form-control" autocomplete="off">
                                <div class="invalid-feedback" data-error-for="email"></div>
                            </div>

                            <div class="mb-3">
                                <label for="userName" class="form-label">Nama</label>
                                <input type="text" id="userName" name="name" class="form-control" autocomplete="off">
                                <div class="invalid-feedback" data-error-for="name"></div>
                            </div>

                            <div class="mb-3">
                                <label for="userPassword" class="form-label">Password</label>
                                <input type="password" id="userPassword" name="password" class="form-control" autocomplete="new-password">
                                <div class="invalid-feedback" data-error-for="password"></div>
                            </div>

                            <div class="mb-0">
                                <label for="profileImage" class="form-label">Image Profile</label>
                                <input type="file" id="profileImage" name="profile_image" class="form-control" accept="image/*">
                                <div class="invalid-feedback" data-error-for="profile_image"></div>
                            </div>
                        </div>

                        <div class="col-md-5">
                            <label class="form-label d-block">Preview</label>
                            <div class="image-preview-wrap">
                                <img id="previewImage" class="image-preview d-none" alt="Preview image profile">
                                <div id="previewEmpty" class="image-preview-empty">
                                    <i data-lucide="image"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" id="saveUserButton" class="btn btn-primary icon-button">
                        <i data-lucide="save"></i>
                        <span>Simpan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title h5" id="deleteModalTitle">Hapus User</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0">Hapus <strong id="deleteUserName">user ini</strong>?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" id="confirmDeleteButton" class="btn btn-danger icon-button">
                    <i data-lucide="trash-2"></i>
                    <span>Hapus</span>
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('vendor_scripts')
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
@endpush

@push('scripts')
<script>
    $(function () {
        const usersBaseUrl = '{{ url('/users') }}';
        const userModal = new bootstrap.Modal(document.getElementById('userModal'));
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        let editingId = null;
        let deletingId = null;

        const table = $('#usersTable').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 10,
            lengthChange: false,
            searching: true,
            ordering: true,
            ajax: '{{ route('users.datatable') }}',
            order: [[1, 'asc']],
            columns: [
                {
                    data: 'profile_image_url',
                    orderable: false,
                    searchable: false,
                    render: function (data) {
                        if (data) {
                            return `<img src="${data}" class="profile-thumb" alt="Image profile">`;
                        }

                        return '<div class="profile-placeholder"><i data-lucide="user"></i></div>';
                    }
                },
                {
                    data: 'email',
                    render: $.fn.dataTable.render.text()
                },
                {
                    data: 'name',
                    render: $.fn.dataTable.render.text()
                },
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    className: 'text-end',
                    render: function (data, type, row) {
                        return `
                            <div class="d-inline-flex gap-2">
                                <button type="button" class="btn btn-outline-secondary action-button edit-user" data-id="${row.id}" title="Edit" data-bs-toggle="tooltip">
                                    <i data-lucide="pencil"></i>
                                    <span class="visually-hidden">Edit</span>
                                </button>
                                <button type="button" class="btn btn-outline-danger action-button delete-user" data-id="${row.id}" title="Hapus" data-bs-toggle="tooltip">
                                    <i data-lucide="trash-2"></i>
                                    <span class="visually-hidden">Hapus</span>
                                </button>
                            </div>
                        `;
                    }
                }
            ],
            drawCallback: function () {
                refreshIcons();
                document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function (element) {
                    bootstrap.Tooltip.getOrCreateInstance(element);
                });
            },
            language: {
                search: 'Cari:',
                processing: 'Memproses...',
                zeroRecords: 'Data tidak ditemukan',
                info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
                infoEmpty: 'Tidak ada data',
                infoFiltered: '(difilter dari _MAX_ total data)',
                paginate: {
                    first: 'Pertama',
                    last: 'Terakhir',
                    next: 'Berikutnya',
                    previous: 'Sebelumnya'
                }
            }
        });

        function userUrl(id) {
            return `${usersBaseUrl}/${id}`;
        }

        function clearFormErrors() {
            $('#formAlert').addClass('d-none').text('');
            $('#userForm .is-invalid').removeClass('is-invalid');
            $('#userForm .invalid-feedback').text('');
        }

        function showFormErrors(errors, fallbackMessage) {
            const firstMessage = errors ? Object.values(errors)[0]?.[0] : fallbackMessage;
            $('#formAlert').removeClass('d-none').text(firstMessage || 'Data gagal disimpan.');

            $.each(errors || {}, function (field, messages) {
                $(`[name="${field}"]`).addClass('is-invalid');
                $(`#userForm [data-error-for="${field}"]`).text(messages[0]);
            });
        }

        function showPageAlert(message, type = 'success') {
            $('#pageAlert')
                .removeClass('d-none alert-success alert-danger alert-warning')
                .addClass(`alert-${type}`)
                .text(message);
        }

        function setPreview(url) {
            if (url) {
                $('#previewImage').attr('src', url).removeClass('d-none');
                $('#previewEmpty').addClass('d-none');
            } else {
                $('#previewImage').attr('src', '').addClass('d-none');
                $('#previewEmpty').removeClass('d-none');
            }

            refreshIcons();
        }

        function resetUserForm() {
            editingId = null;
            clearFormErrors();
            $('#userForm')[0].reset();
            $('#userEmail').prop('readonly', false);
            $('#userPassword').attr('placeholder', '');
            $('#userModalTitle').text('Tambah User');
            $('#saveUserButton span').text('Simpan');
            setPreview(null);
        }

        function setSaveBusy(isBusy) {
            $('#saveUserButton').prop('disabled', isBusy);
            $('#saveUserButton span').text(isBusy ? 'Menyimpan...' : 'Simpan');
        }

        $('#addUserButton').on('click', function () {
            resetUserForm();
            userModal.show();
        });

        $('#profileImage').on('change', function () {
            const file = this.files?.[0];
            setPreview(file ? URL.createObjectURL(file) : null);
        });

        $(document).on('click', '.edit-user', function () {
            resetUserForm();
            editingId = $(this).data('id');
            $('#userModalTitle').text('Edit User');
            $('#userEmail').prop('readonly', true);
            $('#userPassword').attr('placeholder', 'Kosongkan jika tidak diubah');

            $.get(userUrl(editingId), function (response) {
                $('#userEmail').val(response.user.email);
                $('#userName').val(response.user.name);
                setPreview(response.user.profile_image_url);
                userModal.show();
            }).fail(function () {
                showPageAlert('Data user gagal dimuat.', 'danger');
            });
        });

        $('#userForm').on('submit', function (event) {
            event.preventDefault();
            clearFormErrors();
            setSaveBusy(true);

            const formData = new FormData(this);
            let requestUrl = usersBaseUrl;

            if (editingId) {
                requestUrl = userUrl(editingId);
                formData.append('_method', 'PUT');
            }

            $.ajax({
                url: requestUrl,
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    userModal.hide();
                    table.ajax.reload(null, false);
                    showPageAlert(response.message);
                },
                error: function (xhr) {
                    showFormErrors(xhr.responseJSON?.errors, xhr.responseJSON?.message);
                },
                complete: function () {
                    setSaveBusy(false);
                    refreshIcons();
                }
            });
        });

        $(document).on('click', '.delete-user', function () {
            const rowData = table.row($(this).closest('tr')).data();
            deletingId = $(this).data('id');
            $('#deleteUserName').text(rowData?.email || 'user ini');
            deleteModal.show();
        });

        $('#confirmDeleteButton').on('click', function () {
            if (! deletingId) {
                return;
            }

            const $button = $(this);
            $button.prop('disabled', true);
            $button.find('span').text('Menghapus...');

            $.ajax({
                url: userUrl(deletingId),
                method: 'DELETE',
                success: function (response) {
                    deleteModal.hide();

                    if (response.redirect) {
                        window.location.href = response.redirect;
                        return;
                    }

                    table.ajax.reload(null, false);
                    showPageAlert(response.message);
                },
                error: function (xhr) {
                    showPageAlert(xhr.responseJSON?.message || 'User gagal dihapus.', 'danger');
                },
                complete: function () {
                    deletingId = null;
                    $button.prop('disabled', false);
                    $button.find('span').text('Hapus');
                    refreshIcons();
                }
            });
        });

        $('#logoutButton').on('click', function () {
            $.post('{{ route('logout') }}', function (response) {
                window.location.href = response.redirect;
            }).fail(function () {
                showPageAlert('Logout gagal.', 'danger');
            });
        });
    });
</script>
@endpush
