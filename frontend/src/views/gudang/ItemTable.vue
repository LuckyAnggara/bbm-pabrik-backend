<template>
  <div class="card flex bg-neutral flex-col">
    <div class="card shadow-xl rounded-xl p-2">
      <div class="md:flex py-2">
        <div class="w-1/5 ml-2">
          <label
            for="my-modal-3"
            class="btn btn-sm btn-secondary modal-button shadow-md"
            ><span class="text-xs">New Item</span></label
          >
        </div>
        <div class="w-full mx-1">
          <label class="text-sm mr-4">Jumlah Data </label>
          <select
            v-model="currentLimit"
            class="select select-bordered select-sm max-w-xs"
          >
            <option
              :selected="currentLimit == length ? true : false"
              v-for="length in length"
              :key="length"
            >
              {{ length }}
            </option>
          </select>
        </div>

        <div class="justify-end mx-1 w-1/3">
          <div class="form-control">
            <div class="input-group">
              <input
                v-model="search"
                type="text"
                placeholder="Search…"
                class="input input-bordered input-sm w-full max-w-xs"
              />
              <button
                class="btn btn-square btn-sm btn-outline"
                @click="searchData"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-6 w-6"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                  />
                </svg>
              </button>
            </div>
          </div>
        </div>
      </div>

      <div class="flex mt-2 overflow-x-auto">
        <table class="table table-compact w-full">
          <!-- head -->
          <thead>
            <tr>
              <th></th>
              <th>Nama</th>
              <th>Unit</th>
              <th>Tipe</th>
              <th>Saldo</th>
              <th>Action</th>
            </tr>
          </thead>

          <tbody>
            <tr v-if="isLoading">
              <td colspan="6" class="text-center">
                <div role="status">
                  <svg
                    class="inline mr-2 w-8 h-8 text-gray-200 animate-spin dark:text-gray-600 fill-pink-600"
                    viewBox="0 0 100 101"
                    fill="none"
                    xmlns="http://www.w3.org/2000/svg"
                  >
                    <path
                      d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
                      fill="currentColor"
                    />
                    <path
                      d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
                      fill="currentFill"
                    />
                  </svg>
                  <span class="sr-only">Loading...</span>
                </div>
              </td>
            </tr>
            <tr v-else v-for="(item, index) in dataTable" :key="item">
              <template v-if="isDataEmpty">
                <td colspan="6" class="text-center">
                  <a>Tidak ada data</a>
                </td>
              </template>
              <template v-else>
                <td>{{ index }}</td>
                <td>{{ item.name.toUpperCase() }}</td>
                <td>{{ item.unit.name.toUpperCase() }}</td>
                <td>{{ item.type.name.toUpperCase() }}</td>
                <td>{{ item.balance }}</td>
                <td>
                  <div class="mx-2 dropdown" :class="position(index)">
                    <button class="btn btn-sm btn-square btn-ghost">
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        class="inline-block w-5 h-5 stroke-current"
                      >
                        <path
                          stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M5 12h.01M12 12h.01M19 12h.01M6 12a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z"
                        ></path>
                      </svg>
                    </button>
                    <ul
                      tabindex="0"
                      class="mt-3 p-2 shadow menu menu-compact dropdown-content bg-base-200 rounded-box w-52"
                    >
                      <li>
                        <a> Detail </a>
                      </li>
                      <li><a>Hapus</a></li>
                    </ul>
                  </div>
                </td>
              </template>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="btn-group mx-auto mt-4 mb-1 justify-center" v-if="!isLoading">
        <button
          class="btn btn-outline"
          @click="previousPage"
          :disabled="dataApi.current_page == 1 ? true : false"
        >
          «
        </button>
        <button class="btn btn-outline">Page {{ dataApi.current_page }}</button>
        <button
          class="btn btn-outline"
          @click="nextPage"
          :disabled="dataApi.last_page == dataApi.current_page ? true : false"
        >
          »
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import { ref } from 'vue'
export default {
  setup() {
    const search = ref('')
    const isLoading = ref(true)
    const dataApi = ref()
    const length = ref([10, 20, 30, 40, 50])
    const count = ref([1, 2, 3, 4, 5, 6, 7, 8, 9, 10])
    const currentLimit = ref(10)

    // expose to template and other options API hooks
    return {
      search,
      isLoading,
      dataApi,
      length,
      count,
      currentLimit,
    }
  },
  watch: {
    currentLimit() {
      this.getData()
    },
  },
  computed: {
    dataTable() {
      return this.dataApi.data
    },
    searchName() {
      if (this.search == '' || null) {
        return ''
      }
      return '&name=' + this.search
    },
    isDataEmpty() {
      return this.dataTable.length < 1 ? true : false
    },
  },
  methods: {
    searchData() {
      return this.getData(this.search)
    },
    position(index) {
      if (index < 2) {
        return 'dropdown-end'
      }
      return 'dropdown-top dropdown-left'
    },
    getData() {
      this.isLoading = true
      this.$axios
        .get(
          `/items?limit=${this.currentLimit}${this.searchName}`
          // {
          //   headers: {
          //     Authorization: `${this.token.token_type} ${this.token.access_token}`,
          //   },
          // }
        )
        .then((res) => {
          this.dataApi = res.data.data
          this.isLoading = false
          console.info(this.isDataEmpty)
        })
    },
    previousPage() {
      this.isLoading = true
      this.$axios
        .get(
          `/items?limit=${this.currentLimit}${this.searchName}&page=${
            this.dataApi.current_page - 1
          }`
          // {
          //   headers: {
          //     Authorization: `${this.token.token_type} ${this.token.access_token}`,
          //   },
          // }
        )
        .then((res) => {
          this.dataApi = res.data.data
          this.isLoading = false
        })
    },
    nextPage() {
      this.isLoading = true
      this.$axios
        .get(
          `/items?limit=${this.currentLimit}${this.searchName}&page=${
            this.dataApi.current_page + 1
          }`
          // {
          //   headers: {
          //     Authorization: `${this.token.token_type} ${this.token.access_token}`,
          //   },
          // }
        )
        .then((res) => {
          this.dataApi = res.data.data
          this.isLoading = false
        })
    },
  },
  created() {
    this.getData(this.search)
  },
}
</script>
