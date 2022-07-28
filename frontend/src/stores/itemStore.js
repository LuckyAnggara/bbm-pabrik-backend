import { defineStore } from 'pinia'

import { axiosIns } from '../services/axios'

export const useItemStore = defineStore('itemStore', {
  state: () => {
    return { items: [], itemTypes: [], itemUnits: [] }
  },
  getters: {},
  actions: {
    async getItemTypeData() {
      try {
        const response = await axiosIns.get(`/item-types`)
        this.itemTypes = response.data.data.data
      } catch (error) {
        alert(error)
      }
    },
    async getItemUnitData() {
      try {
        const response = await axiosIns.get(`/item-units`)
        this.itemUnits = response.data.data.data
      } catch (error) {
        alert(error)
      }
    },
  },
})
