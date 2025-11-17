@extends('layouts.base')

@section('title', 'Detail Event')

@section('custom_css_link', asset('css/Detail_Event_style/main.css'))

@section('breadcrumbs')
<div class="breadcrumbs-box mt-1 py-2">
  <div class="page-title mb-1">Detail Event</div>
  <nav style="--bs-breadcrumb-divider: '>'" aria-label="breadcrumb">
    <ol class="breadcrumb m-0">
      <li class="breadcrumb-item align-items-center">
        <a href="{{route('admin.home')}}" class="text-decoration-none">Beranda</a>
      </li>
      <li class="breadcrumb-item align-items-center active" aria-current="page">
        Detail Event
      </li>
    </ol>
  </nav>
</div>
@endsection

@section('main-content')
<div class="main-content mt-3">
  <div class="stats">
    <div class="stat-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
      <h3>Total Lapak</h3>
      <div class="number">{{App\Support\Constants\Constants::MAX_STALLS}}</div>
    </div>
    <div class="stat-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
      <h3>Lapak Tersedia</h3>
      <div class="number">{{App\Support\Constants\Constants::MAX_STALLS - $event->total_registrant}}
      </div>
    </div>
    <div class="stat-card" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
      <h3>Total Grup</h3>
      <div class="number">{{$event->groups_count}}</div>
    </div>
    <div class="stat-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
      <h3>Total Pendaftar</h3>
      <div class="number">{{$event->total_registrant}}</div>
    </div>
  </div>
  <div class="tab-panel">
    <ul class="nav nav-tabs" id="myTab" role="tablist">
      <li class="nav-item" role="presentation">
        <button class="nav-link text-black active" id="home-tab" data-bs-toggle="tab"
          data-bs-target="#manage-registrant-tab-pane" type="button" role="tab" data-cy="tab-overview"
          aria-controls="manage-registrant-tab-pane" aria-selected="true">
          👥 Kelola Peserta
        </button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="text-black nav-link" id="profile-tab" data-bs-toggle="tab" data-cy="tab-pdf"
          data-bs-target=" #raffle-tab-pane" type="button" role="tab" aria-controls="raffle-tab-pane"
          aria-selected="false">
          🎲 Pengundian
        </button>
      </li>
    </ul>
    <div class="tab-content" id="myTabContent">
      <div class="content-wrapper bg-white mt-2 p-2 rounded-2 overflow-auto">
        <div class="tab-pane overview-wrapper fade show active" id="manage-registrant-tab-pane" role="tabpanel"
          aria-labelledby="home-tab" tabindex="0">
          <div class="action-wrapper d-lg-flex mt-3 justify-content-between align-items-baseline">
            <div class="wrapper d-flex justify-content-end">
              <a href="#" class="btn btn-success">
                <div data-cy="btn-link-add-type" class="wrapper d-flex gap-2 align-items-center" id="add"
                  data-bs-toggle="modal" data-bs-target="#addNewModal">
                  <span class="fw-medium">Tambah Pendaftar</span>
                </div>
              </a>
            </div>
            <div class="wrapper mt-2 mt-lg-0">
              <div class="input-group">
                <input data-cy="input-type-name" type="text" class="form-control py-2 px-3 search-input border"
                  placeholder="Telusuri" name="type" />
              </div>
            </div>
          </div>
          <div class="table-wrapper">
            <table id="participant-group-table" class="bg-white rounded table mt-3 table-hover  rounded-2"
              style="width: 100%">
              <thead>
                <tr>
                  <th>No.</th>
                  <th>Nama</th>
                  <th>No Telepon</th>
                  <th>Anggota</th>
                  <th>Status</th>
                  <th>Status Pengundian</th>
                  <th>Tanggal</th>
                  <th>Aksi</th>
                  <th>Informasi</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($event->groups as $group)
                <tr>
                  <td>{{$loop->iteration}}</td>
                  <td>{{$group->name}}</td>
                  <td>{{$group->phone_num}}</td>
                  <td>{{$group->total_member}}</td>
                  <td>
                    @switch($group->status)
                    @case(App\Support\Enums\ParticipantGroupStatusEnum::UNPAID->value)
                    <span class="badge bg-danger">Belum Bayar
                    </span>
                    @break
                    @case(App\Support\Enums\ParticipantGroupStatusEnum::DP->value)
                    <span class="badge bg-warning">DP</span>
                    @break
                    @case(App\Support\Enums\ParticipantGroupStatusEnum::PAID->value)
                    <span class="badge bg-primary">Lunas
                    </span>
                    @break
                    @case(App\Support\Enums\ParticipantGroupStatusEnum::COMPLETED->value)
                    <span class="badge bg-success">Selesai Diundi</span>
                    @break
                    @endswitch
                  </td>
                  <td>
                    @switch($group->raffle_status)
                    @case(App\Support\Enums\ParticipantGroupRaffleStatusEnum::NOT_YET->value)
                    <span class="badge bg-danger">Belum Diundi</span>
                    @break
                    @case(App\Support\Enums\ParticipantGroupStatusEnum::COMPLETED->value)
                    <span class="badge bg-success">Selesai Diundi</span>
                    @break
                    @endswitch
                  </td>
                  <td>{{$group->created_at_formatted}}</td>
                  <td>
                    <div class="d-flex gap-1">
                      <div data-bs-toggle="modal" data-bs-target="#editModal"
                        class="btn btn-warning btn-edit text-black" data-participant-group-id="{{$group->id}}">
                        Edit
                      </div>
                      <div class="btn btn-danger btn-delete" data-bs-toggle="modal" data-bs-target="#deleteModal"
                        data-registrant-name="{{$group->name}}"
                        data-delete-link={{route('admin.destroy.ParticipantGroup', $group->id)}}>
                        Hapus
                      </div>
                    </div>
                  </td>
                  <td>{{$group->information}}</td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
        <div class="tab-pane fade document-link-wrapper" data-cy="wrapper-document-link" id="raffle-tab-pane"
          role="tabpanel" aria-labelledby="raffle-tab-pane" tabindex="0">
          <table class="table bg-white mt-3 table-bordered" data-cy="table-document-link">
            <thead>
              <tr>
                <th>No.</th>
                <th>Dokumen</th>
                <th>Keterangan</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td></td>
                <td>
                  <a target="blank" href="" class="text-decoration-none" data-cy="link-document">
                  </a>
                </td>
                <td>
                  <p class="mb-0"></p>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Create Modal -->
