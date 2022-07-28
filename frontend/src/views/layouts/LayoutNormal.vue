<script>
import NavBar from './NavBar.vue'
import MainContent from './MainContent.vue'
import Footer from './Footer.vue'
export default {
  setup() {},
  components: { NavBar, MainContent, Footer },
}
</script>

<template>
  <div class="flex flex-col h-max justify-between">
    <NavBar />
    <MainContent />
    <Footer />
  </div>
</template>
