<template>
  <input type="checkbox" id="my-modal-3" class="modal-toggle" />
  <div class="modal">
    <div class="modal-box bg-neutral text-white">
      <label
        for="my-modal-3"
        class="btn btn-sm btn-circle btn-secondary absolute right-2 top-2"
        >✕</label
      >
      <h3 class="text-2xl font-bold">Item Baru</h3>
      <section class="py-2">
        <div class="form-control my-2">
          <label class="label">
            <span class="label-text">Nama Item</span>
          </label>
          <input
            type="text"
            placeholder="Type here"
            class="input input-bordered w-full"
          />
        </div>
        <div class="form-control my-2">
          <label class="label">
            <span class="label-text">Unit Item</span>
          </label>
          <select class="select select-bordered">
            <option :value="unit.id" v-for="unit in itemUnits" :key="unit">
              {{ unit.name.toUpperCase() }}
            </option>
          </select>
        </div>
        <div class="form-control my-2">
          <label class="label">
            <span class="label-text">Tipe Item</span>
          </label>
          <select class="select select-bordered">
            <option :value="type.id" v-for="type in itemTypes" :key="type">
              {{ type.name.toUpperCase() }}
            </option>
          </select>
        </div>

        <div class="card-actions justify-end mt-10">
          <label for="my-modal-3" class="btn btn-secondary modal-button"
            >Submit</label
          >
        </div>
      </section>
    </div>
  </div>
</template>

<script>
import { mapState, mapStores } from 'pinia'
import { useItemStore } from '../../stores/itemStore'
export default {
  computed: {
    ...mapState(useItemStore, ['itemTypes', 'itemUnits']),
  },
}
</script>
