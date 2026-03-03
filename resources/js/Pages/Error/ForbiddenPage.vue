<template>
  <div class="col-span-full flex flex-col gap-2.5">
    <p class="font-bold text-4xl">403</p>
    <p class="font-bold text-xl">{{ href }}</p>
    <p class="text-xl">Отсутствуют права доступа!</p>
    <p>Извините, но для перехода на эту страницу у вас отсутствуют права доступа.</p>
  </div>
</template>
<script setup>
const urlParams = new URLSearchParams(window.location.search);
const href = urlParams.get("href");
</script>

<style scoped lang="less"></style>
