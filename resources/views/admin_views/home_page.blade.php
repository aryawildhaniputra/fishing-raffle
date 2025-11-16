@extends('layouts.base')

@section('title', 'Home')

@section('custom_css_link', asset('css/Home_style/main.css'))

@section('breadcrumbs')
<div class="breadcrumbs-box mt-1 py-2">
  <nav style="--bs-breadcrumb-divider: '>'" aria-label="breadcrumb">
    <ol class="breadcrumb m-0">
      <li class="breadcrumb-item">
        <a href="" class="text-decoration-none">Beranda</a>
      </li>
    </ol>
  </nav>
</div>

<div class="py-2">
  <div class="head d-flex justify-content-end">
    <div class="btn btn-success" id="add" data-bs-toggle="modal" data-bs-target="#addNewModal">Buat Event Baru</div>
  </div>
  <div class="body mt-2">
    <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3">
      @foreach ($events as $event)
      <div class="col p-1">
        <div class="card h-100">
          <div class="card-body">
            <h5 class="card-title fw-semibold">{{$event->name}}</h5>
            <p class="card-text text-secondary fw-medium date-info mb-0">{{$event->event_date_formatted}}</p>
            <div class="info-content mt-2 d-flex justify-content-between align-items-end">
              <div class="second-info">
                <p class="mb-0">Total Lapak : {{$event->total_stalls}}</p>
                <p class="mb-0">Tersedia : {{$event->total_stalls - $event->total_registrant}}</p>
              </div>
              <div class="price-wrapper">
                <p class="mb-0 fw-bold">{{$event->price_formatted}}</p>
              </div>
            </div>
          </div>
          <div class="card-footer bg-white d-flex justify-content-end gap-2">
            <div class="btn-edit" data-name="{{$event->name}}" data-event-date="{{$event->event_date}}"
              data-price="{{$event->price}}" data-edit-link="{{route('admin.update.event', $event->id)}}"
              data-total-stalls="{{$event->total_stalls}}" data-bs-toggle="modal" data-bs-target="#editModal">
              <i class="ri-edit-line text-warning"></i>
            </div>
            <div class="btn-delete" data-name="{{$event->name}}"
              data-delete-link="{{route('admin.destroy.event', $event->id)}}">
              <i class="ri-delete-bin-line text-danger"></i>
            </div>
          </div>
        </div>
      </div>
      @endforeach
    </div>
  </div>
</div>

<!-- Create Modal -->
<div class="modal fade" id="addNewModal" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog ">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="myModalLabel">Tambah Event</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form action="{{route('admin.store.event')}}" class="form" id="addForm" method="POST">
          @csrf
          <div class="form-group mb-3">
            <label for="name" class="mb-1">Nama</label>
            <input value="" required class="form-control" type="text" name="name" placeholder="Masukkan nama event" />
          </div>
          <div class="form-group mb-3">
            <label for="date" class="mb-1">Tanggal</label>
            <input value="" required class="form-control" type="date" name="event_date" id="date"
              placeholder="Masukkan tanggal event" />
          </div>
          <div class="form-group mb-3">
            <label for="price" class="mb-1">Harga</label>
            <input value="" required class="form-control" type="number" name="price" placeholder="Masukkan harga tiket"
              min="0" />
          </div>
          <div class="form-group mb-3">
            <label for="price" class="mb-1">Jumlah Lapak</label>
            <input value="" required class="form-control" type="number" name="total_stalls"
              placeholder="Masukkan jumlah lapak" min="0" max="222" />
          </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button data-cy="btn-submit-store" type="submit" class="btn btn-submit btn-success text-white">Submit</button>
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
        <h5 class="modal-title" id="myModalLabel">Edit Jenis</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="" class="form" id="editForm" method="post">
        <div class="modal-body">
          @csrf
          <div class="form-group mb-3">
            <label for="name" class="mb-1">Nama</label>
            <input value="" id="name-edit" required class="form-control" type="text" name="name"
              placeholder="Masukkan nama event" />
          </div>
          <div class="form-group mb-3">
            <label for="date" class="mb-1">Tanggal</label>
            <input value="" id="event-date-edit" required class="form-control" type="date" name="event_date" id="date"
              placeholder="Masukkan tanggal event" />
          </div>
          <div class="form-group mb-3">
            <label for="price" class="mb-1">Harga</label>
            <input value="" id="price-edit" required class="form-control" type="number" name="price"
              placeholder="Masukkan harga tiket" min="0" />
          </div>
          <div class="form-group mb-3">
            <label for="price" class="mb-1">Jumlah Lapak</label>
            <input value="" id="total-stalls-edit" required class="form-control" type="number" name="total_stalls"
              placeholder="Masukkan jumlah lapak" min="0" max="222" />
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button data-cy="btn-submit-update" type="submit"
            class="btn btn-warning btn-submit text-black">Submit</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog ">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="myModalLabel">Hapus Event</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <h4 class="text-center">Apakah anda yakin menghapus event <span class="event-name" id="event-name"></span> ?
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

@push('js')
<script>
  $(document).on('click', '.btn-edit', function (event){
          let name = $(this).data('name');
          let eventDate = $(this).data('event-date');
          let price = $(this).data('price');
          let totalStalls = $(this).data('total-stalls');
          let editLink = $(this).data('edit-link');

          $('#name-edit').val(name);
          $('#event-date-edit').val(eventDate);
          $('#price-edit').val(price);
          $('#total-stalls-edit').val(totalStalls);

          // Set form action with Jquery
          $('#editForm').attr('action', editLink);

          $('#editmodal').modal('show');
      });

  $(document).on('click', '.btn-delete', function(event){
  let name = $(this).data('name');
  let deleteLink = $(this).data('delete-link');

  $('#deleteModal').modal('show');
  $('.event-name').html(name);

  $('#deleteForm').attr('action', deleteLink);
});
</script>
@endpush