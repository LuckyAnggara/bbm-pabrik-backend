<template>
  <div class="navbar mb-2 shadow-lg bg-neutral">
    <div class="flex-1 px-2 mx-2">
      <span class="text-lg font-bold"> BBM Manufaktur </span>
    </div>
    <div class="flex-none hidden px-2 mx-2 lg:flex">
      <ul class="text-md menu menu-horizontal p-0">
        <li class="mx-1"><a>Dashboard</a></li>
        <li class="mx-1"><a class="bg-base-200">Gudang</a></li>
        <li class="mx-1"><a>Production</a></li>
        <li class="mx-1"><a>Laporan</a></li>
      </ul>
      <div class="mx-2 dropdown dropdown-end">
        <label tabindex="0" class="btn btn-ghost btn-circle avatar">
          <div class="w-10 rounded-full">
            <img src="https://placeimg.com/80/80/people" />
          </div>
        </label>
        <ul
          tabindex="0"
          class="mt-3 p-2 shadow menu menu-compact dropdown-content bg-neutral rounded-box w-52"
        >
          <li>
            <a class="justify-between">
              Profile
              <span class="badge">New</span>
            </a>
          </li>
          <li><a>Settings</a></li>
          <li><a>Logout</a></li>
        </ul>
      </div>
    </div>
  </div>
</template>
