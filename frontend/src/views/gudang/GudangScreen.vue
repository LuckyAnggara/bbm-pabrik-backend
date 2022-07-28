<template>
  <section>
    <div class="md:flex">
      <div class="card md:w-1/3 w-full bg-neutral shadow-xl mr-10 self-start">
        <div class="card-body">
          <h2 class="card-title">Data Gudang dan Persediaan</h2>
          <p>If a dog chews shoes whose shoes does he choose?</p>
          <div class="card-actions justify-end">
            <label for="my-modal-3" class="btn btn-primary modal-button"
              >open modal</label
            >
          </div>
        </div>
      </div>
      <div class="md:w-2/3 mt-10 md:mt-0 justify-self-end">
        <ItemTable />
      </div>
    </div>
    <ModalNewItem />
  </section>
</template>

<script>
import ItemTable from './ItemTable.vue'
import ModalNewItem from './ModalNewItem.vue'
import { useItemStore } from '@/stores/itemStore'

export default {
  setup() {
    const itemStore = useItemStore()
    itemStore.getItemTypeData()
    itemStore.getItemUnitData()
  },
  components: { ItemTable, ModalNewItem },
}
</script>
