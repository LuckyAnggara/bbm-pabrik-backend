<template>
  <component :is="layout">
    <router-view />
  </component>
</template>

<script>
import LayoutFull from './views/layouts/LayoutFull.vue'
import LayoutNormal from './views/layouts/LayoutNormal.vue'

export default {
  components: {
    LayoutFull,
    LayoutNormal,
  },
  computed: {
    layout() {
      if (this.$route.meta.layout === 'layout-full') return 'layout-full'
      return 'layout-normal'
    },
  },
}
</script>
