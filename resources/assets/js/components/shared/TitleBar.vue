<template>
  <div class="title-bar">
    <h1>{{ title }}</h1>

  </div>
</template>

<script>
import { Event } from "../../app.js";

export default {
  props: {
    title: {
      type: String,
      required: true
    }
  },
  data() {
    return {};
  },
  methods: {}
};
</script>

<style lang="scss" scoped>
</style>