<div class="modal fade" id="addNewModal" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog ">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="myModalLabel">Tambah Pendaftar</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form action="{{route('admin.store.participant.group')}}" class="form" id="addForm" method="POST">
          @csrf
          <input type="hidden" name="event_id" value="{{$event->id}}">
          <div class="form-group mb-3">
            <label for="name" class="mb-1">Nama</label>
            <input value="" required class="form-control" type="text" name="name"
              placeholder="Masukkan nama grup atau orang" />
          </div>
          <div class="form-group mb-3">
            <label for="phone_num" class="mb-1">No Telepon (WA)</label>
            <input value="" required class="form-control" type="text" name="phone_num"
              placeholder="Masukkan no telepon WA" />
          </div>
          <div class="form-group mb-3">
            <label for="total_member" class="mb-1">Jumlah Pemancing</label>
            <input value="" required class="form-control" type="number" min="1" name="total_member"
              placeholder="Masukkan jumlah pemancing" />
          </div>
          <div class="form-group mb-3">
            <label class="form-label">Status</label>
            <select data-cy="input-lecturer" class="form-select" aria-label="Default select example" name="status"
              required>
              <option value="">Pilih Status</option>
              <option value={{App\Support\Enums\ParticipantGroupStatusEnum::UNPAID}}>Belum Lunas</option>
              <option value={{App\Support\Enums\ParticipantGroupStatusEnum::DP}}>DP</option>
              <option value={{App\Support\Enums\ParticipantGroupStatusEnum::PAID}}>Lunas</option>
            </select>
          </div>
          <div class="form-group mb-3">
            <label for="information" class="mb-1">Informasi (Opsional)</label>
            <input value="" required class="form-control" type="text" name="information"
              placeholder="Masukkan informasi tambahan" />
          </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button data-cy="btn-submit-store" type="submit" class="btn btn-submit btn-success text-white">Simpan</button>
      </div>
      </form>
    </div>
  </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog ">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="myModalLabel">Edit Pendaftar</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div id="spinner" class="spinner-wrapper d-flex justify-content-center p-2">
        <div class="spinner-border text-primary" role="status">
          <span class="visually-hidden">Loading...</span>
        </div>
      </div>
      <div class="not-found-state">
        <span class="text-danger">User not found</span>
      </div>
      <div class="content-wrapper d-none" id="content-wrapper">
        <form action="" class="editForm" id="editForm" method="POST">
          <div class="modal-body">
            @csrf
            @method("PUT")
            <input type="hidden" name="participant_group_id" id="participant-group-id" value="{{$event->id}}">
            <div class="form-group mb-3">
              <label for="name" class="mb-1">Nama</label>
              <input value="" required class="form-control" type="text" name="name" id="name_edit"
                placeholder="Masukkan nama grup atau orang" />
            </div>
            <div class="form-group mb-3">
              <label for="phone_num" class="mb-1">No Telepon (WA)</label>
              <input value="" required class="form-control" type="text" name="phone_num" id="phone_num_edit"
                placeholder="Masukkan no telepon WA" />
            </div>
            <div class="form-group mb-3">
              <label for="total_member" class="mb-1">Jumlah Pemancing</label>
              <input value="" required class="form-control" type="number" min="1" name="total_member"
                id="total_member_edit" placeholder="Masukkan jumlah pemancing" />
            </div>
            <div class="form-group mb-3">
              <label class="form-label">Status</label>
              <select data-cy="input-lecturer" id="status_edit" class="form-select" aria-label="Default select example"
                name="status" required>
                <option value="">Pilih Status</option>
                <option value={{App\Support\Enums\ParticipantGroupStatusEnum::UNPAID}}>Belum Lunas</option>
                <option value={{App\Support\Enums\ParticipantGroupStatusEnum::DP}}>DP</option>
                <option value={{App\Support\Enums\ParticipantGroupStatusEnum::PAID}}>Lunas</option>
              </select>
            </div>
            <div class="form-group mb-3">
              <label for="information" class="mb-1">Informasi (Opsional)</label>
              <input value="" required class="form-control" type="text" name="information"
                placeholder="Masukkan informasi tambahan" />
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            <button data-cy="btn-submit-update" type="submit"
              class="btn btn-warning btn-submit text-black">Perbarui</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog ">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="myModalLabel">Hapus Pendaftar</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <h4 class="text-center">Apakah anda yakin menghapus pendaftar <span class="registrant-name"
            id="registrant-name"></span> ?
        </h4>
      </div>
      <form action="" class="form" method="post" id="deleteForm">
        @method('delete')
        @csrf
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button data-cy="btn-delete-confirm" type="submit" id="deleteType"
            class="btn btn-submit btn-danger">Hapus</button>
      </form>
    </div>
  </div>
