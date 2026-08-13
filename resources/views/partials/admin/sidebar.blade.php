<aside :class="sidebarToggle ? 'translate-x-0 lg:w-[90px]' : '-translate-x-full'"
  class="sidebar fixed left-0 top-0 z-9999 flex h-screen w-[290px] flex-col overflow-y-hidden border-r border-gray-200 bg-white px-5 dark:border-gray-800 dark:bg-gray-900 lg:static lg:translate-x-0">
  <!-- SIDEBAR HEADER -->
  <div :class="sidebarToggle ? 'justify-center' : 'justify-between'"
    class="flex items-center gap-2 pt-6 sidebar-header pb-6 border-b border-gray-100 dark:border-gray-800">
    <a href="{{ url('/admin/dashboard') }}" class="flex items-center gap-2">
      <img src="{{ asset('images/logo/lensmatch_logo_transparent_yellow.png') }}" class="h-8 w-auto" alt="LensMatch Logo" :class="sidebarToggle ? 'hidden' : ''" />
    </a>
  </div>

  <div class="flex flex-col overflow-y-auto duration-300 ease-linear no-scrollbar pt-6">
    <nav>
      <div>
        <h3 class="mb-4 text-[10px] font-bold uppercase tracking-wider text-gray-400">
          <span class="menu-group-title" :class="sidebarToggle ? 'lg:hidden' : ''">
            NAVIGASI UTAMA ADMIN
          </span>
        </h3>

        <ul class="flex flex-col gap-1.5 mb-6 text-sm font-semibold">
          <!-- Dashboard -->
          <li>
            <a href="{{ url('/admin/dashboard') }}" class="flex items-center gap-3 rounded-xl px-3.5 py-2.5 {{ request()->is('admin/dashboard') ? 'bg-amber-50 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400 font-bold' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/5' }}">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" class="fill-current" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M5.5 3.25C4.25736 3.25 3.25 4.25736 3.25 5.5V8.99998C3.25 10.2426 4.25736 11.25 5.5 11.25H9C10.2426 11.25 11.25 10.2426 11.25 8.99998V5.5C11.25 4.25736 10.2426 3.25 9 3.25H5.5ZM4.75 5.5C4.75 5.08579 5.08579 4.75 5.5 4.75H9C9.41421 4.75 9.75 5.08579 9.75 5.5V8.99998C9.75 9.41419 9.41421 9.74998 9 9.74998H5.5C5.08579 9.74998 4.75 9.41419 4.75 8.99998V5.5ZM5.5 12.75C4.25736 12.75 3.25 13.7574 3.25 15V18.5C3.25 19.7426 4.25736 20.75 5.5 20.75H9C10.2426 20.75 11.25 19.7427 11.25 18.5V15C11.25 13.7574 10.2426 12.75 9 12.75H5.5ZM4.75 15C4.75 14.5858 5.08579 14.25 5.5 14.25H9C9.41421 14.25 9.75 14.5858 9.75 15V18.5C9.75 18.9142 9.41421 19.25 9 19.25H5.5C5.08579 19.25 4.75 18.9142 4.75 18.5V15ZM12.75 5.5C12.75 4.25736 13.7574 3.25 15 3.25H18.5C19.7426 3.25 20.75 4.25736 20.75 5.5V8.99998C20.75 10.2426 19.7426 11.25 18.5 11.25H15C13.7574 11.25 12.75 10.2426 12.75 8.99998V5.5ZM15 4.75C14.5858 4.75 14.25 5.08579 15 5.5V8.99998C14.25 9.41419 14.5858 9.74998 15 9.74998H18.5C18.9142 9.74998 19.25 9.41419 19.25 8.99998V5.5C19.25 5.08579 18.9142 4.75 18.5 4.75H15ZM15 12.75C13.7574 12.75 12.75 13.7574 12.75 15V18.5C12.75 19.7426 13.7574 20.75 15 20.75H18.5C19.7426 20.75 20.75 19.7427 20.75 18.5V15C20.75 13.7574 19.7426 12.75 18.5 12.75H15C14.25 15C14.25 14.5858 14.5858 14.25 15 14.25H18.5C18.9142 14.25 19.25 14.5858 19.25 15V18.5C19.25 18.9142 18.9142 19.25 18.5 19.25H15C14.5858 19.25 14.25 18.9142 14.25 18.5V15Z"/>
              </svg>
              <span :class="sidebarToggle ? 'lg:hidden' : ''">Dashboard</span>
            </a>
          </li>

          <!-- User & Fotografer -->
          <li>
            <a href="{{ url('/admin/users') }}" class="flex items-center gap-3 rounded-xl px-3.5 py-2.5 {{ request()->is('admin/users') ? 'bg-amber-50 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400 font-bold' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/5' }}">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" class="fill-current" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M12 3.5C7.30558 3.5 3.5 7.30558 3.5 12C3.5 14.1526 4.3002 16.1184 5.61936 17.616C6.17279 15.3096 8.24852 13.5955 10.7246 13.5955H13.2746C15.7509 13.5955 17.8268 15.31 18.38 17.6167C19.6996 16.119 20.5 14.153 20.5 12C20.5 7.30558 16.6944 3.5 12 3.5ZM17.0246 18.8566V18.8455C17.0246 16.7744 15.3457 15.0955 13.2746 15.0955H10.7246C8.65354 15.0955 6.97461 16.7744 6.97461 18.8455V18.856C8.38223 19.8895 10.1198 20.5 12 20.5C13.8798 20.5 15.6171 19.8898 17.0246 18.8566ZM2 12C2 6.47715 6.47715 2 12 2C17.5228 2 22 6.47715 22 12C22 17.5228 17.5228 22 12 22C6.47715 22 2 17.5228 2 12ZM11.9991 7.25C10.8847 7.25 9.98126 8.15342 9.98126 9.26784C9.98126 10.3823 10.8847 11.2857 11.9991 11.2857C13.1135 11.2857 14.0169 10.3823 14.0169 9.26784C14.0169 8.15342 13.1135 7.25 11.9991 7.25Z"/>
              </svg>
              <span :class="sidebarToggle ? 'lg:hidden' : ''">User & Verifikasi Fotografer</span>
            </a>
          </li>

          <!-- Kategori -->
          <li>
            <a href="{{ url('/admin/categories') }}" class="flex items-center gap-3 rounded-xl px-3.5 py-2.5 {{ request()->is('admin/categories') ? 'bg-amber-50 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400 font-bold' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/5' }}">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" class="fill-current" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M5.5 3.25C4.25736 3.25 3.25 4.25736 3.25 5.5V18.5C3.25 19.7426 4.25736 20.75 5.5 20.75H18.5001C19.7427 20.75 20.7501 19.7426 20.7501 18.5V5.5C20.7501 4.25736 19.7427 3.25 18.5001 3.25H5.5ZM4.75 5.5C4.75 5.08579 5.08579 4.75 5.5 4.75H18.5001C18.9143 4.75 19.2501 5.08579 19.2501 5.5V18.5C19.2501 18.9142 18.9143 19.25 18.5001 19.25H5.5C5.08579 19.25 4.75 18.9142 4.75 18.5V5.5ZM6.25005 9.7143C6.25005 9.30008 6.58583 8.9643 7.00005 8.9643L17 8.96429C17.4143 8.96429 17.75 9.30008 17.75 9.71429C17.75 10.1285 17.4143 10.4643 17 10.4643L7.00005 10.4643C6.58583 10.4643 6.25005 10.1285 6.25005 9.7143Z"/>
              </svg>
              <span :class="sidebarToggle ? 'lg:hidden' : ''">Kategori Layanan</span>
            </a>
          </li>

          <!-- Transaksi & Booking -->
          <li>
            <a href="{{ url('/admin/transactions') }}" class="flex items-center gap-3 rounded-xl px-3.5 py-2.5 {{ request()->is('admin/transactions') ? 'bg-amber-50 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400 font-bold' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/5' }}">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" class="fill-current" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M3.25 5.5C3.25 4.25736 4.25736 3.25 5.5 3.25H18.5C19.7426 3.25 20.75 4.25736 20.75 5.5V18.5C20.75 19.7426 19.7426 20.75 18.5 20.75H5.5C4.25736 20.75 3.25 19.7426 3.25 18.5V5.5ZM5.5 4.75C5.08579 4.75 4.75 5.08579 4.75 5.5V8.58325L19.25 8.58325V5.5C19.25 5.08579 18.9142 4.75 18.5 4.75H5.5ZM19.25 10.0833H15.416V13.9165H19.25V10.0833ZM13.916 10.0833L10.083 10.0833V13.9165L13.916 13.9165V10.0833ZM8.58301 10.0833H4.75V13.9165H8.58301V10.0833ZM4.75 18.5V15.4165H8.58301V19.25H5.5C5.08579 19.25 4.75 18.9142 4.75 18.5ZM10.083 19.25V15.4165L13.916 15.4165V19.25H10.083ZM15.416 19.25V15.4165H19.25V18.5C19.25 18.9142 18.9142 19.25 18.5 19.25H15.416Z"/>
              </svg>
              <span :class="sidebarToggle ? 'lg:hidden' : ''">Monitoring Transaksi</span>
            </a>
          </li>

          <!-- Lomba Foto -->
          <li>
            <a href="{{ url('/admin/contests') }}" class="flex items-center gap-3 rounded-xl px-3.5 py-2.5 {{ request()->is('admin/contests') ? 'bg-amber-50 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400 font-bold' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/5' }}">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" class="fill-current" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z"/>
              </svg>
              <span :class="sidebarToggle ? 'lg:hidden' : ''">Kelola Lomba Foto</span>
            </a>
          </li>

          <!-- E-Learning -->
          <li>
            <a href="{{ url('/admin/elearning') }}" class="flex items-center gap-3 rounded-xl px-3.5 py-2.5 {{ request()->is('admin/elearning') ? 'bg-amber-50 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400 font-bold' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/5' }}">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" class="fill-current" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M4 6H20V18H4V6ZM2 4C2 2.89543 2.89543 2 4 2H20C21.1046 2 22 2.89543 22 4V18C22 19.1046 21.1046 20 20 20H4C2.89543 20 2 19.1046 2 18V4Z"/>
              </svg>
              <span :class="sidebarToggle ? 'lg:hidden' : ''">Materi E-Learning</span>
            </a>
          </li>

          <!-- Moderasi Forum -->
          <li>
            <a href="{{ url('/admin/forum') }}" class="flex items-center gap-3 rounded-xl px-3.5 py-2.5 {{ request()->is('admin/forum') ? 'bg-amber-50 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400 font-bold' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/5' }}">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" class="fill-current" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M20 2H4C2.9 2 2 2.9 2 4V22L6 18H20C21.1 18 22 17.1 22 16V4C22 2.9 21.1 2 20 2ZM20 16H5.17L4 17.17V4H20V16Z"/>
              </svg>
              <span :class="sidebarToggle ? 'lg:hidden' : ''">Moderasi Forum</span>
            </a>
          </li>
        </ul>
      </div>
    </nav>
  </div>
</aside>
