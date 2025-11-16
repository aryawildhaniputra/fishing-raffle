<nav class="navbar bg-body-tertiary position-relative">
  <div class="container-lg">
    <div>
      <p class="fw-bold mb-0 text-white text-hero">Sistem Undian Pemancingan</p>
    </div>

    @if (auth()->user())
    <div class="dropdown" data-cy="btn-dropdown-account">
      <a class="nav-link d-flex gap-2 pt-3 pt-md-0 align-items-center justify-content-end dropdown-toggle"
        href="user-edit-profile.html" role="button" aria-current="page" data-bs-toggle="dropdown" aria-expanded="false">
        <img src="{{asset('img/default-profile.png')}}" class="img-fluid img-avatar" />
      </a>
      <ul class="dropdown-menu dropdown-menu-end px-2">
        <li class="rounded-2 dropdown-list">
          <p class="mb-0 text-white text-center">
            {{auth()->user()->name}}
          </p>
        </li>
        <li class="rounded-2 dropdown-list my-profile">
          <a class="dropdown-item text-white rounded-2" href="{{route('admin.editProfile')}}"
            data-cy="btn-edit-account"><i class="ri-user-3-line me-2 text-white"></i>Edit Profil</a>
        </li>
        <li class="rounded-2 dropdown-list">
          <form id="form-tag" action="{{route('logout')}}" method="POST">
            @csrf
            <button data-cy="btn-logout" type="submit" class="dropdown-item btn-submit rounded-2 text-white"><i
                class="ri-logout-circle-line me-2 text-white"></i>Log Out</button>
          </form>
        </li>
      </ul>
    </div>
    @else
    <a href="" class="login-link text-decoration-none d-flex align-items-center gap-1 "><i
        class="ri-login-circle-line"></i>Log In</a>
    @endif
  </div>
</nav>