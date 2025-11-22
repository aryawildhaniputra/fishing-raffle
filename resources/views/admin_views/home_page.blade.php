@extends('layouts.base')

@section('title', 'Beranda')

@section('custom_css_link', asset('css/Home_style/main.css'))

@section('breadcrumbs')
<div class="breadcrumbs-box mt-1 py-2">
  <nav style="--bs-breadcrumb-divider: '>'" aria-label="breadcrumb">
    <ol class="breadcrumb m-0">
      <li class="breadcrumb-item active" aria-current="page">
        Beranda
      </li>
    </ol>
  </nav>
</div>

<div class="py-2">
  <div class="head d-flex justify-content-between align-items-center gap-3">
    <!-- Date Filter & Search Bar (Kiri) -->
    <div class="d-flex gap-2 align-items-center">
      <!-- Date Filter -->
      <div class="date-filter-wrapper">
        <div class="input-group">
          <span class="input-group-text bg-white">
            <i class="ri-calendar-line"></i>
          </span>
          <input type="date" class="form-control py-2 px-3 border-start-0" id="dateFilter" name="date_filter" />
        </div>
      </div>
      
      <!-- Search Bar -->
      <div class="search-wrapper">
        <div class="input-group">
          <span class="input-group-text bg-white">
            <i class="ri-search-line"></i>
          </span>
          <input type="text" class="form-control py-2 px-3 border-start-0" id="searchEvent" placeholder="Telusuri" name="search" />
        </div>
      </div>
    </div>
    
    <!-- Tombol Buat Event Baru (Kanan) -->
    <div class="btn btn-success flex-shrink-0" id="add" data-bs-toggle="modal" data-bs-target="#addNewModal">Buat Event Baru</div>
  </div>
  <div class="body mt-2" id="eventsBodyContainer">
    <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3" id="eventCardsContainer">
      @php
        $cardVariants = ['event-card--forest', 'event-card--spruce', 'event-card--mint', 'event-card--teal', 'event-card--moss'];
      @endphp
      @foreach ($events as $event)
      <div class="col p-1 event-card-wrapper" 
           data-event-name="{{strtolower($event->name)}}" 
           data-event-date="{{$event->event_date_formatted}}" 
           data-event-price="{{$event->price_formatted}}">
        <div class="card event-card {{$cardVariants[$loop->index % count($cardVariants)]}} h-100">
          <a class="card-body text-decoration-none" href="{{route('admin.detail.event', $event->id)}}">
            <h5 class="card-title fw-semibold text-white">{{$event->name}}</h5>
            <p class="card-text text-white fw-light date-info mb-0">{{$event->event_date_formatted}}</p>
            <div class="info-content mt-2 d-flex justify-content-between align-items-end">
              <div class="second-info">
                <p class="mb-0 text-white fw-medium">Lapak Tersedia : {{App\Support\Constants\Constants::MAX_STALLS -
                  $event->total_registrant}}</p>
              </div>
              <div class="price-wrapper">
                <p class="mb-0 fw-semibold text-white">{{$event->price_formatted}}</p>
              </div>
            </div>
          </a>
          <div class="card-footer bg-white d-flex justify-content-end gap-2">
            <div class="btn-edit" data-name="{{$event->name}}" data-event-date="{{$event->event_date}}"
              data-price="{{$event->price}}" data-edit-link="{{route('admin.update.event', $event->id)}}"
              data-bs-toggle="modal" data-bs-target="#editModal">
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
  // Combined Search and Date Filter Function
  function filterEvents() {
    let searchQuery = $('#searchEvent').val().toLowerCase().trim();
    let selectedDate = $('#dateFilter').val();
    let visibleCount = 0;
    
    $('.event-card-wrapper').each(function() {
      let eventName = $(this).data('event-name');
      let eventDate = $(this).data('event-date').toLowerCase();
      let eventPrice = $(this).data('event-price').toLowerCase();
      
      // Check search query match
      let matchesSearch = searchQuery === '' || 
                         eventName.includes(searchQuery) || 
                         eventDate.includes(searchQuery) || 
                         eventPrice.includes(searchQuery);
      
      // Check date filter match
      let matchesDate = true;
      if (selectedDate !== '') {
        // Convert selected date to readable format for comparison
        let dateObj = new Date(selectedDate);
        let options = { day: 'numeric', month: 'long', year: 'numeric' };
        let formattedDate = dateObj.toLocaleDateString('id-ID', options).toLowerCase();
        matchesDate = eventDate.includes(formattedDate);
      }
      
      // Show/hide based on both filters
      if (matchesSearch && matchesDate) {
        $(this).show();
        visibleCount++;
      } else {
        $(this).hide();
      }
    });
    
    // Show/hide "no results" message
    if (visibleCount === 0 && (searchQuery !== '' || selectedDate !== '')) {
      if ($('#noResultsMessage').length === 0) {
        let filterText = searchQuery !== '' ? 'pencarian "<strong>' + searchQuery + '</strong>"' : '';
        if (selectedDate !== '' && searchQuery !== '') {
          filterText += ' dan ';
        }
        if (selectedDate !== '') {
          filterText += 'tanggal yang dipilih';
        }
        
        $('#eventsBodyContainer').append(
          '<div id="noResultsMessage" class="text-center py-5" style="min-height: 300px; display: flex; flex-direction: column; justify-content: center; align-items: center;">' +
            '<i class="ri-search-line" style="font-size: 64px; color: #999;"></i>' +
            '<p class="mt-3 text-muted fs-5">Tidak ada event yang sesuai dengan ' + filterText + '</p>' +
          '</div>'
        );
      }
    } else {
      $('#noResultsMessage').remove();
    }
  }

  // Search Event Functionality
  $('#searchEvent').on('keyup', debounce(filterEvents, 300));
  
  // Date Filter Functionality
  $('#dateFilter').on('change', filterEvents);

  $(document).on('click', '.btn-edit', function (event){
          let name = $(this).data('name');
          let eventDate = $(this).data('event-date');
          let price = $(this).data('price');
          let editLink = $(this).data('edit-link');

          $('#name-edit').val(name);
          $('#event-date-edit').val(eventDate);
          $('#price-edit').val(price);

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