</div>
@endsection

@push('custom_css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.5.6/css/buttons.dataTables.min.css">
@endpush

@push('js')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>
<script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.6/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.flash.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.print.min.js"></script>
<script>
  $('.search-input').keyup(function() {
          let table = $('#participant-group-table').DataTable();
          table.search($(this).val()).draw();
      });

      $('#participant-group-table').DataTable( {
      order: [[0, 'desc']],
      dom: 'Bfrtip',
      buttons: [ 'pdf', 'excel',]
      }); 
      
      $(document).on("click", ".btn-edit", function () {
          $(".not-found-state").addClass('d-none');
          $("#content-wrapper").addClass("d-none");
          $("#spinner").removeClass('d-none'); 

          let participantGroupID = $(this).data("participant-group-id"); // <-- GET ID FROM BUTTON

          let url = "{{ route('admin.get.ParticipantGroupByID', ['id' => ':id']) }}";
          url = url.replace(':id', participantGroupID);
          
          $.ajax({
            url: url,
            type: "GET",
            
            success: function(response) {
                  let urlEditForm = "{{ route('admin.update.participant.group', ['id' => ':id']) }}";
                  urlEditForm = url.replace(':id', participantGroupID);
                  $('#editForm').attr('action', urlEditForm);

                  // fill form
                  $("#participant-group-id").val(participantGroupID);
                  $("#name_edit").val(response.data.name);
                  $("#phone_num_edit").val(response.data.phone_num);
                  $("#total_member_edit").val(response.data.total_member);
                  $("#status_edit").val(response.data.status);
                  $("#registration_date_edit").val(response.data.registration_date);
                  $("#information_edit").val(response.data.information);

                  // show form
                  $("#content-wrapper").removeClass("d-none");
              },

              error: function() {
                  $(".not-found-state").removeClass('d-none');
              },

              complete: function() {
                  $("#spinner").addClass('d-none'); 
              }
          });

      });

  $(document).on('click', '.btn-delete', function(event){
  let name = $(this).data('registrant-name');
  let deleteLink = $(this).data('delete-link');

  $('#deleteModal').modal('show');
  $('.registrant-name').html(name);

  $('#deleteForm').attr('action', deleteLink);
  });
</script>
@endpush