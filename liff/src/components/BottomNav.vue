<template>
  <div class="btm-nav fixed bottom-0 left-0 right-0 max-w-[430px] mx-auto border-t border-base-300 bg-base-100 z-50">
    <router-link to="/receipt" :class="active('/receipt')">
      <i class="fa-solid fa-star text-lg"></i>
      <span class="btm-nav-label text-xs">สะสมคะแนน</span>
    </router-link>
    <router-link to="/exam" :class="active('/exam')">
      <i class="fa-solid fa-book-open text-lg"></i>
      <span class="btm-nav-label text-xs">แบบทดสอบ</span>
    </router-link>
    <router-link to="/rewards" :class="active('/rewards')">
      <i class="fa-solid fa-gift text-lg"></i>
      <span class="btm-nav-label text-xs">แลกรางวัล</span>
    </router-link>
    <router-link to="/profile" :class="active('/profile')">
      <i class="fa-solid fa-user text-lg"></i>
      <span class="btm-nav-label text-xs">โปรไฟล์</span>
    </router-link>
  </div>
</template>

<script setup>
import { useRoute } from 'vue-router';
const route = useRoute();
function active(path) {
    return route.path === path ? 'active text-primary' : 'text-neutral';
}
</script>

<style scoped>
/* LINE browser: safe-area-inset-bottom ถูกใส่ใน item ทำให้ icon ถูกดันขึ้นบน
   ย้าย padding มาไว้ที่ container แทน */
.btm-nav {
    padding-bottom: env(safe-area-inset-bottom, 0px);
    height: auto;
    min-height: 3.5rem;
}
.btm-nav > :deep(a),
.btm-nav > :deep(*) {
    padding-bottom: 0 !important;
    min-height: 3.5rem;
    justify-content: center;
}
</style>
