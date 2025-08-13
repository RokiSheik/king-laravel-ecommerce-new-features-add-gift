<template>
  <section class="pt-8 pb-24 sm:py-12 border-t border-slate-100 w-full">
    <div class="px-4 sm:px-6 lg:px-8">
      <div class="w-full px-10">
        <!-- Display title -->
        <h2 class="text-2xl font-semibold mb-4 text-center">{{ title }}</h2>

        <!-- Display description -->
        <p class="text-sm text-center">
          {{ description }}
        </p>
      </div>
    </div>
  </section>
</template>

<script>
export default {
  name: "HomeDescriptionComponent",
  props: {
    title: {
      type: String,
      required: true,
      default: "Default Title",
    },
    description: {
      type: String,
      required: true,
      default: "This is the default description for Home Page.",
    },
  },
};
</script>

<style scoped>
/* Optional: add custom styles here */
</style>
