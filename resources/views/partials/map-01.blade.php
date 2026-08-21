<div
  class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] sm:p-6"
>
  <div class="flex justify-between">
    <div>
      <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
        Customers Demographic
      </h3>
      <p class="mt-1 text-theme-sm text-gray-500 dark:text-gray-400">
        Number of customer based on country
      </p>
    </div>

    <div x-data="{openDropDown: false}" class="relative h-fit">
      <button
        @click="openDropDown = !openDropDown"
        :class="openDropDown ? 'text-gray-700 dark:text-white' : 'text-gray-400 hover:text-gray-700 dark:hover:text-white'"
      >
        <svg
          class="fill-current"
          width="24"
          height="24"
          viewBox="0 0 24 24"
          fill="none"
          xmlns="http://www.w3.org/2000/svg"
        >
          <path
            fill-rule="evenodd"
            clip-rule="evenodd"
            d="M10.2441 6C10.2441 5.0335 11.0276 4.25 11.9941 4.25H12.0041C12.9706 4.25 13.7541 5.0335 13.7541 6C13.7541 6.9665 12.9706 7.75 12.0041 7.75H11.9941C11.0276 7.75 10.2441 6.9665 10.2441 6ZM10.2441 18C10.2441 17.0335 11.0276 16.25 11.9941 16.25H12.0041C12.9706 16.25 13.7541 17.0335 13.7541 18C13.7541 18.9665 12.9706 19.75 12.0041 19.75H11.9941C11.0276 19.75 10.2441 18.9665 10.2441 18ZM11.9941 10.25C11.0276 10.25 10.2441 11.0335 10.2441 12C10.2441 12.9665 11.0276 13.75 11.9941 13.75H12.0041C12.9706 13.75 13.7541 12.9665 13.7541 12C13.7541 11.0335 12.9706 10.25 12.0041 10.25H11.9941Z"
            fill=""
          />
        </svg>
      </button>
      <div
        x-show="openDropDown"
        @click.outside="openDropDown = false"
        class="absolute right-0 top-full z-40 w-40 space-y-1 rounded-2xl border border-gray-200 bg-white p-2 shadow-theme-lg dark:border-gray-800 dark:bg-gray-dark"
      >
        <button
          class="flex w-full rounded-lg px-3 py-2 text-left text-theme-xs font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-gray-300"
        >
          View More
        </button>
        <button
          class="flex w-full rounded-lg px-3 py-2 text-left text-theme-xs font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-gray-300"
        >
          Delete
        </button>
      </div>
    </div>
  </div>
  <div
    class="border-gary-200 my-6 overflow-hidden rounded-2xl border bg-gray-50 px-4 py-6 dark:border-gray-800 dark:bg-gray-900 sm:px-6"
  >
    <div
      id="mapOne"
      class="mapOne map-btn -mx-4 -my-6 h-[212px] w-[252px] 2xsm:w-[307px] xsm:w-[358px] sm:-mx-6 md:w-[668px] lg:w-[634px] xl:w-[393px] 2xl:w-[554px]"
    ></div>
  </div>

  <div class="space-y-5">
    <div class="flex items-center justify-between">
      <div class="flex items-center gap-3">
        <div class="w-full max-w-8 items-center rounded-full">
          <img onerror="this.onerror=null;this.src=window.SVG_IMAGE_FALLBACK||'data:image/svg+xml;charset=UTF-8,%3Csvg xmlns='http://www.w3.org/2000/svg' width='800' height='600' viewBox='0 0 800 600' fill='none'%3E%3Crect width='800' height='600' fill='%23F3F4F6'/%3E%3Cpath d='M360 260C360 248.954 368.954 240 380 240H420C431.046 240 440 248.954 440 260V265H450C466.569 265 480 278.431 480 295V345C480 361.569 466.569 375 450 375H350C333.431 375 320 361.569 320 345V295C320 278.431 333.431 265 350 265H360V260Z' stroke='%239CA3AF' stroke-width='12' stroke-linecap='round' stroke-linejoin='round'/%3E%3Ccircle cx='400' cy='320' r='30' stroke='%239CA3AF' stroke-width='12'/%3E%3Cline x1='310' y1='240' x2='490' y2='390' stroke='%23EF4444' stroke-width='10' stroke-linecap='round'/%3E%3Ctext x='400' y='450' font-family='sans-serif' font-size='22' font-weight='700' fill='%236B7280' text-anchor='middle'%3EGambar Tidak Dapat Dimuat%3C/text%3E%3C/svg%3E';" src="./images/country/country-01.svg" alt="usa" />
        </div>
        <div>
          <p
            class="text-theme-sm font-semibold text-gray-800 dark:text-white/90"
          >
            USA
          </p>
          <span class="block text-theme-xs text-gray-500 dark:text-gray-400">
            2,379 Customers
          </span>
        </div>
      </div>

      <div class="flex w-full max-w-[140px] items-center gap-3">
        <div
          class="relative block h-2 w-full max-w-[100px] rounded-sm bg-gray-200 dark:bg-gray-800"
        >
          <div
            class="absolute left-0 top-0 flex h-full w-[79%] items-center justify-center rounded-sm bg-brand-500 text-xs font-medium text-white"
          ></div>
        </div>
        <p class="text-theme-sm font-medium text-gray-800 dark:text-white/90">
          79%
        </p>
      </div>
    </div>

    <div class="flex items-center justify-between">
      <div class="flex items-center gap-3">
        <div class="w-full max-w-8 items-center rounded-full">
          <img onerror="this.onerror=null;this.src=window.SVG_IMAGE_FALLBACK||'data:image/svg+xml;charset=UTF-8,%3Csvg xmlns='http://www.w3.org/2000/svg' width='800' height='600' viewBox='0 0 800 600' fill='none'%3E%3Crect width='800' height='600' fill='%23F3F4F6'/%3E%3Cpath d='M360 260C360 248.954 368.954 240 380 240H420C431.046 240 440 248.954 440 260V265H450C466.569 265 480 278.431 480 295V345C480 361.569 466.569 375 450 375H350C333.431 375 320 361.569 320 345V295C320 278.431 333.431 265 350 265H360V260Z' stroke='%239CA3AF' stroke-width='12' stroke-linecap='round' stroke-linejoin='round'/%3E%3Ccircle cx='400' cy='320' r='30' stroke='%239CA3AF' stroke-width='12'/%3E%3Cline x1='310' y1='240' x2='490' y2='390' stroke='%23EF4444' stroke-width='10' stroke-linecap='round'/%3E%3Ctext x='400' y='450' font-family='sans-serif' font-size='22' font-weight='700' fill='%236B7280' text-anchor='middle'%3EGambar Tidak Dapat Dimuat%3C/text%3E%3C/svg%3E';" src="./images/country/country-02.svg" alt="france" />
        </div>
        <div>
          <p
            class="text-theme-sm font-semibold text-gray-800 dark:text-white/90"
          >
            France
          </p>
          <span class="block text-theme-xs text-gray-500 dark:text-gray-400">
            589 Customers
          </span>
        </div>
      </div>

      <div class="flex w-full max-w-[140px] items-center gap-3">
        <div
          class="relative block h-2 w-full max-w-[100px] rounded-sm bg-gray-200 dark:bg-gray-800"
        >
          <div
            class="absolute left-0 top-0 flex h-full w-[23%] items-center justify-center rounded-sm bg-brand-500 text-xs font-medium text-white"
          ></div>
        </div>
        <p class="text-theme-sm font-medium text-gray-800 dark:text-white/90">
          23%
        </p>
      </div>
    </div>
  </div>
</div>
